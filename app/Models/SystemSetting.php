<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Cache duration in seconds.
     */
    protected static int $cacheDuration = 3600;

    /**
     * Get a typed setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("system_setting_{$key}", self::$cacheDuration, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, ?string $group = null, ?string $type = null, ?string $label = null, ?string $description = null): self
    {
        $setting = self::where('key', $key)->first();

        if ($type === null && $setting) {
            $type = $setting->type;
        }

        $type = $type ?? (is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : (is_array($value) ? 'json' : 'string')));
        $rawValue = self::serializeValue($value, $type);

        $data = [
            'value' => $rawValue,
            'type' => $type,
        ];

        if ($group !== null) $data['group'] = $group;
        if ($label !== null) $data['label'] = $label;
        if ($description !== null) $data['description'] = $description;

        $instance = self::updateOrCreate(['key' => $key], $data);

        Cache::forget("system_setting_{$key}");
        Cache::forget("system_settings_group_" . ($setting ? $setting->group : ($group ?? 'general')));
        Cache::forget('system_settings_all_grouped');

        return $instance;
    }

    /**
     * Get all settings in a specific group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("system_settings_group_{$group}", self::$cacheDuration, function () use ($group) {
            $settings = self::where('group', $group)->get();
            $result = [];
            foreach ($settings as $s) {
                $result[$s->key] = self::castValue($s->value, $s->type);
            }
            return $result;
        });
    }

    /**
     * Get all settings grouped by group.
     */
    public static function getAllGrouped(): array
    {
        return Cache::remember('system_settings_all_grouped', self::$cacheDuration, function () {
            return self::all()->groupBy('group')->toArray();
        });
    }

    /**
     * Clear all system settings caches.
     */
    public static function clearCache(): void
    {
        Cache::forget('system_settings_all_grouped');
        $keys = self::pluck('key')->all();
        foreach ($keys as $k) {
            Cache::forget("system_setting_{$k}");
        }
    }

    /**
     * Check if a pipeline workflow step is enabled.
     */
    public static function isPipelineStepEnabled(string $step): bool
    {
        $key = match ($step) {
            'peer_review' => 'enable_peer_review_step',
            'scrutiny' => 'enable_scrutiny_step',
            'appellate' => 'enable_appellate_step',
            'board_interview' => 'enable_board_interview_step',
            default => "enable_{$step}_step",
        };

        return (bool) self::get($key, true);
    }

    /**
     * Check if a document upload is required.
     */
    public static function isDocumentRequired(string $docType): bool
    {
        $key = match ($docType) {
            'transcript', 'transcripts' => 'require_transcript_proof',
            'experience' => 'require_experience_proof',
            'cnic', 'national_id' => 'require_cnic_upload',
            'noc' => 'require_noc_employed',
            'publications' => 'require_publications_academic',
            'challan_receipt' => 'require_challan_receipt_upload',
            'photo', 'profile_picture' => 'require_profile_picture',
            default => "require_{$docType}",
        };

        return (bool) self::get($key, true);
    }

    /**
     * Get default statutory board constitution & required peer reviewers count matching post title and track.
     */
    public static function getConstitutionPresetForPost(string $title, string $trackType = 'ACADEMIC'): array
    {
        $lower = strtolower($title);

        // 1. Assistant Professor: 3 External Experts; Committee: 1 Dean/Convener (Int), 1 Dept Head (Int), 3 Senior Faculty (Int), 1 Professor (Ext) -> 5 Internal, 1 External (Total 6)
        if (str_contains($lower, 'assistant professor') || str_contains($lower, 'asst professor') || str_contains($lower, 'ap computer') || str_contains($lower, 'ap -')) {
            $roles = [
                ['name' => 'Dean of Faculty (Convener)', 'role' => 'Dean of Faculty / Convener', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'count' => 1, 'notes' => 'Faculty Dean / Official Convener of the Selection Board'],
                ['name' => 'Department Head (Concerned)', 'role' => 'Department Head / Chairperson', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'count' => 1, 'notes' => 'Concerned Department Head / Ex-Officio Member'],
                ['name' => 'Internal Subject Faculty (Same Institution)', 'role' => 'Internal Subject Professors (Same Institution)', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 3, 'notes' => '3 Senior Professors/Associate Professors from same institution'],
                ['name' => 'External Subject Professor (Other Institution)', 'role' => 'External Subject Professor (Other Institution)', 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 1, 'notes' => '1 External Professor from another university/institution'],
            ];

            return [
                'required_peer_reviewers' => (int) self::get('asst_prof_required_peer_reviewers', 3),
                'required_total' => 6,
                'required_internal' => 5,
                'required_external' => 1,
                'required_international' => 0,
                'required_independent' => 0,
                'title' => 'Statutory Selection Committee for Assistant Professor',
                'description' => 'Statutory Criteria: 3 External Subject Experts for peer review. Selection Committee: Dean (Convener), Concerned Dept Head, 3 Internal Faculty from same institution, 1 External Professor from other institution.',
                'roles_breakdown' => $roles,
                'rules' => $roles,
            ];
        }

        // 2. Associate Professor / Full Professor: 3 Peer Reviewers; Committee: 4 Internal, 2 External (Total 6)
        if (str_contains($lower, 'professor') || str_contains($lower, 'associate professor') || str_contains($lower, 'assoc professor')) {
            $roles = [
                ['name' => 'Vice-Chancellor / Dean (Convener)', 'role' => 'Vice-Chancellor / Dean / Convener', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'count' => 1, 'notes' => 'Statutory Chairperson/Convener'],
                ['name' => 'Internal Subject Specialists (Same Institution)', 'role' => 'Internal Subject Specialists (Same Institution)', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 3, 'notes' => '3 Internal Senior Professors from same university'],
                ['name' => 'External Subject Expert (Other Institution)', 'role' => 'External Subject Expert (Other Institution)', 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 1, 'notes' => '1 External Professor from another accredited university'],
                ['name' => 'International / Overseas Subject Specialist', 'role' => 'International / Overseas Subject Specialist', 'category' => 'INTERNATIONAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'count' => 1, 'notes' => '1 Overseas Subject Specialist from foreign university'],
            ];

            return [
                'required_peer_reviewers' => (int) self::get('prof_required_peer_reviewers', 3),
                'required_total' => 6,
                'required_internal' => 4,
                'required_external' => 2,
                'required_international' => 1,
                'required_independent' => 0,
                'title' => 'Statutory Professorial Selection Board',
                'description' => 'Statutory Criteria: 3 External / International Subject Referees. Selection Board: Vice-Chancellor / Dean, 3 Internal Senior Leaders, 2 External Subject Specialists (including 1 Overseas).',
                'roles_breakdown' => $roles,
                'rules' => $roles,
            ];
        }

        // 3. Lecturer / Teaching Assistant: 0 (or 2) Peer Reviewers; Committee: 4 Internal, 1 External (Total 5)
        if (str_contains($lower, 'lecturer') || str_contains($lower, 'instructor') || str_contains($lower, 'teaching fellow')) {
            $roles = [
                ['name' => 'Department Head (Convener)', 'role' => 'Department Head / Convener', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'count' => 1, 'notes' => 'Concerned Department Head / Convener'],
                ['name' => 'Internal Faculty Members (Same Institution)', 'role' => 'Internal Faculty Members (Same Institution)', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 3, 'notes' => '3 Internal Faculty Members from same department/faculty'],
                ['name' => 'External Subject Expert (Other Institution)', 'role' => 'External Subject Expert (Other Institution)', 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 1, 'notes' => '1 External Faculty Expert from partner institution'],
            ];

            return [
                'required_peer_reviewers' => (int) self::get('lecturer_required_peer_reviewers', 0),
                'required_total' => 5,
                'required_internal' => 4,
                'required_external' => 1,
                'required_international' => 0,
                'required_independent' => 0,
                'title' => 'Academic Selection Committee for Lecturer / Faculty',
                'description' => 'Statutory Criteria: 4 Internal Faculty/Leadership Members, 1 External Subject Expert from partner institution.',
                'roles_breakdown' => $roles,
                'rules' => $roles,
            ];
        }

        // 4. Non-academic: Corporate / Executive / Commercial / Industrial
        if ($trackType === 'ADMINISTRATIVE' || $trackType === 'EXECUTIVE' || str_contains($lower, 'director') || str_contains($lower, 'manager') || str_contains($lower, 'executive') || str_contains($lower, 'lead') || str_contains($lower, 'head of')) {
            $roles = [
                ['name' => 'Managing Director / Executive Convener', 'role' => 'Managing Director / Executive Convener', 'category' => 'INTERNAL', 'cadre' => 'EXECUTIVE', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'count' => 1, 'notes' => 'Executive Director / Convener of the Committee'],
                ['name' => 'Internal Division Heads / HR Director', 'role' => 'Internal Division Heads / HR Director', 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 2, 'notes' => '2 Internal Officers / Division Leaders'],
                ['name' => 'External Industry Specialists / Board Delegates', 'role' => 'External Industry Specialists / Board Delegates', 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 2, 'notes' => '2 External Industry Advisors / Specialists from other companies'],
            ];

            return [
                'required_peer_reviewers' => 0,
                'required_total' => 5,
                'required_internal' => 3,
                'required_external' => 2,
                'required_international' => 0,
                'required_independent' => 0,
                'title' => 'Executive & Operational Selection Board',
                'description' => 'Enterprise Criteria: Managing Director/Lead, 2 Internal Executive Officers, 2 External Industry Advisors / Technical Specialists.',
                'roles_breakdown' => $roles,
                'rules' => $roles,
            ];
        }

        // 5. Public Sector / Government Agency / Civil Service
        if ($trackType === 'GOVERNMENT' || str_contains($lower, 'officer') || str_contains($lower, 'inspector') || str_contains($lower, 'superintendent') || str_contains($lower, 'assistant director')) {
            $roles = [
                ['name' => 'Departmental Representative / Chairman', 'role' => 'Departmental Representative / Chairman', 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'count' => 1, 'notes' => 'Ex-Officio Chairman / Departmental Representative'],
                ['name' => 'Internal Service Member', 'role' => 'Internal Service Member', 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 1, 'notes' => 'Internal Civil / Administrative Service Officer'],
                ['name' => 'External Public Service Specialist', 'role' => 'External Public Service Specialist', 'category' => 'EXTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 1, 'notes' => '1 Public Service Delegate from external agency'],
                ['name' => 'Independent Legal / Governance Arbiter', 'role' => 'Independent Legal / Governance Arbiter', 'category' => 'INDEPENDENT', 'cadre' => 'LEGAL', 'role_nature' => 'ARBITER', 'is_mandatory' => false, 'count' => 1, 'notes' => '1 Independent Legal or Governance Arbiter'],
            ];

            return [
                'required_peer_reviewers' => 0,
                'required_total' => 4,
                'required_internal' => 2,
                'required_external' => 1,
                'required_international' => 0,
                'required_independent' => 1,
                'title' => 'Public Sector Service Selection Board',
                'description' => 'Public Sector Criteria: 2 Departmental Members, 1 External Public Service Delegate, 1 Independent Legal Arbiter.',
                'roles_breakdown' => $roles,
                'rules' => $roles,
            ];
        }

        // 6. Generic Default Fallback
        $roles = [
            ['name' => 'Internal Convener / Head', 'role' => 'Internal Convener / Head', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'count' => 1, 'notes' => 'Internal Convener / Lead'],
            ['name' => 'Internal Members (Same Organization)', 'role' => 'Internal Members (Same Organization)', 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 2, 'notes' => '2 Internal members from same organization/office'],
            ['name' => 'External Subject Specialists (Other Institutions)', 'role' => 'External Subject Specialists (Other Institutions)', 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'count' => 2, 'notes' => '2 External Specialists from outside institutions'],
        ];

        return [
            'required_peer_reviewers' => $trackType === 'ACADEMIC' ? 3 : 0,
            'required_total' => 5,
            'required_internal' => 3,
            'required_external' => 2,
            'required_international' => 0,
            'required_independent' => 0,
            'title' => 'Standard Statutory Selection Committee',
            'description' => 'Standard Criteria: 3 Internal Members, 2 External Experts / Subject Specialists.',
            'roles_breakdown' => $roles,
            'rules' => $roles,
        ];
    }

    /**
     * Get dynamic application branding name.
     */
    public static function getAppName(): string
    {
        return (string) self::get('app_name', config('app.name', 'Hirely'));
    }

    /**
     * Get primary brand display name for sidebars, compact headers, and mobile titles.
     * Prioritizes the institutional acronym/short name (e.g. AWKUM) if configured.
     */
    public static function getBrandDisplayName(): string
    {
        $short = (string) self::get('org_short_name', '');
        if (!empty($short) && strtolower($short) !== 'hirely') {
            return $short;
        }
        $appName = (string) self::get('app_name', '');
        if (!empty($appName) && strtolower($appName) !== 'hirely') {
            return $appName;
        }
        return !empty($short) ? $short : ($appName ?: 'Hirely');
    }

    /**
     * Get dynamic primary logo asset URL or fallback.
     */
    public static function getAppLogo(): string
    {
        $logo = self::get('app_logo');
        if (!empty($logo)) {
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://') || str_starts_with($logo, '/')) {
                return $logo;
            }
            return asset('storage/' . $logo);
        }
        return '/images/logo-icon.svg';
    }

    /**
     * Get dynamic favicon asset URL or fallback.
     */
    public static function getAppFavicon(): string
    {
        $fav = self::get('app_favicon');
        if (!empty($fav)) {
            if (str_starts_with($fav, 'http://') || str_starts_with($fav, 'https://') || str_starts_with($fav, '/')) {
                return $fav;
            }
            return asset('storage/' . $fav);
        }
        return '/favicon.ico';
    }

    /**
     * Get dynamic organization full name or fallback.
     */
    public static function getOrgName(): string
    {
        return (string) self::get('org_name', 'Hirely');
    }

    /**
     * Get dynamic organization short name / acronym (e.g. AWKUM) or fallback.
     */
    public static function getOrgShortName(): string
    {
        return (string) self::get('org_short_name', 'Hirely');
    }

    /**
     * Get dynamic official letterhead logo asset URL or fallback.
     */
    public static function getLetterheadLogo(): string
    {
        $logo = self::get('letterhead_logo');
        if (!empty($logo) && $logo !== '/images/logo-icon.svg') {
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://') || str_starts_with($logo, '/')) {
                return $logo;
            }
            return asset('storage/' . $logo);
        }

        // Fallback to institutional application logo if uploaded
        $appLogo = self::getAppLogo();
        if (!empty($appLogo) && $appLogo !== '/images/logo-icon.svg') {
            return $appLogo;
        }

        return $logo ?: '/images/logo-icon.svg';
    }

    /**
     * Get consolidated letterhead configuration.
     */
    public static function getLetterheadDetails(): array
    {
        $orgName = self::getOrgName();
        $location = (string) self::get('org_location', '');
        $office = (string) self::get('org_office_title', 'Office of the Registrar');

        $savedHeading = (string) self::get('letterhead_heading', '');
        $genericHeadings = [
            'Hirely Higher Education & Recruitment Commission',
            'Hirely Recruitment Management System',
            'Hirely Higher Education Commission',
            '',
        ];

        $effectiveHeading = (!empty($savedHeading) && !in_array(trim($savedHeading), $genericHeadings))
            ? $savedHeading
            : $orgName;

        return [
            'heading' => $effectiveHeading,
            'subheading' => (string) self::get('letterhead_subheading', $location),
            'office' => (string) self::get('letterhead_office', $office),
            'logo' => self::getLetterheadLogo(),
            'custom_details' => (string) self::get('letterhead_custom_details', ''),
            'layout' => (string) self::get('letterhead_layout', 'classic_left'),
            'dossier_title_admin' => (string) self::get('letterhead_dossier_title_admin', 'Official Master Administrative Application Dossier & Scrutiny Record'),
            'dossier_title_candidate' => (string) self::get('letterhead_dossier_title_candidate', 'Official Application Form for Employment'),
        ];
    }

    /**
     * Get dynamic portal-specific footer message.
     */
    public static function getPortalFooter(string $portal = 'global'): string
    {
        $orgName = self::getOrgName();
        $orgShort = self::getOrgShortName();

        return match ($portal) {
            'candidate', 'applicant' => (string) self::get(
                'footer_candidate_text',
                'Candidate Facilitation Desk: For application guidance or voucher verification, contact ' . self::get('support_email', 'support@hirely.app') . '.'
            ),
            'admin', 'registrar' => (string) self::get(
                'footer_admin_text',
                "Official Administrative Desk • {$orgName} ({$orgShort}) • System Access Audited & Recorded."
            ),
            'board', 'scrutiny' => (string) self::get(
                'footer_board_text',
                "Statutory Selection Board Secretariat • Strictly Confidential & Privileged Record."
            ),
            'reviewer', 'peer_review' => (string) self::get(
                'footer_reviewer_text',
                "Academic Peer Evaluation Desk • External Referee Integrity • Blind Review Protocols Active."
            ),
            default => (string) self::get(
                'footer_copyright_text',
                '© ' . date('Y') . " {$orgName} ({$orgShort}). All rights reserved."
            ),
        };
    }

    /**
     * Get consolidated letter / printed document footer details.
     */
    public static function getLetterFooter(): array
    {
        $orgName = self::getOrgName();
        $orgShort = self::getOrgShortName();

        return [
            'disclaimer' => (string) self::get(
                'letter_footer_text',
                "This is an official computer-generated document issued under the seal and authority of {$orgName} ({$orgShort}). Any alteration, erasure, or unauthorized tampering renders this document null and void."
            ),
            'contact' => (string) self::get(
                'letter_footer_contact',
                'Recruitment Directorate • Candidate Records & Statutory Scrutiny Archive • Verification Portal: https://hirely.app'
            ),
            'show_audit' => (bool) self::get('letter_show_audit_trail', true),
            'show_verify_ref' => (bool) self::get('letter_show_verify_ref', true),
        ];
    }

    /**
     * Check if system maintenance mode is active.
     */
    public static function isMaintenanceModeEnabled(): bool
    {
        return (bool) self::get('system_maintenance_mode', false);
    }

    /**
     * Check if candidate registration is currently open.
     */
    public static function isRegistrationOpen(): bool
    {
        return strtolower((string) self::get('candidate_registration_status', 'open')) === 'open';
    }

    /**
     * Get statutory merit weightage breakdown.
     */
    public static function getMeritFormulaWeights(): array
    {
        return [
            'academic' => (int) self::get('merit_academic_weight', 50),
            'higher_qualification' => (int) self::get('merit_higher_qualification_weight', 5),
            'research' => (int) self::get('merit_research_weight', 15),
            'experience' => (int) self::get('merit_experience_weight', 10),
            'interview' => (int) self::get('merit_interview_weight', 20),
            'passing_aggregate' => (int) self::get('merit_minimum_passing_aggregate', 50),
        ];
    }

    /**
     * Get consolidated security policies.
     */
    public static function getSecurityPolicies(): array
    {
        return [
            'two_factor_policy' => (string) self::get('auth_two_factor_policy', 'optional'),
            'session_timeout' => (int) self::get('auth_session_timeout_minutes', 60),
            'max_login_attempts' => (int) self::get('auth_max_login_attempts', 5),
            'lockout_duration' => (int) self::get('auth_lockout_duration_minutes', 15),
            'registration_status' => (string) self::get('candidate_registration_status', 'open'),
            'registration_closed_message' => (string) self::get('candidate_registration_closed_message', 'Online recruitment intake is currently closed.'),
            'job_board_visibility' => (string) self::get('job_board_visibility', 'public'),
            'password_min_length' => (int) self::get('password_min_length', 8),
            'password_require_uppercase' => (bool) self::get('password_require_uppercase', true),
            'password_require_numeric' => (bool) self::get('password_require_numeric', true),
            'password_require_special' => (bool) self::get('password_require_special', false),
            'force_first_login_password_change' => (bool) self::get('force_first_login_password_change', false),
            'enforce_strict_cnic_format' => (bool) self::get('enforce_strict_cnic_format', true),
            'max_upload_size_mb' => (int) self::get('max_upload_size_mb', 5),
            'allowed_file_types' => (string) self::get('allowed_file_types', 'pdf, jpg, jpeg, png, docx'),
        ];
    }

    /**
     * Get SMS Gateway configuration.
     */
    public static function getSmsGatewayDetails(): array
    {
        return [
            'enabled' => (bool) self::get('sms_enabled', false),
            'provider' => (string) self::get('sms_provider', 'twilio'),
            'sender_id' => (string) self::get('sms_sender_id', 'HIRELY'),
            'api_key' => (string) self::get('sms_api_key', ''),
            'api_url' => (string) self::get('sms_api_url', ''),
            'trigger_app_submitted' => (bool) self::get('sms_trigger_app_submitted', true),
            'trigger_payment_verified' => (bool) self::get('sms_trigger_payment_verified', true),
            'trigger_scrutiny_deficiency' => (bool) self::get('sms_trigger_scrutiny_deficiency', true),
            'trigger_interview_call' => (bool) self::get('sms_trigger_interview_call', true),
        ];
    }

    /**
     * Convert hexadecimal color code to RGBA format.
     */
    public static function hexToRgba(string $hex, float $alpha = 1.0): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return "rgba(79, 70, 229, {$alpha})";
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    /**
     * Adjust color brightness for hover or border states.
     */
    public static function adjustHexBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return '#4338ca';
        }
        $r = max(0, min(255, (int)(hexdec(substr($hex, 0, 2)) * (1 + $percent / 100))));
        $g = max(0, min(255, (int)(hexdec(substr($hex, 2, 2)) * (1 + $percent / 100))));
        $b = max(0, min(255, (int)(hexdec(substr($hex, 4, 2)) * (1 + $percent / 100))));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Get consolidated theme variables dictionary.
     */
    public static function getThemeVariables(): array
    {
        $primary = (string) self::get('theme_accent_color', '#4f46e5');
        $secondary = (string) self::get('theme_secondary_color', '#06b6d4');
        $radiusStyle = (string) self::get('theme_radius_style', 'modern');
        $fontFamily = (string) self::get('theme_font_family', 'jakarta');
        $elevation = (string) self::get('theme_card_elevation', 'bordered');
        $sidebarStyle = (string) self::get('theme_sidebar_style', 'glass-dark');
        $loginLayout = (string) self::get('theme_login_layout', 'split-modern');
        $glassmorphism = (bool) self::get('ui_glassmorphism_enabled', true);
        $animations = (bool) self::get('ui_animations_enabled', true);
        $density = (string) self::get('ui_table_density', 'comfortable');

        return compact(
            'primary', 'secondary', 'radiusStyle', 'fontFamily', 'elevation',
            'sidebarStyle', 'loginLayout', 'glassmorphism', 'animations', 'density'
        );
    }

    /**
     * Render dynamic institutional theme styles for inclusion in layout headers.
     */
    public static function renderThemeCss(): string
    {
        $vars = self::getThemeVariables();
        $primary = $vars['primary'];
        $secondary = $vars['secondary'];
        $primaryHover = self::adjustHexBrightness($primary, -12);
        $primaryLight = self::hexToRgba($primary, 0.12);
        $primaryGlow = self::hexToRgba($primary, 0.35);

        $secondaryHover = self::adjustHexBrightness($secondary, -12);
        $secondaryLight = self::hexToRgba($secondary, 0.12);
        $secondaryGlow = self::hexToRgba($secondary, 0.30);

        // Radius mapping
        $radii = match($vars['radiusStyle']) {
            'compact' => [
                'xs' => '3px',
                'sm' => '5px',
                'md' => '8px',
                'lg' => '12px',
                'xl' => '16px',
            ],
            'pill' => [
                'xs' => '10px',
                'sm' => '16px',
                'md' => '24px',
                'lg' => '32px',
                'xl' => '44px',
            ],
            'sharp' => [
                'xs' => '1px',
                'sm' => '2px',
                'md' => '3px',
                'lg' => '4px',
                'xl' => '6px',
            ],
            default => [ // modern
                'xs' => '6px',
                'sm' => '8px',
                'md' => '14px',
                'lg' => '20px',
                'xl' => '28px',
            ],
        };

        // Typography mapping
        $fontCss = match($vars['fontFamily']) {
            'inter' => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            'outfit' => "'Outfit', 'Plus Jakarta Sans', sans-serif",
            'system' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
            'academic' => "'Playfair Display', Georgia, 'Plus Jakarta Sans', serif",
            default => "'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
        };

        $css = "<style id=\"dynamic-theme-overrides\">\n";
        $css .= ":root {\n";
        $css .= "    --primary: {$primary} !important;\n";
        $css .= "    --primary-hover: {$primaryHover} !important;\n";
        $css .= "    --primary-light: {$primaryLight} !important;\n";
        $css .= "    --primary-glow: {$primaryGlow} !important;\n";
        $css .= "    --secondary: {$secondary} !important;\n";
        $css .= "    --secondary-hover: {$secondaryHover} !important;\n";
        $css .= "    --secondary-light: {$secondaryLight} !important;\n";
        $css .= "    --secondary-glow: {$secondaryGlow} !important;\n";
        $css .= "    --radius-xs: {$radii['xs']} !important;\n";
        $css .= "    --radius-sm: {$radii['sm']} !important;\n";
        $css .= "    --radius-md: {$radii['md']} !important;\n";
        $css .= "    --radius-lg: {$radii['lg']} !important;\n";
        $css .= "    --radius-xl: {$radii['xl']} !important;\n";
        $css .= "    --font-sans: {$fontCss} !important;\n";

        if ($vars['elevation'] === 'floating') {
            $css .= "    --shadow-sm: 0 4px 12px -2px rgba(0, 0, 0, 0.4) !important;\n";
            $css .= "    --shadow-md: 0 14px 34px -6px rgba(0, 0, 0, 0.5), 0 0 1px 1px rgba(255, 255, 255, 0.08) !important;\n";
            $css .= "    --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.65), 0 0 20px {$primaryGlow} !important;\n";
        } elseif ($vars['elevation'] === 'minimal') {
            $css .= "    --shadow-sm: none !important;\n";
            $css .= "    --shadow-md: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;\n";
            $css .= "    --shadow-lg: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;\n";
            $css .= "    --shadow-glow: none !important;\n";
        }

        if (!$vars['glassmorphism']) {
            $css .= "    --glass-blur: 0px !important;\n";
            $css .= "    --bg-card: var(--bg-card-solid) !important;\n";
            $css .= "    backdrop-filter: none !important;\n";
            $css .= "    -webkit-backdrop-filter: none !important;\n";
        }

        if ($vars['sidebarStyle'] === 'light-solid') {
            $css .= "    --bg-sidebar: #0f172a;\n";
        } elseif ($vars['sidebarStyle'] === 'accent-tint') {
            $tint = self::hexToRgba($primary, 0.08);
            $css .= "    --bg-sidebar: linear-gradient(180deg, {$tint} 0%, rgba(10, 15, 26, 0.98) 100%) !important;\n";
        }

        $css .= "}\n";

        // Light mode specifics
        $css .= "html.light {\n";
        $css .= "    --primary: {$primary} !important;\n";
        $css .= "    --primary-hover: {$primaryHover} !important;\n";
        $css .= "    --primary-light: {$primaryLight} !important;\n";
        $css .= "    --primary-glow: {$primaryGlow} !important;\n";
        $css .= "    --secondary: {$secondary} !important;\n";
        $css .= "    --secondary-hover: {$secondaryHover} !important;\n";
        $css .= "    --secondary-light: {$secondaryLight} !important;\n";
        $css .= "    --secondary-glow: {$secondaryGlow} !important;\n";
        if ($vars['sidebarStyle'] === 'light-solid') {
            $css .= "    --bg-sidebar: #ffffff !important;\n";
            $css .= "    --sidebar-link-active: {$primaryLight} !important;\n";
            $css .= "    --sidebar-link-hover: rgba(0, 0, 0, 0.04) !important;\n";
        } elseif ($vars['sidebarStyle'] === 'accent-tint') {
            $tintLight = self::hexToRgba($primary, 0.06);
            $css .= "    --bg-sidebar: linear-gradient(180deg, {$tintLight} 0%, #f8fafc 100%) !important;\n";
        }
        $css .= "}\n";

        // Global font application
        $css .= "body, button, input, select, textarea { font-family: {$fontCss}; }\n";

        // Animation disabling if turned off
        if (!$vars['animations']) {
            $css .= "*, *::before, *::after { transition-duration: 0.0001ms !important; animation-duration: 0.0001ms !important; }\n";
        }

        // Table density overrides
        if ($vars['density'] === 'compact') {
            $css .= ".table th, .table td { padding: 0.45rem 0.65rem !important; font-size: 0.8rem !important; }\n";
        } elseif ($vars['density'] === 'spacious') {
            $css .= ".table th, .table td { padding: 1rem 1.25rem !important; font-size: 0.88rem !important; }\n";
        }

        $css .= "</style>\n";

        return $css;
    }

    /**
     * Populate standard system default settings.
     */
    public static function seedDefaultSettings(): void
    {
        $defaults = [
            // 🎨 Application Identity & Branding
            [
                'key' => 'app_name',
                'value' => 'Hirely',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Application Title & Brand Name',
                'description' => 'The public and administrative platform name displayed in navigation, browser tabs, and emails.',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Statutory Recruitment & Selection Operating System',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Application Tagline / Subtitle',
                'description' => 'Displayed beneath the logo on login, candidate portal banners, and portal headers.',
            ],
            [
                'key' => 'app_description',
                'value' => 'Enterprise recruitment, statutory scrutiny, and merit selection operating system for higher education and statutory institutions.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Application Meta Description',
                'description' => 'Portal summary text used for SEO meta tags, portal footers, and informational summaries.',
            ],
            [
                'key' => 'app_logo',
                'value' => '/images/logo-icon.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Primary Brand Logo (Light Mode / Standard)',
                'description' => 'Brand logo image used on the sidebar, header bar, and authentication portals.',
            ],
            [
                'key' => 'app_logo_dark',
                'value' => '/images/logo-icon.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Secondary Brand Logo (Dark Mode)',
                'description' => 'Alternative brand logo displayed when dark theme is activated.',
            ],
            [
                'key' => 'app_favicon',
                'value' => '/favicon.ico',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Browser Favicon Asset',
                'description' => 'Favicon icon displayed in browser tabs and bookmark shortcuts (.ico, .svg, .png).',
            ],
            [
                'key' => 'app_version',
                'value' => 'v2.4 Enterprise OS',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Platform Release & Version Badge',
                'description' => 'Version badge shown in the sidebar header beside the brand title.',
            ],
            [
                'key' => 'theme_accent_color',
                'value' => '#4f46e5',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Theme Primary Accent Color',
                'description' => 'Hexadecimal color code used for buttons, badges, and brand highlights across the UI.',
            ],
            [
                'key' => 'theme_secondary_color',
                'value' => '#06b6d4',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Theme Secondary Highlight Color',
                'description' => 'Secondary accent color for badges, gradients, and subtle UI highlights.',
            ],
            [
                'key' => 'theme_radius_style',
                'value' => 'modern',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'UI Corner Radius Style',
                'description' => 'Global border radius style: modern (14px), compact (8px), pill (24px), or sharp (2px).',
            ],
            [
                'key' => 'theme_font_family',
                'value' => 'jakarta',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Platform Typography Family',
                'description' => 'Global font family preset: jakarta (Plus Jakarta Sans), inter (Inter UI), outfit (Outfit Geometric), system (Native OS), or academic (Playfair & Jakarta).',
            ],
            [
                'key' => 'theme_card_elevation',
                'value' => 'bordered',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Card Elevation & Shadows',
                'description' => 'Shadow depth and border style for cards, modals, and panel containers: bordered, floating, or minimal.',
            ],
            [
                'key' => 'theme_sidebar_style',
                'value' => 'glass-dark',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Sidebar Visual Appearance',
                'description' => 'Visual style for the navigation sidebar: glass-dark (Obsidian Glass), light-solid (Pure White), or accent-tint (Subtle Brand Tint).',
            ],
            [
                'key' => 'theme_login_layout',
                'value' => 'split-modern',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Login Portal Layout',
                'description' => 'Public user portal layout: split-modern (Hero Left & Drawer Right), center-glass (Frosted Glass Center), or minimalist (Focused Center Card).',
            ],
            [
                'key' => 'ui_glassmorphism_enabled',
                'value' => '1',
                'group' => 'branding',
                'type' => 'boolean',
                'label' => 'Glassmorphic Backdrop Blur',
                'description' => 'Enables translucent frosted-glass blur effects on cards, modals, and navigation elements.',
            ],
            [
                'key' => 'ui_animations_enabled',
                'value' => '1',
                'group' => 'branding',
                'type' => 'boolean',
                'label' => 'Micro-Interactions & Transitions',
                'description' => 'Enables smooth hover animations, badge scaling, and dialog transitions across the portal.',
            ],
            [
                'key' => 'ui_table_density',
                'value' => 'comfortable',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Data Table Density',
                'description' => 'Spacing and padding inside listings, tables, and data grids: compact, comfortable, or spacious.',
            ],
            [
                'key' => 'portal_announcement_enabled',
                'value' => '0',
                'group' => 'branding',
                'type' => 'boolean',
                'label' => 'Candidate Portal Announcement Banner',
                'description' => 'Enables a prominent alert broadcast banner across the top of applicant and administrative workspaces.',
            ],
            [
                'key' => 'portal_announcement_type',
                'value' => 'info',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Announcement Banner Variant',
                'description' => 'Visual style: info (blue), warning (amber), danger (red), or success (green).',
            ],
            [
                'key' => 'portal_announcement_message',
                'value' => '',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Announcement Banner Text',
                'description' => 'The message broadcasted to candidates and institutional officers.',
            ],
            [
                'key' => 'footer_copyright_text',
                'value' => '© ' . date('Y') . ' Hirely. All rights reserved.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Portal Footer Copyright Notice',
                'description' => 'Legal and copyright text displayed at the bottom of candidate and public pages.',
            ],
            [
                'key' => 'footer_candidate_text',
                'value' => 'Candidate Facilitation Desk: For application guidance or voucher verification, contact support@hirely.app or submit an inquiry through the helpdesk.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Candidate Portal Footer Notice',
                'description' => 'Helpdesk note and guidance displayed in the candidate portal workspace.',
            ],
            [
                'key' => 'footer_admin_text',
                'value' => 'Official Administrative Desk • Hirely Enterprise OS • System Access Audited & Recorded.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Admin & Registrar Portal Footer Notice',
                'description' => 'Audit and governance note displayed in administrative workspaces.',
            ],
            [
                'key' => 'footer_board_text',
                'value' => 'Statutory Selection Board Secretariat • Strictly Confidential & Privileged Record.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Selection Board & Scrutiny Portal Footer Notice',
                'description' => 'Statutory non-disclosure notice displayed on scrutiny and selection board panels.',
            ],
            [
                'key' => 'footer_reviewer_text',
                'value' => 'Academic Peer Evaluation Desk • External Referee Integrity • Blind Review Protocols Active.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Peer Review Portal Footer Notice',
                'description' => 'Confidentiality note displayed to external academic referees.',
            ],
            [
                'key' => 'letter_footer_text',
                'value' => 'This is an official computer-generated document issued under statutory recruitment authority. Any alteration, erasure, or unauthorized tampering renders this document null and void.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Printed Document Statutory Disclaimer',
                'description' => 'Legal and provenance disclaimer printed at the bottom of candidate dossiers, gazettes, and letters.',
            ],
            [
                'key' => 'letter_footer_contact',
                'value' => 'Recruitment Directorate • Candidate Records & Statutory Scrutiny Archive • Verification Portal: https://hirely.app',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Printed Document Footer Contact Line',
                'description' => 'Contact details printed beneath the legal disclaimer on official documents.',
            ],
            [
                'key' => 'letter_show_audit_trail',
                'value' => '1',
                'group' => 'branding',
                'type' => 'boolean',
                'label' => 'Print Document User Audit Trail',
                'description' => 'Whether to print "Printed / Downloaded by [Name] ([Username]) - [Role] on [Date Time]" on documents.',
            ],
            [
                'key' => 'letter_show_verify_ref',
                'value' => '1',
                'group' => 'branding',
                'type' => 'boolean',
                'label' => 'Print Cryptographic Verification Reference',
                'description' => 'Whether to print the SHA-256 verification reference code at the bottom of documents.',
            ],

            // 📞 Contact & Helpdesk Channels
            [
                'key' => 'support_email',
                'value' => 'support@hirely.app',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Candidate Support & Inquiries Email',
                'description' => 'Primary email displayed to applicants for technical support and voucher reconciliation.',
            ],
            [
                'key' => 'support_phone',
                'value' => '+1 (800) 555-0199',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Recruitment Helpdesk Phone Line',
                'description' => 'Telephone helpline available for candidate guidance and institutional communications.',
            ],
            [
                'key' => 'support_whatsapp',
                'value' => '',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'WhatsApp Candidate Helpline',
                'description' => 'WhatsApp business or support line for quick messaging and document guidance.',
            ],
            [
                'key' => 'registrar_hotline',
                'value' => '',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Registrar / Emergency Secretariat Hotline',
                'description' => 'Direct contact for statutory committee members and urgent administrative queries.',
            ],
            [
                'key' => 'support_hours',
                'value' => 'Monday to Friday: 09:00 AM – 05:00 PM',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Support Service Hours',
                'description' => 'Operating schedule when helpdesk officers and live telephone lines are active.',
            ],
            [
                'key' => 'helpdesk_location',
                'value' => 'Central Recruitment & Candidate Facilitation Center',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Physical Helpdesk & Facilitation Center',
                'description' => 'Physical campus location where candidates can visit for identity or physical dossier verification.',
            ],
            [
                'key' => 'org_website',
                'value' => 'https://hirely.app',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Official Organization / University Website',
                'description' => 'Primary external institutional portal URL.',
            ],

            // ✉️ Email Delivery & SMTP Configuration
            [
                'key' => 'mail_from_name',
                'value' => 'Hirely Recruitment Desk',
                'group' => 'email',
                'type' => 'string',
                'label' => 'Default Mailer From Name',
                'description' => 'The sender display name attached to automated appointment letters and candidate alerts.',
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@hirely.app',
                'group' => 'email',
                'type' => 'string',
                'label' => 'Default Mailer From Email Address',
                'description' => 'The sender email address attached to outgoing automated notifications.',
            ],
            [
                'key' => 'mail_reply_to',
                'value' => 'support@hirely.app',
                'group' => 'email',
                'type' => 'string',
                'label' => 'Default Reply-To Email Address',
                'description' => 'Where candidate responses will be delivered if they reply to system emails.',
            ],
            [
                'key' => 'mail_mailer',
                'value' => 'smtp',
                'group' => 'email',
                'type' => 'string',
                'label' => 'Mail Transport Protocol / Driver',
                'description' => 'Delivery driver: smtp, sendmail, mailgun, ses, or log.',
            ],
            [
                'key' => 'mail_host',
                'value' => '127.0.0.1',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMTP Server Hostname',
                'description' => 'Hostname of the outgoing mail server (e.g. 127.0.0.1 for Mailpit, smtp.gmail.com).',
            ],
            [
                'key' => 'mail_port',
                'value' => '1025',
                'group' => 'email',
                'type' => 'integer',
                'label' => 'SMTP Server Port',
                'description' => 'Port for outgoing mail connection (e.g. 1025 for Mailpit, 587 for TLS, 465 for SSL).',
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'none',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMTP Encryption Protocol',
                'description' => 'Security protocol: none (for Mailpit), tls, or ssl.',
            ],
            [
                'key' => 'mail_username',
                'value' => '',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMTP Authentication Username / API Key',
                'description' => 'Account username or API credential for outgoing SMTP server.',
            ],
            [
                'key' => 'mail_password',
                'value' => '',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMTP Authentication Password',
                'description' => 'Account password or secret key for SMTP server.',
            ],
            // 🏢 Organization & Sector Profile
            [
                'key' => 'org_name',
                'value' => 'Hirely',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Organization / University Full Legal Name',
                'description' => 'The full official name of the hiring entity or university used in wide headers, login banners, and official documents.',
            ],
            [
                'key' => 'org_short_name',
                'value' => 'Hirely',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Organization Short Name / Acronym',
                'description' => 'Compact abbreviation used in sidebar navigation, mobile headers, and constrained space menus (e.g. AWKUM).',
            ],
            [
                'key' => 'org_location',
                'value' => 'National Directorate',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Institutional Jurisdiction / Location',
                'description' => 'State, province, or territory displayed on letterheads and official notices.',
            ],
            [
                'key' => 'org_office_title',
                'value' => 'Recruitment & Statutory Selection Directorate',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Executing Directorate / Office Name',
                'description' => 'The administrative secretariat issuing advertisements and dossiers (e.g. Office of the Registrar).',
            ],
            [
                'key' => 'letterhead_heading',
                'value' => 'Hirely Higher Education & Recruitment Commission',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Primary Letterhead Title / Heading',
                'description' => 'Top heading printed on candidate dossiers, merit gazettes, appointment orders, and external letters.',
            ],
            [
                'key' => 'letterhead_subheading',
                'value' => 'Statutory Recruitment & Merit Selection Directorate',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Letterhead Sub-Heading / Region',
                'description' => 'Secondary title displayed below the main heading on official letterheads.',
            ],
            [
                'key' => 'letterhead_office',
                'value' => 'Office of the Central Registrar & Selection Board Secretariat',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Letterhead Office / Directorate',
                'description' => 'Executing office printed on the official document header.',
            ],
            [
                'key' => 'letterhead_logo',
                'value' => '/images/logo-icon.svg',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Letterhead Crest / Official Logo',
                'description' => 'Emblem image printed in the letterhead header box on official documents.',
            ],
            [
                'key' => 'letterhead_custom_details',
                'value' => "Central Secretariat & Statutory Directorate\nWebsite: https://hirely.app • Email: support@hirely.app • Verification Portal: https://hirely.app/verify",
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Letterhead Rich Details / Contact Block',
                'description' => 'Rich / multi-line details such as address, telephone, email, and statutory registration numbers.',
            ],
            [
                'key' => 'letterhead_layout',
                'value' => 'classic_left',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Letterhead Header Layout Style',
                'description' => 'Visual layout: classic_left (left logo, right photo), crest_center (centered emblem), or modern_clean.',
            ],
            [
                'key' => 'org_type',
                'value' => 'HIGHER_EDUCATION',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Entity Sector / Organization Type',
                'description' => 'Adapts platform terminology and conventions: Higher Education / University, Corporate Enterprise, Public Sector / Govt, Research Institute, Healthcare Network.',
            ],
            [
                'key' => 'sector_label',
                'value' => 'Department / Division',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Unit / Sub-Entity Label',
                'description' => 'Display label for administrative units (e.g., Department, Faculty, Division, Business Unit, Wing).',
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'PKR',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Voucher / Payment Currency',
                'description' => 'Currency displayed on bank challans and fees (e.g. PKR, USD, GBP, EUR).',
            ],
            [
                'key' => 'default_application_fee',
                'value' => '3000',
                'group' => 'organization',
                'type' => 'integer',
                'label' => 'Default Campaign Application Fee',
                'description' => 'Default processing fee pre-filled when registering new recruitment campaigns.',
            ],

            // ⚡ Workflow Pipeline Stages
            [
                'key' => 'enable_peer_review_step',
                'value' => '1',
                'group' => 'pipeline',
                'type' => 'boolean',
                'label' => 'External Academic / Subject Peer Review Stage',
                'description' => 'Allows dispatching candidate dossiers to external subject referees and collecting structured evaluations.',
            ],
            [
                'key' => 'enable_scrutiny_step',
                'value' => '1',
                'group' => 'pipeline',
                'type' => 'boolean',
                'label' => 'Document Scrutiny & Preliminary Eligibility Stage',
                'description' => 'Enables verification committee desk to audit candidate proofs, transcripts, and mark eligible/ineligible.',
            ],
            [
                'key' => 'enable_appellate_step',
                'value' => '1',
                'group' => 'pipeline',
                'type' => 'boolean',
                'label' => 'Candidate Grievance & Appellate Review Stage',
                'description' => 'Enables candidate grievance submission window and appellate committee arbitration desk.',
            ],
            [
                'key' => 'enable_board_interview_step',
                'value' => '1',
                'group' => 'pipeline',
                'type' => 'boolean',
                'label' => 'Live Selection Board & Interview Grading Stage',
                'description' => 'Enables live scoring panel for board members during synchronous candidate interviews.',
            ],
            [
                'key' => 'enforce_strict_committee_quorum',
                'value' => '1',
                'group' => 'pipeline',
                'type' => 'boolean',
                'label' => 'Strict Board Quorum Enforcement',
                'description' => 'Requires exact member slots (Internal, External, International) to match statutory constitution.',
            ],
            [
                'key' => 'enforce_strict_peer_review_quorum',
                'value' => '0',
                'group' => 'pipeline',
                'type' => 'boolean',
                'label' => 'Strict Peer Review Quota Enforcement',
                'description' => 'Enforces dispatching exactly the statutory number of external experts (e.g. 3 experts for Assistant Professor).',
            ],

            // 📑 Document Requests & Mandatory Uploads
            [
                'key' => 'require_transcript_proof',
                'value' => '1',
                'group' => 'documents',
                'type' => 'boolean',
                'label' => 'Mandatory Degree Transcripts / Detailed Marks Certificates (DMC)',
                'description' => 'Requires applicants to upload official academic transcripts with credit hours and grade breakdowns.',
            ],
            [
                'key' => 'require_experience_proof',
                'value' => '1',
                'group' => 'documents',
                'type' => 'boolean',
                'label' => 'Mandatory Employment / Experience Certificates',
                'description' => 'Requires applicants to attach proof of service, experience letters, or appointment orders.',
            ],
            [
                'key' => 'require_cnic_upload',
                'value' => '1',
                'group' => 'documents',
                'type' => 'boolean',
                'label' => 'Mandatory CNIC / National Identity Card Proof',
                'description' => 'Requires applicants to upload front/back copy of their National Identity Card or Passport.',
            ],
            [
                'key' => 'require_noc_employed',
                'value' => '0',
                'group' => 'documents',
                'type' => 'boolean',
                'label' => 'Mandatory Employer NOC for Employed Applicants',
                'description' => 'Requires current government or institutional employees to upload a No Objection Certificate.',
            ],
            [
                'key' => 'require_publications_academic',
                'value' => '1',
                'group' => 'documents',
                'type' => 'boolean',
                'label' => 'Mandatory Research Publications for Academic Roles',
                'description' => 'Requires research papers, journal citations, and DOI proofs for professorial and research positions.',
            ],
            [
                'key' => 'require_challan_receipt_upload',
                'value' => '0',
                'group' => 'documents',
                'type' => 'boolean',
                'label' => 'Manual Paid Challan Stamp / Receipt Upload',
                'description' => 'Requires applicant to upload a scanned bank-stamped receipt if 1Link automated settlement is bypassed.',
            ],

            // ⚖️ Statutory Quota Defaults
            [
                'key' => 'asst_prof_required_peer_reviewers',
                'value' => '3',
                'group' => 'constitution_defaults',
                'type' => 'integer',
                'label' => 'Assistant Professor: Required Peer Review Experts',
                'description' => 'Statutory number of external subject experts required for Assistant Professor peer review dossier dispatch.',
            ],
            [
                'key' => 'prof_required_peer_reviewers',
                'value' => '3',
                'group' => 'constitution_defaults',
                'type' => 'integer',
                'label' => 'Associate / Full Professor: Required Peer Review Experts',
                'description' => 'Statutory number of external / international experts required for Associate Professor and Professor candidacies.',
            ],
            [
                'key' => 'lecturer_required_peer_reviewers',
                'value' => '0',
                'group' => 'constitution_defaults',
                'type' => 'integer',
                'label' => 'Lecturer / Junior Cadre: Required Peer Review Experts',
                'description' => 'Number of external peer experts required for entry-level teaching positions (default 0).',
            ],

            // 👥 Candidate & Application Rules
            [
                'key' => 'allow_multiple_applications',
                'value' => '1',
                'group' => 'candidate_rules',
                'type' => 'boolean',
                'label' => 'Allow Candidates to Apply for Multiple Positions',
                'description' => 'Enables candidates to submit separate dossiers for different open recruitment campaigns.',
            ],
            [
                'key' => 'prevent_duplicate_campaign_applications',
                'value' => '1',
                'group' => 'candidate_rules',
                'type' => 'boolean',
                'label' => 'Strictly Block Duplicate Applications for the Same Position',
                'description' => 'Prevents candidates from submitting more than one application to the same recruitment campaign.',
            ],
            [
                'key' => 'challan_grace_days',
                'value' => '14',
                'group' => 'candidate_rules',
                'type' => 'integer',
                'label' => 'Fee Voucher Settlement Grace Period (Days)',
                'description' => 'Number of days granted to candidate to pay the fee voucher before it expires.',
            ],
            [
                'key' => 'auto_lock_profile_on_voucher',
                'value' => '1',
                'group' => 'candidate_rules',
                'type' => 'boolean',
                'label' => 'Auto-Lock Demographic Profile Upon Voucher Generation',
                'description' => 'Prevents changes to candidate CNIC, name, and core demographics once a fee voucher is issued.',
            ],

            // 🔒 Security, Access Governance & Session Policies
            [
                'key' => 'auth_two_factor_policy',
                'value' => 'optional',
                'group' => 'security',
                'type' => 'string',
                'label' => 'Two-Factor Authentication (2FA) Enforcement Policy',
                'description' => 'Enforcement policy: disabled, optional (user choice), enforced_staff (mandatory for administrators and reviewers), or enforced_all.',
            ],
            [
                'key' => 'auth_session_timeout_minutes',
                'value' => '60',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Idle Session Inactivity Timeout (Minutes)',
                'description' => 'Automatically terminates idle administrative and reviewer sessions to prevent unauthorized desk access.',
            ],
            [
                'key' => 'auth_max_login_attempts',
                'value' => '5',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Maximum Failed Login Attempts Before Lockout',
                'description' => 'Number of consecutive invalid credential submissions before the account is temporarily suspended.',
            ],
            [
                'key' => 'auth_lockout_duration_minutes',
                'value' => '15',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Account Lockout Duration (Minutes)',
                'description' => 'Minutes an account remains locked after exceeding maximum failed login attempts.',
            ],
            [
                'key' => 'candidate_registration_status',
                'value' => 'open',
                'group' => 'security',
                'type' => 'string',
                'label' => 'Candidate Online Registration Status',
                'description' => 'Controls whether new applicants can create accounts: open (active recruitment window) or closed (admissions suspended).',
            ],
            [
                'key' => 'candidate_registration_closed_message',
                'value' => 'Online recruitment intake is currently closed. Please review active advertisements for upcoming announcement schedules.',
                'group' => 'security',
                'type' => 'string',
                'label' => 'Registration Closed Announcement Message',
                'description' => 'Guidance banner presented to candidates when registration is disabled.',
            ],
            [
                'key' => 'job_board_visibility',
                'value' => 'public',
                'group' => 'security',
                'type' => 'string',
                'label' => 'Public Job Vacancy Board Visibility',
                'description' => 'Controls career portal access: public (anyone can view positions) or members_only (must authenticate to browse).',
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Minimum Password Length',
                'description' => 'Enforces character length threshold on applicant and employee accounts.',
            ],
            [
                'key' => 'password_require_uppercase',
                'value' => '1',
                'group' => 'security',
                'type' => 'boolean',
                'label' => 'Require Uppercase Letter in Passwords',
                'description' => 'Mandates at least one uppercase letter (A-Z) in passwords.',
            ],
            [
                'key' => 'password_require_numeric',
                'value' => '1',
                'group' => 'security',
                'type' => 'boolean',
                'label' => 'Require Numeric Digit in Passwords',
                'description' => 'Mandates at least one numerical digit (0-9) in passwords.',
            ],
            [
                'key' => 'password_require_special',
                'value' => '0',
                'group' => 'security',
                'type' => 'boolean',
                'label' => 'Require Special Character in Passwords',
                'description' => 'Mandates at least one symbol (!@#$%^&*) in passwords.',
            ],
            [
                'key' => 'force_first_login_password_change',
                'value' => '0',
                'group' => 'security',
                'type' => 'boolean',
                'label' => 'Force Password Change on Initial Login',
                'description' => 'Requires newly empanelled reviewers and staff to set a new password upon first session.',
            ],
            [
                'key' => 'enforce_strict_cnic_format',
                'value' => '1',
                'group' => 'security',
                'type' => 'boolean',
                'label' => 'Enforce 13-Digit Pakistani CNIC Format',
                'description' => 'Requires 13-digit hyphenated format (XXXXX-XXXXXXX-X) for national identity entries.',
            ],
            [
                'key' => 'max_upload_size_mb',
                'value' => '5',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Maximum Single Document Upload Size (MB)',
                'description' => 'Storage limit for individual candidate transcripts, certificates, and publications (MB).',
            ],
            [
                'key' => 'allowed_file_types',
                'value' => 'pdf, jpg, jpeg, png, docx',
                'group' => 'security',
                'type' => 'string',
                'label' => 'Permitted Candidate Document File Formats',
                'description' => 'Comma-separated list of safe MIME file extensions accepted by the document upload validator.',
            ],

            // ⚙️ System Health, Diagnostics & Maintenance Mode
            [
                'key' => 'system_maintenance_mode',
                'value' => '0',
                'group' => 'system',
                'type' => 'boolean',
                'label' => 'Portal Maintenance Mode',
                'description' => 'Puts candidate-facing portal into maintenance mode with custom notice while administrators retain access.',
            ],
            [
                'key' => 'maintenance_notice_heading',
                'value' => 'Scheduled Statutory System Maintenance',
                'group' => 'system',
                'type' => 'string',
                'label' => 'Maintenance Notice Heading',
                'description' => 'Title banner displayed during maintenance windows.',
            ],
            [
                'key' => 'maintenance_notice_message',
                'value' => 'The Hirely recruitment portal is undergoing scheduled maintenance and database optimization. Access will be restored shortly. Thank you for your patience.',
                'group' => 'system',
                'type' => 'string',
                'label' => 'Maintenance Notice Body Text',
                'description' => 'Explanatory notice explaining scheduled maintenance schedule to applicants.',
            ],

            // 📱 SMS & Multi-Channel Alert Gateway
            [
                'key' => 'sms_enabled',
                'value' => '0',
                'group' => 'email',
                'type' => 'boolean',
                'label' => 'Enable Automated SMS Alert Gateway',
                'description' => 'Enables automated transactional SMS alerts to candidate mobile numbers.',
            ],
            [
                'key' => 'sms_provider',
                'value' => 'twilio',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMS Gateway Provider',
                'description' => 'Selected gateway provider: twilio, zong, telenor, or generic_webhook.',
            ],
            [
                'key' => 'sms_sender_id',
                'value' => 'HIRELY',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMS Alphanumeric Sender ID / Mask',
                'description' => 'Registered brand mask displayed on candidates phones (e.g. AWKUM-HR).',
            ],
            [
                'key' => 'sms_api_key',
                'value' => '',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMS Provider API Key / Secret',
                'description' => 'Authentication credential or API secret key for the SMS gateway.',
            ],
            [
                'key' => 'sms_api_url',
                'value' => '',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMS Gateway Endpoint URL / Webhook',
                'description' => 'HTTP POST endpoint for custom or telecom SMS gateway integration.',
            ],
            [
                'key' => 'sms_trigger_app_submitted',
                'value' => '1',
                'group' => 'email',
                'type' => 'boolean',
                'label' => 'Dispatch SMS on Application Submission',
                'description' => 'Sends confirmation text with tracking ID when candidate finalizes dossier.',
            ],
            [
                'key' => 'sms_trigger_payment_verified',
                'value' => '1',
                'group' => 'email',
                'type' => 'boolean',
                'label' => 'Dispatch SMS on Challan Payment Verified',
                'description' => 'Sends automated text when bank challan is verified or reconciled.',
            ],
            [
                'key' => 'sms_trigger_scrutiny_deficiency',
                'value' => '1',
                'group' => 'email',
                'type' => 'boolean',
                'label' => 'Dispatch SMS on Scrutiny Deficiency / Ineligibility',
                'description' => 'Alerts candidate via SMS to inspect deficiencies and lodge statutory appeal.',
            ],
            [
                'key' => 'sms_trigger_interview_call',
                'value' => '1',
                'group' => 'email',
                'type' => 'boolean',
                'label' => 'Dispatch SMS on Selection Board Interview Call',
                'description' => 'Sends urgent SMS alert when candidate is shortlisted for live selection board interview.',
            ],

            // ⚖️ Statutory Merit Formulation & Selection Board Scoring Weights
            [
                'key' => 'merit_academic_weight',
                'value' => '50',
                'group' => 'academic_criteria',
                'type' => 'integer',
                'label' => 'Academic Qualification Merit Weightage (%)',
                'description' => 'Standard weightage allocated to candidate secondary, bachelor, and master marks (default 50%).',
            ],
            [
                'key' => 'merit_higher_qualification_weight',
                'value' => '5',
                'group' => 'academic_criteria',
                'type' => 'integer',
                'label' => 'Additional Higher Qualification Weightage (%)',
                'description' => 'Bonus statutory weightage for relevant M.Phil / Ph.D degrees beyond minimum eligibility (default 5%).',
            ],
            [
                'key' => 'merit_research_weight',
                'value' => '15',
                'group' => 'academic_criteria',
                'type' => 'integer',
                'label' => 'Research Publications Merit Weightage (%)',
                'description' => 'Weightage assigned to HEC recognized publications in W, X, and Y categories (default 15%).',
            ],
            [
                'key' => 'merit_experience_weight',
                'value' => '10',
                'group' => 'academic_criteria',
                'type' => 'integer',
                'label' => 'Professional Experience Merit Weightage (%)',
                'description' => 'Weightage allocated to relevant teaching and industrial experience (default 10%).',
            ],
            [
                'key' => 'merit_interview_weight',
                'value' => '20',
                'group' => 'academic_criteria',
                'type' => 'integer',
                'label' => 'Selection Board Interview Weightage (%)',
                'description' => 'Marks assigned by the statutory selection board during live candidate interview and presentation (default 20%).',
            ],
            [
                'key' => 'merit_minimum_passing_aggregate',
                'value' => '50',
                'group' => 'academic_criteria',
                'type' => 'integer',
                'label' => 'Minimum Qualifying Aggregate Pass Marks (%)',
                'description' => 'Minimum composite score threshold required to qualify for merit gazette recommendation (default 50%).',
            ],
        ];

        foreach ($defaults as $def) {
            self::firstOrCreate(['key' => $def['key']], $def);
        }
    }

    /**
     * Cast string representation to native PHP type.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) return null;

        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Serialize PHP value for database storage.
     */
    protected static function serializeValue(mixed $value, string $type): ?string
    {
        if ($value === null) return null;

        return match ($type) {
            'boolean', 'bool' => $value ? '1' : '0',
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };
    }
}

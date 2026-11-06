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
                'value' => '© 2026 Hirely OS. All rights reserved.',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Portal Footer Copyright Notice',
                'description' => 'Legal and copyright text displayed at the bottom of candidate and public pages.',
            ],

            // 📞 Contact & Helpdesk Channels
            [
                'key' => 'support_email',
                'value' => 'support@hirely.edu.pk',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Candidate Support & Inquiries Email',
                'description' => 'Primary email displayed to applicants for technical support and voucher reconciliation.',
            ],
            [
                'key' => 'support_phone',
                'value' => '+92-51-9250000',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Recruitment Helpdesk Phone Line',
                'description' => 'Telephone helpline available for candidate guidance and institutional communications.',
            ],
            [
                'key' => 'support_whatsapp',
                'value' => '+92-300-1234567',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'WhatsApp Candidate Helpline',
                'description' => 'WhatsApp business or support line for quick messaging and document guidance.',
            ],
            [
                'key' => 'registrar_hotline',
                'value' => '+92-51-9250001',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Registrar / Emergency Secretariat Hotline',
                'description' => 'Direct contact for statutory committee members and urgent administrative queries.',
            ],
            [
                'key' => 'support_hours',
                'value' => 'Monday to Friday: 08:30 AM – 04:30 PM PKT',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Support Service Hours',
                'description' => 'Operating schedule when helpdesk officers and live telephone lines are active.',
            ],
            [
                'key' => 'helpdesk_location',
                'value' => 'Recruitment & Facilitation Center, Academic Block B, Main Campus',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Physical Helpdesk & Facilitation Center',
                'description' => 'Physical campus location where candidates can visit for identity or physical dossier verification.',
            ],
            [
                'key' => 'org_website',
                'value' => 'https://hirely.edu.pk',
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
                'value' => 'noreply@hirely.edu.pk',
                'group' => 'email',
                'type' => 'string',
                'label' => 'Default Mailer From Email Address',
                'description' => 'The sender email address attached to outgoing automated notifications.',
            ],
            [
                'key' => 'mail_reply_to',
                'value' => 'support@hirely.edu.pk',
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
                'value' => 'smtp.mailgun.org',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMTP Server Hostname',
                'description' => 'Hostname of the outgoing mail server (e.g. smtp.gmail.com, smtp.office365.com).',
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'group' => 'email',
                'type' => 'integer',
                'label' => 'SMTP Server Port',
                'description' => 'Port for outgoing mail connection (e.g. 587 for TLS, 465 for SSL, 25 for plain).',
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'group' => 'email',
                'type' => 'string',
                'label' => 'SMTP Encryption Protocol',
                'description' => 'Security protocol: tls, ssl, or none.',
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
                'value' => 'Hirely Recruitment Management System',
                'group' => 'organization',
                'type' => 'string',
                'label' => 'Organization / Institution Name',
                'description' => 'The official name of the hiring entity, university, corporate enterprise, or public sector agency.',
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

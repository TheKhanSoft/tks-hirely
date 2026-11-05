<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SystemSettings extends Component
{
    use WithFileUploads;

    public $activeTab = 'pipeline'; // 'pipeline', 'documents', 'candidate_rules', 'organization', 'academic_criteria', 'branding', 'contact', 'email'

    // ⚡ Pipeline Toggles
    public $enable_peer_review_step;
    public $enable_scrutiny_step;
    public $enable_appellate_step;
    public $enable_board_interview_step;
    public $enforce_strict_committee_quorum;
    public $enforce_strict_peer_review_quorum;

    // 📑 Document Request Toggles
    public $require_transcript_proof;
    public $require_experience_proof;
    public $require_cnic_upload;
    public $require_noc_employed;
    public $require_publications_academic;
    public $require_challan_receipt_upload;

    // 👥 Candidate & Application Rules
    public $allow_multiple_applications;
    public $prevent_duplicate_campaign_applications;
    public $challan_grace_days;
    public $auto_lock_profile_on_voucher;

    // 🏢 Organization Profile
    public $org_name;
    public $org_type;
    public $sector_label;
    public $currency_symbol;
    public $default_application_fee;

    // 🎨 App Identity & Branding
    public $app_name;
    public $app_tagline;
    public $app_description;
    public $app_logo;
    public $app_logo_dark;
    public $app_favicon;
    public $app_version;
    public $theme_accent_color;
    public $portal_announcement_enabled = false;
    public $portal_announcement_type = 'info';
    public $portal_announcement_message = '';
    public $footer_copyright_text;

    // Upload properties
    public $logoUpload;
    public $logoDarkUpload;
    public $faviconUpload;

    // 📞 Contact & Helpdesk
    public $support_email;
    public $support_phone;
    public $support_whatsapp;
    public $registrar_hotline;
    public $support_hours;
    public $helpdesk_location;
    public $org_website;

    // ✉️ Email Delivery & SMTP Gateway
    public $mail_from_name;
    public $mail_from_address;
    public $mail_reply_to;
    public $mail_mailer = 'smtp';
    public $mail_host;
    public $mail_port = 587;
    public $mail_encryption = 'tls';
    public $mail_username;
    public $mail_password;

    public $test_email_recipient = '';
    public $showPassword = false;

    // ⚖️ Statutory Quota Defaults
    public $asst_prof_required_peer_reviewers;
    public $prof_required_peer_reviewers;
    public $lecturer_required_peer_reviewers;

    // 🎓 Global Academic Division & Marks Benchmarks
    public $global_disallow_third_division = false;
    public $global_max_allowed_third_divisions = '';
    public $global_disallow_third_in_terminal = true;
    public $global_disallow_two_second_divisions = false;
    public $global_max_allowed_second_divisions = '';
    public $global_disallow_second_in_terminal = false;
    public $global_min_marks_percentage = '';
    public $global_min_terminal_percentage = '';
    public $global_min_terminal_cgpa = '';

    public function mount($tab = null)
    {
        abort_unless(Auth::check() && (Auth::user()->hasRole('super-admin') || Auth::user()->can('manage-settings')), 403);
        $this->loadSettings();

        if ($tab) {
            $this->activeTab = $this->resolveTabSlug((string) $tab);
        } elseif (request()->has('tab')) {
            $this->activeTab = $this->resolveTabSlug((string) request()->query('tab'));
        }
    }

    public function resolveTabSlug(string $slug): string
    {
        $normalized = strtolower(trim($slug));
        return match ($normalized) {
            'workflows', 'workflow', 'workflow-pipeline', 'pipeline' => 'pipeline',
            'documents', 'document-proofs', 'proofs' => 'documents',
            'candidate-rules', 'candidates', 'candidate-policies', 'candidate_rules' => 'candidate_rules',
            'organization', 'institution', 'org' => 'organization',
            'academic-criteria', 'criteria', 'benchmarks', 'academic_criteria' => 'academic_criteria',
            'branding', 'brand', 'app', 'app-branding', 'app_branding', 'logos', 'logo' => 'branding',
            'contact', 'helpdesk', 'support', 'contacts' => 'contact',
            'email', 'mail', 'smtp', 'notifications' => 'email',
            default => 'pipeline',
        };
    }

    public function setTab($tab)
    {
        $this->activeTab = $this->resolveTabSlug((string) $tab);
        $slug = match ($this->activeTab) {
            'documents' => 'documents',
            'candidate_rules' => 'candidate-rules',
            'organization' => 'organization',
            'academic_criteria' => 'academic-criteria',
            'branding' => 'branding',
            'contact' => 'contact',
            'email' => 'email',
            default => 'workflows',
        };
        $prefix = request()->is('registrar*') ? '/registrar' : '/admin';
        $this->dispatch('url-changed', url: "{$prefix}/settings/{$slug}");
    }

    public function loadSettings()
    {
        // Pipeline
        $this->enable_peer_review_step = (bool) SystemSetting::get('enable_peer_review_step', true);
        $this->enable_scrutiny_step = (bool) SystemSetting::get('enable_scrutiny_step', true);
        $this->enable_appellate_step = (bool) SystemSetting::get('enable_appellate_step', true);
        $this->enable_board_interview_step = (bool) SystemSetting::get('enable_board_interview_step', true);
        $this->enforce_strict_committee_quorum = (bool) SystemSetting::get('enforce_strict_committee_quorum', true);
        $this->enforce_strict_peer_review_quorum = (bool) SystemSetting::get('enforce_strict_peer_review_quorum', false);

        // Documents
        $this->require_transcript_proof = (bool) SystemSetting::get('require_transcript_proof', true);
        $this->require_experience_proof = (bool) SystemSetting::get('require_experience_proof', true);
        $this->require_cnic_upload = (bool) SystemSetting::get('require_cnic_upload', true);
        $this->require_noc_employed = (bool) SystemSetting::get('require_noc_employed', false);
        $this->require_publications_academic = (bool) SystemSetting::get('require_publications_academic', true);
        $this->require_challan_receipt_upload = (bool) SystemSetting::get('require_challan_receipt_upload', false);

        // Candidate Rules
        $this->allow_multiple_applications = (bool) SystemSetting::get('allow_multiple_applications', true);
        $this->prevent_duplicate_campaign_applications = (bool) SystemSetting::get('prevent_duplicate_campaign_applications', true);
        $this->challan_grace_days = (int) SystemSetting::get('challan_grace_days', 14);
        $this->auto_lock_profile_on_voucher = (bool) SystemSetting::get('auto_lock_profile_on_voucher', true);

        // Organization
        $this->org_name = SystemSetting::get('org_name', 'Hirely Recruitment Management System');
        $this->org_type = SystemSetting::get('org_type', 'HIGHER_EDUCATION');
        $this->sector_label = SystemSetting::get('sector_label', 'Department / Division');
        $this->currency_symbol = SystemSetting::get('currency_symbol', 'PKR');
        $this->default_application_fee = (int) SystemSetting::get('default_application_fee', 3000);

        // App Branding & Identity
        $this->app_name = SystemSetting::get('app_name', 'Hirely');
        $this->app_tagline = SystemSetting::get('app_tagline', 'Statutory Recruitment & Selection Operating System');
        $this->app_description = SystemSetting::get('app_description', 'Enterprise recruitment, statutory scrutiny, and merit selection operating system for higher education and statutory institutions.');
        $this->app_logo = SystemSetting::get('app_logo', '/images/logo-icon.svg');
        $this->app_logo_dark = SystemSetting::get('app_logo_dark', '/images/logo-icon.svg');
        $this->app_favicon = SystemSetting::get('app_favicon', '/favicon.ico');
        $this->app_version = SystemSetting::get('app_version', 'v2.4 Enterprise OS');
        $this->theme_accent_color = SystemSetting::get('theme_accent_color', '#4f46e5');
        $this->portal_announcement_enabled = (bool) SystemSetting::get('portal_announcement_enabled', false);
        $this->portal_announcement_type = SystemSetting::get('portal_announcement_type', 'info');
        $this->portal_announcement_message = SystemSetting::get('portal_announcement_message', '');
        $this->footer_copyright_text = SystemSetting::get('footer_copyright_text', '© ' . date('Y') . ' Hirely OS. All rights reserved.');

        // Contact & Helpdesk
        $this->support_email = SystemSetting::get('support_email', 'support@hirely.edu.pk');
        $this->support_phone = SystemSetting::get('support_phone', '+92-51-9250000');
        $this->support_whatsapp = SystemSetting::get('support_whatsapp', '+92-300-1234567');
        $this->registrar_hotline = SystemSetting::get('registrar_hotline', '+92-51-9250001');
        $this->support_hours = SystemSetting::get('support_hours', 'Monday to Friday: 08:30 AM – 04:30 PM PKT');
        $this->helpdesk_location = SystemSetting::get('helpdesk_location', 'Recruitment & Facilitation Center, Academic Block B, Main Campus');
        $this->org_website = SystemSetting::get('org_website', 'https://hirely.edu.pk');

        // Email & Delivery
        $this->mail_from_name = SystemSetting::get('mail_from_name', 'Hirely Recruitment Desk');
        $this->mail_from_address = SystemSetting::get('mail_from_address', 'noreply@hirely.edu.pk');
        $this->mail_reply_to = SystemSetting::get('mail_reply_to', 'support@hirely.edu.pk');
        $this->mail_mailer = SystemSetting::get('mail_mailer', 'smtp');
        $this->mail_host = SystemSetting::get('mail_host', 'smtp.mailgun.org');
        $this->mail_port = (int) SystemSetting::get('mail_port', 587);
        $this->mail_encryption = SystemSetting::get('mail_encryption', 'tls');
        $this->mail_username = SystemSetting::get('mail_username', '');
        $this->mail_password = SystemSetting::get('mail_password', '');

        // Statutory Quotas
        $this->asst_prof_required_peer_reviewers = (int) SystemSetting::get('asst_prof_required_peer_reviewers', 3);
        $this->prof_required_peer_reviewers = (int) SystemSetting::get('prof_required_peer_reviewers', 3);
        $this->lecturer_required_peer_reviewers = (int) SystemSetting::get('lecturer_required_peer_reviewers', 0);

        // Global Academic Division & Qualifications Criteria
        $globalCriteria = \App\Services\AcademicEligibilityEvaluator::getGlobalCriteria();
        $this->global_disallow_third_division = (bool)($globalCriteria['disallow_third_division'] ?? false);
        $this->global_max_allowed_third_divisions = $globalCriteria['max_allowed_third_divisions'] ?? '';
        $this->global_disallow_third_in_terminal = (bool)($globalCriteria['disallow_third_in_terminal'] ?? true);
        $this->global_disallow_two_second_divisions = (bool)($globalCriteria['disallow_two_second_divisions'] ?? false);
        $this->global_max_allowed_second_divisions = $globalCriteria['max_allowed_second_divisions'] ?? '';
        $this->global_disallow_second_in_terminal = (bool)($globalCriteria['disallow_second_in_terminal'] ?? false);
        $this->global_min_marks_percentage = $globalCriteria['min_marks_percentage'] ?? '';
        $this->global_min_terminal_percentage = $globalCriteria['min_terminal_percentage'] ?? '';
        $this->global_min_terminal_cgpa = $globalCriteria['min_terminal_cgpa'] ?? '';
    }

    public function toggleSetting($key)
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $current = (bool) SystemSetting::get($key, false);
        $new = !$current;
        SystemSetting::set($key, $new, type: 'boolean');
        $this->$key = $new;
        session()->flash('message', "Option '" . ucwords(str_replace('_', ' ', $key)) . "' updated to " . ($new ? 'Enabled (ON)' : 'Disabled (OFF)') . ".");
    }

    public function savePipelineSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        SystemSetting::set('enable_peer_review_step', $this->enable_peer_review_step, 'pipeline', 'boolean');
        SystemSetting::set('enable_scrutiny_step', $this->enable_scrutiny_step, 'pipeline', 'boolean');
        SystemSetting::set('enable_appellate_step', $this->enable_appellate_step, 'pipeline', 'boolean');
        SystemSetting::set('enable_board_interview_step', $this->enable_board_interview_step, 'pipeline', 'boolean');
        SystemSetting::set('enforce_strict_committee_quorum', $this->enforce_strict_committee_quorum, 'pipeline', 'boolean');
        SystemSetting::set('enforce_strict_peer_review_quorum', $this->enforce_strict_peer_review_quorum, 'pipeline', 'boolean');

        session()->flash('message', 'Workflow pipeline stage settings successfully saved.');
    }

    public function saveDocumentSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        SystemSetting::set('require_transcript_proof', $this->require_transcript_proof, 'documents', 'boolean');
        SystemSetting::set('require_experience_proof', $this->require_experience_proof, 'documents', 'boolean');
        SystemSetting::set('require_cnic_upload', $this->require_cnic_upload, 'documents', 'boolean');
        SystemSetting::set('require_noc_employed', $this->require_noc_employed, 'documents', 'boolean');
        SystemSetting::set('require_publications_academic', $this->require_publications_academic, 'documents', 'boolean');
        SystemSetting::set('require_challan_receipt_upload', $this->require_challan_receipt_upload, 'documents', 'boolean');

        session()->flash('message', 'Candidate document requests and upload rules successfully updated.');
    }

    public function saveCandidateRules()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'challan_grace_days' => 'required|integer|min:1|max:90',
        ]);

        SystemSetting::set('allow_multiple_applications', $this->allow_multiple_applications, 'candidate_rules', 'boolean');
        SystemSetting::set('prevent_duplicate_campaign_applications', $this->prevent_duplicate_campaign_applications, 'candidate_rules', 'boolean');
        SystemSetting::set('challan_grace_days', $this->challan_grace_days, 'candidate_rules', 'integer');
        SystemSetting::set('auto_lock_profile_on_voucher', $this->auto_lock_profile_on_voucher, 'candidate_rules', 'boolean');

        session()->flash('message', 'Candidate application and voucher settlement rules updated.');
    }

    public function saveOrganizationProfile()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'org_name' => 'required|string|max:190',
            'org_type' => 'required|string',
            'sector_label' => 'required|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'default_application_fee' => 'required|integer|min:0',
        ]);

        SystemSetting::set('org_name', $this->org_name, 'organization', 'string');
        SystemSetting::set('org_type', $this->org_type, 'organization', 'string');
        SystemSetting::set('sector_label', $this->sector_label, 'organization', 'string');
        SystemSetting::set('currency_symbol', $this->currency_symbol, 'organization', 'string');
        SystemSetting::set('default_application_fee', $this->default_application_fee, 'organization', 'integer');

        session()->flash('message', 'Organization sector profile and system nomenclature updated.');
    }

    public function saveConstitutions()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'asst_prof_required_peer_reviewers' => 'required|integer|min:1|max:10',
            'prof_required_peer_reviewers' => 'required|integer|min:1|max:10',
            'lecturer_required_peer_reviewers' => 'required|integer|min:0|max:10',
        ]);

        SystemSetting::set('asst_prof_required_peer_reviewers', $this->asst_prof_required_peer_reviewers, 'constitution_defaults', 'integer');
        SystemSetting::set('prof_required_peer_reviewers', $this->prof_required_peer_reviewers, 'constitution_defaults', 'integer');
        SystemSetting::set('lecturer_required_peer_reviewers', $this->lecturer_required_peer_reviewers, 'constitution_defaults', 'integer');

        session()->flash('message', 'Default statutory board quorums and peer review quotas successfully saved.');
    }

    public function saveAcademicCriteriaSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        \App\Services\AcademicEligibilityEvaluator::setGlobalCriteria([
            'disallow_third_division' => (bool)$this->global_disallow_third_division,
            'max_allowed_third_divisions' => $this->global_max_allowed_third_divisions !== '' ? (int)$this->global_max_allowed_third_divisions : null,
            'disallow_third_in_terminal' => (bool)$this->global_disallow_third_in_terminal,
            'disallow_two_second_divisions' => (bool)$this->global_disallow_two_second_divisions,
            'max_allowed_second_divisions' => $this->global_max_allowed_second_divisions !== '' ? (int)$this->global_max_allowed_second_divisions : null,
            'disallow_second_in_terminal' => (bool)$this->global_disallow_second_in_terminal,
            'min_marks_percentage' => $this->global_min_marks_percentage !== '' ? (float)$this->global_min_marks_percentage : null,
            'min_terminal_percentage' => $this->global_min_terminal_percentage !== '' ? (float)$this->global_min_terminal_percentage : null,
            'min_terminal_cgpa' => $this->global_min_terminal_cgpa !== '' ? (float)$this->global_min_terminal_cgpa : null,
            'scope' => 'GLOBAL',
            'rule_notes' => 'Institutional global qualifications and division benchmarks',
        ]);

        session()->flash('message', 'Global statutory academic division and marks benchmarks successfully updated.');
    }

    public function saveBrandingSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'app_name' => 'required|string|max:100',
            'app_tagline' => 'nullable|string|max:200',
            'app_description' => 'nullable|string|max:500',
            'app_version' => 'nullable|string|max:50',
            'theme_accent_color' => 'nullable|string|max:20',
            'logoUpload' => 'nullable|image|max:2048',
            'logoDarkUpload' => 'nullable|image|max:2048',
            'faviconUpload' => 'nullable|mimes:ico,png,svg,webp|max:1024',
        ]);

        if ($this->logoUpload) {
            $path = $this->logoUpload->store('branding', 'public');
            $this->app_logo = $path;
            SystemSetting::set('app_logo', $path, 'branding', 'string');
            $this->logoUpload = null;
        } elseif ($this->app_logo) {
            SystemSetting::set('app_logo', $this->app_logo, 'branding', 'string');
        }

        if ($this->logoDarkUpload) {
            $path = $this->logoDarkUpload->store('branding', 'public');
            $this->app_logo_dark = $path;
            SystemSetting::set('app_logo_dark', $path, 'branding', 'string');
            $this->logoDarkUpload = null;
        } elseif ($this->app_logo_dark) {
            SystemSetting::set('app_logo_dark', $this->app_logo_dark, 'branding', 'string');
        }

        if ($this->faviconUpload) {
            $path = $this->faviconUpload->store('branding', 'public');
            $this->app_favicon = $path;
            SystemSetting::set('app_favicon', $path, 'branding', 'string');
            $this->faviconUpload = null;
        } elseif ($this->app_favicon) {
            SystemSetting::set('app_favicon', $this->app_favicon, 'branding', 'string');
        }

        SystemSetting::set('app_name', $this->app_name, 'branding', 'string');
        SystemSetting::set('app_tagline', $this->app_tagline, 'branding', 'string');
        SystemSetting::set('app_description', $this->app_description, 'branding', 'string');
        SystemSetting::set('app_version', $this->app_version, 'branding', 'string');
        SystemSetting::set('theme_accent_color', $this->theme_accent_color, 'branding', 'string');
        SystemSetting::set('portal_announcement_enabled', (bool) $this->portal_announcement_enabled, 'branding', 'boolean');
        SystemSetting::set('portal_announcement_type', $this->portal_announcement_type, 'branding', 'string');
        SystemSetting::set('portal_announcement_message', $this->portal_announcement_message, 'branding', 'string');
        SystemSetting::set('footer_copyright_text', $this->footer_copyright_text, 'branding', 'string');

        session()->flash('message', 'Application branding, visual logos, favicon, and announcement banner saved.');
    }

    public function resetLogoToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->app_logo = '/images/logo-icon.svg';
        $this->app_logo_dark = '/images/logo-icon.svg';
        $this->logoUpload = null;
        $this->logoDarkUpload = null;
        SystemSetting::set('app_logo', '/images/logo-icon.svg', 'branding', 'string');
        SystemSetting::set('app_logo_dark', '/images/logo-icon.svg', 'branding', 'string');
        session()->flash('message', 'Brand logos reset to default Hirely SVG asset.');
    }

    public function resetFaviconToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->app_favicon = '/favicon.ico';
        $this->faviconUpload = null;
        SystemSetting::set('app_favicon', '/favicon.ico', 'branding', 'string');
        session()->flash('message', 'Browser favicon reset to default Hirely favicon.');
    }

    public function saveContactSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'support_email' => 'nullable|email|max:150',
            'support_phone' => 'nullable|string|max:50',
            'support_whatsapp' => 'nullable|string|max:50',
            'registrar_hotline' => 'nullable|string|max:50',
            'support_hours' => 'nullable|string|max:150',
            'helpdesk_location' => 'nullable|string|max:250',
            'org_website' => 'nullable|url|max:200',
        ]);

        SystemSetting::set('support_email', $this->support_email, 'contact', 'string');
        SystemSetting::set('support_phone', $this->support_phone, 'contact', 'string');
        SystemSetting::set('support_whatsapp', $this->support_whatsapp, 'contact', 'string');
        SystemSetting::set('registrar_hotline', $this->registrar_hotline, 'contact', 'string');
        SystemSetting::set('support_hours', $this->support_hours, 'contact', 'string');
        SystemSetting::set('helpdesk_location', $this->helpdesk_location, 'contact', 'string');
        SystemSetting::set('org_website', $this->org_website, 'contact', 'string');

        session()->flash('message', 'Candidate support channels, helpline numbers, and helpdesk locations saved.');
    }

    public function saveEmailSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'mail_from_name' => 'required|string|max:100',
            'mail_from_address' => 'required|email|max:150',
            'mail_reply_to' => 'nullable|email|max:150',
            'mail_mailer' => 'required|string|in:smtp,sendmail,mailgun,ses,log,array',
            'mail_host' => 'nullable|string|max:150',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|string|in:tls,ssl,none',
            'mail_username' => 'nullable|string|max:150',
            'mail_password' => 'nullable|string|max:255',
        ]);

        SystemSetting::set('mail_from_name', $this->mail_from_name, 'email', 'string');
        SystemSetting::set('mail_from_address', $this->mail_from_address, 'email', 'string');
        SystemSetting::set('mail_reply_to', $this->mail_reply_to, 'email', 'string');
        SystemSetting::set('mail_mailer', $this->mail_mailer, 'email', 'string');
        SystemSetting::set('mail_host', $this->mail_host, 'email', 'string');
        SystemSetting::set('mail_port', $this->mail_port, 'email', 'integer');
        SystemSetting::set('mail_encryption', $this->mail_encryption, 'email', 'string');
        SystemSetting::set('mail_username', $this->mail_username, 'email', 'string');
        SystemSetting::set('mail_password', $this->mail_password, 'email', 'string');

        session()->flash('message', 'Mail transport protocol, sender identities, and SMTP credentials saved.');
    }

    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function sendTestEmail()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'test_email_recipient' => 'required|email',
        ]);

        try {
            Config::set('mail.default', $this->mail_mailer);
            if ($this->mail_mailer === 'smtp') {
                Config::set('mail.mailers.smtp.host', $this->mail_host);
                Config::set('mail.mailers.smtp.port', (int) $this->mail_port);
                Config::set('mail.mailers.smtp.encryption', $this->mail_encryption === 'none' ? null : $this->mail_encryption);
                Config::set('mail.mailers.smtp.username', $this->mail_username);
                Config::set('mail.mailers.smtp.password', $this->mail_password);
            }
            Config::set('mail.from.address', $this->mail_from_address);
            Config::set('mail.from.name', $this->mail_from_name);

            $recipient = $this->test_email_recipient;
            $appName = $this->app_name ?: 'Hirely';
            $sender = $this->mail_from_name;
            $timestamp = now()->toDayDateTimeString();

            Mail::raw("Congratulations! This is an official test notification confirming that the {$appName} mail gateway is successfully configured and operating.\n\nDispatched on: {$timestamp}\nSender: {$sender} <{$this->mail_from_address}>\nTransport Driver: {$this->mail_mailer}", function ($message) use ($recipient, $appName) {
                $message->to($recipient)
                    ->subject("{$appName} Mail Gateway — Configuration Verification Test");
            });

            session()->flash('test_email_status', [
                'type' => 'success',
                'message' => "Test email successfully dispatched to '{$this->test_email_recipient}'. Please verify receipt in your inbox/spam folder.",
            ]);
        } catch (\Throwable $e) {
            session()->flash('test_email_status', [
                'type' => 'danger',
                'message' => "Mail dispatch failed: " . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.system-settings')
            ->layout('layouts.app', ['title' => 'System & Workflow Settings - Hirely']);
    }
}

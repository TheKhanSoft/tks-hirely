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
    public $enable_interview_call_letters = true;
    public $enable_referee_magic_links = true;
    public $enable_hjrs_journal_lookup = true;
    public $enable_appellate_workbench = true;
    public $enable_projector_merit_board = true;
    public $enable_executive_analytics = true;

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
    public $candidate_allow_application_withdrawal = false;
    public $max_active_applications_per_candidate = 5;
    public $max_upload_file_size_mb = 5;
    public $enable_online_challan_verification = true;
    public $submission_cutoff_time = '23:59:59';
    public $allow_late_fee_submission = false;
    public $late_fee_grace_days = 3;
    public $late_fee_amount = 1000;
    public $max_upload_size_per_dossier_mb = 50;
    public $lock_dossier_after_submission = true;

    // 🏢 Organization Profile
    public $org_name;
    public $org_short_name;
    public $org_location;
    public $org_office_title;
    public $org_type;
    public $sector_label;
    public $currency_symbol;
    public $default_application_fee;

    // 📜 Official Letterhead & Document Formatting
    public $letterhead_heading;
    public $letterhead_subheading;
    public $letterhead_office;
    public $letterhead_logo;
    public $letterhead_custom_details;
    public $letterhead_layout = 'classic_left';
    public $letterhead_office_font_size = 'standard';
    public $letterhead_dossier_title_admin = 'Official Master Application Dossier & Scrutiny Record';
    public $letterhead_dossier_title_candidate = 'Official Application Form for Employment';
    public $letterhead_show_document_title = true;
    public $letterhead_show_statutory_note = true;
    public $dossier_show_photo = true;
    public $dossier_show_draft_watermark = true;
    public $letterheadLogoUpload;

    // 🌐 General Platform Format & Localization
    public $app_date_format = 'd M, Y';
    public $app_time_format = '12h';
    public $app_timezone_display = 'PKT (UTC+5)';
    public $app_tracking_prefix = 'APP-';
    public $public_jobs_enabled = true;

    // 🎨 App Identity & Branding
    public $app_name;
    public $app_tagline;
    public $app_description;
    public $app_logo;
    public $app_logo_dark;
    public $app_favicon;
    public $app_version;
    public $theme_accent_color;
    public $theme_secondary_color;
    public $theme_radius_style = 'modern';
    public $theme_font_family = 'jakarta';
    public $theme_card_elevation = 'bordered';
    public $theme_sidebar_style = 'glass-dark';
    public $theme_login_layout = 'split-modern';
    public $ui_glassmorphism_enabled = true;
    public $ui_animations_enabled = true;
    public $ui_table_density = 'comfortable';
    public $portal_announcement_enabled = false;
    public $portal_announcement_type = 'info';
    public $portal_announcement_message = '';
    public $footer_copyright_text;
    public $footer_candidate_text;
    public $footer_admin_text;
    public $footer_board_text;
    public $footer_reviewer_text;
    public $letter_footer_text;
    public $letter_footer_contact;
    public $letter_show_audit_trail = true;
    public $letter_show_verify_ref = true;

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

    // ⚖️ Statutory Merit Formulation & Selection Board Scoring Weights
    public $merit_academic_weight = 50;
    public $merit_higher_qualification_weight = 5;
    public $merit_research_weight = 15;
    public $merit_experience_weight = 10;
    public $merit_interview_weight = 20;
    public $merit_minimum_passing_aggregate = 50;

    // Advanced Academic & HEC Standards
    public $hec_minimum_phd_criteria_enabled = true;
    public $foreign_degree_equivalence_mandatory = true;
    public $publication_category_w_weight = 5.0;
    public $publication_category_x_weight = 3.0;
    public $publication_category_y_weight = 1.0;
    public $minimum_publications_asst_prof = 0;
    public $minimum_publications_assoc_prof = 10;
    public $minimum_publications_prof = 15;

    // 🔒 Security, Access Governance & Session Policies
    public $auth_two_factor_policy = 'optional';
    public $auth_session_timeout_minutes = 60;
    public $auth_max_login_attempts = 5;
    public $auth_lockout_duration_minutes = 15;
    public $candidate_registration_status = 'open';
    public $candidate_registration_closed_message = '';
    public $job_board_visibility = 'public';
    public $password_min_length = 8;
    public $password_require_uppercase = true;
    public $password_require_numeric = true;
    public $password_require_special = false;
    public $force_first_login_password_change = false;
    public $enforce_strict_cnic_format = true;
    public $max_upload_size_mb = 5;
    public $allowed_file_types = 'pdf, jpg, jpeg, png, docx';

    // Advanced Security & Integrity Controls
    public $session_ip_lock_enabled = true;
    public $force_logout_on_password_change = true;
    public $tamper_evident_dossier_hash = true;
    public $watermark_text_custom = 'CONFIDENTIAL • STATUTORY RECORD';

    // ⚙️ System Health, Diagnostics & Maintenance
    public $system_maintenance_mode = false;
    public $maintenance_notice_heading = 'Scheduled Statutory System Maintenance';
    public $maintenance_notice_message = '';

    // 📱 SMS & Multi-Channel Alert Gateway
    public $sms_enabled = false;
    public $sms_provider = 'twilio';
    public $sms_sender_id = 'HIRELY';
    public $sms_api_key = '';
    public $sms_api_url = '';
    public $sms_api_secret = '';
    public $sms_username = '';
    public $sms_trigger_app_submitted = true;
    public $sms_trigger_payment_verified = true;
    public $sms_trigger_scrutiny_deficiency = true;
    public $sms_trigger_interview_call = true;
    public $test_sms_recipient = '';
    public $whatsapp_business_enabled = false;
    public $whatsapp_business_phone = '+92-300-1234567';

    // ✉️ Email Templates & Custom Dispatch Desk
    public $templateRecipientFilter = 'ALL';
    public $templateSearch = '';
    public $selectedTemplateId = null;
    public $template_name = '';
    public $template_subject = '';
    public $template_preheader = '';
    public $template_body = '';
    public $template_is_trigger_enabled = true;
    public $template_placeholders = [];
    public $isTemplateModalOpen = false;

    public $isPreviewModalOpen = false;
    public $previewHtml = '';
    public $previewSubject = '';
    public $previewTemplateName = '';
    public $previewMode = 'sample'; // 'sample' or 'raw'

    // Custom Email Composer
    public $isCustomEmailModalOpen = false;
    public $custom_recipient_type = 'custom'; // 'candidate', 'reviewer', 'board', 'custom'
    public $custom_recipient_user_id = null;
    public $custom_recipient_email = '';
    public $custom_recipient_name = '';
    public $custom_subject = '';
    public $custom_body = '';
    public $custom_selected_template_slug = '';
    public $custom_action_url = '';
    public $custom_action_text = '';

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
            'email', 'mail', 'smtp', 'notifications', 'sms' => 'email',
            'security', 'sec', 'auth', 'access', 'session', 'passwords' => 'security',
            'system', 'maintenance', 'diagnostics', 'health', 'cache' => 'system',
            'email-templates', 'email_templates', 'templates', 'template', 'communications' => 'email_templates',
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
            'security' => 'security',
            'system' => 'system',
            'email_templates' => 'email-templates',
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
        $this->enable_interview_call_letters = (bool) SystemSetting::get('enable_interview_call_letters', true);
        $this->enable_referee_magic_links = (bool) SystemSetting::get('enable_referee_magic_links', true);
        $this->enable_hjrs_journal_lookup = (bool) SystemSetting::get('enable_hjrs_journal_lookup', true);
        $this->enable_appellate_workbench = (bool) SystemSetting::get('enable_appellate_workbench', true);
        $this->enable_projector_merit_board = (bool) SystemSetting::get('enable_projector_merit_board', true);
        $this->enable_executive_analytics = (bool) SystemSetting::get('enable_executive_analytics', true);

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
        $this->candidate_allow_application_withdrawal = (bool) SystemSetting::get('candidate_allow_application_withdrawal', false);
        $this->max_active_applications_per_candidate = (int) SystemSetting::get('max_active_applications_per_candidate', 5);
        $this->max_upload_file_size_mb = (int) SystemSetting::get('max_upload_file_size_mb', 5);
        $this->enable_online_challan_verification = (bool) SystemSetting::get('enable_online_challan_verification', true);
        $this->submission_cutoff_time = (string) SystemSetting::get('submission_cutoff_time', '23:59:59');
        $this->allow_late_fee_submission = (bool) SystemSetting::get('allow_late_fee_submission', false);
        $this->late_fee_grace_days = (int) SystemSetting::get('late_fee_grace_days', 3);
        $this->late_fee_amount = (int) SystemSetting::get('late_fee_amount', 1000);
        $this->max_upload_size_per_dossier_mb = (int) SystemSetting::get('max_upload_size_per_dossier_mb', 50);
        $this->lock_dossier_after_submission = (bool) SystemSetting::get('lock_dossier_after_submission', true);

        // Organization
        $this->org_name = SystemSetting::get('org_name', 'Hirely');
        $this->org_short_name = SystemSetting::get('org_short_name', 'Hirely');
        $this->org_location = SystemSetting::get('org_location', 'National Directorate');
        $this->org_office_title = SystemSetting::get('org_office_title', 'Recruitment & Statutory Selection Directorate');
        $this->org_type = SystemSetting::get('org_type', 'HIGHER_EDUCATION');
        $this->sector_label = SystemSetting::get('sector_label', 'Department / Division');
        $this->currency_symbol = SystemSetting::get('currency_symbol', 'PKR');
        $this->default_application_fee = (int) SystemSetting::get('default_application_fee', 3000);

        // Letterhead & Printed Documents
        $this->letterhead_heading = SystemSetting::get('letterhead_heading', $this->org_name);
        $this->letterhead_subheading = SystemSetting::get('letterhead_subheading', $this->org_location);
        $this->letterhead_office = SystemSetting::get('letterhead_office', $this->org_office_title);
        $this->letterhead_logo = SystemSetting::get('letterhead_logo', '/images/logo-icon.svg');
        $this->letterhead_custom_details = SystemSetting::get('letterhead_custom_details', "Central Secretariat & Statutory Directorate\nWebsite: https://hirely.app • Email: support@hirely.app • Verification Portal: https://hirely.app/verify");
        $this->letterhead_layout = SystemSetting::get('letterhead_layout', 'classic_left');
        $this->letterhead_office_font_size = SystemSetting::get('letterhead_office_font_size', 'standard');
        $rawAdminTitle = SystemSetting::get('letterhead_dossier_title_admin', 'Official Master Application Dossier & Scrutiny Record');
        $this->letterhead_dossier_title_admin = trim(str_ireplace('Administrative ', '', (string)$rawAdminTitle)) ?: 'Official Master Application Dossier & Scrutiny Record';
        $this->letterhead_dossier_title_candidate = SystemSetting::get('letterhead_dossier_title_candidate', 'Official Application Form for Employment');
        $this->letterhead_show_document_title = (bool) SystemSetting::get('letterhead_show_document_title', true);
        $this->letterhead_show_statutory_note = (bool) SystemSetting::get('letterhead_show_statutory_note', true);
        $this->dossier_show_photo = (bool) SystemSetting::get('dossier_show_photo', true);
        $this->dossier_show_draft_watermark = (bool) SystemSetting::get('dossier_show_draft_watermark', true);

        // App Branding & Identity
        $this->app_name = SystemSetting::get('app_name', 'Hirely');
        $this->app_tagline = SystemSetting::get('app_tagline', 'Statutory Recruitment & Selection Operating System');
        $this->app_description = SystemSetting::get('app_description', 'Enterprise recruitment, statutory scrutiny, and merit selection operating system for higher education and statutory institutions.');
        $this->app_logo = SystemSetting::get('app_logo', '/images/logo-icon.svg');
        $this->app_logo_dark = SystemSetting::get('app_logo_dark', '/images/logo-icon.svg');
        $this->app_favicon = SystemSetting::get('app_favicon', '/favicon.ico');
        $this->app_version = SystemSetting::get('app_version', 'v2.4 Enterprise OS');
        $this->theme_accent_color = SystemSetting::get('theme_accent_color', '#4f46e5');
        $this->theme_secondary_color = SystemSetting::get('theme_secondary_color', '#06b6d4');
        $this->theme_radius_style = SystemSetting::get('theme_radius_style', 'modern');
        $this->theme_font_family = SystemSetting::get('theme_font_family', 'jakarta');
        $this->theme_card_elevation = SystemSetting::get('theme_card_elevation', 'bordered');
        $this->theme_sidebar_style = SystemSetting::get('theme_sidebar_style', 'glass-dark');
        $this->theme_login_layout = SystemSetting::get('theme_login_layout', 'split-modern');
        $this->ui_glassmorphism_enabled = (bool) SystemSetting::get('ui_glassmorphism_enabled', true);
        $this->ui_animations_enabled = (bool) SystemSetting::get('ui_animations_enabled', true);
        $this->ui_table_density = SystemSetting::get('ui_table_density', 'comfortable');
        $this->portal_announcement_enabled = (bool) SystemSetting::get('portal_announcement_enabled', false);
        $this->portal_announcement_type = SystemSetting::get('portal_announcement_type', 'info');
        $this->portal_announcement_message = SystemSetting::get('portal_announcement_message', '');
        $this->footer_copyright_text = SystemSetting::get('footer_copyright_text', '© ' . date('Y') . ' Hirely. All rights reserved.');
        $this->footer_candidate_text = SystemSetting::get('footer_candidate_text', 'Candidate Facilitation Desk: For application guidance or voucher verification, contact support@hirely.app or submit an inquiry through the helpdesk.');
        $this->footer_admin_text = SystemSetting::get('footer_admin_text', 'Official Administrative Desk • Hirely Enterprise OS • System Access Audited & Recorded.');
        $this->footer_board_text = SystemSetting::get('footer_board_text', 'Statutory Selection Board Secretariat • Strictly Confidential & Privileged Record.');
        $this->footer_reviewer_text = SystemSetting::get('footer_reviewer_text', 'Academic Peer Evaluation Desk • External Referee Integrity • Blind Review Protocols Active.');
        $this->letter_footer_text = SystemSetting::get('letter_footer_text', 'This is an official computer-generated document issued under statutory recruitment authority. Any alteration, erasure, or unauthorized tampering renders this document null and void.');
        $this->letter_footer_contact = SystemSetting::get('letter_footer_contact', 'Recruitment Directorate • Candidate Records & Statutory Scrutiny Archive • Verification Portal: https://hirely.app');
        $this->letter_show_audit_trail = (bool) SystemSetting::get('letter_show_audit_trail', true);
        $this->letter_show_verify_ref = (bool) SystemSetting::get('letter_show_verify_ref', true);
        $this->app_date_format = SystemSetting::get('app_date_format', 'd M, Y');
        $this->app_time_format = SystemSetting::get('app_time_format', '12h');
        $this->app_timezone_display = SystemSetting::get('app_timezone_display', 'PKT (UTC+5)');
        $this->app_tracking_prefix = SystemSetting::get('app_tracking_prefix', 'APP-');
        $this->public_jobs_enabled = (bool) SystemSetting::get('public_jobs_enabled', true);

        // Contact & Helpdesk
        $this->support_email = SystemSetting::get('support_email', 'support@hirely.app');
        $this->support_phone = SystemSetting::get('support_phone', '+1 (800) 555-0199');
        $this->support_whatsapp = SystemSetting::get('support_whatsapp', '');
        $this->registrar_hotline = SystemSetting::get('registrar_hotline', '');
        $this->support_hours = SystemSetting::get('support_hours', 'Monday to Friday: 09:00 AM – 05:00 PM');
        $this->helpdesk_location = SystemSetting::get('helpdesk_location', 'Central Recruitment & Candidate Facilitation Center');
        $this->org_website = SystemSetting::get('org_website', 'https://hirely.app');

        // Email & Delivery
        $this->mail_from_name = SystemSetting::get('mail_from_name', 'Hirely Recruitment Desk');
        $this->mail_from_address = SystemSetting::get('mail_from_address', 'noreply@hirely.app');
        $this->mail_reply_to = SystemSetting::get('mail_reply_to', 'support@hirely.app');
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

        // Statutory Merit Formulation Weightages
        $this->merit_academic_weight = (int) SystemSetting::get('merit_academic_weight', 50);
        $this->merit_higher_qualification_weight = (int) SystemSetting::get('merit_higher_qualification_weight', 5);
        $this->merit_research_weight = (int) SystemSetting::get('merit_research_weight', 15);
        $this->merit_experience_weight = (int) SystemSetting::get('merit_experience_weight', 10);
        $this->merit_interview_weight = (int) SystemSetting::get('merit_interview_weight', 20);
        $this->merit_minimum_passing_aggregate = (int) SystemSetting::get('merit_minimum_passing_aggregate', 50);

        // Advanced Academic & HEC Standards
        $this->hec_minimum_phd_criteria_enabled = (bool) SystemSetting::get('hec_minimum_phd_criteria_enabled', true);
        $this->foreign_degree_equivalence_mandatory = (bool) SystemSetting::get('foreign_degree_equivalence_mandatory', true);
        $this->publication_category_w_weight = (float) SystemSetting::get('publication_category_w_weight', 5.0);
        $this->publication_category_x_weight = (float) SystemSetting::get('publication_category_x_weight', 3.0);
        $this->publication_category_y_weight = (float) SystemSetting::get('publication_category_y_weight', 1.0);
        $this->minimum_publications_asst_prof = (int) SystemSetting::get('minimum_publications_asst_prof', 0);
        $this->minimum_publications_assoc_prof = (int) SystemSetting::get('minimum_publications_assoc_prof', 10);
        $this->minimum_publications_prof = (int) SystemSetting::get('minimum_publications_prof', 15);

        // Security, Access Governance & Session Policies
        $this->auth_two_factor_policy = (string) SystemSetting::get('auth_two_factor_policy', 'optional');
        $this->auth_session_timeout_minutes = (int) SystemSetting::get('auth_session_timeout_minutes', 60);
        $this->auth_max_login_attempts = (int) SystemSetting::get('auth_max_login_attempts', 5);
        $this->auth_lockout_duration_minutes = (int) SystemSetting::get('auth_lockout_duration_minutes', 15);
        $this->candidate_registration_status = (string) SystemSetting::get('candidate_registration_status', 'open');
        $this->candidate_registration_closed_message = (string) SystemSetting::get('candidate_registration_closed_message', 'Online recruitment intake is currently closed. Please review active advertisements for upcoming announcement schedules.');
        $this->job_board_visibility = (string) SystemSetting::get('job_board_visibility', 'public');
        $this->password_min_length = (int) SystemSetting::get('password_min_length', 8);
        $this->password_require_uppercase = (bool) SystemSetting::get('password_require_uppercase', true);
        $this->password_require_numeric = (bool) SystemSetting::get('password_require_numeric', true);
        $this->password_require_special = (bool) SystemSetting::get('password_require_special', false);
        $this->force_first_login_password_change = (bool) SystemSetting::get('force_first_login_password_change', false);
        $this->enforce_strict_cnic_format = (bool) SystemSetting::get('enforce_strict_cnic_format', true);
        $this->max_upload_size_mb = (int) SystemSetting::get('max_upload_size_mb', 5);
        $this->allowed_file_types = (string) SystemSetting::get('allowed_file_types', 'pdf, jpg, jpeg, png, docx');

        // Advanced Security & Integrity Controls
        $this->session_ip_lock_enabled = (bool) SystemSetting::get('session_ip_lock_enabled', true);
        $this->force_logout_on_password_change = (bool) SystemSetting::get('force_logout_on_password_change', true);
        $this->tamper_evident_dossier_hash = (bool) SystemSetting::get('tamper_evident_dossier_hash', true);
        $this->watermark_text_custom = (string) SystemSetting::get('watermark_text_custom', 'CONFIDENTIAL • STATUTORY RECORD');

        // System Health, Diagnostics & Maintenance
        $this->system_maintenance_mode = (bool) SystemSetting::get('system_maintenance_mode', false);
        $this->maintenance_notice_heading = (string) SystemSetting::get('maintenance_notice_heading', 'Scheduled Statutory System Maintenance');
        $this->maintenance_notice_message = (string) SystemSetting::get('maintenance_notice_message', 'The Hirely recruitment portal is undergoing scheduled maintenance and database optimization. Access will be restored shortly. Thank you for your patience.');

        // SMS & Multi-Channel Alert Gateway
        $this->sms_enabled = (bool) SystemSetting::get('sms_enabled', false);
        $this->sms_provider = (string) SystemSetting::get('sms_provider', 'twilio');
        $this->sms_sender_id = (string) SystemSetting::get('sms_sender_id', 'AWKUM-HR');
        $this->sms_api_key = (string) SystemSetting::get('sms_api_key', '');
        $this->sms_api_url = (string) SystemSetting::get('sms_api_url', '');
        $this->sms_api_secret = (string) SystemSetting::get('sms_api_secret', '');
        $this->sms_username = (string) SystemSetting::get('sms_username', '');
        $this->sms_trigger_app_submitted = (bool) SystemSetting::get('sms_trigger_app_submitted', true);
        $this->sms_trigger_payment_verified = (bool) SystemSetting::get('sms_trigger_payment_verified', true);
        $this->sms_trigger_scrutiny_deficiency = (bool) SystemSetting::get('sms_trigger_scrutiny_deficiency', true);
        $this->sms_trigger_interview_call = (bool) SystemSetting::get('sms_trigger_interview_call', true);
        $this->whatsapp_business_enabled = (bool) SystemSetting::get('whatsapp_business_enabled', false);
        $this->whatsapp_business_phone = (string) SystemSetting::get('whatsapp_business_phone', '+92-300-1234567');
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
        SystemSetting::set('enable_interview_call_letters', (bool) $this->enable_interview_call_letters, 'pipeline', 'boolean');
        SystemSetting::set('enable_referee_magic_links', (bool) $this->enable_referee_magic_links, 'pipeline', 'boolean');
        SystemSetting::set('enable_hjrs_journal_lookup', (bool) $this->enable_hjrs_journal_lookup, 'academic_criteria', 'boolean');
        SystemSetting::set('enable_appellate_workbench', (bool) $this->enable_appellate_workbench, 'pipeline', 'boolean');
        SystemSetting::set('enable_projector_merit_board', (bool) $this->enable_projector_merit_board, 'pipeline', 'boolean');
        SystemSetting::set('enable_executive_analytics', (bool) $this->enable_executive_analytics, 'governance', 'boolean');

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
            'max_active_applications_per_candidate' => 'required|integer|min:1|max:50',
            'max_upload_file_size_mb' => 'required|integer|min:1|max:50',
            'submission_cutoff_time' => 'required|string',
            'late_fee_grace_days' => 'required|integer|min:0|max:30',
            'late_fee_amount' => 'required|integer|min:0|max:100000',
            'max_upload_size_per_dossier_mb' => 'required|integer|min:5|max:200',
        ]);

        SystemSetting::set('allow_multiple_applications', $this->allow_multiple_applications, 'candidate_rules', 'boolean');
        SystemSetting::set('prevent_duplicate_campaign_applications', $this->prevent_duplicate_campaign_applications, 'candidate_rules', 'boolean');
        SystemSetting::set('challan_grace_days', $this->challan_grace_days, 'candidate_rules', 'integer');
        SystemSetting::set('auto_lock_profile_on_voucher', $this->auto_lock_profile_on_voucher, 'candidate_rules', 'boolean');
        SystemSetting::set('candidate_allow_application_withdrawal', $this->candidate_allow_application_withdrawal, 'candidate_rules', 'boolean');
        SystemSetting::set('max_active_applications_per_candidate', $this->max_active_applications_per_candidate, 'candidate_rules', 'integer');
        SystemSetting::set('max_upload_file_size_mb', $this->max_upload_file_size_mb, 'candidate_rules', 'integer');
        SystemSetting::set('enable_online_challan_verification', $this->enable_online_challan_verification, 'candidate_rules', 'boolean');
        SystemSetting::set('submission_cutoff_time', $this->submission_cutoff_time, 'candidate_rules', 'string');
        SystemSetting::set('allow_late_fee_submission', (bool) $this->allow_late_fee_submission, 'candidate_rules', 'boolean');
        SystemSetting::set('late_fee_grace_days', (int) $this->late_fee_grace_days, 'candidate_rules', 'integer');
        SystemSetting::set('late_fee_amount', (int) $this->late_fee_amount, 'candidate_rules', 'integer');
        SystemSetting::set('max_upload_size_per_dossier_mb', (int) $this->max_upload_size_per_dossier_mb, 'candidate_rules', 'integer');
        SystemSetting::set('lock_dossier_after_submission', (bool) $this->lock_dossier_after_submission, 'candidate_rules', 'boolean');

        session()->flash('message', 'Candidate application, intake policies, and voucher settlement rules updated.');
    }

    public function saveOrganizationProfile()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'org_name' => 'required|string|max:190',
            'org_short_name' => 'required|string|max:40',
            'org_location' => 'nullable|string|max:190',
            'org_office_title' => 'nullable|string|max:190',
            'org_type' => 'required|string',
            'sector_label' => 'required|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'default_application_fee' => 'required|integer|min:0',
        ]);

        SystemSetting::set('org_name', $this->org_name, 'organization', 'string');
        SystemSetting::set('org_short_name', $this->org_short_name, 'organization', 'string');
        SystemSetting::set('org_location', $this->org_location, 'organization', 'string');
        SystemSetting::set('org_office_title', $this->org_office_title, 'organization', 'string');
        SystemSetting::set('org_type', $this->org_type, 'organization', 'string');
        SystemSetting::set('sector_label', $this->sector_label, 'organization', 'string');
        SystemSetting::set('currency_symbol', $this->currency_symbol, 'organization', 'string');
        SystemSetting::set('default_application_fee', $this->default_application_fee, 'organization', 'integer');

        // Also sync letterhead default heading if unconfigured or generic
        $genericHeadings = [
            'Hirely Higher Education & Recruitment Commission',
            'Hirely Recruitment Management System',
            'Hirely Higher Education Commission',
            '',
        ];
        if (empty($this->letterhead_heading) || in_array(trim($this->letterhead_heading), $genericHeadings)) {
            $this->letterhead_heading = $this->org_name;
            SystemSetting::set('letterhead_heading', $this->letterhead_heading, 'organization', 'string');
        }

        // Keep app branding aligned with organization short name if default
        if (empty($this->app_name) || $this->app_name === 'Hirely') {
            $this->app_name = $this->org_short_name;
            SystemSetting::set('app_name', $this->app_name, 'branding', 'string');
        }

        SystemSetting::clearCache();

        session()->flash('message', 'Organization legal title, short name, sector taxonomy, and fee standards saved.');
    }

    public function saveLetterheadSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->validate([
            'letterhead_heading' => 'required|string|max:190',
            'letterhead_subheading' => 'nullable|string|max:190',
            'letterhead_office' => 'nullable|string|max:190',
            'letterhead_custom_details' => 'nullable|string|max:2000',
            'letterhead_layout' => 'nullable|string|in:classic_left,crest_center,modern_clean',
            'letterhead_office_font_size' => 'nullable|string|in:standard,large,compact',
            'letterhead_dossier_title_admin' => 'nullable|string|max:190',
            'letterhead_dossier_title_candidate' => 'nullable|string|max:190',
            'letterheadLogoUpload' => 'nullable|image|max:2048',
        ]);

        if ($this->letterheadLogoUpload) {
            $path = $this->letterheadLogoUpload->store('branding', 'public');
            $this->letterhead_logo = $path;
            SystemSetting::set('letterhead_logo', $path, 'organization', 'string');
            $this->letterheadLogoUpload = null;
        } elseif ($this->letterhead_logo) {
            SystemSetting::set('letterhead_logo', $this->letterhead_logo, 'organization', 'string');
        }

        // Clean out legacy "Administrative" from title
        $this->letterhead_dossier_title_admin = trim(str_ireplace('Administrative ', '', (string)$this->letterhead_dossier_title_admin));

        SystemSetting::set('letterhead_heading', $this->letterhead_heading, 'organization', 'string');
        SystemSetting::set('letterhead_subheading', $this->letterhead_subheading, 'organization', 'string');
        SystemSetting::set('letterhead_office', $this->letterhead_office, 'organization', 'string');
        SystemSetting::set('letterhead_office_font_size', $this->letterhead_office_font_size, 'organization', 'string');
        SystemSetting::set('letterhead_custom_details', $this->letterhead_custom_details, 'organization', 'string');
        SystemSetting::set('letterhead_layout', $this->letterhead_layout, 'organization', 'string');
        SystemSetting::set('letterhead_dossier_title_admin', $this->letterhead_dossier_title_admin, 'organization', 'string');
        SystemSetting::set('letterhead_dossier_title_candidate', $this->letterhead_dossier_title_candidate, 'organization', 'string');
        SystemSetting::set('letterhead_show_document_title', $this->letterhead_show_document_title, 'organization', 'boolean');
        SystemSetting::set('letterhead_show_statutory_note', $this->letterhead_show_statutory_note, 'organization', 'boolean');
        SystemSetting::set('dossier_show_photo', $this->dossier_show_photo, 'organization', 'boolean');
        SystemSetting::set('dossier_show_draft_watermark', $this->dossier_show_draft_watermark, 'organization', 'boolean');

        session()->flash('message', 'Official printable letterhead header & styling successfully saved.');
    }

    public function resetLetterheadToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->letterhead_heading = $this->org_name ?: 'Hirely';
        $this->letterhead_subheading = $this->org_location ?: '';
        $this->letterhead_office = $this->org_office_title ?: 'Office of the Registrar';
        $this->letterhead_office_font_size = 'standard';
        $this->letterhead_logo = SystemSetting::getAppLogo() ?: '/images/logo-icon.svg';
        $this->letterhead_custom_details = "Central Secretariat & Statutory Directorate\nWebsite: https://hirely.app • Email: support@hirely.app • Verification Portal: https://hirely.app/verify";
        $this->letterhead_layout = 'classic_left';
        $this->letterhead_dossier_title_admin = 'Official Master Application Dossier & Scrutiny Record';
        $this->letterhead_dossier_title_candidate = 'Official Application Form for Employment';
        $this->letterhead_show_document_title = true;
        $this->letterhead_show_statutory_note = true;
        $this->dossier_show_photo = true;
        $this->dossier_show_draft_watermark = true;
        $this->letterheadLogoUpload = null;
        $this->saveLetterheadSettings();
        session()->flash('message', 'Letterhead header configuration restored to official default standards.');
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

    public function updatedGlobalDisallowThirdDivision($value): void
    {
        if ($value) {
            $this->global_disallow_third_in_terminal = true;
            $this->global_max_allowed_third_divisions = '0';
        }
    }

    public function saveAcademicCriteriaSettings()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);

        if ($this->global_disallow_third_division) {
            $this->global_disallow_third_in_terminal = true;
            $this->global_max_allowed_third_divisions = '0';
        }

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
        SystemSetting::set('enable_hjrs_journal_lookup', (bool) $this->enable_hjrs_journal_lookup, 'academic_criteria', 'boolean');

        SystemSetting::set('asst_prof_required_peer_reviewers', (int)$this->asst_prof_required_peer_reviewers, 'academic_criteria', 'integer');
        SystemSetting::set('prof_required_peer_reviewers', (int)$this->prof_required_peer_reviewers, 'academic_criteria', 'integer');
        SystemSetting::set('lecturer_required_peer_reviewers', (int)$this->lecturer_required_peer_reviewers, 'academic_criteria', 'integer');

        // Statutory Selection Board Merit Formula Weightages
        SystemSetting::set('merit_academic_weight', (int)$this->merit_academic_weight, 'academic_criteria', 'integer');
        SystemSetting::set('merit_higher_qualification_weight', (int)$this->merit_higher_qualification_weight, 'academic_criteria', 'integer');
        SystemSetting::set('merit_research_weight', (int)$this->merit_research_weight, 'academic_criteria', 'integer');
        SystemSetting::set('merit_experience_weight', (int)$this->merit_experience_weight, 'academic_criteria', 'integer');
        SystemSetting::set('merit_interview_weight', (int)$this->merit_interview_weight, 'academic_criteria', 'integer');
        SystemSetting::set('merit_minimum_passing_aggregate', (int)$this->merit_minimum_passing_aggregate, 'academic_criteria', 'integer');

        // Advanced Academic & HEC Standards
        SystemSetting::set('hec_minimum_phd_criteria_enabled', (bool) $this->hec_minimum_phd_criteria_enabled, 'academic_criteria', 'boolean');
        SystemSetting::set('foreign_degree_equivalence_mandatory', (bool) $this->foreign_degree_equivalence_mandatory, 'academic_criteria', 'boolean');
        SystemSetting::set('publication_category_w_weight', (float) $this->publication_category_w_weight, 'academic_criteria', 'float');
        SystemSetting::set('publication_category_x_weight', (float) $this->publication_category_x_weight, 'academic_criteria', 'float');
        SystemSetting::set('publication_category_y_weight', (float) $this->publication_category_y_weight, 'academic_criteria', 'float');
        SystemSetting::set('minimum_publications_asst_prof', (int) $this->minimum_publications_asst_prof, 'academic_criteria', 'integer');
        SystemSetting::set('minimum_publications_assoc_prof', (int) $this->minimum_publications_assoc_prof, 'academic_criteria', 'integer');
        SystemSetting::set('minimum_publications_prof', (int) $this->minimum_publications_prof, 'academic_criteria', 'integer');

        session()->flash('message', 'Global statutory academic division benchmarks, HEC faculty criteria, and selection merit weights successfully updated.');
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
            'theme_secondary_color' => 'nullable|string|max:20',
            'theme_radius_style' => 'nullable|string|in:modern,compact,pill,sharp',
            'theme_font_family' => 'nullable|string|in:jakarta,inter,outfit,system,academic',
            'theme_card_elevation' => 'nullable|string|in:bordered,floating,minimal',
            'theme_sidebar_style' => 'nullable|string|in:glass-dark,light-solid,accent-tint',
            'theme_login_layout' => 'nullable|string|in:split-modern,center-glass,minimalist',
            'ui_table_density' => 'nullable|string|in:compact,comfortable,spacious',
            'logoUpload' => 'nullable|image|max:2048',
            'logoDarkUpload' => 'nullable|image|max:2048',
            'faviconUpload' => 'nullable|mimes:ico,png,svg,webp|max:1024',
            'footer_copyright_text' => 'nullable|string|max:250',
            'footer_candidate_text' => 'nullable|string|max:500',
            'footer_admin_text' => 'nullable|string|max:500',
            'footer_board_text' => 'nullable|string|max:500',
            'footer_reviewer_text' => 'nullable|string|max:500',
            'letter_footer_text' => 'nullable|string|max:1000',
            'letter_footer_contact' => 'nullable|string|max:500',
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
        SystemSetting::set('theme_secondary_color', $this->theme_secondary_color, 'branding', 'string');
        SystemSetting::set('theme_radius_style', $this->theme_radius_style, 'branding', 'string');
        SystemSetting::set('theme_font_family', $this->theme_font_family, 'branding', 'string');
        SystemSetting::set('theme_card_elevation', $this->theme_card_elevation, 'branding', 'string');
        SystemSetting::set('theme_sidebar_style', $this->theme_sidebar_style, 'branding', 'string');
        SystemSetting::set('theme_login_layout', $this->theme_login_layout, 'branding', 'string');
        SystemSetting::set('ui_glassmorphism_enabled', (bool) $this->ui_glassmorphism_enabled, 'branding', 'boolean');
        SystemSetting::set('ui_animations_enabled', (bool) $this->ui_animations_enabled, 'branding', 'boolean');
        SystemSetting::set('ui_table_density', $this->ui_table_density, 'branding', 'string');
        SystemSetting::set('portal_announcement_enabled', (bool) $this->portal_announcement_enabled, 'branding', 'boolean');
        SystemSetting::set('portal_announcement_type', $this->portal_announcement_type, 'branding', 'string');
        SystemSetting::set('portal_announcement_message', $this->portal_announcement_message, 'branding', 'string');
        SystemSetting::set('footer_copyright_text', $this->footer_copyright_text, 'branding', 'string');
        SystemSetting::set('footer_candidate_text', $this->footer_candidate_text, 'branding', 'string');
        SystemSetting::set('footer_admin_text', $this->footer_admin_text, 'branding', 'string');
        SystemSetting::set('footer_board_text', $this->footer_board_text, 'branding', 'string');
        SystemSetting::set('footer_reviewer_text', $this->footer_reviewer_text, 'branding', 'string');
        SystemSetting::set('letter_footer_text', $this->letter_footer_text, 'branding', 'string');
        SystemSetting::set('letter_footer_contact', $this->letter_footer_contact, 'branding', 'string');
        SystemSetting::set('letter_show_audit_trail', (bool) $this->letter_show_audit_trail, 'branding', 'boolean');
        SystemSetting::set('letter_show_verify_ref', (bool) $this->letter_show_verify_ref, 'branding', 'boolean');
        SystemSetting::set('app_date_format', $this->app_date_format, 'branding', 'string');
        SystemSetting::set('app_time_format', $this->app_time_format, 'branding', 'string');
        SystemSetting::set('app_timezone_display', $this->app_timezone_display, 'branding', 'string');
        SystemSetting::set('app_tracking_prefix', $this->app_tracking_prefix, 'branding', 'string');
        SystemSetting::set('public_jobs_enabled', (bool) $this->public_jobs_enabled, 'branding', 'boolean');

        SystemSetting::clearCache();

        session()->flash('message', 'Platform theme, brand identity, layout aesthetics, and portal footers saved.');
    }

    public function applyThemePreset(string $preset)
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        match ($preset) {
            'oxford_indigo' => [
                $this->theme_accent_color = '#4f46e5',
                $this->theme_secondary_color = '#06b6d4',
                $this->theme_radius_style = 'modern',
                $this->theme_font_family = 'jakarta',
                $this->theme_card_elevation = 'bordered',
            ],
            'cambridge_emerald' => [
                $this->theme_accent_color = '#059669',
                $this->theme_secondary_color = '#0d9488',
                $this->theme_radius_style = 'modern',
                $this->theme_font_family = 'jakarta',
                $this->theme_card_elevation = 'floating',
            ],
            'harvard_crimson' => [
                $this->theme_accent_color = '#be123c',
                $this->theme_secondary_color = '#f59e0b',
                $this->theme_radius_style = 'sharp',
                $this->theme_font_family = 'academic',
                $this->theme_card_elevation = 'bordered',
            ],
            'mit_sapphire' => [
                $this->theme_accent_color = '#0284c7',
                $this->theme_secondary_color = '#6366f1',
                $this->theme_radius_style = 'modern',
                $this->theme_font_family = 'outfit',
                $this->theme_card_elevation = 'floating',
            ],
            'imperial_purple' => [
                $this->theme_accent_color = '#7c3aed',
                $this->theme_secondary_color = '#ec4899',
                $this->theme_radius_style = 'pill',
                $this->theme_font_family = 'jakarta',
                $this->theme_card_elevation = 'floating',
            ],
            'sunset_amber' => [
                $this->theme_accent_color = '#d97706',
                $this->theme_secondary_color = '#e11d48',
                $this->theme_radius_style = 'modern',
                $this->theme_font_family = 'outfit',
                $this->theme_card_elevation = 'bordered',
            ],
            'slate_minimalist' => [
                $this->theme_accent_color = '#1e293b',
                $this->theme_secondary_color = '#475569',
                $this->theme_radius_style = 'compact',
                $this->theme_font_family = 'inter',
                $this->theme_card_elevation = 'minimal',
            ],
            default => null,
        };
        $this->saveBrandingSettings();
        session()->flash('message', "Theme preset '" . ucwords(str_replace('_', ' ', $preset)) . "' applied across all portals.");
    }

    public function resetPipelineToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->enable_peer_review_step = true;
        $this->enable_scrutiny_step = true;
        $this->enable_appellate_step = true;
        $this->enable_board_interview_step = true;
        $this->enforce_strict_committee_quorum = true;
        $this->enforce_strict_peer_review_quorum = false;
        $this->enable_interview_call_letters = true;
        $this->enable_referee_magic_links = true;
        $this->enable_hjrs_journal_lookup = true;
        $this->enable_appellate_workbench = true;
        $this->enable_projector_merit_board = true;
        $this->enable_executive_analytics = true;
        $this->savePipelineSettings();
        session()->flash('message', 'Workflow pipeline settings restored to institutional statutory default.');
    }

    public function resetDocumentsToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->require_transcript_proof = true;
        $this->require_experience_proof = true;
        $this->require_cnic_upload = true;
        $this->require_noc_employed = false;
        $this->require_publications_academic = true;
        $this->require_challan_receipt_upload = false;
        $this->saveDocumentSettings();
        session()->flash('message', 'Candidate document requirements restored to standard verification policy.');
    }

    public function resetCandidateRulesToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->allow_multiple_applications = true;
        $this->prevent_duplicate_campaign_applications = true;
        $this->challan_grace_days = 14;
        $this->auto_lock_profile_on_voucher = true;
        $this->candidate_allow_application_withdrawal = false;
        $this->max_active_applications_per_candidate = 5;
        $this->max_upload_file_size_mb = 5;
        $this->enable_online_challan_verification = true;
        $this->saveCandidateRules();
        session()->flash('message', 'Candidate application & challan rules restored to default recommendations.');
    }

    public function resetOrganizationToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->org_name = 'Hirely';
        $this->org_short_name = 'Hirely';
        $this->org_location = 'National Directorate';
        $this->org_office_title = 'Recruitment & Statutory Selection Directorate';
        $this->org_type = 'HIGHER_EDUCATION';
        $this->sector_label = 'Department / Division';
        $this->currency_symbol = 'PKR';
        $this->default_application_fee = 3000;
        $this->saveOrganizationProfile();
        $this->resetLetterheadToDefault();
        session()->flash('message', 'Organization profile, legal name, short name, and letterhead reset to default.');
    }

    public function resetAcademicCriteriaToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->global_disallow_third_division = true;
        $this->global_max_allowed_third_divisions = '';
        $this->global_disallow_third_in_terminal = true;
        $this->global_disallow_two_second_divisions = true;
        $this->global_max_allowed_second_divisions = '';
        $this->global_disallow_second_in_terminal = false;
        $this->global_min_marks_percentage = '';
        $this->global_min_terminal_percentage = '';
        $this->global_min_terminal_cgpa = '';
        $this->asst_prof_required_peer_reviewers = 3;
        $this->prof_required_peer_reviewers = 3;
        $this->lecturer_required_peer_reviewers = 0;
        $this->merit_academic_weight = 50;
        $this->merit_higher_qualification_weight = 5;
        $this->merit_research_weight = 15;
        $this->merit_experience_weight = 10;
        $this->merit_interview_weight = 20;
        $this->merit_minimum_passing_aggregate = 50;
        $this->saveAcademicCriteriaSettings();
        session()->flash('message', 'Statutory academic benchmarks reset to HEC standard baseline.');
    }

    public function resetThemeToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->theme_accent_color = '#4f46e5';
        $this->theme_secondary_color = '#06b6d4';
        $this->theme_radius_style = 'modern';
        $this->theme_font_family = 'jakarta';
        $this->theme_card_elevation = 'bordered';
        $this->theme_sidebar_style = 'glass-dark';
        $this->theme_login_layout = 'split-modern';
        $this->ui_glassmorphism_enabled = true;
        $this->ui_animations_enabled = true;
        $this->ui_table_density = 'comfortable';
        $this->saveBrandingSettings();
        session()->flash('message', 'Theme aesthetics, colors, typography, and layout restored to default.');
    }

    public function resetBrandingToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->app_name = 'Hirely';
        $this->app_tagline = 'Statutory Recruitment & Selection Operating System';
        $this->app_description = 'Enterprise recruitment, statutory scrutiny, and merit selection operating system for higher education and statutory institutions.';
        $this->app_version = 'v2.4 Enterprise OS';
        $this->app_logo = '/images/logo-icon.svg';
        $this->app_logo_dark = '/images/logo-icon.svg';
        $this->app_favicon = '/favicon.ico';
        $this->logoUpload = null;
        $this->logoDarkUpload = null;
        $this->faviconUpload = null;
        $this->portal_announcement_enabled = false;
        $this->portal_announcement_message = '';
        $this->footer_copyright_text = '© ' . date('Y') . ' Hirely. All rights reserved.';
        $this->footer_candidate_text = 'Candidate Facilitation Desk: For application guidance or voucher verification, contact support@hirely.app or submit an inquiry through the helpdesk.';
        $this->footer_admin_text = 'Official Administrative Desk • Hirely Enterprise OS • System Access Audited & Recorded.';
        $this->footer_board_text = 'Statutory Selection Board Secretariat • Strictly Confidential & Privileged Record.';
        $this->footer_reviewer_text = 'Academic Peer Evaluation Desk • External Referee Integrity • Blind Review Protocols Active.';
        $this->letter_footer_text = 'This is an official computer-generated document issued under statutory recruitment authority. Any alteration, erasure, or unauthorized tampering renders this document null and void.';
        $this->letter_footer_contact = 'Recruitment Directorate • Candidate Records & Statutory Scrutiny Archive • Verification Portal: https://hirely.app';
        $this->letter_show_audit_trail = true;
        $this->letter_show_verify_ref = true;
        $this->saveBrandingSettings();
        session()->flash('message', 'Brand identity, titles, description, logos, and portal footers reset to default.');
    }

    public function resetContactToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->support_email = 'support@hirely.app';
        $this->support_phone = '+1 (800) 555-0199';
        $this->support_whatsapp = '';
        $this->registrar_hotline = '';
        $this->support_hours = 'Monday to Friday: 09:00 AM – 05:00 PM';
        $this->helpdesk_location = 'Central Recruitment & Candidate Facilitation Center';
        $this->org_website = 'https://hirely.app';
        $this->saveContactSettings();
        session()->flash('message', 'Helpdesk contact channels restored to standard presets.');
    }

    public function resetEmailToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings'), 403);
        $this->mail_from_name = 'Hirely Recruitment Desk';
        $this->mail_from_address = 'noreply@hirely.app';
        $this->mail_reply_to = 'support@hirely.app';
        $this->mail_mailer = 'smtp';
        $this->mail_host = 'smtp.mailgun.org';
        $this->mail_port = 587;
        $this->mail_encryption = 'tls';
        $this->mail_username = '';
        $this->mail_password = '';
        $this->saveEmailSettings();
        session()->flash('message', 'Mail transport and SMTP gateway configuration reset to default.');
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

    // 🔒 Security, Access Governance & Session Policies
    public function saveSecuritySettings()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-security-settings'), 403);
        $this->validate([
            'auth_two_factor_policy' => 'required|string|in:disabled,optional,enforced_staff,enforced_all',
            'auth_session_timeout_minutes' => 'required|integer|min:5|max:1440',
            'auth_max_login_attempts' => 'required|integer|min:1|max:20',
            'auth_lockout_duration_minutes' => 'required|integer|min:1|max:1440',
            'candidate_registration_status' => 'required|string|in:open,closed',
            'candidate_registration_closed_message' => 'nullable|string|max:500',
            'job_board_visibility' => 'required|string|in:public,members_only',
            'password_min_length' => 'required|integer|min:6|max:64',
            'max_upload_size_mb' => 'required|integer|min:1|max:100',
            'allowed_file_types' => 'required|string|max:250',
        ]);

        SystemSetting::set('auth_two_factor_policy', $this->auth_two_factor_policy, 'security', 'string');
        SystemSetting::set('auth_session_timeout_minutes', (int) $this->auth_session_timeout_minutes, 'security', 'integer');
        SystemSetting::set('auth_max_login_attempts', (int) $this->auth_max_login_attempts, 'security', 'integer');
        SystemSetting::set('auth_lockout_duration_minutes', (int) $this->auth_lockout_duration_minutes, 'security', 'integer');
        SystemSetting::set('candidate_registration_status', $this->candidate_registration_status, 'security', 'string');
        SystemSetting::set('candidate_registration_closed_message', $this->candidate_registration_closed_message, 'security', 'string');
        SystemSetting::set('job_board_visibility', $this->job_board_visibility, 'security', 'string');
        SystemSetting::set('password_min_length', (int) $this->password_min_length, 'security', 'integer');
        SystemSetting::set('password_require_uppercase', (bool) $this->password_require_uppercase, 'security', 'boolean');
        SystemSetting::set('password_require_numeric', (bool) $this->password_require_numeric, 'security', 'boolean');
        SystemSetting::set('password_require_special', (bool) $this->password_require_special, 'security', 'boolean');
        SystemSetting::set('force_first_login_password_change', (bool) $this->force_first_login_password_change, 'security', 'boolean');
        SystemSetting::set('enforce_strict_cnic_format', (bool) $this->enforce_strict_cnic_format, 'security', 'boolean');
        SystemSetting::set('max_upload_size_mb', (int) $this->max_upload_size_mb, 'security', 'integer');
        SystemSetting::set('allowed_file_types', $this->allowed_file_types, 'security', 'string');
        SystemSetting::set('session_ip_lock_enabled', (bool) $this->session_ip_lock_enabled, 'security', 'boolean');
        SystemSetting::set('force_logout_on_password_change', (bool) $this->force_logout_on_password_change, 'security', 'boolean');
        SystemSetting::set('tamper_evident_dossier_hash', (bool) $this->tamper_evident_dossier_hash, 'security', 'boolean');
        SystemSetting::set('watermark_text_custom', (string) $this->watermark_text_custom, 'security', 'string');

        session()->flash('message', 'Security governance, access policies, and authentication thresholds successfully saved.');
    }

    public function resetSecurityToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-security-settings'), 403);
        $this->auth_two_factor_policy = 'optional';
        $this->auth_session_timeout_minutes = 60;
        $this->auth_max_login_attempts = 5;
        $this->auth_lockout_duration_minutes = 15;
        $this->candidate_registration_status = 'open';
        $this->candidate_registration_closed_message = 'Online recruitment intake is currently closed. Please review active advertisements for upcoming announcement schedules.';
        $this->job_board_visibility = 'public';
        $this->password_min_length = 8;
        $this->password_require_uppercase = true;
        $this->password_require_numeric = true;
        $this->password_require_special = false;
        $this->force_first_login_password_change = false;
        $this->enforce_strict_cnic_format = true;
        $this->max_upload_size_mb = 5;
        $this->allowed_file_types = 'pdf, jpg, jpeg, png, docx';

        $this->saveSecuritySettings();
        session()->flash('message', 'Security policies restored to standard institutional defaults.');
    }

    // ⚙️ System Health, Diagnostics & Maintenance
    public function saveMaintenanceSettings()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'), 403);
        $this->validate([
            'maintenance_notice_heading' => 'required|string|max:150',
            'maintenance_notice_message' => 'required|string|max:500',
        ]);

        SystemSetting::set('system_maintenance_mode', (bool) $this->system_maintenance_mode, 'system', 'boolean');
        SystemSetting::set('maintenance_notice_heading', $this->maintenance_notice_heading, 'system', 'string');
        SystemSetting::set('maintenance_notice_message', $this->maintenance_notice_message, 'system', 'string');

        session()->flash('message', 'System maintenance mode and service notification status updated.');
    }

    public function resetMaintenanceToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'), 403);
        $this->system_maintenance_mode = false;
        $this->maintenance_notice_heading = 'Scheduled Statutory System Maintenance';
        $this->maintenance_notice_message = 'The Hirely recruitment portal is undergoing scheduled maintenance and database optimization. Access will be restored shortly. Thank you for your patience.';

        $this->saveMaintenanceSettings();
        session()->flash('message', 'System maintenance configurations restored to standard defaults.');
    }

    // 📱 SMS & Multi-Channel Alert Gateway
    public function saveSmsSettings()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-sms-settings'), 403);
        $this->validate([
            'sms_provider' => 'required|string|in:twilio,zong,telenor,jazz,generic_rest,generic_webhook,log',
            'sms_sender_id' => 'required|string|max:20',
            'sms_api_key' => 'nullable|string|max:250',
            'sms_api_url' => 'nullable|string|max:250',
            'sms_api_secret' => 'nullable|string|max:250',
            'sms_username' => 'nullable|string|max:100',
        ]);

        SystemSetting::set('sms_enabled', (bool) $this->sms_enabled, 'email', 'boolean');
        SystemSetting::set('sms_provider', $this->sms_provider, 'email', 'string');
        SystemSetting::set('sms_sender_id', $this->sms_sender_id, 'email', 'string');
        SystemSetting::set('sms_api_key', $this->sms_api_key, 'email', 'string');
        SystemSetting::set('sms_api_url', $this->sms_api_url, 'email', 'string');
        SystemSetting::set('sms_api_secret', $this->sms_api_secret, 'email', 'string');
        SystemSetting::set('sms_username', $this->sms_username, 'email', 'string');
        SystemSetting::set('sms_trigger_app_submitted', (bool) $this->sms_trigger_app_submitted, 'email', 'boolean');
        SystemSetting::set('sms_trigger_payment_verified', (bool) $this->sms_trigger_payment_verified, 'email', 'boolean');
        SystemSetting::set('sms_trigger_scrutiny_deficiency', (bool) $this->sms_trigger_scrutiny_deficiency, 'email', 'boolean');
        SystemSetting::set('sms_trigger_interview_call', (bool) $this->sms_trigger_interview_call, 'email', 'boolean');
        SystemSetting::set('whatsapp_business_enabled', (bool) $this->whatsapp_business_enabled, 'email', 'boolean');
        SystemSetting::set('whatsapp_business_phone', (string) $this->whatsapp_business_phone, 'email', 'string');

        session()->flash('message', 'SMS & WhatsApp multi-channel notification gateways saved.');
    }

    public function resetSmsToDefault()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-sms-settings'), 403);
        $this->sms_enabled = false;
        $this->sms_provider = 'twilio';
        $this->sms_sender_id = 'HIRELY';
        $this->sms_api_key = '';
        $this->sms_api_url = '';
        $this->sms_api_secret = '';
        $this->sms_username = '';
        $this->sms_trigger_app_submitted = true;
        $this->sms_trigger_payment_verified = true;
        $this->sms_trigger_scrutiny_deficiency = true;
        $this->sms_trigger_interview_call = true;

        $this->saveSmsSettings();
        session()->flash('message', 'SMS gateway settings restored to defaults.');
    }

    public function sendTestSms()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-sms-settings'), 403);
        $this->validate([
            'test_sms_recipient' => 'required|string|min:10|max:20',
        ]);

        $result = \App\Services\Sms\SmsGatewayService::send(
            $this->test_sms_recipient,
            "Hirely Recruitment: Test notification dispatched successfully via {$this->sms_provider} gateway (Sender: {$this->sms_sender_id}).",
            $this->sms_sender_id
        );

        if ($result['success']) {
            session()->flash('test_sms_status', [
                'type' => 'success',
                'message' => $result['detail'],
            ]);
        } else {
            session()->flash('test_sms_status', [
                'type' => 'error',
                'message' => $result['detail'],
            ]);
        }
    }

    // 🖥️ Diagnostics & Cache Clearing Actions
    public function getServerStatsProperty(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'db_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_connection' => config('queue.default'),
            'timezone' => config('app.timezone'),
            'memory_limit' => ini_get('memory_limit') ?: 'N/A',
            'max_execution_time' => (ini_get('max_execution_time') ?: '0') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize') ?: 'N/A',
            'post_max_size' => ini_get('post_max_size') ?: 'N/A',
        ];
    }

    public function getTableCountsProperty(): array
    {
        return [
            'users' => \App\Models\User::count(),
            'applications' => \App\Models\Application::count(),
            'campaigns' => \App\Models\Campaign::count(),
            'advertisements' => \App\Models\Advertisement::count(),
            'payments' => class_exists(\App\Models\BillingLedger::class) ? \App\Models\BillingLedger::count() : 0,
            'audit_logs' => class_exists(\App\Models\SecurityAuditLog::class) ? \App\Models\SecurityAuditLog::count() : 0,
        ];
    }

    public function clearApplicationCache()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'), 403);
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        session()->flash('maintenance_action_status', [
            'type' => 'success',
            'message' => 'Application data cache successfully flushed.',
        ]);
    }

    public function clearViewCache()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'), 403);
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        session()->flash('maintenance_action_status', [
            'type' => 'success',
            'message' => 'Compiled Blade template views successfully cleared.',
        ]);
    }

    public function clearRouteCache()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'), 403);
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        session()->flash('maintenance_action_status', [
            'type' => 'success',
            'message' => 'Route registration cache successfully reset.',
        ]);
    }

    public function clearConfigCache()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'), 403);
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        session()->flash('maintenance_action_status', [
            'type' => 'success',
            'message' => 'Configuration environment cache cleared.',
        ]);
    }

    public function optimizeSystem()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'), 403);
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        session()->flash('maintenance_action_status', [
            'type' => 'success',
            'message' => 'Full system optimization executed (all framework caches reset).',
        ]);
    }

    // =========================================================================
    // ✉️ EMAIL TEMPLATES & COMMUNICATIONS ACTIONS
    // =========================================================================

    public function toggleTemplateTrigger($id)
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('toggle-email-triggers') || Auth::user()->can('manage-email-templates'), 403);
        $template = \App\Models\EmailTemplate::findOrFail($id);
        $newState = $template->toggleTrigger();

        session()->flash('message', "Trigger for [{$template->name}] is now " . ($newState ? 'ENABLED (Automated emails active).' : 'DISABLED (Automated emails paused).'));
    }

    public function openEditTemplateModal($id)
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-email-templates'), 403);
        $template = \App\Models\EmailTemplate::findOrFail($id);
        $this->selectedTemplateId = $template->id;
        $this->template_name = $template->name;
        $this->template_subject = $template->subject;
        $this->template_preheader = $template->preheader ?? '';
        $this->template_body = $template->body_html;
        $this->template_is_trigger_enabled = $template->is_trigger_enabled;
        $this->template_placeholders = $template->placeholders ?? [];
        $this->isTemplateModalOpen = true;
    }

    public function closeTemplateModal()
    {
        $this->isTemplateModalOpen = false;
        $this->selectedTemplateId = null;
    }

    public function saveTemplate()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-email-templates'), 403);
        $this->validate([
            'template_subject' => 'required|string|max:255',
            'template_body' => 'required|string',
        ]);

        $template = \App\Models\EmailTemplate::findOrFail($this->selectedTemplateId);
        $template->update([
            'subject' => $this->template_subject,
            'preheader' => $this->template_preheader,
            'body_html' => $this->template_body,
            'is_trigger_enabled' => (bool)$this->template_is_trigger_enabled,
            'last_updated_by' => Auth::id(),
        ]);

        $this->isTemplateModalOpen = false;
        session()->flash('message', "Email Template [{$template->name}] successfully updated.");
    }

    public function resetTemplateToDefault($id)
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-email-templates'), 403);
        $template = \App\Models\EmailTemplate::findOrFail($id);
        $allDefaults = \App\Models\EmailTemplate::getDefaultTemplateDefinitions();
        $def = collect($allDefaults)->firstWhere('slug', $template->slug);

        if ($def) {
            $template->update([
                'subject' => $def['subject'],
                'preheader' => $def['preheader'] ?? null,
                'body_html' => $def['body_html'],
                'is_trigger_enabled' => true,
                'last_updated_by' => Auth::id(),
            ]);

            $this->template_subject = $def['subject'];
            $this->template_preheader = $def['preheader'] ?? '';
            $this->template_body = $def['body_html'];
            $this->template_is_trigger_enabled = true;

            session()->flash('message', "Template [{$template->name}] restored to statutory default layout.");
        }
    }

    public function openPreviewModal($id)
    {
        $template = \App\Models\EmailTemplate::findOrFail($id);
        $this->selectedTemplateId = $template->id;
        $this->previewTemplateName = $template->name;
        $this->previewMode = 'sample';
        $dummyData = \App\Models\EmailTemplate::getDummySampleData();
        $this->previewSubject = $template->renderSubject($dummyData);
        $this->previewHtml = $template->renderHtml($dummyData);
        $this->isPreviewModalOpen = true;
    }

    public function setPreviewMode($mode)
    {
        $this->previewMode = in_array($mode, ['sample', 'raw']) ? $mode : 'sample';
        if ($this->selectedTemplateId) {
            $template = \App\Models\EmailTemplate::find($this->selectedTemplateId);
            if ($template) {
                if ($this->previewMode === 'sample') {
                    $dummyData = \App\Models\EmailTemplate::getDummySampleData();
                    $this->previewSubject = $template->renderSubject($dummyData);
                    $this->previewHtml = $template->renderHtml($dummyData);
                } else {
                    $this->previewSubject = $template->subject;
                    $this->previewHtml = $template->renderHtml([]);
                }
            }
        }
    }

    public function closePreviewModal()
    {
        $this->isPreviewModalOpen = false;
        $this->previewHtml = '';
    }

    public function openCustomEmailModal(?string $templateSlug = null)
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('send-custom-email'), 403);
        $this->custom_recipient_type = 'custom';
        $this->custom_recipient_user_id = null;
        $this->custom_recipient_email = '';
        $this->custom_recipient_name = '';
        $this->custom_subject = '';
        $this->custom_body = '';
        $this->custom_selected_template_slug = $templateSlug ?? '';

        if (!empty($templateSlug)) {
            $this->loadTemplateForCustomEmail();
        }

        $this->isCustomEmailModalOpen = true;
    }

    public function closeCustomEmailModal()
    {
        $this->isCustomEmailModalOpen = false;
    }

    public function loadTemplateForCustomEmail()
    {
        if (empty($this->custom_selected_template_slug)) {
            return;
        }

        $tmpl = \App\Models\EmailTemplate::getBySlug($this->custom_selected_template_slug);
        if ($tmpl) {
            $this->custom_subject = $tmpl->subject;
            $this->custom_body = $tmpl->body_html;
        }
    }

    public function updatedCustomRecipientUserId($userId)
    {
        if (empty($userId)) return;

        $user = \App\Models\User::find($userId);
        if ($user) {
            $this->custom_recipient_email = $user->email;
            $this->custom_recipient_name = $user->reviewerProfile?->name 
                ?? ($user->basicInfo ? ($user->basicInfo->first_name . ' ' . $user->basicInfo->last_name) : $user->email);
        }
    }

    public function sendCustomEmailAction()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('send-custom-email'), 403);

        $this->validate([
            'custom_recipient_email' => 'required|email|max:255',
            'custom_subject' => 'required|string|max:255',
            'custom_body' => 'required|string|min:5',
        ]);

        $recipientUser = !empty($this->custom_recipient_user_id) 
            ? \App\Models\User::find($this->custom_recipient_user_id) 
            : \App\Models\User::where('email', $this->custom_recipient_email)->first();

        $result = \App\Services\MailNotificationService::sendCustomEmail(
            recipientEmail: trim($this->custom_recipient_email),
            subject: $this->custom_subject,
            bodyHtml: $this->custom_body,
            recipientName: $this->custom_recipient_name ?: null,
            recipientUser: $recipientUser,
            senderUser: Auth::user()
        );

        if ($result['status'] === 'sent') {
            $this->isCustomEmailModalOpen = false;
            session()->flash('message', "✓ Custom email dispatched successfully to {$this->custom_recipient_email}.");
        } else {
            session()->flash('error', "Failed to dispatch email: " . ($result['message'] ?? 'Unknown mail server error.'));
        }
    }

    public function sendTestTemplateEmail($templateId)
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->can('manage-email-templates'), 403);

        $template = \App\Models\EmailTemplate::findOrFail($templateId);
        $testEmail = Auth::user()->email;
        $dummyData = \App\Models\EmailTemplate::getDummySampleData();
        $dummyData['{recipient_name}'] = Auth::user()->name ?? 'Administrator';
        $dummyData['{recipient_email}'] = $testEmail;
        $dummyData['{candidate_name}'] = Auth::user()->name ?? 'Administrator';
        $dummyData['{candidate_email}'] = $testEmail;

        $result = \App\Services\MailNotificationService::sendTemplate(
            slug: $template->slug,
            recipientEmail: $testEmail,
            variables: $dummyData,
            recipientName: Auth::user()->name ?? 'Administrator',
            recipientUser: Auth::user(),
            senderUser: Auth::user(),
            dispatchType: 'SYSTEM_AUTOMATED'
        );

        if ($result['status'] === 'sent') {
            session()->flash('message', "✓ Test email for [{$template->name}] dispatched to your inbox ({$testEmail}) with realistic sample data.");
        } elseif ($result['status'] === 'skipped') {
            session()->flash('warning', "Notice: Trigger is currently disabled for this template. (Logged in email logs as skipped).");
        } else {
            session()->flash('error', "Could not send test email: " . ($result['message'] ?? 'Unknown error.'));
        }
    }

    public function render()
    {
        $emailTemplates = null;
        $emailLogs = null;
        $candidatesList = collect();
        $reviewersList = collect();
        $boardMembersList = collect();

        if ($this->activeTab === 'email_templates') {
            if (\Illuminate\Support\Facades\Schema::hasTable('email_templates')) {
                $query = \App\Models\EmailTemplate::query();
                if ($this->templateRecipientFilter !== 'ALL') {
                    $query->where('recipient_group', $this->templateRecipientFilter);
                }
                if (!empty($this->templateSearch)) {
                    $s = '%' . trim($this->templateSearch) . '%';
                    $query->where(function($q) use ($s) {
                        $q->where('name', 'like', $s)
                          ->orWhere('slug', 'like', $s)
                          ->orWhere('subject', 'like', $s);
                    });
                }
                $emailTemplates = $query->orderBy('recipient_group')->orderBy('name')->get();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('email_logs')) {
                $emailLogs = \App\Models\EmailLog::latest('id')->take(8)->get();
            }

            $candidatesList = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'applicant'))
                ->with('basicInfo')
                ->take(40)
                ->get();

            $reviewersList = \App\Models\ReviewerProfile::with('user')->take(40)->get();

            $boardMembersList = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['selection-board-member', 'board-member', 'scrutiny-committee']))
                ->take(40)
                ->get();
        }

        return view('livewire.admin.system-settings', [
            'emailTemplates' => $emailTemplates,
            'emailLogs' => $emailLogs,
            'candidatesList' => $candidatesList,
            'reviewersList' => $reviewersList,
            'boardMembersList' => $boardMembersList,
        ])->layout('layouts.app', ['title' => 'System & Workflow Settings - Hirely']);
    }
}

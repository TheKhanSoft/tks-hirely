<?php

namespace App\Models;

use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';

    protected $fillable = [
        'slug',
        'name',
        'recipient_group',
        'trigger_event',
        'subject',
        'preheader',
        'body_html',
        'body_plain',
        'placeholders',
        'is_trigger_enabled',
        'is_active',
        'description',
        'last_updated_by',
        'last_sent_at',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_trigger_enabled' => 'boolean',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Fetch template by slug.
     */
    public static function getBySlug(string $slug): ?self
    {
        if (!Schema::hasTable('email_templates')) {
            return null;
        }

        return static::where('slug', $slug)->first();
    }

    /**
     * Check if the automated system trigger for this email template is enabled.
     */
    public static function isTriggerEnabled(string $slug): bool
    {
        $template = static::getBySlug($slug);
        if (!$template) {
            return true; // Default to true if not yet seeded
        }

        return $template->is_active && $template->is_trigger_enabled;
    }

    /**
     * Toggle the automated trigger state.
     */
    public function toggleTrigger(): bool
    {
        $this->is_trigger_enabled = !$this->is_trigger_enabled;
        $this->save();

        return $this->is_trigger_enabled;
    }

    /**
     * Interpolate variables into subject line.
     */
    public function renderSubject(array $variables = []): string
    {
        return $this->interpolateString($this->subject, $variables);
    }

    /**
     * Interpolate variables and wrap body inside the master modern institutional layout.
     */
    public function renderHtml(array $variables = []): string
    {
        $interpolatedBody = $this->interpolateString($this->body_html, $variables);
        return static::wrapInMasterLayout($interpolatedBody, $this->renderSubject($variables), $this->recipient_group, $variables);
    }

    /**
     * Render subject with rich realistic dummy sample data.
     */
    public function renderSampleSubject(array $overrides = []): string
    {
        $sample = array_merge(static::getDummySampleData(), $overrides);
        return $this->renderSubject($sample);
    }

    /**
     * Render HTML body inside master layout with rich realistic dummy sample data.
     */
    public function renderSampleHtml(array $overrides = []): string
    {
        $sample = array_merge(static::getDummySampleData(), $overrides);
        return $this->renderHtml($sample);
    }

    /**
     * Helper to replace {variable} with real values.
     */
    public function interpolateString(string $text, array $variables = []): string
    {
        $defaults = [
            '{app_name}' => SystemSetting::getAppName(),
            '{org_name}' => SystemSetting::getOrgName(),
            '{org_short_name}' => SystemSetting::getOrgShortName(),
            '{support_email}' => SystemSetting::get('support_email', 'support@hirely.app'),
            '{support_phone}' => SystemSetting::get('support_phone', '+92-51-9250000'),
            '{login_url}' => url('/login'),
            '{current_year}' => date('Y'),
        ];

        $merged = array_merge($defaults, $variables);

        foreach ($merged as $key => $val) {
            $bracketKey = str_starts_with($key, '{') ? $key : '{' . $key . '}';
            $text = str_replace($bracketKey, (string)$val, $text);
        }

        return $text;
    }

    /**
     * Comprehensive dictionary of realistic dummy / sample data for previews and test sends.
     */
    public static function getDummySampleData(): array
    {
        $orgName = SystemSetting::getOrgName();
        $orgShort = SystemSetting::getOrgShortName();
        $appName = SystemSetting::getAppName();
        $supportEmail = SystemSetting::get('support_email', 'support@hirely.app');
        $supportPhone = SystemSetting::get('support_phone', '+92-51-9250000');

        return [
            // Institutional & Identity
            '{app_name}' => $appName,
            '{org_name}' => $orgName,
            '{org_short_name}' => $orgShort,
            '{support_email}' => $supportEmail,
            '{support_phone}' => $supportPhone,
            '{login_url}' => url('/login'),
            '{portal_url}' => url('/applicant/dashboard'),
            '{dossier_url}' => url('/application/1/print'),
            '{current_year}' => date('Y'),

            // Stakeholders & Security
            '{recipient_name}' => 'Dr. Tariq Mahmood Khan',
            '{recipient_email}' => 'tariq.mahmood@example.edu.pk',
            '{candidate_name}' => 'Dr. Tariq Mahmood Khan',
            '{candidate_email}' => 'tariq.mahmood@example.edu.pk',
            '{candidate_phone}' => '+92-300-1234567',
            '{user_name}' => 'Dr. Tariq Mahmood Khan',
            '{email}' => 'tariq.mahmood@example.edu.pk',
            '{temporary_password}' => 'Hirely#Secure2026!',
            '{reset_url}' => url('/password/reset/sample-token-884920'),
            '{reset_token}' => 'rst_99e821fa04b',
            '{token_expiry}' => '60 minutes',
            '{changed_at}' => '06 November 2026, 04:30 PM PKT',
            '{reset_method}' => 'Self-Service Security Reset Portal',
            '{otp_code}' => '849201',
            '{otp_expiry}' => '10 minutes',
            '{profile_field_changed}' => 'Official Contact Email & Phone Number',
            '{changed_timestamp}' => '06 November 2026, 04:30 PM PKT',
            '{lock_timestamp}' => '07 November 2026, 11:30 AM PKT',
            '{dossier_hash}' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
            '{candidate_count}' => '18',
            '{referee_name}' => 'Prof. Dr. Arshad Ali',
            '{referee_title}' => 'Department Chair / Dean',
            '{referee_institution}' => 'Quaid-i-Azam University, Islamabad',
            '{referee_portal_url}' => url('/referee/recommendation/sample-token-99'),
            '{reissue_url}' => url('/applicant/wizard?step=8'),
            '{ip_address}' => '103.102.158.29',
            '{device_info}' => 'Chrome on Windows 11 (Desktop)',

            // Positions, Vacancies & Applications
            '{job_title}' => 'Associate Professor of Computer Science (BPS-20)',
            '{post_title}' => 'Associate Professor of Computer Science (BPS-20)',
            '{designation}' => 'Associate Professor (BPS-20)',
            '{job_code}' => 'FAC-CS-BPS20-2026',
            '{department}' => 'Department of Computer Science & Software Engineering',
            '{advertisement_no}' => 'Advt. No. ' . $orgShort . '/04-2026',
            '{advertisement_title}' => 'Statutory Faculty & Administrative Intake 2026',
            '{tracking_number}' => 'APP-2026-CS-0842',
            '{application_id}' => 'APP-2026-CS-0842',
            '{submission_date}' => '04 November 2026',
            '{submission_time}' => '02:45 PM PKT',
            '{closing_date}' => '30 November 2026 (05:00 PM PKT)',
            '{dossier_url}' => url('/candidate/dashboard'),
            '{action_url}' => url('/candidate/dashboard'),
            '{action_text}' => 'Access Recruitment Console',

            // Fees, Vouchers & Payments
            '{challan_no}' => 'CHL-2026-98214',
            '{consumer_id}' => '100088921004',
            '{amount}' => 'PKR 4,000',
            '{fee_amount}' => 'PKR 4,000',
            '{currency}' => 'PKR',
            '{due_date}' => '18 November 2026',
            '{fee_due_date}' => '18 November 2026',
            '{download_voucher_url}' => url('/candidate/vouchers/CHL-2026-98214'),
            '{transaction_id}' => '1LINK-8849201-SETTLED',
            '{amount_paid}' => 'PKR 4,000.00',
            '{settled_at}' => '05 November 2026, 11:20 AM PKT',
            '{payment_status}' => 'Verified & Settled via 1Link Auto-Settlement',
            '{receipt_url}' => url('/candidate/receipts/CHL-2026-98214'),

            // Scrutiny, Deficiencies & Grievances
            '{scrutiny_status}' => 'ELIGIBLE (Shortlisted for Selection Board)',
            '{scrutiny_outcome}' => 'ELIGIBLE',
            '{scrutiny_remarks}' => 'Candidate meets all statutory HEC criteria: PhD in Computer Science, 10 years postgraduate teaching experience, and 12 research publications in HEC W/X recognized journals.',
            '{deficiency_details}' => 'Official HEC Equivalence Certificate for Foreign Doctorate degree and Departmental NOC are required.',
            '{deficiency_deadline}' => '12 November 2026, 05:00 PM PKT',
            '{upload_proof_url}' => url('/candidate/deficiency-upload/APP-2026-CS-0842'),
            '{appeal_deadline}' => '15 November 2026 (05:00 PM PKT)',
            '{appeal_url}' => url('/candidate/appeals/new'),
            '{appeal_ticket_no}' => 'APL-2026-8821',
            '{appeal_submission_date}' => '08 November 2026',
            '{appeal_outcome}' => 'APPEAL UPHELD — CANDIDATE DECLARED ELIGIBLE',
            '{appeal_status}' => 'Upheld / Restored to Shortlist',
            '{appellate_remarks}' => 'The Appellate Scrutiny Committee thoroughly examined the original HEC Equivalence Certificate provided; candidate fulfills all statutory criteria and is restored to the shortlisted interview pool.',
            '{decision_date}' => '14 November 2026',

            // Screening Tests & Merit Lists
            '{test_title}' => 'Higher Education Screening & Subject Aptitude Evaluation',
            '{test_date}' => 'Saturday, 28 November 2026',
            '{test_time}' => '10:00 AM PKT (Reporting at 09:15 AM)',
            '{test_center}' => 'Examination Hall Complex, Main Campus, Garden Campus',
            '{roll_number}' => 'ROLL-CS-2026-0842',
            '{admit_card_url}' => url('/candidate/admit-card/ROLL-CS-2026-0842'),
            '{merit_rank}' => 'Merit Rank # 01',
            '{merit_score}' => '88.40 / 100',
            '{merit_list_link}' => url('/candidate/merit-list/advt-04-2026'),

            // Selection Board Interviews & Offers
            '{interview_date}' => 'Thursday, 18 December 2026',
            '{interview_time}' => '10:30 AM PKT',
            '{reporting_time}' => '09:45 AM PKT',
            '{venue}' => 'Senate & Syndicate Conference Hall, Central Administration Block',
            '{committee_name}' => 'Statutory Selection Board for Faculty Appointments',
            '{required_documents}' => 'Original PhD degree & transcripts, HEC equivalence certificate, PEC/relevant professional registration, original CNIC, departmental NOC, and complete published research portfolio.',
            '{call_letter_download_url}' => url('/candidate/call-letter/APP-2026-CS-0842'),
            '{offer_date}' => '22 December 2026',
            '{basic_pay_scale}' => 'BPS-20 (Basic Pay Scale: Rs. 145,000 - 8,500 - 247,000)',
            '{offer_reference_no}' => $orgShort . '/REG/STAT-APPT/2026-8842',
            '{acceptance_deadline}' => '15 January 2027 (04:00 PM PKT)',
            '{joining_deadline}' => '01 February 2027',
            '{next_steps}' => 'Submit medical fitness certificate from the Standing Medical Board and original departmental release/NOC within the stipulated window.',
            '{offer_letter_url}' => url('/candidate/appointment-offer/sample'),

            // External Academic Referees
            '{reviewer_name}' => 'Prof. Dr. Sarah Elizabeth Wright',
            '{reviewer_rank}' => 'Tenured Professor of Computer Science',
            '{university}' => 'University of Oxford, Department of Computer Science, UK',
            '{referee_institution}' => 'University of Oxford, UK',
            '{candidate_cadre}' => 'Associate Professor of Computer Science (BPS-20 / TTS)',
            '{honorarium_details}' => 'PKR 25,000 / USD $100 per completed dossier evaluation',
            '{portal_url}' => url('/reviewer/portal'),
            '{dossier_code}' => 'DOSSIER-BLIND-CS-04',
            '{specialization}' => 'Artificial Intelligence, Large Language Models & Distributed Systems',
            '{evaluation_deadline}' => '30 November 2026',
            '{review_deadline}' => '30 November 2026',
            '{dossier_link}' => url('/reviewer/dossier/token-ref-88219'),
            '{eval_token_link}' => url('/reviewer/dossier/token-ref-88219'),

            // Selection Board Members
            '{board_member_name}' => 'Prof. Dr. Zahid Hussain (Syndicate Expert Member)',
            '{meeting_title}' => '48th Statutory Meeting of the Selection Board',
            '{meeting_date}' => 'Monday, 22 December 2026',
            '{meeting_time}' => '09:30 AM PKT',
            '{meeting_venue}' => 'Executive Syndicate Hall, Vice Chancellor Secretariat',
            '{roster_count}' => '14 Shortlisted Candidates',
            '{agenda_summary}' => 'Statutory selection of Professors (BPS-21), Associate Professors (BPS-20), and Assistant Professors (BPS-19) in Computing, Engineering, and Life Sciences.',
            '{roster_link}' => url('/selection-board/roster/mtg-48'),
            '{board_portal_url}' => url('/selection-board/portal'),
            '{receipt_reference}' => 'SBR-REC-2026-48-CS',
            '{submission_timestamp}' => '22 December 2026, 04:15 PM PKT',

            // General & Advertisements
            '{advertisement_link}' => url('/careers'),
        ];
    }

    /**
     * Wrap any HTML email body inside the responsive, ultra-premium master layout.
     */
    public static function wrapInMasterLayout(string $content, string $subject = '', string $recipientGroup = 'GENERAL', array $variables = []): string
    {
        $orgName = $variables['{org_name}'] ?? SystemSetting::getOrgName();
        $orgShort = $variables['{org_short_name}'] ?? SystemSetting::getOrgShortName();
        $supportEmail = $variables['{support_email}'] ?? SystemSetting::get('support_email', 'support@hirely.app');
        $supportPhone = $variables['{support_phone}'] ?? SystemSetting::get('support_phone', '+92-51-9250000');
        $loginUrl = $variables['{login_url}'] ?? url('/login');
        $portalFooter = SystemSetting::getPortalFooter(strtolower($recipientGroup));
        $letterFooterRaw = SystemSetting::getLetterFooter();
        $letterFooter = is_array($letterFooterRaw)
            ? trim(($letterFooterRaw['disclaimer'] ?? '') . ' ' . ($letterFooterRaw['contact'] ?? ''))
            : (string) $letterFooterRaw;
        $logo = SystemSetting::get('letterhead_logo', '/images/logo-icon.svg');

        // Color coding per stakeholder category
        $palette = match ($recipientGroup) {
            'REVIEWER' => [
                'accent' => '#0284c7',
                'grad_start' => '#06b6d4',
                'grad_end' => '#0284c7',
                'btn_shadow' => 'rgba(2, 132, 199, 0.35)',
                'badge_bg' => 'rgba(6, 182, 212, 0.12)',
                'badge_color' => '#0891b2',
                'badge_border' => 'rgba(6, 182, 212, 0.3)',
                'tag_label' => 'ACADEMIC REFEREE DESK',
            ],
            'BOARD_EXPERT' => [
                'accent' => '#7c3aed',
                'grad_start' => '#8b5cf6',
                'grad_end' => '#6d28d9',
                'btn_shadow' => 'rgba(124, 58, 237, 0.35)',
                'badge_bg' => 'rgba(139, 92, 246, 0.12)',
                'badge_color' => '#7c3aed',
                'badge_border' => 'rgba(139, 92, 246, 0.3)',
                'tag_label' => 'STATUTORY SELECTION BOARD',
            ],
            'SECURITY_AUTH' => [
                'accent' => '#e11d48',
                'grad_start' => '#f43f5e',
                'grad_end' => '#be123c',
                'btn_shadow' => 'rgba(225, 29, 72, 0.35)',
                'badge_bg' => 'rgba(244, 63, 94, 0.12)',
                'badge_color' => '#e11d48',
                'badge_border' => 'rgba(244, 63, 94, 0.3)',
                'tag_label' => 'IDENTITY & ACCESS GOVERNANCE',
            ],
            'CANDIDATE' => [
                'accent' => '#4f46e5',
                'grad_start' => '#6366f1',
                'grad_end' => '#4338ca',
                'btn_shadow' => 'rgba(79, 70, 229, 0.35)',
                'badge_bg' => 'rgba(99, 102, 241, 0.12)',
                'badge_color' => '#4f46e5',
                'badge_border' => 'rgba(99, 102, 241, 0.3)',
                'tag_label' => 'CANDIDATE RECRUITMENT DESK',
            ],
            default => [
                'accent' => '#059669',
                'grad_start' => '#10b981',
                'grad_end' => '#047857',
                'btn_shadow' => 'rgba(5, 150, 105, 0.35)',
                'badge_bg' => 'rgba(16, 185, 129, 0.12)',
                'badge_color' => '#059669',
                'badge_border' => 'rgba(16, 185, 129, 0.3)',
                'tag_label' => 'OFFICIAL COMMUNICATIONS',
            ],
        };

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{$subject}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #0b1120; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b1120; padding: 36px 12px; }
        .main-card { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 45px -10px rgba(0,0,0,0.45); border: 1px solid #1e293b; }
        .header-bar { background: linear-gradient(135deg, #090d16 0%, #1e1b4b 60%, #0f172a 100%); padding: 32px 36px 28px; text-align: left; position: relative; }
        .ribbon-bar { height: 4px; width: 100%; background: linear-gradient(90deg, {$palette['grad_start']}, {$palette['grad_end']}); }
        .content-body { padding: 36px 36px 28px; color: #334155; line-height: 1.7; font-size: 15px; }
        .callout-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid {$palette['accent']}; border-radius: 10px; padding: 18px 22px; margin: 22px 0; }
        .status-pill { display: inline-block; padding: 5px 14px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; background: {$palette['badge_bg']}; color: {$palette['badge_color']}; border: 1px solid {$palette['badge_border']}; margin-bottom: 16px; }
        .btn-action { display: inline-block; background: linear-gradient(135deg, {$palette['grad_start']}, {$palette['grad_end']}); color: #ffffff !important; text-decoration: none; padding: 14px 34px; border-radius: 8px; font-weight: 800; font-size: 14px; letter-spacing: 0.02em; box-shadow: 0 4px 16px {$palette['btn_shadow']}; }
        .param-table { width: 100%; border-collapse: separate; border-spacing: 0; margin: 20px 0; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #ffffff; }
        .param-table td { padding: 10px 14px; font-size: 13.5px; border-bottom: 1px solid #edf2f7; vertical-align: top; }
        .param-table tr:last-child td { border-bottom: none; }
        .param-label { color: #64748b; font-weight: 600; width: 38%; }
        .param-val { color: #0f172a; font-weight: 700; }
        .security-stamp { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px 16px; font-size: 12px; color: #64748b; margin-top: 24px; line-height: 1.5; }
        .footer-bar { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 26px 36px 30px; font-size: 12px; color: #64748b; line-height: 1.6; }
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 12px 6px !important; }
            .header-bar, .content-body, .footer-bar { padding: 24px 20px !important; }
            .param-table td { padding: 8px 10px !important; font-size: 12.5px !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-card">
            <!-- Institutional Header -->
            <div class="header-bar">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td valign="middle">
                            <div style="font-size: 20px; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; line-height: 1.2;">
                                {$orgName}
                            </div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 4px; font-weight: 700;">
                                Statutory Recruitment &amp; Selection Directorate
                            </div>
                        </td>
                        <td align="right" valign="middle">
                            <span style="background: rgba(255,255,255,0.08); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.18); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em; display: inline-block;">
                                {$orgShort} RECRUITMENT
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Category Gradient Ribbon -->
            <div class="ribbon-bar"></div>

            <!-- Content Body -->
            <div class="content-body">
                <div class="status-pill">
                    {$palette['tag_label']}
                </div>
                {$content}
            </div>

            <!-- Institutional Footer -->
            <div class="footer-bar">
                <div style="font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 12.5px;">
                    {$portalFooter}
                </div>
                <div style="font-size: 11px; color: #94a3b8; margin-bottom: 14px; line-height: 1.5;">
                    {$letterFooter}
                </div>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 14px; margin-top: 14px;">
                    <tr>
                        <td>
                            Helpdesk: <a href="mailto:{$supportEmail}" style="color: {$palette['accent']}; text-decoration: none; font-weight: 700;">{$supportEmail}</a>
                            &bull; <span>{$supportPhone}</span>
                        </td>
                        <td align="right">
                            <a href="{$loginUrl}" style="color: #475569; text-decoration: none; font-weight: 600;">Recruitment Console &rarr;</a>
                        </td>
                    </tr>
                </table>
                <div style="margin-top: 12px; font-size: 10px; color: #94a3b8; text-align: center;">
                    Statutory Computer-Generated Dispatch &bull; Confidential &amp; Privileged &bull; &copy; {$orgShort}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Seed all standard institutional email templates.
     */
    public static function seedDefaultTemplates(): void
    {
        $templates = static::getDefaultTemplateDefinitions();

        foreach ($templates as $tmpl) {
            static::updateOrCreate(
                ['slug' => $tmpl['slug']],
                $tmpl
            );
        }
    }

    /**
     * Complete catalog of 32 institutional recruitment & statutory email templates.
     */
    public static function getDefaultTemplateDefinitions(): array
    {
        return [
            // =========================================================================
            // 1. SECURITY & AUTHENTICATION TEMPLATES (5 Templates)
            // =========================================================================
            [
                'slug' => 'account-created',
                'name' => 'Account Registration & Portal Credentials',
                'recipient_group' => 'SECURITY_AUTH',
                'trigger_event' => 'account.created',
                'subject' => 'Welcome to {app_name} — Your Official Account Credentials',
                'preheader' => 'Your official account has been created on the institutional recruitment portal.',
                'description' => 'Sent automatically when an applicant, reviewer, or administrative user account is created.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Full Name of Recipient',
                    '{recipient_email}' => 'Login Work/Personal Email',
                    '{temporary_password}' => 'Assigned Initial / One-Time Password',
                    '{login_url}' => 'Direct Login URL',
                    '{app_name}' => 'Application Portal Name',
                    '{org_name}' => 'Full Organization Name',
                    '{support_email}' => 'Helpdesk Contact Email',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Welcome to {org_name}</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>An official profile account has been provisioned for you on the <strong>{app_name}</strong> recruitment operations portal. You may now securely log in to access your dashboard, submit applications, track scrutiny evaluations, or perform statutory reviews.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">🔐 Access Credentials</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Login Work/Email:</td>
            <td class="param-val">{recipient_email}</td>
        </tr>
        <tr>
            <td class="param-label">Initial Password:</td>
            <td class="param-val"><code style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-family: monospace; font-size: 14px; font-weight: 700; color: #0f172a;">{temporary_password}</code></td>
        </tr>
        <tr>
            <td class="param-label">Security Protocol:</td>
            <td class="param-val" style="color: #059669; font-weight: 700;">Encrypted &bull; Mandatory First-Login Password Change</td>
        </tr>
    </table>
</div>

<p style="margin: 26px 0 30px; text-align: center;">
    <a href="{login_url}" class="btn-action">Sign In to Recruitment Console &rarr;</a>
</p>

<div class="security-stamp">
    <strong>Security Recommendation:</strong> For institutional data protection, please update your temporary password immediately upon your first sign-in. Never share your credentials or multi-factor tokens with anyone.
</div>
HTML
            ],

            [
                'slug' => 'password-reset',
                'name' => 'Password Reset Request & Security Passcode',
                'recipient_group' => 'SECURITY_AUTH',
                'trigger_event' => 'password.reset',
                'subject' => 'Password Reset Request — {app_name} Security Center',
                'preheader' => 'A password reset was requested for your account.',
                'description' => 'Sent when a user requests a password reset token from the login page.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Full Name of Recipient',
                    '{recipient_email}' => 'Registered Email Address',
                    '{reset_url}' => 'Secure Password Reset Link',
                    '{token_expiry}' => 'Token Expiration Window (e.g. 60 minutes)',
                    '{app_name}' => 'Application Portal Name',
                    '{org_name}' => 'Full Organization Name',
                    '{support_email}' => 'Helpdesk Contact Email',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Password Reset Verification</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>We received an official request to reset the password for your profile associated with <strong>{recipient_email}</strong> on the <strong>{app_name}</strong> recruitment operations portal.</p>

<div class="callout-box">
    <div style="font-size: 13px; color: #334155; line-height: 1.6;">
        Click the button below to establish a new strong password. For your security, this single-use link will expire in <strong>{token_expiry}</strong>.
    </div>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{reset_url}" class="btn-action" style="background: linear-gradient(135deg, #f43f5e, #be123c); box-shadow: 0 4px 16px rgba(225, 29, 72, 0.35);">Reset Account Password &rarr;</a>
</p>

<div class="security-stamp">
    <strong>Notice:</strong> If you did not initiate this password reset request, no action is required. Your password remains safe and unchanged. If you suspect unauthorized access, please notify the Institutional Security Desk at <a href="mailto:{support_email}" style="color: #e11d48; font-weight: 700;">{support_email}</a>.
</div>
HTML
            ],

            [
                'slug' => 'password-changed',
                'name' => 'Password Changed & Security Confirmation',
                'recipient_group' => 'SECURITY_AUTH',
                'trigger_event' => 'password.changed',
                'subject' => 'Security Alert: Password Successfully Changed — {app_name}',
                'preheader' => 'Your account password was updated successfully.',
                'description' => 'Sent immediately after a user or administrator updates account credentials.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Full Name of Recipient',
                    '{recipient_email}' => 'Registered Email Address',
                    '{changed_at}' => 'Timestamp of Password Change',
                    '{reset_method}' => 'Self-Service or Administrative Reset',
                    '{app_name}' => 'Application Portal Name',
                    '{support_email}' => 'Helpdesk Contact Email',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Password Change Confirmation</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>This is a formal security alert confirming that the password for your account <strong>{recipient_email}</strong> was successfully changed.</p>

<table class="param-table">
    <tr>
        <td class="param-label">Security Event:</td>
        <td class="param-val" style="color: #059669;">Credential Update / Password Reset Completed</td>
    </tr>
    <tr>
        <td class="param-label">Execution Time:</td>
        <td class="param-val">{changed_at}</td>
    </tr>
    <tr>
        <td class="param-label">Method Used:</td>
        <td class="param-val">{reset_method}</td>
    </tr>
</table>

<div class="security-stamp" style="background: #fffbeb; border-color: #fde68a; color: #92400e;">
    <strong>Crucial Security Notice:</strong> If you did not perform this change, your account may be compromised. Please reset your credentials immediately or notify the Security Directorate at <a href="mailto:{support_email}" style="color: #92400e; font-weight: 700;">{support_email}</a>.
</div>
HTML
            ],

            [
                'slug' => 'two-factor-auth-code',
                'name' => 'Two-Factor Authentication (2FA) Security Passcode',
                'recipient_group' => 'SECURITY_AUTH',
                'trigger_event' => 'auth.two_factor',
                'subject' => 'Your One-Time Security Passcode (OTP): {otp_code} — {app_name}',
                'preheader' => 'Your one-time authentication code for secure console access.',
                'description' => 'Sent during multi-factor authentication sign-in challenges for privileged roles.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Full Name of Recipient',
                    '{otp_code}' => '6-Digit Verification Code',
                    '{otp_expiry}' => 'Validity Duration (e.g. 10 minutes)',
                    '{ip_address}' => 'Sign-In Request IP Address',
                    '{device_info}' => 'Client Browser / Operating System',
                    '{app_name}' => 'Application Portal Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Two-Factor Authentication Challenge</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>You are attempting to sign in to the <strong>{app_name}</strong> Institutional Console. Use the one-time passcode below to verify your identity.</p>

<div style="background: #f1f5f9; border-radius: 12px; padding: 24px; text-align: center; margin: 24px 0; border: 1px solid #cbd5e1;">
    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.1em; margin-bottom: 8px;">One-Time Security Passcode (OTP)</div>
    <div style="font-family: 'JetBrains Mono', monospace; font-size: 34px; font-weight: 900; letter-spacing: 0.2em; color: #0f172a;">{otp_code}</div>
    <div style="font-size: 12px; color: #64748b; margin-top: 8px;">Valid for <strong>{otp_expiry}</strong> &bull; Single-use only</div>
</div>

<table class="param-table">
    <tr>
        <td class="param-label">Client IP Address:</td>
        <td class="param-val">{ip_address}</td>
    </tr>
    <tr>
        <td class="param-label">Device &amp; Browser:</td>
        <td class="param-val">{device_info}</td>
    </tr>
</table>

<div class="security-stamp">
    <strong>Security Warning:</strong> Institutional personnel will never ask for your verification code. Do not share this code under any circumstances.
</div>
HTML
            ],

            [
                'slug' => 'profile-updated-security-notice',
                'name' => 'Security Notice: Profile Details Modified',
                'recipient_group' => 'SECURITY_AUTH',
                'trigger_event' => 'profile.updated',
                'subject' => 'Security Alert: Your Profile Details Were Modified — {app_name}',
                'preheader' => 'A change was made to your profile contact or personal information.',
                'description' => 'Sent whenever sensitive candidate or officer profile fields (CNIC, phone, address, email) are updated.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Full Name of Recipient',
                    '{profile_field_changed}' => 'Description of Field(s) Modified',
                    '{changed_timestamp}' => 'Date & Time of Modification',
                    '{ip_address}' => 'Client IP Address',
                    '{app_name}' => 'Application Portal Name',
                    '{support_email}' => 'Helpdesk Contact Email',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Profile Information Updated</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>This is an automated audit alert to confirm that modifications were made to your official user profile on <strong>{app_name}</strong>.</p>

<table class="param-table">
    <tr>
        <td class="param-label">Attributes Modified:</td>
        <td class="param-val">{profile_field_changed}</td>
    </tr>
    <tr>
        <td class="param-label">Timestamp:</td>
        <td class="param-val">{changed_timestamp}</td>
    </tr>
    <tr>
        <td class="param-label">Origination IP:</td>
        <td class="param-val">{ip_address}</td>
    </tr>
</table>

<div class="security-stamp">
    <strong>Verification:</strong> If you authorized this change, you may safely disregard this message. If you did not make this change, please report this immediately to <a href="mailto:{support_email}" style="color: #e11d48; font-weight: 700;">{support_email}</a>.
</div>
HTML
            ],

            // =========================================================================
            // 2. CANDIDATE LIFECYCLE & STATUTORY INTAKE (14 Templates)
            // =========================================================================
            [
                'slug' => 'candidate-application-submitted',
                'name' => 'Application Submission Confirmation',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'application.submitted',
                'subject' => 'Application Received: {post_title} (Tracking: {tracking_number})',
                'preheader' => 'Your official recruitment application dossier has been received.',
                'description' => 'Sent to applicant when an application dossier is formally submitted.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Applied Post / Position Title',
                    '{department}' => 'Target Department / Faculty',
                    '{tracking_number}' => 'Master Tracking Reference ID',
                    '{submission_date}' => 'Date of Final Submission',
                    '{dossier_url}' => 'Link to View Submitted Dossier',
                    '{app_name}' => 'Application Portal Name',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Application Successfully Received</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>Your formal recruitment application dossier has been successfully submitted to the <strong>Statutory Recruitment Directorate</strong> of <strong>{org_name}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">📋 Dossier Intake Specifications</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Tracking Number:</td>
            <td class="param-val" style="color: #4f46e5; font-size: 15px;">{tracking_number}</td>
        </tr>
        <tr>
            <td class="param-label">Applied Post:</td>
            <td class="param-val">{post_title}</td>
        </tr>
        <tr>
            <td class="param-label">Department / Faculty:</td>
            <td class="param-val">{department}</td>
        </tr>
        <tr>
            <td class="param-label">Submission Date:</td>
            <td class="param-val">{submission_date}</td>
        </tr>
        <tr>
            <td class="param-label">Next Milestone:</td>
            <td class="param-val" style="color: #059669;">Statutory Scrutiny &amp; Qualification Vetting</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{dossier_url}" class="btn-action">Track Application Status &rarr;</a>
</p>

<p style="font-size: 13.5px; color: #64748b;">
    <strong>Please Note:</strong> Please quote your <strong>Tracking Number ({tracking_number})</strong> in all official correspondence with the Recruitment Directorate. Keep your contact details updated in your candidate profile.
</p>
HTML
            ],

            [
                'slug' => 'candidate-application-draft-reminder',
                'name' => 'Gentle Reminder: Incomplete Application Draft Pending Submission',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'application.draft_reminder',
                'subject' => 'Action Required: Complete Your Application for {post_title} before Closing Date',
                'preheader' => 'You have an incomplete draft application awaiting final submission.',
                'description' => 'Sent to candidates who created a draft application but have not finalized it before deadline.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Applied Post Title',
                    '{department}' => 'Department / Faculty',
                    '{closing_date}' => 'Final Application Deadline Date & Time',
                    '{dossier_url}' => 'Link to Resume Draft Application',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Incomplete Application Pending Submission</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>Our records indicate that you have initiated an application for the statutory position of <strong>{post_title}</strong> in the <strong>{department}</strong> at <strong>{org_name}</strong>, but have not yet finalized and submitted your dossier.</p>

<div class="callout-box" style="border-left-color: #d97706;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #d97706; margin-bottom: 8px;">⏳ Strict Deadline Notice</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Position:</td>
            <td class="param-val">{post_title}</td>
        </tr>
        <tr>
            <td class="param-label">Application Status:</td>
            <td class="param-val" style="color: #d97706;">DRAFT &bull; Not Yet Submitted</td>
        </tr>
        <tr>
            <td class="param-label">Submission Deadline:</td>
            <td class="param-val" style="color: #dc2626; font-size: 14.5px;">{closing_date}</td>
        </tr>
    </table>
</div>

<p>Applications submitted after the closing deadline cannot be entertained under Statutory Recruitment Regulations. Please complete your publications, experience records, and document uploads now.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{dossier_url}" class="btn-action" style="background: linear-gradient(135deg, #d97706, #b45309);">Resume &amp; Submit Application &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-voucher-generated',
                'name' => 'Recruitment Fee Challan Voucher Issued',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'voucher.generated',
                'subject' => 'Challan Voucher Issued: {challan_no} for {post_title} — {amount}',
                'preheader' => 'Your official recruitment processing fee challan voucher is ready.',
                'description' => 'Sent when a bank challan or 1Bill voucher is generated for application processing.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{challan_no}' => 'Bank Challan / Voucher Reference',
                    '{consumer_id}' => '1Bill / Online Consumer Number',
                    '{amount}' => 'Fee Amount with Currency',
                    '{due_date}' => 'Challan Expiration / Due Date',
                    '{download_voucher_url}' => 'Link to Download PDF Voucher',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Recruitment Fee Voucher Generated</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>An official statutory processing fee challan has been issued for your application for the post of <strong>{post_title}</strong> at <strong>{org_name}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">💳 Payment Billing Breakdown</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Challan Number:</td>
            <td class="param-val">{challan_no}</td>
        </tr>
        <tr>
            <td class="param-label">1Bill Consumer ID:</td>
            <td class="param-val" style="font-family: monospace; font-size: 14.5px; color: #4f46e5;">{consumer_id}</td>
        </tr>
        <tr>
            <td class="param-label">Payable Amount:</td>
            <td class="param-val" style="color: #059669; font-size: 16px;">{amount}</td>
        </tr>
        <tr>
            <td class="param-label">Payment Due Date:</td>
            <td class="param-val" style="color: #dc2626;">{due_date}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{download_voucher_url}" class="btn-action">Download Printable Challan PDF &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    <strong>Payment Instructions:</strong> You may settle this fee via 1Bill on ATM / Mobile Banking apps using the Consumer ID, or at designated bank branches using the printed challan. Once paid, the system automatically verifies settlement within 24 hours.
</p>
HTML
            ],

            [
                'slug' => 'candidate-payment-settled',
                'name' => 'Recruitment Fee Settlement & Official Receipt',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'payment.settled',
                'subject' => 'Payment Received & Verified: Challan {challan_no} ({amount_paid})',
                'preheader' => 'Your application processing fee has been verified and settled.',
                'description' => 'Sent upon successful bank reconciliation or automated 1Bill settlement.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{challan_no}' => 'Challan Voucher Number',
                    '{transaction_id}' => 'Bank / Gateway Transaction ID',
                    '{amount_paid}' => 'Total Amount Settled',
                    '{settled_at}' => 'Date & Time of Settlement',
                    '{receipt_url}' => 'Link to View Verified Electronic Receipt',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Fee Settlement Verified</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>We are pleased to confirm that your statutory application processing fee for the position of <strong>{post_title}</strong> has been successfully settled and reconciled in our accounts ledger.</p>

<div class="callout-box" style="border-left-color: #059669;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #059669; margin-bottom: 8px;">✓ Official Electronic Payment Receipt</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Challan Reference:</td>
            <td class="param-val">{challan_no}</td>
        </tr>
        <tr>
            <td class="param-label">Gateway Transaction ID:</td>
            <td class="param-val" style="font-family: monospace;">{transaction_id}</td>
        </tr>
        <tr>
            <td class="param-label">Settled Amount:</td>
            <td class="param-val" style="color: #059669; font-size: 15px;">{amount_paid}</td>
        </tr>
        <tr>
            <td class="param-label">Settlement Timestamp:</td>
            <td class="param-val">{settled_at}</td>
        </tr>
        <tr>
            <td class="param-label">Fee Status:</td>
            <td class="param-val" style="color: #059669; font-weight: 800;">VERIFIED &bull; ELIGIBLE FOR SCRUTINY</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{receipt_url}" class="btn-action" style="background: linear-gradient(135deg, #10b981, #047857);">View Verified Receipt &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    Your application dossier has now moved into the formal Scrutiny and Qualification Vetting pipeline.
</p>
HTML
            ],

            [
                'slug' => 'candidate-document-deficiency-notice',
                'name' => 'Urgent: Document Deficiency Notice / Additional Proof Required',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'scrutiny.deficiency_notified',
                'subject' => 'Urgent Notice: Scrutiny Deficiency for {post_title} (Tracking: {tracking_number})',
                'preheader' => 'The Scrutiny Committee requires additional documentation to process your application.',
                'description' => 'Sent when scrutiny vetting flags missing proofs (equivalence certificate, NOC, experience letter).',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{tracking_number}' => 'Application Tracking Reference',
                    '{deficiency_details}' => 'Explicit List of Missing Documents',
                    '{deficiency_deadline}' => 'Strict Rectification Deadline Date & Time',
                    '{upload_proof_url}' => 'Direct Link to Upload Deficient Documents',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Scrutiny Deficiency Notification</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>During the preliminary scrutiny of your application dossier for the position of <strong>{post_title}</strong> (Tracking ID: <strong>{tracking_number}</strong>), the Scrutiny Committee has identified deficient documentation that requires immediate rectification.</p>

<div class="callout-box" style="border-left-color: #dc2626;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #dc2626; margin-bottom: 8px;">⚠️ Required Deficiency Rectification</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Deficiency Details:</td>
            <td class="param-val" style="color: #b91c1c; font-weight: 700;">{deficiency_details}</td>
        </tr>
        <tr>
            <td class="param-label">Submission Deadline:</td>
            <td class="param-val" style="color: #dc2626; font-size: 15px;">{deficiency_deadline}</td>
        </tr>
    </table>
</div>

<p>Failure to upload the deficient documents within the stipulated timeframe will result in your application being declared <strong>Ineligible</strong> in the final scrutiny list.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{upload_proof_url}" class="btn-action" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">Upload Required Documents &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-scrutiny-status-updated',
                'name' => 'Statutory Scrutiny Vetting Outcome Notice',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'scrutiny.status_updated',
                'subject' => 'Statutory Scrutiny Disposition: {post_title} — Status: {scrutiny_status}',
                'preheader' => 'The Statutory Scrutiny Committee has finalized evaluation of your application credentials.',
                'description' => 'Sent when scrutiny officer records qualification outcome (Eligible, Ineligible, or Deficient).',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{tracking_number}' => 'Application Tracking Reference',
                    '{scrutiny_status}' => 'Final Outcome: ELIGIBLE / INELIGIBLE',
                    '{scrutiny_remarks}' => 'Formal Justification & Criteria Assessment',
                    '{appeal_deadline}' => 'Deadline for Lodging Grievance / Appeal',
                    '{appeal_url}' => 'Link to Submit Statutory Appeal',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Statutory Scrutiny Outcome</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>The <strong>Statutory Scrutiny Committee</strong> of <strong>{org_name}</strong> has concluded its qualification vetting for your application for the post of <strong>{post_title}</strong> (Tracking ID: <strong>{tracking_number}</strong>).</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">⚖️ Scrutiny Determination</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Assessed Disposition:</td>
            <td class="param-val" style="font-size: 15px; font-weight: 900; color: #4f46e5;">{scrutiny_status}</td>
        </tr>
        <tr>
            <td class="param-label">Committee Remarks:</td>
            <td class="param-val">{scrutiny_remarks}</td>
        </tr>
        <tr>
            <td class="param-label">Statutory Appeal Cutoff:</td>
            <td class="param-val" style="color: #dc2626;">{appeal_deadline}</td>
        </tr>
    </table>
</div>

<p>Candidates having any grievance or additional documented proof regarding their scrutiny determination have the right to submit an appeal to the Appellate Scrutiny Committee within the prescribed statutory period.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{appeal_url}" class="btn-action">View Evaluation &amp; File Appeal &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-scrutiny-eligible',
                'name' => 'Congratulations: Statutory Scrutiny Cleared & Shortlisted',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'scrutiny.cleared_eligible',
                'subject' => 'Scrutiny Clearance Notice: You are Shortlisted for {post_title}',
                'preheader' => 'Congratulations! You have successfully cleared the statutory scrutiny stage.',
                'description' => 'Dedicated positive alert dispatched when candidate is verified as fully eligible.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{department}' => 'Department / Faculty',
                    '{tracking_number}' => 'Application Tracking Reference',
                    '{scrutiny_remarks}' => 'Scrutiny Committee Observations',
                    '{dossier_url}' => 'Link to View Candidate Portal',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #059669; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Congratulations — Scrutiny Cleared!</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>We are pleased to inform you that your application dossier for the statutory position of <strong>{post_title}</strong> in the <strong>{department}</strong> has successfully cleared all qualification scrutiny checks and HEC benchmark requirements at <strong>{org_name}</strong>.</p>

<div class="callout-box" style="border-left-color: #059669;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #059669; margin-bottom: 8px;">✓ Clearance Summary</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Eligibility Status:</td>
            <td class="param-val" style="color: #059669; font-size: 15px; font-weight: 900;">PROVISIONALLY ELIGIBLE &bull; SHORTLISTED</td>
        </tr>
        <tr>
            <td class="param-label">Committee Remarks:</td>
            <td class="param-val">{scrutiny_remarks}</td>
        </tr>
        <tr>
            <td class="param-label">Next Statutory Phase:</td>
            <td class="param-val" style="color: #4f46e5;">Peer Review / Selection Board Interview Call</td>
        </tr>
    </table>
</div>

<p>Your dossier is being forwarded to the next evaluation milestone. You will receive an official notification with details regarding test schedules or Selection Board interview calls in due course.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{dossier_url}" class="btn-action" style="background: linear-gradient(135deg, #10b981, #047857);">View Shortlist Status &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-scrutiny-ineligible-appeal',
                'name' => 'Scrutiny Disposition: Provisionally Ineligible & Right to Appeal',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'scrutiny.provisionally_ineligible',
                'subject' => 'Scrutiny Outcome: Provisional Ineligibility & Statutory Appeal Window',
                'preheader' => 'Notice regarding provisional ineligibility and procedure for filing an appeal.',
                'description' => 'Formal notice sent to candidates marked ineligible explaining the reasons and appeal process.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{tracking_number}' => 'Application Tracking Reference',
                    '{scrutiny_remarks}' => 'Specific Deficiency or Disqualification Reason',
                    '{appeal_deadline}' => 'Strict Statutory Appeal Cutoff Date & Time',
                    '{appeal_url}' => 'Link to Lodge Formal Appeal',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #b91c1c; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Scrutiny Disposition Notice</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>The Scrutiny Committee has conducted the preliminary assessment of your application for the post of <strong>{post_title}</strong> (Tracking ID: <strong>{tracking_number}</strong>) and provisionally determined your candidature to be <strong>Ineligible</strong> based on current documentation.</p>

<div class="callout-box" style="border-left-color: #dc2626;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #dc2626; margin-bottom: 8px;">⚠️ Basis of Ineligibility</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Assessed Reason:</td>
            <td class="param-val" style="color: #b91c1c; font-weight: 700;">{scrutiny_remarks}</td>
        </tr>
        <tr>
            <td class="param-label">Statutory Appeal Cutoff:</td>
            <td class="param-val" style="color: #dc2626; font-size: 14.5px;">{appeal_deadline}</td>
        </tr>
    </table>
</div>

<p>Under Statutory Recruitment Regulations, you are entitled to submit an appeal to the Appellate Scrutiny Committee accompanied by requisite documentary evidence before the expiry of the deadline stated above.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{appeal_url}" class="btn-action" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">Lodge Statutory Appeal &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-appeal-submitted',
                'name' => 'Statutory Appeal / Grievance Registered',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'appeal.submitted',
                'subject' => 'Appeal Lodged: Ticket {appeal_ticket_no} for {post_title}',
                'preheader' => 'Your statutory appeal has been registered and placed before the Appellate Committee.',
                'description' => 'Sent to candidate upon submission of a formal grievance or scrutiny appeal.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{appeal_ticket_no}' => 'Appellate Docket / Reference Number',
                    '{appeal_submission_date}' => 'Date of Appeal Lodging',
                    '{dossier_url}' => 'Link to View Appeal Status',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Statutory Appeal Registered</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>Your formal appeal regarding the scrutiny determination for the position of <strong>{post_title}</strong> has been registered with the <strong>Appellate Scrutiny Committee</strong> of <strong>{org_name}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">⚖️ Appellate Docket Details</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Appeal Ticket:</td>
            <td class="param-val" style="color: #4f46e5; font-size: 15px;">{appeal_ticket_no}</td>
        </tr>
        <tr>
            <td class="param-label">Applied Post:</td>
            <td class="param-val">{post_title}</td>
        </tr>
        <tr>
            <td class="param-label">Lodging Date:</td>
            <td class="param-val">{appeal_submission_date}</td>
        </tr>
        <tr>
            <td class="param-label">Appellate Status:</td>
            <td class="param-val" style="color: #d97706; font-weight: 700;">UNDER REVIEW BY APPELLATE PANEL</td>
        </tr>
    </table>
</div>

<p>The Appellate Committee will re-examine your dossier alongside the fresh proofs provided. The committee's final determination will be notified to you promptly via email and candidate portal.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{dossier_url}" class="btn-action">Track Appeal Docket &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-appeal-decision',
                'name' => 'Appellate Scrutiny Committee Final Review Decision',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'appeal.decided',
                'subject' => 'Appellate Committee Disposition: Appeal Ticket {appeal_ticket_no} ({appeal_outcome})',
                'preheader' => 'The Appellate Scrutiny Committee has finalized review of your grievance appeal.',
                'description' => 'Sent after appellate scrutiny committee reviews an appeal and records a disposition.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{appeal_ticket_no}' => 'Appellate Docket Number',
                    '{appeal_outcome}' => 'Final Disposition: UPHELD / REJECTED',
                    '{appellate_remarks}' => 'Appellate Committee Reasoning & Assessment',
                    '{decision_date}' => 'Date of Committee Decision',
                    '{dossier_url}' => 'Link to View Official Appellate Order',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Appellate Scrutiny Decision</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>The <strong>Appellate Scrutiny Committee</strong> of <strong>{org_name}</strong> has convened and finalized its disposition on your statutory appeal docket <strong>{appeal_ticket_no}</strong> for the post of <strong>{post_title}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">⚖️ Appellate Panel Determination</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Final Outcome:</td>
            <td class="param-val" style="font-size: 15px; font-weight: 900; color: #4f46e5;">{appeal_outcome}</td>
        </tr>
        <tr>
            <td class="param-label">Panel Reasoning:</td>
            <td class="param-val">{appellate_remarks}</td>
        </tr>
        <tr>
            <td class="param-label">Order Date:</td>
            <td class="param-val">{decision_date}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{dossier_url}" class="btn-action">Download Appellate Order &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    This determination represents the final statutory decision of the Scrutiny Appellate Committee.
</p>
HTML
            ],

            [
                'slug' => 'candidate-test-call-letter',
                'name' => 'Screening / Subject Evaluation Test Admit Card & Schedule',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'screening_test.scheduled',
                'subject' => 'Written Evaluation / Screening Test Admit Card: {post_title} (Roll No: {roll_number})',
                'preheader' => 'Your written evaluation / screening test schedule and admit card are issued.',
                'description' => 'Sent to eligible candidates when a preliminary screening test is scheduled.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{roll_number}' => 'Examination Roll Number',
                    '{test_title}' => 'Title of Evaluation Test',
                    '{test_date}' => 'Examination Date',
                    '{test_time}' => 'Reporting & Start Time',
                    '{test_center}' => 'Examination Hall & Center Address',
                    '{admit_card_url}' => 'Link to Download Roll No Slip / Admit Card',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Screening Test Schedule &amp; Admit Card</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>You have been shortlisted to appear in the written screening / subject aptitude evaluation for the post of <strong>{post_title}</strong> at <strong>{org_name}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">📝 Examination Particulars</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Candidate Roll No:</td>
            <td class="param-val" style="font-size: 15px; color: #4f46e5; font-family: monospace;">{roll_number}</td>
        </tr>
        <tr>
            <td class="param-label">Examination:</td>
            <td class="param-val">{test_title}</td>
        </tr>
        <tr>
            <td class="param-label">Date &amp; Time:</td>
            <td class="param-val" style="color: #059669; font-weight: 800;">{test_date} at {test_time}</td>
        </tr>
        <tr>
            <td class="param-label">Test Center:</td>
            <td class="param-val">{test_center}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{admit_card_url}" class="btn-action">Download Official Admit Card PDF &rarr;</a>
</p>

<div class="security-stamp">
    <strong>Mandatory Notice:</strong> Candidates must bring the printed Admit Card along with their original CNIC. Mobile phones, programmable calculators, and electronic gadgets are strictly prohibited inside the examination center.
</div>
HTML
            ],

            [
                'slug' => 'candidate-interview-call',
                'name' => 'Selection Board Interview Call Letter & Statutory Summons',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'interview.scheduled',
                'subject' => 'Statutory Interview Call Letter: {post_title} — {interview_date}',
                'preheader' => 'You are formally summoned to appear before the Statutory Selection Board.',
                'description' => 'Official call letter summoning candidate to interview before Selection Board.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{interview_date}' => 'Interview Date',
                    '{interview_time}' => 'Interview Session Time',
                    '{reporting_time}' => 'Arrival & Document Verification Time',
                    '{venue}' => 'Official Interview Committee Room',
                    '{required_documents}' => 'Original Credentials to Present',
                    '{call_letter_download_url}' => 'Link to Download Official Call Letter PDF',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Selection Board Interview Call Letter</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>By the direction of the competent statutory authority of <strong>{org_name}</strong>, you are hereby requested to appear before the <strong>Statutory Selection Board</strong> for interview for the position of <strong>{post_title}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">🏛️ Statutory Summons Schedule</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Interview Date:</td>
            <td class="param-val" style="color: #4f46e5; font-size: 15px;">{interview_date}</td>
        </tr>
        <tr>
            <td class="param-label">Reporting Time:</td>
            <td class="param-val" style="color: #dc2626;">{reporting_time}</td>
        </tr>
        <tr>
            <td class="param-label">Interview Session:</td>
            <td class="param-val">{interview_time}</td>
        </tr>
        <tr>
            <td class="param-label">Session Venue:</td>
            <td class="param-val">{venue}</td>
        </tr>
        <tr>
            <td class="param-label">Mandatory Documents:</td>
            <td class="param-val" style="font-size: 13px;">{required_documents}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{call_letter_download_url}" class="btn-action">Download Signed Call Letter &rarr;</a>
</p>

<div class="security-stamp">
    <strong>Mandatory Statutory Notice:</strong> Candidates must bring all original degrees, transcripts, research publication proofs, departmental NOC (for in-service personnel), and CNIC. No TA/DA is admissible under statutory university employment codes.
</div>
HTML
            ],

            [
                'slug' => 'candidate-merit-list-published',
                'name' => 'Provisional Merit List & Scored Standings Published',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'merit_list.published',
                'subject' => 'Provisional Merit List Published: {post_title} — {advertisement_no}',
                'preheader' => 'The provisional merit list and aggregated academic score standings are published.',
                'description' => 'Sent to all shortlisted candidates when a scored merit list is issued.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Title',
                    '{advertisement_no}' => 'Advertisement Reference',
                    '{merit_rank}' => 'Assigned Candidate Merit Rank',
                    '{merit_score}' => 'Aggregated Merit Score',
                    '{merit_list_link}' => 'Link to Public Merit Roster',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Provisional Merit List Notification</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>The provisional merit list for the position of <strong>{post_title}</strong> under <strong>{advertisement_no}</strong> has been officially published by <strong>{org_name}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">📊 Standing &amp; Score Summary</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Position:</td>
            <td class="param-val">{post_title}</td>
        </tr>
        <tr>
            <td class="param-label">Your Merit Rank:</td>
            <td class="param-val" style="color: #4f46e5; font-size: 16px; font-weight: 900;">{merit_rank}</td>
        </tr>
        <tr>
            <td class="param-label">Aggregated Score:</td>
            <td class="param-val" style="color: #059669; font-weight: 800;">{merit_score}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{merit_list_link}" class="btn-action">Inspect Official Merit List &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    This is a provisional scored listing subject to final approval and ratification by the Executive Syndicate.
</p>
HTML
            ],

            [
                'slug' => 'candidate-appointment-offer',
                'name' => 'Provisional Recommendation & Statutory Appointment Offer',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'appointment.offered',
                'subject' => 'Official Appointment Offer: {post_title} ({basic_pay_scale}) — {org_name}',
                'preheader' => 'Congratulations on your statutory recommendation and appointment offer.',
                'description' => 'Formal offer letter dispatched to selected candidates upon Syndicate / Board approval.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Offered',
                    '{basic_pay_scale}' => 'Pay Scale / Band (e.g. BPS-20)',
                    '{offer_reference_no}' => 'Official Institutional Dispatch Number',
                    '{offer_date}' => 'Issuance Date of Offer',
                    '{acceptance_deadline}' => 'Deadline to Accept and Return Formal Offer',
                    '{next_steps}' => 'Instructions for Medical Vetting and Joining',
                    '{offer_letter_url}' => 'Link to Download Official Offer Package',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #059669; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Statutory Appointment Offer</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>On the recommendation of the Statutory Selection Board and subsequent approval by the competent authority, we are pleased to offer you appointment to the faculty position of <strong>{post_title}</strong> in <strong>{basic_pay_scale}</strong> at <strong>{org_name}</strong>.</p>

<div class="callout-box" style="border-left-color: #059669;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #059669; margin-bottom: 8px;">📜 Offer Specifications</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Dispatch Ref No:</td>
            <td class="param-val">{offer_reference_no}</td>
        </tr>
        <tr>
            <td class="param-label">Offered Cadre:</td>
            <td class="param-val" style="color: #059669; font-size: 15px;">{post_title}</td>
        </tr>
        <tr>
            <td class="param-label">Pay Scale &amp; Cadre:</td>
            <td class="param-val">{basic_pay_scale}</td>
        </tr>
        <tr>
            <td class="param-label">Offer Date:</td>
            <td class="param-val">{offer_date}</td>
        </tr>
        <tr>
            <td class="param-label">Acceptance Cutoff:</td>
            <td class="param-val" style="color: #dc2626; font-weight: 800;">{acceptance_deadline}</td>
        </tr>
    </table>
</div>

<p><strong>Next Steps:</strong> {next_steps}</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{offer_letter_url}" class="btn-action" style="background: linear-gradient(135deg, #10b981, #047857);">Download Offer Package &amp; Accept &rarr;</a>
</p>
HTML
            ],

            // =========================================================================
            // 3. ACADEMIC REVIEWERS & EXTERNAL REFEREES (6 Templates)
            // =========================================================================
            [
                'slug' => 'reviewer-invitation',
                'name' => 'Formal Invitation to Serve as External Academic Referee',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.invited',
                'subject' => 'Invitation to Serve as Academic Peer Referee — {candidate_cadre} Evaluation',
                'preheader' => 'You are cordially invited to evaluate faculty research dossiers.',
                'description' => 'Sent to renowned national or international professors inviting them to serve as referees.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Referee Full Name',
                    '{reviewer_rank}' => 'Academic Rank of Referee',
                    '{university}' => 'Referee Home Institution',
                    '{candidate_cadre}' => 'Cadre Under Evaluation (e.g. Professor / Associate Professor)',
                    '{honorarium_details}' => 'Honorarium and Statutory Compensation',
                    '{portal_url}' => 'Link to Secure Reviewer Portal',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Invitation to Serve as External Academic Referee</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>In view of your distinguished international standing in research and scholarly excellence, the Vice Chancellor and Statutory Selection Directorate of <strong>{org_name}</strong> cordially invite you to serve as an <strong>External Academic Peer Referee</strong> for the statutory evaluation of faculty candidates applying for the position of <strong>{candidate_cadre}</strong>.</p>

<div class="callout-box" style="border-left-color: #0284c7;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #0284c7; margin-bottom: 8px;">🎓 Evaluation Protocols</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Target Cadre:</td>
            <td class="param-val">{candidate_cadre}</td>
        </tr>
        <tr>
            <td class="param-label">Protocol:</td>
            <td class="param-val" style="color: #0284c7;">Confidential Double-Blind Peer Review</td>
        </tr>
        <tr>
            <td class="param-label">Honorarium:</td>
            <td class="param-val">{honorarium_details}</td>
        </tr>
    </table>
</div>

<p>Your expert judgment will play an instrumental role in maintaining the rigorous merit and academic standards of higher education appointments.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{portal_url}" class="btn-action" style="background: linear-gradient(135deg, #06b6d4, #0284c7);">Access Referee Console &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'reviewer-dossier-assigned',
                'name' => 'Confidential Candidate Dossier Assigned for Evaluation',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.dossier_assigned',
                'subject' => 'Confidential Research Dossier Assigned: {dossier_code} — {specialization}',
                'preheader' => 'A research portfolio is ready for your expert evaluation.',
                'description' => 'Sent to empaneled reviewer when research papers and dossiers are assigned.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Referee Full Name',
                    '{dossier_code}' => 'Anonymized Blind Dossier Identifier',
                    '{specialization}' => 'Academic Domain / Subject Area',
                    '{evaluation_deadline}' => 'Target Submission Date for Evaluation Report',
                    '{eval_token_link}' => 'One-Click Direct Link to Blind Dossier',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Confidential Research Dossier Assigned</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>An anonymized academic research dossier has been assigned to you for comprehensive peer review under statutory university service statutes at <strong>{org_name}</strong>.</p>

<div class="callout-box" style="border-left-color: #0284c7;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #0284c7; margin-bottom: 8px;">🔬 Dossier Specifications</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Blind Dossier Code:</td>
            <td class="param-val" style="font-family: monospace; font-size: 14.5px; color: #0284c7;">{dossier_code}</td>
        </tr>
        <tr>
            <td class="param-label">Field of Study:</td>
            <td class="param-val">{specialization}</td>
        </tr>
        <tr>
            <td class="param-label">Evaluation Deadline:</td>
            <td class="param-val" style="color: #dc2626; font-weight: 800;">{evaluation_deadline}</td>
        </tr>
    </table>
</div>

<p>You may inspect the candidate's publications, citation profile, books, and patents directly through the secure encrypted referee portal below.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{eval_token_link}" class="btn-action" style="background: linear-gradient(135deg, #06b6d4, #0284c7);">Open Confidential Dossier &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'reviewer-reminder',
                'name' => 'Academic Peer Evaluation Pending Reminder',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.reminder_sent',
                'subject' => 'Gentle Reminder: Confidential Evaluation Pending for Dossier {dossier_code}',
                'preheader' => 'A gentle reminder regarding your pending academic peer review report.',
                'description' => 'Automated or manual ping sent to external reviewers approaching evaluation deadline.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Referee Full Name',
                    '{dossier_code}' => 'Anonymized Blind Dossier Identifier',
                    '{evaluation_deadline}' => 'Target Submission Date for Evaluation Report',
                    '{eval_token_link}' => 'Direct Link to Pending Dossier',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Peer Evaluation Reminder</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>We hope this communication finds you well. This is a gentle reminder regarding the confidential research evaluation for dossier <strong>{dossier_code}</strong> at <strong>{org_name}</strong>.</p>

<div class="callout-box" style="border-left-color: #0284c7;">
    <table class="param-table">
        <tr>
            <td class="param-label">Dossier Identifier:</td>
            <td class="param-val" style="font-family: monospace;">{dossier_code}</td>
        </tr>
        <tr>
            <td class="param-label">Target Due Date:</td>
            <td class="param-val" style="color: #dc2626; font-weight: 800;">{evaluation_deadline}</td>
        </tr>
    </table>
</div>

<p>As the statutory Selection Board meeting is scheduled shortly, your timely assessment is essential to complete the candidate's academic evaluation profile.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{eval_token_link}" class="btn-action" style="background: linear-gradient(135deg, #06b6d4, #0284c7);">Submit Evaluation Report &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'reviewer-deadline-warning',
                'name' => 'Urgent: Peer Review Evaluation Deadline Approaching',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.deadline_warning',
                'subject' => 'Urgent Notice: Confidential Peer Review Deadline Approaching for Dossier {dossier_code}',
                'preheader' => 'Urgent reminder: Only 48 hours remaining to submit your evaluation.',
                'description' => 'Automated 48-hour deadline warning sent to external reviewers.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Referee Full Name',
                    '{dossier_code}' => 'Dossier Code',
                    '{evaluation_deadline}' => 'Final Due Date',
                    '{eval_token_link}' => 'One-Click Submission Link',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #dc2626; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Urgent: Evaluation Deadline Approaching</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>This is an urgent notification regarding the peer evaluation for dossier <strong>{dossier_code}</strong>. The statutory review period concludes on <strong>{evaluation_deadline}</strong>.</p>

<div class="callout-box" style="border-left-color: #dc2626;">
    <p style="margin: 0; font-size: 13.5px; color: #991b1b; font-weight: 700;">
        The Selection Board proceedings depend on receiving your independent evaluation score to establish candidate quorum.
    </p>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{eval_token_link}" class="btn-action" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">Finalize &amp; Lock Evaluation &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'reviewer-report-acknowledged',
                'name' => 'Confidential Referee Evaluation Acknowledged & Received',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.report_received',
                'subject' => 'Formal Acknowledgment: Confidential Evaluation Received for Dossier {dossier_code}',
                'preheader' => 'Your expert evaluation report has been received and securely sealed.',
                'description' => 'Sent to reviewer immediately upon their submission of an evaluation report.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Referee Full Name',
                    '{dossier_code}' => 'Dossier Code',
                    '{receipt_reference}' => 'Electronic Submission Reference Hash',
                    '{submission_timestamp}' => 'Date & Time of Report Receipt',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #059669; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Evaluation Report Acknowledged</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>On behalf of the Vice Chancellor and Statutory Selection Directorate of <strong>{org_name}</strong>, we formally acknowledge the successful receipt of your confidential peer evaluation report for dossier <strong>{dossier_code}</strong>.</p>

<div class="callout-box" style="border-left-color: #059669;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #059669; margin-bottom: 8px;">🔒 Cryptographic Security Receipt</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Submission Receipt:</td>
            <td class="param-val" style="font-family: monospace; font-size: 13px;">{receipt_reference}</td>
        </tr>
        <tr>
            <td class="param-label">Received At:</td>
            <td class="param-val">{submission_timestamp}</td>
        </tr>
        <tr>
            <td class="param-label">Report Status:</td>
            <td class="param-val" style="color: #059669; font-weight: 700;">SEALED &amp; FILED FOR SELECTION BOARD</td>
        </tr>
    </table>
</div>

<p>We extend our deepest gratitude for your scholarly contribution and dedication to academic excellence.</p>
HTML
            ],

            [
                'slug' => 'reviewer-evaluation-completed',
                'name' => 'Peer Review Completion & Honorarium Processing Confirmation',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.honorarium_processed',
                'subject' => 'Thank You: Peer Evaluation Completed & Honorarium Processing Initiated',
                'preheader' => 'Confirmation of evaluation completion and honorarium processing.',
                'description' => 'Sent after all assigned reviews are locked and honorarium invoice is generated.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Referee Full Name',
                    '{honorarium_details}' => 'Disbursed Honorarium Details',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Peer Evaluation Completed</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>Your academic peer review assignment with <strong>{org_name}</strong> has been successfully concluded. We sincerely thank you for your invaluable time, rigorous appraisal, and scholarly insight.</p>

<div class="callout-box" style="border-left-color: #0284c7;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #0284c7; margin-bottom: 8px;">💳 Honorarium Details</div>
    <p style="margin: 0; font-size: 14px; color: #0f172a;">
        The Finance Directorate has been authorized to process your statutory referee honorarium: <strong>{honorarium_details}</strong>.
    </p>
</div>
HTML
            ],

            // =========================================================================
            // 4. STATUTORY SELECTION BOARD & COMMITTEES (5 Templates)
            // =========================================================================
            [
                'slug' => 'board-meeting-scheduled',
                'name' => 'Notice of Statutory Selection Board Meeting & Agenda',
                'recipient_group' => 'BOARD_EXPERT',
                'trigger_event' => 'board.meeting_scheduled',
                'subject' => 'Statutory Notice: {meeting_title} Scheduled for {meeting_date}',
                'preheader' => 'Official meeting notice and working agenda for Selection Board members.',
                'description' => 'Dispatched to Selection Board members, Chancellor nominees, and Subject Experts.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{board_member_name}' => 'Member Full Name & Title',
                    '{meeting_title}' => 'Meeting Title / Number',
                    '{meeting_date}' => 'Meeting Date',
                    '{meeting_time}' => 'Session Start Time',
                    '{meeting_venue}' => 'Executive Syndicate Room / Hall',
                    '{agenda_summary}' => 'Overview of Faculty Positions & Roster',
                    '{board_portal_url}' => 'Link to Secure Selection Board Desk',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Statutory Selection Board Notice</h2>
<p>Respected <strong>{board_member_name}</strong>,</p>
<p>Under the orders of the Vice Chancellor / Chairman of the Selection Board of <strong>{org_name}</strong>, notice is hereby given that the <strong>{meeting_title}</strong> has been convened.</p>

<div class="callout-box" style="border-left-color: #7c3aed;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #7c3aed; margin-bottom: 8px;">🏛️ Meeting Particulars</div>
    <table class="param-table">
        <tr>
            <td class="param-label">Meeting Session:</td>
            <td class="param-val" style="color: #7c3aed; font-size: 15px;">{meeting_title}</td>
        </tr>
        <tr>
            <td class="param-label">Date &amp; Time:</td>
            <td class="param-val" style="color: #0f172a; font-weight: 800;">{meeting_date} at {meeting_time}</td>
        </tr>
        <tr>
            <td class="param-label">Session Venue:</td>
            <td class="param-val">{meeting_venue}</td>
        </tr>
        <tr>
            <td class="param-label">Agenda Summary:</td>
            <td class="param-val">{agenda_summary}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{board_portal_url}" class="btn-action" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">Access Board Console &amp; Working Papers &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'board-roster-assigned',
                'name' => 'Candidate Dossiers & Scrutiny Records Gated for Board Review',
                'recipient_group' => 'BOARD_EXPERT',
                'trigger_event' => 'board.roster_assigned',
                'subject' => 'Confidential Scrutiny Roster & Portfolios Dispatched: {meeting_title}',
                'preheader' => 'Candidate dossiers and scrutiny comparative charts are accessible.',
                'description' => 'Sent when candidate comparative charts and dossiers are assigned to board members.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{board_member_name}' => 'Board Member Full Name',
                    '{meeting_title}' => 'Selection Board Meeting Title',
                    '{roster_count}' => 'Total Number of Vetted Candidates',
                    '{roster_link}' => 'Secure Link to Scrutiny Comparative Matrix',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Scrutiny Roster &amp; Dossiers Available</h2>
<p>Respected <strong>{board_member_name}</strong>,</p>
<p>The comparative qualification matrices, academic publications, referee reports, and scrutiny summaries for <strong>{meeting_title}</strong> have been published to your secure confidential portal.</p>

<div class="callout-box" style="border-left-color: #7c3aed;">
    <table class="param-table">
        <tr>
            <td class="param-label">Shortlisted Pool:</td>
            <td class="param-val" style="color: #7c3aed; font-size: 15px; font-weight: 800;">{roster_count}</td>
        </tr>
        <tr>
            <td class="param-label">Review Protocol:</td>
            <td class="param-val">Confidential Pre-Meeting Digital Inspection</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{roster_link}" class="btn-action" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">Open Confidential Board Roster &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'board-scores-submitted-receipt',
                'name' => 'Selection Board Score Submission Receipt & Quorum Recorded',
                'recipient_group' => 'BOARD_EXPERT',
                'trigger_event' => 'board.scores_submitted',
                'subject' => 'Official Receipt: Selection Board Assessment Scores Confirmed & Sealed',
                'preheader' => 'Your interview evaluation scores have been locked and submitted into the official minutes.',
                'description' => 'Sent to each board member when their scores are digitally sealed.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{board_member_name}' => 'Board Member Full Name',
                    '{meeting_title}' => 'Meeting Title',
                    '{receipt_reference}' => 'Cryptographic Lock Reference',
                    '{submission_timestamp}' => 'Date & Time of Scoring Lock',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #059669; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Selection Board Scores Recorded</h2>
<p>Respected <strong>{board_member_name}</strong>,</p>
<p>Your confidential candidate scoring evaluations for <strong>{meeting_title}</strong> have been digitally sealed and locked into the official proceedings archive of <strong>{org_name}</strong>.</p>

<div class="callout-box" style="border-left-color: #059669;">
    <table class="param-table">
        <tr>
            <td class="param-label">Digital Seal ID:</td>
            <td class="param-val" style="font-family: monospace;">{receipt_reference}</td>
        </tr>
        <tr>
            <td class="param-label">Locked At:</td>
            <td class="param-val">{submission_timestamp}</td>
        </tr>
    </table>
</div>
HTML
            ],

            [
                'slug' => 'candidate-final-selection-regret',
                'name' => 'Selection Board Regret Notice',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'candidate.regret_issued',
                'subject' => 'Notification of Selection Board Proceedings: {post_title}',
                'preheader' => 'Official update regarding the outcome of the Selection Board proceedings.',
                'description' => 'Polite, respectful notification sent to candidates not recommended for appointment.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{post_title}' => 'Position Applied For',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Selection Board Proceedings Outcome</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>Thank you for your interest in joining <strong>{org_name}</strong> and for participating in the statutory selection proceedings for the position of <strong>{post_title}</strong>.</p>

<p>The Selection Board considered many accomplished candidates for this limited vacancy. We regret to inform you that on this occasion, another candidate was recommended for the position.</p>

<p>We deeply appreciate the time, effort, and scholarly accomplishments you demonstrated throughout the selection process. We wish you every success in your future academic pursuits and encourage you to apply for upcoming openings aligned with your specialization.</p>
HTML
            ],

            [
                'slug' => 'syndicate-approval-concurrence',
                'name' => 'Syndicate / Executive Board Concurrence Notification',
                'recipient_group' => 'BOARD_EXPERT',
                'trigger_event' => 'syndicate.concurrence_issued',
                'subject' => 'Statutory Notification: Syndicate Concurrence for Faculty Recommendations',
                'preheader' => 'Executive Syndicate has confirmed and approved the Selection Board recommendations.',
                'description' => 'Notification confirming Syndicate approval of Selection Board minutes.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{board_member_name}' => 'Member Full Name',
                    '{meeting_title}' => 'Selection Board Meeting Ref',
                    '{decision_date}' => 'Date of Syndicate Approval',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Syndicate Concurrence Confirmed</h2>
<p>Respected <strong>{board_member_name}</strong>,</p>
<p>This is to inform you that the Executive Syndicate of <strong>{org_name}</strong> in its recent meeting has approved the recommendations of <strong>{meeting_title}</strong>.</p>

<p>The appointment offers and formal gazette notifications are being dispatched to the recommended candidates.</p>
HTML
            ],

            // =========================================================================
            // 5. GENERAL, ADVERTISEMENT & BESPOKE DISPATCH (2 Templates)
            // =========================================================================
            [
                'slug' => 'job-advertisement-announcement',
                'name' => 'New Faculty & Executive Vacancy Advertisement Announcement',
                'recipient_group' => 'GENERAL',
                'trigger_event' => 'advertisement.published',
                'subject' => 'Career Opportunity: {advertisement_title} at {org_name}',
                'preheader' => 'New faculty and administrative career openings announced.',
                'description' => 'Sent to registered candidate pool announcing newly published employment advertisements.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Recipient Name',
                    '{advertisement_title}' => 'Advertisement Title',
                    '{advertisement_no}' => 'Advertisement Reference Number',
                    '{closing_date}' => 'Final Application Submission Deadline',
                    '{advertisement_link}' => 'Link to View Vacancy Listings',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">New Statutory Career Openings</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p><strong>{org_name}</strong> has announced new statutory recruitment openings under <strong>{advertisement_title}</strong> ({advertisement_no}).</p>

<div class="callout-box" style="border-left-color: #059669;">
    <table class="param-table">
        <tr>
            <td class="param-label">Intake Reference:</td>
            <td class="param-val">{advertisement_no}</td>
        </tr>
        <tr>
            <td class="param-label">Submission Deadline:</td>
            <td class="param-val" style="color: #dc2626; font-weight: 800;">{closing_date}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{advertisement_link}" class="btn-action" style="background: linear-gradient(135deg, #10b981, #047857);">Explore Vacancies &amp; Apply Online &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-challan-expired-notice',
                'name' => 'Recruitment Fee Challan Expired & Grace Period Notice',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'challan.expired',
                'subject' => 'Notice: Recruitment Fee Challan Expired for {job_title} ({voucher_id})',
                'preheader' => 'Your recruitment fee voucher payment window has expired.',
                'description' => 'Sent to applicant when an unpaid recruitment fee challan voucher passes its statutory cutoff date.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Applied Post Title',
                    '{voucher_id}' => 'Kuickpay / 1Bill Consumer Number',
                    '{amount}' => 'Fee Amount in PKR',
                    '{due_date}' => 'Voucher Cutoff Date',
                    '{reissue_url}' => 'Direct Reissue URL',
                    '{org_name}' => 'Full Organization Name',
                    '{support_email}' => 'Helpdesk Contact Email',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #b91c1c; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Recruitment Fee Voucher Expired</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>This is an automated statutory notice from <strong>{org_name}</strong> informing you that the payment validity period for your recruitment fee challan voucher has lapsed without settlement.</p>

<div class="data-table-container">
    <table class="data-table">
        <tr>
            <td class="param-label">Target Position:</td>
            <td class="param-val"><strong>{job_title}</strong></td>
        </tr>
        <tr>
            <td class="param-label">Consumer Voucher ID:</td>
            <td class="param-val" style="font-family: monospace; font-weight: 800; color: #b91c1c;">{voucher_id}</td>
        </tr>
        <tr>
            <td class="param-label">Prescribed Amount:</td>
            <td class="param-val">PKR {amount}</td>
        </tr>
        <tr>
            <td class="param-label">Cutoff Expiry Date:</td>
            <td class="param-val" style="color: #b91c1c; font-weight: 700;">{due_date}</td>
        </tr>
    </table>
</div>

<div class="callout-box" style="border-left-color: #f59e0b; background: #fffbeb;">
    <p style="margin: 0; font-size: 14.5px; line-height: 1.6; color: #92400e;">
        <strong>Important Action Required:</strong> In accordance with statutory recruitment policies, unverified applications cannot be forwarded to the Scrutiny Committee. If the advertisement validity remains active, you may regenerate a new voucher from your candidate portal.
    </p>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{reissue_url}" class="btn-action" style="background: linear-gradient(135deg, #4f46e5, #3730a3);">Regenerate Payment Voucher &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-profile-locked-confirmation',
                'name' => 'Official Notice: Application Credentials Locked & Archived',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'candidate.profile_locked',
                'subject' => 'Application Dossier Locked & Archived for Statutory Scrutiny: {job_title}',
                'preheader' => 'Your qualification records have been locked for scrutiny integrity.',
                'description' => 'Sent when candidate generates voucher or completes submission, locking credentials for scrutiny audit integrity.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Applied Cadre / Position Title',
                    '{tracking_number}' => 'Official Dossier Tracking Number',
                    '{lock_timestamp}' => 'Dossier Lock Timestamp',
                    '{dossier_hash}' => 'SHA-256 Tamper-Evident Fingerprint',
                    '{org_name}' => 'Full Organization Name',
                    '{dossier_url}' => 'Printable Dossier URL',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Application Dossier Locked &amp; Archived</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>Your candidate profile, educational qualifications, research publications, and employment credentials for <strong>{job_title}</strong> have been officially locked and cryptographically archived for statutory scrutiny vetting.</p>

<div class="data-table-container">
    <table class="data-table">
        <tr>
            <td class="param-label">Target Position:</td>
            <td class="param-val"><strong>{job_title}</strong></td>
        </tr>
        <tr>
            <td class="param-label">Dossier Reference ID:</td>
            <td class="param-val" style="font-family: monospace; font-weight: 800; color: #4f46e5;">{tracking_number}</td>
        </tr>
        <tr>
            <td class="param-label">Archival Timestamp:</td>
            <td class="param-val">{lock_timestamp}</td>
        </tr>
        <tr>
            <td class="param-label">Audit Fingerprint:</td>
            <td class="param-val" style="font-family: monospace; font-size: 11px; word-break: break-all; color: #64748b;">{dossier_hash}</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{dossier_url}" class="btn-action">Download Master Application Dossier &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'referee-reference-request',
                'name' => 'Confidential Letter of Recommendation Request for Candidate',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'referee.recommendation_request',
                'subject' => 'Confidential Reference Request: Candidacy of {candidate_name} for {job_title}',
                'preheader' => 'Official request for statutory letter of recommendation.',
                'description' => 'Dispatched to external academic and professional referees listed by the candidate in their dossier.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{referee_name}' => 'Referee Full Name',
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Candidate Applied Position',
                    '{org_name}' => 'Full Organization Name',
                    '{referee_portal_url}' => 'Secure Recommendation Link',
                    '{support_email}' => 'Helpdesk Contact Email',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Confidential Academic Reference Request</h2>
<p>Dear <strong>{referee_name}</strong>,</p>
<p><strong>{candidate_name}</strong> has applied for the statutory faculty/officer position of <strong>{job_title}</strong> at <strong>{org_name}</strong> and has nominated you as an official referee.</p>
<p>Under our statutory selection regulations, the Selection Board requests your confidential assessment regarding the candidate's academic competence, scholarly integrity, research capability, and professional conduct.</p>

<p style="margin: 28px 0; text-align: center;">
    <a href="{referee_portal_url}" class="btn-action" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">Submit Confidential Reference &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    All recommendations are strictly privileged and accessible only to the Statutory Selection Board members.
</p>
HTML
            ],

            [
                'slug' => 'scrutiny-committee-dossier-batched',
                'name' => 'Scrutiny Committee Dossier Batch Dispatched for Vetting',
                'recipient_group' => 'BOARD_EXPERT',
                'trigger_event' => 'scrutiny.batch_assigned',
                'subject' => 'New Scrutiny Batch Forwarded: {candidate_count} Dossiers for {job_title}',
                'preheader' => 'New candidate dossiers are ready for statutory credential auditing.',
                'description' => 'Alert dispatched to scrutiny committee members when candidates clear fee verification and advance to scrutiny.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Scrutiny Committee Member Name',
                    '{job_title}' => 'Target Recruitment Campaign Title',
                    '{job_code}' => 'Campaign Job Code',
                    '{candidate_count}' => 'Number of Dossiers in Batch',
                    '{scrutiny_url}' => 'Scrutiny Queue URL',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Scrutiny Queue Assignment</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>A new cohort of candidate dossiers has cleared bank fee verification and has been forwarded to the <strong>Standing Scrutiny &amp; Equivalence Committee</strong> for credential vetting.</p>

<div class="data-table-container">
    <table class="data-table">
        <tr>
            <td class="param-label">Target Campaign:</td>
            <td class="param-val"><strong>{job_title} ({job_code})</strong></td>
        </tr>
        <tr>
            <td class="param-label">Pending Dossiers:</td>
            <td class="param-val" style="font-weight: 800; color: #f59e0b;">{candidate_count} Candidates</td>
        </tr>
        <tr>
            <td class="param-label">Governing Mandate:</td>
            <td class="param-val">HEC Equivalence, Division Criteria &amp; Document Adequacy</td>
        </tr>
    </table>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{scrutiny_url}" class="btn-action">Open Scrutiny Workstation &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'admin-custom-dispatch',
                'name' => 'Bespoke Institutional Dispatch Desk',
                'recipient_group' => 'GENERAL',
                'trigger_event' => 'admin.custom_email',
                'subject' => 'Official Institutional Communication — {org_name}',
                'preheader' => 'Official correspondence from the Recruitment Directorate.',
                'description' => 'Base layout utilized by authorized officers to compose and dispatch bespoke custom communications.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Addressee Name',
                    '{candidate_name}' => 'Candidate Name (If Applicable)',
                    '{post_title}' => 'Target Position (If Applicable)',
                    '{tracking_number}' => 'Dossier Tracking ID (If Applicable)',
                    '{action_url}' => 'Target Action URL',
                    '{action_text}' => 'Button Label Text',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 21px; font-weight: 800; letter-spacing: -0.01em;">Official Institutional Notice</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>This is an official communication dispatched from the <strong>Statutory Recruitment &amp; Selection Directorate</strong> of <strong>{org_name}</strong> regarding your academic record, application dossier, or institutional engagement.</p>

<div class="callout-box">
    <p style="margin: 0; font-size: 14.5px; line-height: 1.7; color: #1e293b;">
        Please review your updated status and documentation in the Recruitment Console.
    </p>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{action_url}" class="btn-action">{action_text} &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    If you require clarification, please reply directly or reach out to the Recruitment Helpdesk.
</p>
HTML
            ],
        ];
    }
}

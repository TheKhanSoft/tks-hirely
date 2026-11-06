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
     * Wrap any HTML email body inside the responsive, modern master layout.
     */
    public static function wrapInMasterLayout(string $content, string $subject = '', string $recipientGroup = 'GENERAL', array $variables = []): string
    {
        $orgName = $variables['{org_name}'] ?? SystemSetting::getOrgName();
        $orgShort = $variables['{org_short_name}'] ?? SystemSetting::getOrgShortName();
        $supportEmail = $variables['{support_email}'] ?? SystemSetting::get('support_email', 'support@hirely.app');
        $loginUrl = $variables['{login_url}'] ?? url('/login');
        $portalFooter = SystemSetting::getPortalFooter(strtolower($recipientGroup));
        $letterFooter = SystemSetting::getLetterFooter();
        $logo = SystemSetting::get('letterhead_logo', '/images/logo-icon.svg');
        $accentColor = match ($recipientGroup) {
            'REVIEWER' => '#0ea5e9',
            'BOARD_EXPERT' => '#8b5cf6',
            'SECURITY_AUTH' => '#e11d48',
            default => '#4f46e5',
        };

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$subject}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0f172a; padding: 40px 10px; }
        .main-card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1px solid #1e293b; }
        .header-bar { background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); padding: 28px 32px; border-bottom: 3px solid {$accentColor}; text-align: left; }
        .content-body { padding: 36px 32px 28px; color: #334155; line-height: 1.65; font-size: 15px; }
        .callout-box { background: #f8fafc; border-left: 4px solid {$accentColor}; border-radius: 8px; padding: 18px 20px; margin: 24px 0; }
        .btn-action { display: inline-block; background: {$accentColor}; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; letter-spacing: 0.02em; }
        .param-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .param-table td { padding: 8px 10px; font-size: 14px; border-bottom: 1px solid #e2e8f0; }
        .footer-bar { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 32px; font-size: 12px; color: #64748b; line-height: 1.6; }
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
                            <div style="font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: -0.01em;">
                                {$orgName}
                            </div>
                            <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 3px; font-weight: 600;">
                                Statutory Recruitment &amp; Selection Directorate
                            </div>
                        </td>
                        <td align="right" valign="middle">
                            <span style="background: rgba(255,255,255,0.12); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">
                                {$orgShort} OS
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Content Body -->
            <div class="content-body">
                {$content}
            </div>

            <!-- Institutional Footer -->
            <div class="footer-bar">
                <div style="font-weight: 700; color: #475569; margin-bottom: 6px;">
                    {$portalFooter}
                </div>
                <div style="font-size: 11px; color: #94a3b8; margin-bottom: 12px;">
                    {$letterFooter}
                </div>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 12px;">
                    <tr>
                        <td>
                            Need assistance? Email: <a href="mailto:{$supportEmail}" style="color: {$accentColor}; text-decoration: none; font-weight: 600;">{$supportEmail}</a>
                        </td>
                        <td align="right">
                            <a href="{$loginUrl}" style="color: #64748b; text-decoration: none;">Access Portal</a>
                        </td>
                    </tr>
                </table>
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
     * Catalog of default system email templates.
     */
    public static function getDefaultTemplateDefinitions(): array
    {
        return [
            // =========================================================================
            // 1. SECURITY & AUTHENTICATION TEMPLATES
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
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Welcome to {org_name}</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>An official profile account has been provisioned for you on the <strong>{app_name}</strong> recruitment operations portal. You may now securely log in to access your dashboard, submit applications, or perform statutory reviews.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">🔐 Access Credentials</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Login Email:</td>
            <td><strong>{recipient_email}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Temporary Password:</td>
            <td><code style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-family: monospace; font-size: 14px; font-weight: 700;">{temporary_password}</code></td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 30px; text-align: center;">
    <a href="{login_url}" class="btn-action">Sign In to Recruitment Console &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    <strong>Security Recommendation:</strong> For your security, please update your password immediately upon your first sign-in. Do not share your login credentials with anyone.
</p>
HTML
            ],

            [
                'slug' => 'password-reset',
                'name' => 'Password Reset Request',
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
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Password Reset Verification</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>We received an official request to reset the password for your account associated with <strong>{recipient_email}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 13px; color: #334155;">
        Click the button below to establish a new strong password. This secure one-time link will expire in <strong>{token_expiry}</strong>.
    </div>
</div>

<p style="margin: 28px 0; text-align: center;">
    <a href="{reset_url}" class="btn-action" style="background: #e11d48;">Reset Account Password &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b; line-height: 1.6;">
    If you did not request this password reset, please ignore this notice or contact the institutional helpdesk immediately at <a href="mailto:{support_email}" style="color: #4f46e5;">{support_email}</a>. No changes have been made to your credentials.
</p>
HTML
            ],

            [
                'slug' => 'password-changed',
                'name' => 'Password Changed & Security Confirmation',
                'recipient_group' => 'SECURITY_AUTH',
                'trigger_event' => 'password.changed',
                'subject' => 'Security Alert: Your Password Was Successfully Updated — {app_name}',
                'preheader' => 'Your portal account password has been changed successfully.',
                'description' => 'Sent immediately following a successful password update or administrative reset.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Full Name of Recipient',
                    '{recipient_email}' => 'Registered Email Address',
                    '{changed_at}' => 'Timestamp of Password Change',
                    '{reset_method}' => 'Method of Reset (Self-Service or Administrative)',
                    '{app_name}' => 'Application Portal Name',
                    '{login_url}' => 'Direct Login URL',
                    '{support_email}' => 'Helpdesk Contact Email',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Password Security Confirmation</h2>
<p>Dear <strong>{recipient_name}</strong>,</p>
<p>This is an automated security notice confirming that the password for your account <strong>{recipient_email}</strong> was successfully updated.</p>

<div class="callout-box" style="border-left-color: #10b981;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #10b981; margin-bottom: 8px;">✓ Security Event Audit</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Update Timestamp:</td>
            <td><strong>{changed_at}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Reset Mode:</td>
            <td><strong>{reset_method}</strong></td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{login_url}" class="btn-action">Access Your Account &rarr;</a>
</p>

<p style="font-size: 13px; color: #dc2626;">
    <strong>Didn't make this change?</strong> If you did not authorize this action, your account may be compromised. Please notify the security team immediately at <a href="mailto:{support_email}" style="color: #dc2626; font-weight: 700;">{support_email}</a>.
</p>
HTML
            ],

            // =========================================================================
            // 2. CANDIDATE LIFECYCLE TEMPLATES
            // =========================================================================
            [
                'slug' => 'candidate-application-submitted',
                'name' => 'Application Submission Confirmation',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'application.submitted',
                'subject' => 'Application Submitted Successfully: {job_title} [{tracking_number}] — {org_name}',
                'preheader' => 'Your application has been received and logged in the statutory repository.',
                'description' => 'Sent to candidate upon formal submission of their application dossier.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Position Title',
                    '{job_code}' => 'Job Code Identifier',
                    '{tracking_number}' => 'Master Application Tracking Number',
                    '{department}' => 'Department or Academic Faculty',
                    '{submission_date}' => 'Date of Application Filing',
                    '{dossier_url}' => 'Link to View Candidate Dossier',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Application Dossier Lodged</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>Thank you for applying to <strong>{org_name}</strong>. Your recruitment application and academic dossier have been successfully registered under statutory intake protocols.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">📋 Dossier Filing Receipt</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Tracking Number:</td>
            <td><strong style="font-family: monospace; font-size: 15px; color: #4f46e5;">{tracking_number}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Position Title:</td>
            <td><strong>{job_title}</strong> ({job_code})</td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Department:</td>
            <td>{department}</td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Submission Date:</td>
            <td>{submission_date}</td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{dossier_url}" class="btn-action">View Application Status &amp; Dossier &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    <strong>Next Steps:</strong> If your application fee remains unsettled, please verify and settle your 1Link/1Bill challan voucher before the statutory closing date. Once verified, your dossier will advance to qualification scrutiny vetting.
</p>
HTML
            ],

            [
                'slug' => 'candidate-voucher-generated',
                'name' => 'Recruitment Fee Challan Voucher Issued',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'voucher.generated',
                'subject' => 'Payment Challan Voucher Generated: {job_title} — Consumer #{consumer_id}',
                'preheader' => 'Your 1Link / 1Bill payment challan voucher is ready for settlement.',
                'description' => 'Sent to candidate when their payment voucher is generated.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Position Title',
                    '{consumer_id}' => '1Link / 1Bill Consumer Number',
                    '{amount}' => 'Fee Amount',
                    '{currency}' => 'Currency (e.g. PKR)',
                    '{due_date}' => 'Payment Validity Due Date',
                    '{download_voucher_url}' => 'Direct Voucher Download URL',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Recruitment Fee Payment Voucher</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>Your official recruitment application fee challan has been generated for the post of <strong>{job_title}</strong>.</p>

<div class="callout-box" style="border-left-color: #f59e0b;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #d97706; margin-bottom: 8px;">💳 1Bill / 1Link Payment Parameters</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Consumer Number:</td>
            <td><strong style="font-family: monospace; font-size: 16px; color: #d97706;">{consumer_id}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Prescribed Fee:</td>
            <td><strong>{currency} {amount}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Payment Due Date:</td>
            <td><strong style="color: #dc2626;">{due_date}</strong></td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{download_voucher_url}" class="btn-action" style="background: #f59e0b;">Download Official Printable Voucher &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    <strong>Settlement Instructions:</strong> You can pay via ATM, Mobile Banking, Internet Banking, or Over-the-Counter at any 1Link / 1Bill member bank using your <strong>Consumer Number</strong> above. Upon payment, settlement is verified automatically.
</p>
HTML
            ],

            [
                'slug' => 'candidate-payment-settled',
                'name' => 'Payment Settled & Official Fee Receipt',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'payment.settled',
                'subject' => 'Payment Confirmed & Verified — Official Receipt for {job_title}',
                'preheader' => 'Your application fee payment has been settled and verified.',
                'description' => 'Sent to candidate upon successful settlement of their application challan.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Position Title',
                    '{consumer_id}' => '1Link / 1Bill Consumer Number',
                    '{transaction_id}' => 'Settlement Transaction ID',
                    '{amount_paid}' => 'Total Amount Settled',
                    '{settled_at}' => 'Settlement Timestamp',
                    '{receipt_url}' => 'Link to View Verified Receipt',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Payment Verification Receipt</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>We are pleased to confirm that your recruitment application fee for <strong>{job_title}</strong> has been fully settled and reconciled with the institutional treasury.</p>

<div class="callout-box" style="border-left-color: #10b981;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #10b981; margin-bottom: 8px;">✓ Verified Transaction Receipt</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Transaction ID:</td>
            <td><code style="font-family: monospace; font-size: 14px; color: #0f172a; font-weight: 700;">{transaction_id}</code></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Consumer ID:</td>
            <td><strong>{consumer_id}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Amount Settled:</td>
            <td><strong>{amount_paid}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Settlement Date:</td>
            <td>{settled_at}</td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{receipt_url}" class="btn-action" style="background: #10b981;">View Verified Application Dossier &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    Your application has been locked and forwarded to the Scrutiny Committee for qualification vetting. You can track progress anytime through your applicant desk.
</p>
HTML
            ],

            [
                'slug' => 'candidate-scrutiny-status-updated',
                'name' => 'Statutory Scrutiny Vetting Outcome Notice',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'scrutiny.updated',
                'subject' => 'Statutory Scrutiny Vetting Update: {job_title} [{scrutiny_status}]',
                'preheader' => 'The Scrutiny Committee has finalized evaluation of your candidacy credentials.',
                'description' => 'Sent to candidate when their scrutiny committee evaluation is updated (Eligible, Conditionally Eligible, Ineligible).',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Position Title',
                    '{scrutiny_status}' => 'Evaluation Outcome (Eligible / Ineligible / Conditional)',
                    '{scrutiny_remarks}' => 'Official Scrutiny Committee Findings & Remarks',
                    '{appeal_deadline}' => 'Statutory Appeal Filing Deadline',
                    '{appeal_url}' => 'Link to Lodge Grievance Appeal',
                    '{dossier_url}' => 'Direct Dossier URL',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Scrutiny Committee Evaluation Notice</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>The Statutory Scrutiny Committee has finalized the preliminary verification of your academic degrees, employment credentials, and publication benchmarks for the post of <strong>{job_title}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">⚖️ Preliminary Scrutiny Findings</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Status Outcome:</td>
            <td><strong style="font-size: 15px; color: #4f46e5;">{scrutiny_status}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Statutory Remarks:</td>
            <td>{scrutiny_remarks}</td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Appeal Window:</td>
            <td>Until <strong>{appeal_deadline}</strong></td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{appeal_url}" class="btn-action">Lodge Grievance Appeal / View Dossier &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    Candidates marked as conditionally eligible or ineligible who possess valid documentary proof may lodge an appeal before the Appellate Review Committee within the statutory deadline stated above.
</p>
HTML
            ],

            [
                'slug' => 'candidate-appeal-decision',
                'name' => 'Appellate Committee Review Decision',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'appeal.updated',
                'subject' => 'Appellate Committee Review Decision: {job_title} [{appeal_outcome}]',
                'preheader' => 'The Appellate Committee has reviewed your grievance appeal.',
                'description' => 'Sent to candidate after the Appellate Review Committee pronounces decision on a grievance appeal.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Position Title',
                    '{appeal_outcome}' => 'Appellate Decision (Upheld / Overruled / Conditionally Admitted)',
                    '{appellate_remarks}' => 'Statutory Appellate Findings & Justification',
                    '{decision_date}' => 'Date of Appellate Ruling',
                    '{dossier_url}' => 'Link to View Updated Dossier',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Appellate Review Committee Decision</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>The Statutory Appellate Review Committee has reviewed the grievance appeal lodged regarding your candidacy for the post of <strong>{job_title}</strong>.</p>

<div class="callout-box">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">🏛️ Appellate Order &amp; Final Disposition</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Final Decision:</td>
            <td><strong style="font-size: 15px; color: #4f46e5;">{appeal_outcome}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Committee Order:</td>
            <td>{appellate_remarks}</td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Ruling Date:</td>
            <td>{decision_date}</td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{dossier_url}" class="btn-action">View Updated Dossier Record &rarr;</a>
</p>
HTML
            ],

            [
                'slug' => 'candidate-interview-call',
                'name' => 'Selection Board Interview Call Letter',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'interview.scheduled',
                'subject' => 'Official Call Letter: Statutory Selection Board Interview for {job_title}',
                'preheader' => 'You are invited to appear before the Statutory Selection Board.',
                'description' => 'Sent to shortlisted eligible candidates summoned for Selection Board interview / demonstration.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Position Title',
                    '{interview_date}' => 'Scheduled Interview Date',
                    '{reporting_time}' => 'Mandatory Reporting Time',
                    '{venue}' => 'Official Meeting Venue / Room',
                    '{required_documents}' => 'Checklist of Original Documents Required',
                    '{call_letter_download_url}' => 'Link to Download Printable Signed Call Letter',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Statutory Selection Board Call Letter</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>By direction of the Competent Authority, you are hereby invited to appear before the <strong>Statutory Selection Board</strong> for interview and evaluation for appointment to the post of <strong>{job_title}</strong> at <strong>{org_name}</strong>.</p>

<div class="callout-box" style="border-left-color: #6366f1;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4f46e5; margin-bottom: 8px;">🗓️ Schedule &amp; Reporting Details</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Interview Date:</td>
            <td><strong>{interview_date}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Reporting Time:</td>
            <td><strong>{reporting_time}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Interview Venue:</td>
            <td><strong>{venue}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Required Credentials:</td>
            <td>{required_documents}</td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{call_letter_download_url}" class="btn-action">Download Official Signed Call Letter &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    <strong>Mandatory Note:</strong> Please bring all original degrees, transcripts, experience certificates, research publications, CNIC, and NOC (if in government service) for physical verification at the desk. No TA/DA is admissible.
</p>
HTML
            ],

            [
                'slug' => 'candidate-appointment-offer',
                'name' => 'Provisional Recommendation & Appointment Offer',
                'recipient_group' => 'CANDIDATE',
                'trigger_event' => 'appointment.offer',
                'subject' => 'Provisional Recommendation & Offer of Appointment: {job_title}',
                'preheader' => 'Congratulations: You have been recommended for statutory appointment.',
                'description' => 'Sent to selected candidates upon approval by Selection Board & Syndicate.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{candidate_name}' => 'Candidate Full Name',
                    '{job_title}' => 'Position Title',
                    '{basic_pay_scale}' => 'BPS / TTS Cadre',
                    '{department}' => 'Assigned Department / Institute',
                    '{offer_reference_no}' => 'Offer Reference Number',
                    '{acceptance_deadline}' => 'Offer Acceptance Deadline',
                    '{next_steps}' => 'Instructions for Medical Examination & Verification',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Offer of Statutory Appointment</h2>
<p>Dear <strong>{candidate_name}</strong>,</p>
<p>We are pleased to inform you that upon the recommendations of the Statutory Selection Board and subsequent approval by the Syndicate of <strong>{org_name}</strong>, you have been selected for appointment to the post of <strong>{job_title}</strong> (<strong>{basic_pay_scale}</strong>).</p>

<div class="callout-box" style="border-left-color: #10b981;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #10b981; margin-bottom: 8px;">🎖️ Appointment Specifications</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Reference Number:</td>
            <td><strong>{offer_reference_no}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Position &amp; Scale:</td>
            <td><strong>{job_title} ({basic_pay_scale})</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Department:</td>
            <td>{department}</td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Acceptance Due:</td>
            <td><strong style="color: #dc2626;">{acceptance_deadline}</strong></td>
        </tr>
    </table>
</div>

<p style="font-size: 14px; color: #334155;">
    <strong>Next Steps:</strong> {next_steps}
</p>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{login_url}" class="btn-action" style="background: #10b981;">Access Official Offer Letter &amp; Terms &rarr;</a>
</p>
HTML
            ],

            // =========================================================================
            // 3. ACADEMIC REVIEWER & REFEREE TEMPLATES
            // =========================================================================
            [
                'slug' => 'reviewer-invitation',
                'name' => 'Invitation to Serve as Academic Peer Referee',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.invited',
                'subject' => 'Confidential Invitation: Academic Peer Reviewer for Faculty Recruitment — {org_name}',
                'preheader' => 'You are invited to serve as an expert peer referee.',
                'description' => 'Sent to senior faculty / international experts when empaneled for blind peer evaluation.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Reviewer Full Name',
                    '{reviewer_rank}' => 'Academic Rank (e.g. Professor)',
                    '{university}' => 'Reviewer University / Institution',
                    '{candidate_cadre}' => 'Cadre Under Evaluation (e.g. Professor of CS)',
                    '{honorarium_details}' => 'Honorarium and Statutory Remuneration',
                    '{portal_url}' => 'Referee Evaluation Console Link',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Confidential Academic Peer Reviewer Invitation</h2>
<p>Dear <strong>{reviewer_rank} {reviewer_name}</strong>,</p>
<p>On behalf of the Vice Chancellor and Statutory Selection Board of <strong>{org_name}</strong>, we have the honor to invite you to serve as an expert peer referee to evaluate candidates applied for faculty positions in the cadre of <strong>{candidate_cadre}</strong>.</p>

<div class="callout-box" style="border-left-color: #0ea5e9;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #0284c7; margin-bottom: 8px;">🔬 Blind Peer Review Protocol</div>
    <p style="font-size: 13.5px; margin: 0 0 10px; color: #334155;">
        As prescribed under Higher Education Commission (HEC) statutory regulations, appointments to senior academic posts require independent peer evaluations by eminent international and national specialists.
    </p>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Remuneration:</td>
            <td><strong>{honorarium_details}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Confidentiality:</td>
            <td>Strictly Blind &amp; Privileged Review Protocol Active</td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{portal_url}" class="btn-action" style="background: #0ea5e9;">Access Referee Evaluation Portal &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    Your expert opinion will assist the Selection Board in maintaining the highest standards of academic merit and scholarly excellence.
</p>
HTML
            ],

            [
                'slug' => 'reviewer-dossier-assigned',
                'name' => 'Confidential Candidate Dossier Assigned for Review',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.assigned',
                'subject' => 'Confidential Dossier Assigned for Blind Peer Review — Ref #{dossier_code}',
                'preheader' => 'A new faculty research dossier has been assigned to you.',
                'description' => 'Sent to reviewer when a candidate research profile is assigned for scoring.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Reviewer Full Name',
                    '{dossier_code}' => 'Confidential Candidate Anonymous Ref Code',
                    '{specialization}' => 'Subject Specialization & Field',
                    '{publications_count}' => 'Number of Submitted Papers',
                    '{due_date}' => 'Statutory Evaluation Deadline',
                    '{review_desk_url}' => 'Link to Digital Evaluation Scorecard',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Faculty Research Dossier Assigned</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>A candidate research publication dossier has been assigned to you for confidential blind evaluation at <strong>{org_name}</strong>.</p>

<div class="callout-box" style="border-left-color: #0ea5e9;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #0284c7; margin-bottom: 8px;">📑 Evaluation Assignment Details</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Dossier Code:</td>
            <td><strong style="font-family: monospace; font-size: 15px; color: #0284c7;">{dossier_code}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Subject Domain:</td>
            <td>{specialization}</td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Publications Filed:</td>
            <td><strong>{publications_count} HEC-Recognized Publications</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Target Deadline:</td>
            <td><strong style="color: #dc2626;">{due_date}</strong></td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{review_desk_url}" class="btn-action" style="background: #0ea5e9;">Open Secure Evaluation Scorecard &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    You can preview full-text research PDFs, view Turnitin similarity indexes, and enter your qualitative rubric scoring directly through the digital scoring console.
</p>
HTML
            ],

            [
                'slug' => 'reviewer-reminder',
                'name' => 'Peer Review Deadline Approaching Reminder',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.reminder',
                'subject' => 'Urgent Reminder: Pending Peer Review Evaluation — Due {due_date}',
                'preheader' => 'A reminder regarding your pending academic peer review report.',
                'description' => 'Sent automatically or manually as a deadline reminder to referees.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Reviewer Full Name',
                    '{dossier_code}' => 'Confidential Candidate Anonymous Ref Code',
                    '{due_date}' => 'Evaluation Due Date',
                    '{days_remaining}' => 'Days Remaining Until Deadline',
                    '{review_desk_url}' => 'Link to Digital Evaluation Scorecard',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Peer Review Submission Reminder</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>This is a gentle statutory reminder regarding the pending peer evaluation report for candidate dossier <strong>#{dossier_code}</strong>.</p>

<div class="callout-box" style="border-left-color: #f59e0b;">
    <div style="font-size: 13px; color: #334155;">
        The scheduled meeting of the Selection Board is approaching. The target deadline for this evaluation is <strong>{due_date}</strong> (<strong>{days_remaining} remaining</strong>).
    </div>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{review_desk_url}" class="btn-action" style="background: #f59e0b;">Complete Evaluation Online &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    If you require a deadline extension or technical assistance with the dossier vault, please reply to this notice or contact the Registrar's Office.
</p>
HTML
            ],

            [
                'slug' => 'reviewer-report-acknowledged',
                'name' => 'Referee Report Submission Acknowledgement',
                'recipient_group' => 'REVIEWER',
                'trigger_event' => 'reviewer.submitted',
                'subject' => 'Acknowledgement of Confidential Peer Review Report Submission — {org_name}',
                'preheader' => 'Your peer review scoring report has been received and locked.',
                'description' => 'Sent to reviewer upon successfully submitting their consensus scorecard.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{reviewer_name}' => 'Reviewer Full Name',
                    '{dossier_code}' => 'Confidential Candidate Anonymous Ref Code',
                    '{submission_timestamp}' => 'Submission Date & Time',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Report Received &amp; Reconciled</h2>
<p>Dear <strong>{reviewer_name}</strong>,</p>
<p>Thank you for submitting your expert peer evaluation report for candidate dossier <strong>#{dossier_code}</strong>. Your scoring and qualitative critique were securely recorded at <strong>{submission_timestamp}</strong>.</p>

<div class="callout-box" style="border-left-color: #10b981;">
    <div style="font-size: 13px; color: #334155;">
        Your report has been encrypted and deposited into the official Selection Board dossier vault. We deeply appreciate your valuable contribution to the preservation of academic integrity and scholarly standards at <strong>{org_name}</strong>.
    </div>
</div>
HTML
            ],

            // =========================================================================
            // 4. STATUTORY SELECTION BOARD & COMMITTEE TEMPLATES
            // =========================================================================
            [
                'slug' => 'board-meeting-scheduled',
                'name' => 'Statutory Selection Board Meeting Notice',
                'recipient_group' => 'BOARD_EXPERT',
                'trigger_event' => 'board.scheduled',
                'subject' => 'Notice of Statutory Selection Board Meeting: {campaign_title}',
                'preheader' => 'Notice and agenda for upcoming Selection Board proceedings.',
                'description' => 'Sent to committee / board members convening a selection session.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{member_name}' => 'Board Member Full Name',
                    '{campaign_title}' => 'Recruitment Campaign Title',
                    '{meeting_date}' => 'Convening Date',
                    '{meeting_time}' => 'Meeting Time',
                    '{meeting_venue}' => 'Venue / Syndicate Room',
                    '{candidate_dockets_url}' => 'Link to Candidate Summary Dockets',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Notice of Statutory Selection Board Meeting</h2>
<p>Dear <strong>{member_name}</strong>,</p>
<p>A formal meeting of the <strong>Statutory Selection Board</strong> has been convened to conduct interviews and evaluations for <strong>{campaign_title}</strong>.</p>

<div class="callout-box" style="border-left-color: #8b5cf6;">
    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #7c3aed; margin-bottom: 8px;">🏛️ Meeting Schedule &amp; Venue</div>
    <table class="param-table">
        <tr>
            <td width="35%" style="color: #64748b; font-weight: 600;">Meeting Date:</td>
            <td><strong>{meeting_date}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Commencement:</td>
            <td><strong>{meeting_time}</strong></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-weight: 600;">Venue:</td>
            <td><strong>{meeting_venue}</strong></td>
        </tr>
    </table>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{candidate_dockets_url}" class="btn-action" style="background: #8b5cf6;">Access Electronic Candidate Dockets &rarr;</a>
</p>

<p style="font-size: 13px; color: #64748b;">
    All comparative merit sheets, scrutiny reports, and referee scores have been empanelled for digital review.
</p>
HTML
            ],

            [
                'slug' => 'board-roster-assigned',
                'name' => 'Statutory Committee Empanelment Notification',
                'recipient_group' => 'BOARD_EXPERT',
                'trigger_event' => 'board.roster.assigned',
                'subject' => 'Notification of Statutory Committee Empanelment: {committee_title}',
                'preheader' => 'You have been appointed to a statutory evaluation committee.',
                'description' => 'Sent to internal or external specialists appointed to a scrutiny or selection committee.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{member_name}' => 'Committee Member Full Name',
                    '{committee_title}' => 'Committee Name / Constitution',
                    '{campaign_title}' => 'Assigned Campaign Title',
                    '{statutory_role}' => 'Role (Convener, Subject Specialist, Member)',
                    '{portal_url}' => 'Direct Portal Link',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<h2 style="margin-top: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Committee Empanelment Notification</h2>
<p>Dear <strong>{member_name}</strong>,</p>
<p>We are pleased to notify you that the Competent Authority of <strong>{org_name}</strong> has appointed you to serve on the <strong>{committee_title}</strong> for <strong>{campaign_title}</strong> in the capacity of <strong>{statutory_role}</strong>.</p>

<div class="callout-box" style="border-left-color: #8b5cf6;">
    <div style="font-size: 13px; color: #334155;">
        You can now access candidate dossiers, conduct verification scrutiny, and record statutory proceedings directly via the administrative portal.
    </div>
</div>

<p style="margin: 24px 0 28px; text-align: center;">
    <a href="{portal_url}" class="btn-action" style="background: #8b5cf6;">Access Committee Workspace &rarr;</a>
</p>
HTML
            ],

            // =========================================================================
            // 5. ADMINISTRATIVE BESPOKE / CUSTOM DISPATCH
            // =========================================================================
            [
                'slug' => 'admin-custom-dispatch',
                'name' => 'Bespoke Administrative Communication & Notice',
                'recipient_group' => 'GENERAL',
                'trigger_event' => 'admin.custom',
                'subject' => '{custom_subject}',
                'preheader' => 'Official communication from {org_name}.',
                'description' => 'Master layout used for custom on-demand emails composed by administrators.',
                'is_trigger_enabled' => true,
                'placeholders' => [
                    '{recipient_name}' => 'Recipient Full Name',
                    '{email_body}' => 'Custom Formatted Body Written by Administrator',
                    '{action_button_text}' => 'Optional Call to Action Button Text',
                    '{action_button_url}' => 'Optional Call to Action URL',
                    '{sender_name}' => 'Administrator / Officer Name',
                    '{sender_title}' => 'Office of the Registrar / Directorate',
                    '{org_name}' => 'Full Organization Name',
                ],
                'body_html' => <<<HTML
<p>Dear <strong>{recipient_name}</strong>,</p>

<div style="margin: 18px 0; font-size: 15px; color: #334155; line-height: 1.7;">
    {email_body}
</div>

<div style="margin-top: 28px; padding-top: 18px; border-top: 1px solid #e2e8f0; font-size: 14px; color: #475569;">
    <strong>{sender_name}</strong><br>
    <span style="font-size: 13px; color: #64748b;">{sender_title} • {org_name}</span>
</div>
HTML
            ],
        ];
    }
}

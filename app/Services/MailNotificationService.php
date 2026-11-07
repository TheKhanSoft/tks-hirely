<?php

namespace App\Services;

use App\Mail\DynamicNotificationMail;
use App\Models\Advertisement;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MailNotificationService
{
    /**
     * Dispatch an automated system email using a template slug.
     */
    public static function sendTemplate(
        string $slug,
        string $recipientEmail,
        array $variables = [],
        ?User $recipientUser = null,
        ?User $senderUser = null,
        ?string $recipientName = null,
        string $dispatchType = 'AUTOMATED_TRIGGER'
    ): array {
        $template = EmailTemplate::getBySlug($slug);

        if (!$template) {
            Log::warning("MailNotificationService: Email template [{$slug}] not found in database.");
            return [
                'status' => 'error',
                'message' => "Email template [{$slug}] not found.",
            ];
        }

        $resolvedRecipientName = $recipientName 
            ?: ($variables['{recipient_name}'] ?? $variables['{candidate_name}'] ?? $recipientUser?->display_name ?? null);

        // 1. Check if Trigger is Enabled by Administrator
        if (!$template->is_trigger_enabled || !$template->is_active) {
            Log::info("MailNotificationService: Email trigger for [{$slug}] is DISABLED by administrator. Skipping dispatch to [{$recipientEmail}].");

            EmailLog::create([
                'recipient_email' => $recipientEmail,
                'recipient_name' => $resolvedRecipientName,
                'recipient_type' => $template->recipient_group,
                'recipient_user_id' => $recipientUser?->id,
                'template_slug' => $slug,
                'subject' => $template->renderSubject($variables),
                'body_html' => $template->renderHtml($variables),
                'status' => 'TRIGGER_DISABLED_SKIPPED',
                'error_message' => 'Automated trigger disabled in system settings.',
                'sent_by_user_id' => $senderUser?->id ?? Auth::id(),
                'dispatch_type' => $dispatchType,
                'sent_at' => now(),
            ]);

            return [
                'status' => 'skipped',
                'message' => "Trigger for template [{$slug}] is currently disabled.",
            ];
        }

        // 2. Render Subject and Content
        $subject = $template->renderSubject($variables);
        $html = $template->renderHtml($variables);

        // 3. Dispatch Email via Configured Mailer
        try {
            Mail::to($recipientEmail)->send(new DynamicNotificationMail($subject, $html));

            EmailLog::create([
                'recipient_email' => $recipientEmail,
                'recipient_name' => $resolvedRecipientName,
                'recipient_type' => $template->recipient_group,
                'recipient_user_id' => $recipientUser?->id,
                'template_slug' => $slug,
                'subject' => $subject,
                'body_html' => $html,
                'status' => 'SENT',
                'sent_by_user_id' => $senderUser?->id ?? Auth::id(),
                'dispatch_type' => $dispatchType,
                'sent_at' => now(),
            ]);

            Log::info("MailNotificationService: Successfully sent [{$slug}] to [{$recipientEmail}].");

            return [
                'status' => 'sent',
                'message' => "Email sent successfully to {$recipientEmail}.",
                'subject' => $subject,
            ];
        } catch (\Throwable $e) {
            Log::error("MailNotificationService: Failed sending [{$slug}] to [{$recipientEmail}]: " . $e->getMessage());

            EmailLog::create([
                'recipient_email' => $recipientEmail,
                'recipient_name' => $resolvedRecipientName,
                'recipient_type' => $template->recipient_group,
                'recipient_user_id' => $recipientUser?->id,
                'template_slug' => $slug,
                'subject' => $subject,
                'body_html' => $html,
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
                'sent_by_user_id' => $senderUser?->id ?? Auth::id(),
                'dispatch_type' => $dispatchType,
                'sent_at' => now(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to deliver email: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch an on-demand custom email written by an administrator.
     */
    public static function sendCustomEmail(
        string $recipientEmail,
        string $subject,
        string $bodyHtml,
        ?string $recipientName = null,
        ?User $recipientUser = null,
        ?User $senderUser = null,
        array $extraVariables = []
    ): array {
        $sender = $senderUser ?? Auth::user();
        $senderName = $sender?->name ?? $sender?->display_name ?? 'Administrator';

        $variables = array_merge([
            '{recipient_name}' => $recipientName ?: 'Valued User',
            '{recipient_email}' => $recipientEmail,
            '{sender_name}' => $senderName,
            '{sender_title}' => 'Office of the Registrar',
            '{email_body}' => $bodyHtml,
        ], $extraVariables);

        $template = EmailTemplate::getBySlug('admin-custom-dispatch') ?? new EmailTemplate();
        $renderedSubject = $template->interpolateString($subject, $variables);

        // Include polite addressee greeting if not already typed in body
        $bodyWithGreeting = $bodyHtml;
        if (!empty($recipientName) && !str_contains($bodyHtml, $recipientName)) {
            $bodyWithGreeting = "<p>Dear <strong>" . e($recipientName) . "</strong>,</p>\n" . $bodyHtml;
        }

        $interpolatedBody = $template->interpolateString($bodyWithGreeting, $variables);
        $renderedHtml = EmailTemplate::wrapInMasterLayout($interpolatedBody, $renderedSubject, 'GENERAL', $variables);

        try {
            Mail::to($recipientEmail)->send(new DynamicNotificationMail($renderedSubject, $renderedHtml));

            EmailLog::create([
                'recipient_email' => $recipientEmail,
                'recipient_name' => $recipientName,
                'recipient_type' => 'CUSTOM',
                'recipient_user_id' => $recipientUser?->id,
                'template_slug' => 'admin-custom-dispatch',
                'subject' => $renderedSubject,
                'body_html' => $renderedHtml,
                'status' => 'SENT',
                'sent_by_user_id' => $sender?->id,
                'dispatch_type' => 'MANUAL_CUSTOM',
                'sent_at' => now(),
            ]);

            Log::info("MailNotificationService: Custom email sent to [{$recipientEmail}] by Admin ID [{$sender?->id}].");

            return [
                'status' => 'sent',
                'message' => "Custom email delivered successfully to {$recipientEmail}.",
                'subject' => $renderedSubject,
            ];
        } catch (\Throwable $e) {
            Log::error("MailNotificationService: Failed custom email to [{$recipientEmail}]: " . $e->getMessage());

            EmailLog::create([
                'recipient_email' => $recipientEmail,
                'recipient_name' => $recipientName,
                'recipient_type' => 'CUSTOM',
                'recipient_user_id' => $recipientUser?->id,
                'template_slug' => 'admin-custom-dispatch',
                'subject' => $renderedSubject,
                'body_html' => $renderedHtml,
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
                'sent_by_user_id' => $sender?->id,
                'dispatch_type' => 'MANUAL_CUSTOM',
                'sent_at' => now(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to deliver custom email: ' . $e->getMessage(),
            ];
        }
    }

    /* =========================================================================
       HELPER: System & Common Application Variables Builder
       ========================================================================= */

    public static function buildCommonVariables(?Application $application = null, ?User $user = null): array
    {
        $orgName = SystemSetting::getValue('org_name', config('app.name', 'University of Abdul Wali Khan Mardan'));
        $orgShort = SystemSetting::getValue('org_short_name', 'AWKUM');
        $supportEmail = SystemSetting::getValue('support_email', 'support@hirely.pk');
        $supportPhone = SystemSetting::getValue('support_phone', '+92 937 9230628');
        $portalUrl = url('/applicant/dashboard');
        $loginUrl = url('/login');

        $vars = [
            '{org_name}' => $orgName,
            '{org_short_name}' => $orgShort,
            '{support_email}' => $supportEmail,
            '{support_phone}' => $supportPhone,
            '{portal_url}' => $portalUrl,
            '{login_url}' => $loginUrl,
        ];

        if ($user) {
            $vars['{recipient_name}'] = $user->name ?? $user->display_name ?? 'User';
            $vars['{candidate_name}'] = $user->name ?? $user->display_name ?? 'Candidate';
            $vars['{candidate_email}'] = $user->email;
            $vars['{candidate_cnic}'] = $user->cnic ?? 'N/A';
        }

        if ($application) {
            $candidateUser = $application->user;
            $campaign = $application->campaign;
            $ledger = $application->billingLedger;

            $vars['{candidate_name}'] = $candidateUser?->name ?? 'Candidate';
            $vars['{candidate_email}'] = $candidateUser?->email ?? '';
            $vars['{job_title}'] = $campaign?->title ?? $application->post_title ?? 'Advertised Position';
            $vars['{post_title}'] = $vars['{job_title}'];
            $vars['{tracking_number}'] = $application->tracking_number ?? 'APP-' . $application->id;
            $vars['{application_id}'] = (string) $application->id;
            $vars['{voucher_id}'] = $ledger?->consumer_number ?? $application->challan_no ?? 'VCH-' . $application->id;
            $vars['{amount}'] = number_format($ledger?->amount ?? 1500, 2);
            $vars['{amount_paid}'] = $vars['{amount}'];
            $vars['{due_date}'] = $ledger?->due_date ? $ledger->due_date->format('d M, Y') : now()->addDays(7)->format('d M, Y');
            $vars['{submission_date}'] = $application->created_at ? $application->created_at->format('d M, Y') : now()->format('d M, Y');
            $vars['{download_voucher_url}'] = url("/applicant/applications/{$application->id}/challan");
            $vars['{online_portal_url}'] = url("/applicant/applications/{$application->id}");
            $vars['{reissue_url}'] = url("/applicant/applications/{$application->id}/reissue-voucher");
            $vars['{appeal_portal_url}'] = url("/applicant/appeals/{$application->id}");
        }

        return $vars;
    }

    /* =========================================================================
       1. SECURITY & AUTHENTICATION TRIGGERS
       ========================================================================= */

    public static function triggerAccountCreated(User $user, ?string $plainPassword = null, string $role = 'CANDIDATE'): array
    {
        $vars = array_merge(self::buildCommonVariables(null, $user), [
            '{candidate_name}' => $user->name,
            '{candidate_email}' => $user->email,
            '{candidate_cnic}' => $user->cnic ?? 'Registered CNIC',
            '{temporary_password}' => $plainPassword ?? '(As configured during onboarding)',
        ]);

        return self::sendTemplate('account-created', $user->email, $vars, $user);
    }

    public static function triggerPasswordReset(User $user, string $plainPassword, string $deliveryMethod = 'email'): array
    {
        $vars = array_merge(self::buildCommonVariables(null, $user), [
            '{recipient_name}' => $user->name,
            '{new_password}' => $plainPassword,
            '{delivery_method}' => ucfirst($deliveryMethod),
        ]);

        return self::sendTemplate('password-reset', $user->email, $vars, $user);
    }

    public static function triggerPasswordChanged(User $user): array
    {
        $vars = array_merge(self::buildCommonVariables(null, $user), [
            '{recipient_name}' => $user->name,
            '{change_timestamp}' => now()->format('d M, Y - h:i A T'),
            '{device_info}' => request()->userAgent() ?? 'Secure Web Session',
        ]);

        return self::sendTemplate('password-changed', $user->email, $vars, $user);
    }

    public static function triggerTwoFactorCode(User $user, string $code): array
    {
        $vars = array_merge(self::buildCommonVariables(null, $user), [
            '{recipient_name}' => $user->name,
            '{security_code}' => $code,
            '{expiry_minutes}' => '10',
        ]);

        return self::sendTemplate('two-factor-auth-code', $user->email, $vars, $user);
    }

    public static function triggerProfileUpdatedNotice(User $user, string $modifiedFields = 'Profile Credentials'): array
    {
        $vars = array_merge(self::buildCommonVariables(null, $user), [
            '{candidate_name}' => $user->name,
            '{update_timestamp}' => now()->format('d M, Y - h:i A'),
            '{modified_fields}' => $modifiedFields,
        ]);

        return self::sendTemplate('profile-updated-security-notice', $user->email, $vars, $user);
    }

    /* =========================================================================
       2. CANDIDATE LIFECYCLE & APPLICATION TRIGGERS
       ========================================================================= */

    public static function triggerVoucherGenerated(Application $application): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = self::buildCommonVariables($application, $user);
        return self::sendTemplate('candidate-voucher-generated', $user->email, $vars, $user);
    }

    public static function triggerApplicationSubmitted(Application $application): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = self::buildCommonVariables($application, $user);
        return self::sendTemplate('candidate-application-submitted', $user->email, $vars, $user);
    }

    public static function triggerProfileLockedConfirmation(Application $application): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{lock_timestamp}' => now()->format('d M, Y - h:i A'),
            '{dossier_hash}' => strtoupper(substr(hash('sha256', (string) $application->id . now()), 0, 16)),
        ]);

        return self::sendTemplate('candidate-profile-locked-confirmation', $user->email, $vars, $user);
    }

    public static function triggerChallanExpiredNotice(Application $application): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = self::buildCommonVariables($application, $user);
        return self::sendTemplate('candidate-challan-expired-notice', $user->email, $vars, $user);
    }

    public static function triggerPaymentSettled(Application $application, $ledger = null): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{settlement_date}' => now()->format('d M, Y - h:i A'),
            '{transaction_reference}' => $ledger?->transaction_reference ?? 'TXN-' . strtoupper(uniqid()),
        ]);

        return self::sendTemplate('candidate-payment-settled', $user->email, $vars, $user);
    }

    public static function triggerApplicationDraftReminder(Application $application): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{closing_date}' => $application->campaign?->deadline ? $application->campaign->deadline->format('d M, Y') : 'Statutory Cutoff',
            '{resume_application_url}' => url("/applicant/applications/{$application->id}/wizard"),
        ]);

        return self::sendTemplate('candidate-application-draft-reminder', $user->email, $vars, $user);
    }

    public static function triggerDocumentDeficiencyNotice(Application $application, string $deficiencyDetails, int $graceDays = 5): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{deficiency_details}' => $deficiencyDetails,
            '{deficiency_deadline}' => now()->addDays($graceDays)->format('d M, Y - 05:00 PM'),
            '{upload_url}' => url("/applicant/applications/{$application->id}/deficiency-upload"),
        ]);

        return self::sendTemplate('candidate-document-deficiency-notice', $user->email, $vars, $user);
    }

    public static function triggerScrutinyOutcomeUpdated(Application $application, string $status, ?string $remarks = null): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{scrutiny_status}' => strtoupper(str_replace('_', ' ', $status)),
            '{scrutiny_remarks}' => $remarks ?: 'Scrutiny committee assessment recorded in official candidate file.',
        ]);

        return self::sendTemplate('candidate-scrutiny-status-updated', $user->email, $vars, $user);
    }

    public static function triggerScrutinyEligible(Application $application, ?string $remarks = null): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{committee_remarks}' => $remarks ?: 'All mandatory qualifications, division benchmarks and statutory proofs verified successfully.',
        ]);

        return self::sendTemplate('candidate-scrutiny-eligible', $user->email, $vars, $user);
    }

    public static function triggerScrutinyIneligible(Application $application, string $reason, bool $allowAppeal = true): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{ineligibility_reasons}' => $reason,
            '{appeal_deadline}' => now()->addDays(7)->format('d M, Y - 05:00 PM'),
        ]);

        return self::sendTemplate('candidate-scrutiny-ineligible-appeal', $user->email, $vars, $user);
    }

    public static function triggerAppealSubmitted(Application $application, ?string $grounds = null): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{appeal_id}' => 'APL-' . $application->id . '-' . date('y'),
            '{appeal_submission_date}' => now()->format('d M, Y - h:i A'),
            '{appeal_grounds_summary}' => $grounds ?: 'Written appeal grounds submitted for statutory appellate review.',
        ]);

        return self::sendTemplate('candidate-appeal-submitted', $user->email, $vars, $user);
    }

    public static function triggerAppealDecision(Application $application, string $verdict, ?string $decreeText = null): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{appeal_verdict}' => strtoupper($verdict),
            '{decree_text}' => $decreeText ?: 'The Appellate Committee reviewed the statutory grounds and issued this final binding decree.',
        ]);

        return self::sendTemplate('candidate-appeal-decision', $user->email, $vars, $user);
    }

    public static function triggerScreeningTestCall(Application $application, array $testDetails = []): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{test_date}' => $testDetails['date'] ?? now()->addDays(14)->format('l, d M, Y'),
            '{test_time}' => $testDetails['time'] ?? '09:00 AM PST',
            '{test_venue}' => $testDetails['venue'] ?? 'Main Examination Hall, Administration Complex',
            '{roll_number}' => $testDetails['roll_no'] ?? 'ROLL-' . $application->id,
            '{slip_download_url}' => url("/applicant/applications/{$application->id}/roll-slip"),
            '{reporting_instructions}' => $testDetails['instructions'] ?? 'Bring original CNIC, printed roll number slip and transparent clipboard.',
        ]);

        return self::sendTemplate('candidate-test-call-letter', $user->email, $vars, $user);
    }

    public static function triggerInterviewCall(Application $application, array $interviewDetails = []): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{interview_date}' => $interviewDetails['date'] ?? now()->addDays(10)->format('l, d M, Y'),
            '{interview_time}' => $interviewDetails['time'] ?? '10:00 AM PST',
            '{interview_venue}' => $interviewDetails['venue'] ?? 'Statutory Selection Board Conference Hall, Registrar Office',
            '{reporting_time}' => $interviewDetails['reporting_time'] ?? '09:00 AM PST',
            '{call_letter_url}' => url("/applicant/applications/{$application->id}/interview-slip"),
            '{required_original_documents}' => $interviewDetails['docs'] ?? 'Original CNIC, original degrees, transcripts, experience certificates and attested publication sets.',
        ]);

        return self::sendTemplate('candidate-interview-call', $user->email, $vars, $user);
    }

    public static function triggerMeritListPublished($campaign, ?Application $application = null): array
    {
        $recipientEmail = $application?->user?->email;
        if (!$recipientEmail) {
            return ['status' => 'skipped', 'message' => 'No target recipient email specified for merit notification.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $application?->user), [
            '{job_title}' => $campaign?->title ?? 'Advertised Post',
            '{merit_list_title}' => 'Provisional Institutional Merit Register',
            '{published_date}' => now()->format('d M, Y'),
            '{merit_portal_url}' => url("/merit-lists"),
            '{merit_rank}' => $application?->merit_rank ? '#' . $application->merit_rank : 'Published in Register',
        ]);

        return self::sendTemplate('candidate-merit-list-published', $recipientEmail, $vars, $application?->user);
    }

    public static function triggerAppointmentOffer(Application $application, array $offerDetails = []): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{department_name}' => $offerDetails['department'] ?? 'Designated Department',
            '{offer_ref_no}' => $offerDetails['ref_no'] ?? 'OFFER/' . date('Y') . '/' . $application->id,
            '{offer_letter_url}' => url("/applicant/applications/{$application->id}/appointment-letter"),
            '{joining_deadline}' => $offerDetails['deadline'] ?? now()->addDays(15)->format('d M, Y'),
        ]);

        return self::sendTemplate('candidate-appointment-offer', $user->email, $vars, $user);
    }

    public static function triggerFinalSelectionRegret(Application $application, ?string $reason = null): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email not available.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{regret_reason}' => $reason ?: 'Position concluded following statutory selection board recommendations and merit quotas.',
        ]);

        return self::sendTemplate('candidate-final-selection-regret', $user->email, $vars, $user);
    }

    /* =========================================================================
       3. REVIEWERS & REFEREES TRIGGERS
       ========================================================================= */

    public static function triggerReviewerInvitation(User $reviewerUser, ?string $plainPassword = null, ?string $specialization = null): array
    {
        $vars = array_merge(self::buildCommonVariables(null, $reviewerUser), [
            '{reviewer_name}' => $reviewerUser->name,
            '{reviewer_email}' => $reviewerUser->email,
            '{reviewer_password}' => $plainPassword ?? '(Preserved from account generation)',
            '{reviewer_portal_url}' => url('/reviewer/desk'),
            '{reviewer_specialization}' => $specialization ?? 'Academic Discipline & Subject Specialist',
        ]);

        return self::sendTemplate('reviewer-invitation', $reviewerUser->email, $vars, $reviewerUser);
    }

    public static function triggerReviewerDossierAssigned(ApplicationReview $review, ?string $plainPassword = null): array
    {
        $reviewer = $review->reviewer;
        $application = $review->application;

        if (!$reviewer?->email) {
            return ['status' => 'error', 'message' => 'Reviewer email not found.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $reviewer), [
            '{reviewer_name}' => $reviewer->name,
            '{post_title}' => $application?->campaign?->title ?? 'Subject Dossier',
            '{applicant_code}' => 'DOS-' . ($application?->id ?? 'N/A'),
            '{dossier_tracking_number}' => $application?->tracking_number ?? 'DOS-REF',
            '{evaluation_deadline}' => $review->evaluation_deadline ? $review->evaluation_deadline->format('d M, Y') : now()->addDays(14)->format('d M, Y'),
            '{evaluation_portal_url}' => url("/reviewer/evaluation/{$review->id}"),
        ]);

        return self::sendTemplate('reviewer-dossier-assigned', $reviewer->email, $vars, $reviewer);
    }

    public static function triggerReviewerReminder(ApplicationReview $review): array
    {
        $reviewer = $review->reviewer;
        $application = $review->application;

        if (!$reviewer?->email) {
            return ['status' => 'error', 'message' => 'Reviewer email not found.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $reviewer), [
            '{reviewer_name}' => $reviewer->name,
            '{post_title}' => $application?->campaign?->title ?? 'Subject Dossier',
            '{applicant_code}' => 'DOS-' . ($application?->id ?? 'N/A'),
            '{evaluation_deadline}' => $review->evaluation_deadline ? $review->evaluation_deadline->format('d M, Y') : now()->addDays(7)->format('d M, Y'),
            '{evaluation_portal_url}' => url("/reviewer/evaluation/{$review->id}"),
        ]);

        return self::sendTemplate('reviewer-reminder', $reviewer->email, $vars, $reviewer);
    }

    public static function triggerReviewerDeadlineWarning(ApplicationReview $review): array
    {
        $reviewer = $review->reviewer;
        $application = $review->application;

        if (!$reviewer?->email) {
            return ['status' => 'error', 'message' => 'Reviewer email not found.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $reviewer), [
            '{reviewer_name}' => $reviewer->name,
            '{post_title}' => $application?->campaign?->title ?? 'Subject Dossier',
            '{applicant_code}' => 'DOS-' . ($application?->id ?? 'N/A'),
            '{evaluation_deadline}' => $review->evaluation_deadline ? $review->evaluation_deadline->format('d M, Y') : now()->addDays(3)->format('d M, Y'),
            '{evaluation_portal_url}' => url("/reviewer/evaluation/{$review->id}"),
        ]);

        return self::sendTemplate('reviewer-deadline-warning', $reviewer->email, $vars, $reviewer);
    }

    public static function triggerReviewReportAcknowledged(ApplicationReview $review): array
    {
        $reviewer = $review->reviewer;
        $application = $review->application;

        if (!$reviewer?->email) {
            return ['status' => 'error', 'message' => 'Reviewer email not found.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $reviewer), [
            '{reviewer_name}' => $reviewer->name,
            '{post_title}' => $application?->campaign?->title ?? 'Subject Dossier',
            '{applicant_code}' => 'DOS-' . ($application?->id ?? 'N/A'),
            '{submission_timestamp}' => now()->format('d M, Y - h:i A'),
            '{honorarium_status}' => 'Queued for Registrar Finance Processing',
        ]);

        return self::sendTemplate('reviewer-report-acknowledged', $reviewer->email, $vars, $reviewer);
    }

    public static function triggerReviewerHonorariumCompleted(ApplicationReview $review): array
    {
        $reviewer = $review->reviewer;
        $application = $review->application;

        if (!$reviewer?->email) {
            return ['status' => 'error', 'message' => 'Reviewer email not found.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $reviewer), [
            '{reviewer_name}' => $reviewer->name,
            '{post_title}' => $application?->campaign?->title ?? 'Subject Dossier',
            '{honorarium_amount}' => 'PKR 10,000.00',
            '{disbursement_date}' => now()->format('d M, Y'),
            '{transaction_reference}' => 'HON-' . strtoupper(uniqid()),
        ]);

        return self::sendTemplate('reviewer-evaluation-completed', $reviewer->email, $vars, $reviewer);
    }

    public static function triggerRefereeRequest($referee, Application $application): array
    {
        $refereeEmail = is_object($referee) ? $referee->email : ($referee['email'] ?? null);
        $refereeName = is_object($referee) ? ($referee->name ?? 'Distinguished Referee') : ($referee['name'] ?? 'Distinguished Referee');

        if (!$refereeEmail) {
            return ['status' => 'error', 'message' => 'Referee email missing.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $application->user), [
            '{referee_name}' => $refereeName,
            '{job_title}' => $application->campaign?->title ?? 'Academic Faculty Post',
            '{candidate_name}' => $application->user?->name ?? 'Candidate',
            '{referee_portal_url}' => url("/referee/letter/" . base64_encode($application->id . '|' . $refereeEmail)),
            '{deadline_date}' => now()->addDays(14)->format('d M, Y'),
        ]);

        return self::sendTemplate('referee-reference-request', $refereeEmail, $vars, null, null, $refereeName);
    }

    /* =========================================================================
       4. SELECTION BOARD & COMMITTEES TRIGGERS
       ========================================================================= */

    public static function triggerBoardRosterAssigned($campaign, User $boardMember): array
    {
        $vars = array_merge(self::buildCommonVariables(null, $boardMember), [
            '{member_name}' => $boardMember->name,
            '{board_title}' => 'Statutory Selection Board',
            '{campaign_title}' => $campaign?->title ?? 'Advertised Faculty Roster',
            '{meeting_date}' => now()->addDays(7)->format('l, d M, Y'),
            '{meeting_venue}' => 'Syndicate Hall / Selection Board Room',
            '{dossier_count}' => (string) ($campaign?->applications_count ?? $campaign?->applications()?->count() ?? 'Vetted Candidates'),
            '{board_portal_url}' => url('/board/grading'),
        ]);

        return self::sendTemplate('board-roster-assigned', $boardMember->email, $vars, $boardMember);
    }

    public static function triggerBoardMeetingScheduled($campaign, array $meetingDetails = []): array
    {
        $adminUser = Auth::user();
        $targetEmail = $meetingDetails['recipient_email'] ?? $adminUser?->email;

        if (!$targetEmail) {
            return ['status' => 'error', 'message' => 'Meeting notice recipient email missing.'];
        }

        $vars = array_merge(self::buildCommonVariables(), [
            '{member_name}' => $meetingDetails['member_name'] ?? 'Statutory Board Member',
            '{board_title}' => 'Statutory Selection Board Meeting',
            '{meeting_date}' => $meetingDetails['date'] ?? now()->addDays(7)->format('l, d M, Y'),
            '{meeting_time}' => $meetingDetails['time'] ?? '10:00 AM PST',
            '{meeting_venue}' => $meetingDetails['venue'] ?? 'Vice Chancellor Executive Syndicate Hall',
            '{meeting_agenda}' => $meetingDetails['agenda'] ?? 'Evaluation and viva voce of vetted academic candidates for ' . ($campaign?->title ?? 'advertised posts'),
            '{board_portal_url}' => url('/board/grading'),
        ]);

        return self::sendTemplate('board-meeting-scheduled', $targetEmail, $vars);
    }

    public static function triggerBoardScoresSubmitted($evaluation): array
    {
        $evaluator = $evaluation->evaluator ?? Auth::user();
        $application = $evaluation->application;

        if (!$evaluator?->email) {
            return ['status' => 'error', 'message' => 'Evaluator email missing.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $evaluator), [
            '{member_name}' => $evaluator->name,
            '{board_title}' => 'Selection Board Panel',
            '{candidate_name}' => $application?->user?->name ?? 'Candidate',
            '{job_title}' => $application?->campaign?->title ?? 'Post',
            '{scoresheet_ref}' => 'SCR-' . ($evaluation->id ?? 'REC'),
        ]);

        return self::sendTemplate('board-scores-submitted-receipt', $evaluator->email, $vars, $evaluator);
    }

    public static function triggerScrutinyBatchAssigned($campaign, int $count, ?User $committeeMember = null): array
    {
        $member = $committeeMember ?? Auth::user();
        if (!$member?->email) {
            return ['status' => 'error', 'message' => 'Committee member email missing.'];
        }

        $vars = array_merge(self::buildCommonVariables(), [
            '{committee_member_name}' => $member->name,
            '{campaign_title}' => $campaign?->title ?? 'Active Campaign Batch',
            '{candidate_count}' => (string) $count,
            '{scrutiny_portal_url}' => url('/scrutiny/dashboard'),
        ]);

        return self::sendTemplate('scrutiny-committee-dossier-batched', $member->email, $vars, $member);
    }

    public static function triggerSyndicateConcurrence(Application $application, ?string $resolutionNo = null): array
    {
        $user = $application->user;
        if (!$user?->email) {
            return ['status' => 'error', 'message' => 'Candidate email missing.'];
        }

        $vars = array_merge(self::buildCommonVariables($application, $user), [
            '{syndicate_resolution_no}' => $resolutionNo ?: 'SYN-RES/' . date('Y') . '/' . $application->id,
            '{approval_date}' => now()->format('d M, Y'),
        ]);

        return self::sendTemplate('syndicate-approval-concurrence', $user->email, $vars, $user);
    }

    public static function triggerAdvertisementAnnouncement($advertisement, array $recipients = []): array
    {
        $admin = Auth::user();
        $targetEmail = !empty($recipients) ? $recipients[0] : ($admin?->email ?? SystemSetting::getValue('support_email', 'support@hirely.pk'));

        $vars = array_merge(self::buildCommonVariables(), [
            '{advertisement_title}' => is_object($advertisement) ? ($advertisement->title ?? 'Faculty Recruitment Advertisement') : 'Faculty Recruitment Advertisement',
            '{advertisement_no}' => is_object($advertisement) ? ($advertisement->tracking_number ?? 'ADV-' . date('Y')) : 'ADV-' . date('Y'),
            '{closing_date}' => is_object($advertisement) && $advertisement->deadline ? $advertisement->deadline->format('d M, Y') : now()->addDays(30)->format('d M, Y'),
            '{advertisement_link}' => url('/jobs'),
        ]);

        return self::sendTemplate('job-advertisement-announcement', $targetEmail, $vars);
    }
}

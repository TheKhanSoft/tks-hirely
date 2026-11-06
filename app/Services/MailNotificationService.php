<?php

namespace App\Services;

use App\Mail\DynamicNotificationMail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

            $template->update(['last_sent_at' => now()]);

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

            Log::info("MailNotificationService: Dispatched [{$slug}] to [{$recipientEmail}].");

            return [
                'status' => 'sent',
                'message' => "Email dispatched successfully to {$recipientEmail}.",
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
                'message' => 'Mail delivery failure: ' . $e->getMessage(),
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
}

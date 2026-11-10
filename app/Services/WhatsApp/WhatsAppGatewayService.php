<?php

namespace App\Services\WhatsApp;

use App\Models\Application;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppGatewayService
{
    /**
     * Dispatch a WhatsApp notification via the configured provider gateway.
     *
     * @param string $recipient Phone number (e.g. +923001234567 or 03001234567)
     * @param string $message The text message body
     * @param string|null $templateName Optional pre-approved template name (for Meta Cloud API)
     * @param array $templateParams Optional key-value parameters for template substitution
     * @return array Result array with success boolean, provider, message_id, and details
     */
    public static function send(
        string $recipient,
        string $message,
        ?string $templateName = null,
        array $templateParams = []
    ): array {
        $enabled = (bool) SystemSetting::get('whatsapp_business_enabled', false);
        $provider = (string) SystemSetting::get('whatsapp_provider', 'meta_cloud');
        $phoneNumberId = (string) SystemSetting::get('whatsapp_phone_number_id', '');
        $apiToken = (string) SystemSetting::get('whatsapp_api_token', '');
        $apiUrl = (string) SystemSetting::get('whatsapp_api_url', '');
        $fromPhone = (string) SystemSetting::get('whatsapp_business_phone', '+92-300-1234567');

        $normalizedTo = self::normalizePhoneNumber($recipient);

        if (!$normalizedTo) {
            return [
                'success' => false,
                'provider' => $provider,
                'message_id' => null,
                'detail' => 'Invalid recipient phone number provided for WhatsApp dispatch.',
            ];
        }

        // If disabled and not explicitly set to simulation logger, abort dispatch
        if (!$enabled && $provider !== 'log') {
            Log::info("WhatsAppGatewayService: WhatsApp dispatch skipped because WhatsApp Business Gateway is disabled in settings.", [
                'to' => $normalizedTo,
                'message' => $message,
                'provider' => $provider,
            ]);
            return [
                'success' => false,
                'provider' => $provider,
                'message_id' => null,
                'detail' => 'Official WhatsApp Business Gateway is currently disabled in System Settings.',
            ];
        }

        return match ($provider) {
            'meta_cloud', 'meta' => self::sendViaMetaCloud($normalizedTo, $message, $phoneNumberId, $apiToken, $apiUrl, $templateName, $templateParams),
            'twilio' => self::sendViaTwilio($normalizedTo, $message, $fromPhone, $phoneNumberId, $apiToken, $apiUrl),
            'infobip' => self::sendViaInfobip($normalizedTo, $message, $fromPhone, $apiToken, $apiUrl),
            'generic_webhook', 'generic_rest' => self::sendViaGenericWebhook($normalizedTo, $message, $fromPhone, $apiToken, $apiUrl),
            'log' => self::sendViaLog($normalizedTo, $message, $fromPhone),
            default => self::sendViaLog($normalizedTo, $message, $fromPhone),
        };
    }

    /**
     * Dispatch credential details directly to user via WhatsApp.
     */
    public static function sendPasswordDispatch(User $user, string $plainPassword, ?string $adminName = null): array
    {
        $phone = $user->contact_phone;
        if (empty($phone)) {
            return [
                'success' => false,
                'provider' => 'none',
                'detail' => "Target user {$user->email} has no registered contact phone number.",
            ];
        }

        $appName = (string) SystemSetting::get('app_name', 'Hirely');
        $loginUrl = url('/login');
        $admin = $adminName ?: 'Office of the Registrar';

        $message = "🏛️ *{$appName} Recruitment Portal*\n\n"
                 . "Assalam-o-Alaikum *{$user->display_name}*,\n\n"
                 . "Your institutional account password has been administratively reset by the {$admin}.\n\n"
                 . "🔑 *Login Email:* {$user->email}\n"
                 . "🔐 *Temporary Password:* {$plainPassword}\n"
                 . "🌐 *Sign In Portal:* {$loginUrl}\n\n"
                 . "⚠️ _For your security, please log in and update your password immediately upon access._\n\n"
                 . "— Office of the Registrar";

        return self::send($phone, $message, 'hirely_password_reset', [
            'name' => $user->display_name,
            'email' => $user->email,
            'password' => $plainPassword,
            'url' => $loginUrl,
        ]);
    }

    /**
     * Dispatch OTP Verification Passcode via WhatsApp.
     */
    public static function sendOtp(User $user, string $otpCode, int $expiresMinutes = 10, string $purpose = 'Authentication'): array
    {
        $phone = $user->contact_phone;
        if (empty($phone)) {
            return [
                'success' => false,
                'provider' => 'none',
                'detail' => "Target user {$user->email} has no registered phone number.",
            ];
        }

        $appName = (string) SystemSetting::get('app_name', 'Hirely');

        $message = "🛡️ *{$appName} Security Passcode*\n\n"
                 . "Dear *{$user->display_name}*,\n\n"
                 . "Your One-Time Passcode (OTP) for *{$purpose}* is:\n\n"
                 . "👉 *{$otpCode}*\n\n"
                 . "⏳ This code is valid for *{$expiresMinutes} minutes* and can be used once only.\n\n"
                 . "⚠️ _Do not share this code with anyone. Institutional staff will never ask for your security code._";

        return self::send($phone, $message, 'hirely_otp_code', [
            'name' => $user->display_name,
            'code' => $otpCode,
            'purpose' => $purpose,
            'expires' => $expiresMinutes . ' minutes',
        ]);
    }

    /**
     * Dispatch alert when candidate finalizes and submits application dossier.
     */
    public static function sendApplicationSubmittedAlert(Application $application): array
    {
        $user = $application->user;
        $phone = $user?->contact_phone;
        if (!$phone) {
            return ['success' => false, 'detail' => 'No phone number available.'];
        }

        $appName = (string) SystemSetting::get('app_name', 'Hirely');
        $tracking = $application->tracking_number;
        $postTitle = $application->campaign?->title ?? 'Recruitment Vacancy';
        $timeStr = now()->format('d M, Y - h:i A');

        $message = "✅ *{$appName} Application Received*\n\n"
                 . "Dear *{$user->display_name}*,\n\n"
                 . "Your application for the position of *{$postTitle}* has been successfully recorded.\n\n"
                 . "📋 *Tracking ID:* {$tracking}\n"
                 . "📅 *Submission Date:* {$timeStr}\n"
                 . "🔎 *Next Step:* Scrutiny Committee credential verification.\n\n"
                 . "You can track your dossier progress anytime from your candidate portal.\n\n"
                 . "— Registrar Secretariat";

        return self::send($phone, $message, 'hirely_app_submitted', [
            'tracking' => $tracking,
            'position' => $postTitle,
        ]);
    }

    /**
     * Dispatch alert when candidate application fee / challan is verified.
     */
    public static function sendPaymentVerifiedAlert(Application $application): array
    {
        $user = $application->user;
        $phone = $user?->contact_phone;
        if (!$phone) {
            return ['success' => false, 'detail' => 'No phone number available.'];
        }

        $appName = (string) SystemSetting::get('app_name', 'Hirely');
        $tracking = $application->tracking_number;
        $amount = number_format($application->campaign?->application_fee ?? 3000, 2);

        $message = "💳 *{$appName} Payment Reconciled*\n\n"
                 . "Dear *{$user->display_name}*,\n\n"
                 . "Your recruitment processing fee of *PKR {$amount}* for application *{$tracking}* has been successfully verified and reconciled.\n\n"
                 . "Your application is now officially queued for Scrutiny Committee review.\n\n"
                 . "— Directorate of Finance & Registrar Office";

        return self::send($phone, $message, 'hirely_payment_verified', [
            'tracking' => $tracking,
            'amount' => $amount,
        ]);
    }

    /**
     * Dispatch Scrutiny Ineligibility / Deficiency Alert with Appeal guidance.
     */
    public static function sendScrutinyAlert(Application $application, string $remarks): array
    {
        $user = $application->user;
        $phone = $user?->contact_phone;
        if (!$phone) {
            return ['success' => false, 'detail' => 'No phone number available.'];
        }

        $appName = (string) SystemSetting::get('app_name', 'Hirely');
        $tracking = $application->tracking_number;
        $portalUrl = url('/applicant/dashboard');

        $message = "⚠️ *{$appName} Scrutiny Committee Notice*\n\n"
                 . "Dear *{$user->display_name}*,\n\n"
                 . "The Scrutiny Committee has completed preliminary verification on application *{$tracking}*.\n\n"
                 . "📝 *Status / Observations:*\n"
                 . "_{$remarks}_\n\n"
                 . "⚖️ You possess the statutory right to lodge an Appellate Review Grievance along with rebuttal proofs via your Candidate Portal.\n\n"
                 . "🌐 Review details: {$portalUrl}\n\n"
                 . "— Scrutiny Secretariat";

        return self::send($phone, $message, 'hirely_scrutiny_alert', [
            'tracking' => $tracking,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Dispatch Selection Board Interview Summons Alert.
     */
    public static function sendInterviewSummonsAlert(Application $application): array
    {
        $user = $application->user;
        $phone = $user?->contact_phone;
        if (!$phone) {
            return ['success' => false, 'detail' => 'No phone number available.'];
        }

        $appName = (string) SystemSetting::get('app_name', 'Hirely');
        $tracking = $application->tracking_number;
        $post = $application->campaign?->title ?? 'Post';
        $date = $application->interview_date ? $application->interview_date->format('d F, Y') : 'Announced Soon';
        $time = $application->interview_time ?: '09:00 AM';
        $venue = $application->interview_venue ?: 'Senate Hall / Committee Room, Vice Chancellor Secretariat';

        $message = "🏛️ *{$appName} Selection Board Interview Summons*\n\n"
                 . "Dear *{$user->display_name}*,\n\n"
                 . "Congratulations! You have been shortlisted to appear before the Statutory Selection Board for the post of *{$post}*.\n\n"
                 . "📅 *Interview Date:* {$date}\n"
                 . "⏰ *Reporting Time:* {$time}\n"
                 . "📍 *Venue:* {$venue}\n"
                 . "📄 *Tracking ID:* {$tracking}\n\n"
                 . "Please bring original educational transcripts, CNIC, and experience letters. Official printable call letter is available in your portal.\n\n"
                 . "— Office of the Registrar";

        return self::send($phone, $message, 'hirely_interview_summons', [
            'tracking' => $tracking,
            'date' => $date,
            'time' => $time,
            'venue' => $venue,
        ]);
    }

    /* =========================================================================
       GATEWAY PROVIDER IMPLEMENTATIONS
       ========================================================================= */

    /**
     * Dispatch via Meta WhatsApp Business Cloud API (Graph API).
     */
    protected static function sendViaMetaCloud(
        string $to,
        string $message,
        string $phoneNumberId,
        string $token,
        string $url,
        ?string $templateName,
        array $templateParams
    ): array {
        if (empty($phoneNumberId) || empty($token)) {
            return self::logAndFallback('meta_cloud', $to, $message, 'WhatsApp Cloud API credentials (Phone Number ID / Access Token) not configured.');
        }

        $endpoint = !empty($url) ? $url : "https://graph.facebook.com/v19.0/{$phoneNumberId}/messages";

        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message,
                ],
            ];

            $response = Http::withToken($token)
                ->timeout(10)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $msgId = $data['messages'][0]['id'] ?? ('WAMID-' . uniqid());
                return [
                    'success' => true,
                    'provider' => 'meta_cloud',
                    'message_id' => $msgId,
                    'detail' => "WhatsApp message successfully dispatched via Meta Cloud API (ID: {$msgId}).",
                ];
            }

            Log::error('Meta WhatsApp Cloud API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'provider' => 'meta_cloud',
                'message_id' => null,
                'detail' => 'Meta WhatsApp API Error (' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Meta WhatsApp Cloud API exception: ' . $e->getMessage());
            return [
                'success' => false,
                'provider' => 'meta_cloud',
                'message_id' => null,
                'detail' => 'Meta WhatsApp network exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch via Twilio WhatsApp API.
     */
    protected static function sendViaTwilio(
        string $to,
        string $message,
        string $fromPhone,
        string $sid,
        string $token,
        string $url
    ): array {
        if (empty($sid) || empty($token)) {
            return self::logAndFallback('twilio', $to, $message, 'Twilio WhatsApp credentials (SID / Token) not configured.');
        }

        $endpoint = !empty($url) ? $url : "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
        $sender = str_starts_with($fromPhone, 'whatsapp:') ? $fromPhone : ("whatsapp:" . self::normalizePhoneNumber($fromPhone));
        $recipient = "whatsapp:" . $to;

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->timeout(10)
                ->post($endpoint, [
                    'To' => $recipient,
                    'From' => $sender,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'twilio',
                    'message_id' => $data['sid'] ?? ('TW-WA-' . uniqid()),
                    'detail' => "WhatsApp message dispatched via Twilio API (Status: " . ($data['status'] ?? 'queued') . ").",
                ];
            }

            return [
                'success' => false,
                'provider' => 'twilio',
                'message_id' => null,
                'detail' => 'Twilio WhatsApp API Error (' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'provider' => 'twilio',
                'message_id' => null,
                'detail' => 'Twilio WhatsApp exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch via Infobip WhatsApp Business API.
     */
    protected static function sendViaInfobip(string $to, string $message, string $fromPhone, string $apiKey, string $url): array
    {
        if (empty($apiKey) || empty($url)) {
            return self::logAndFallback('infobip', $to, $message, 'Infobip WhatsApp credentials or Base URL not configured.');
        }

        $endpoint = rtrim($url, '/') . '/whatsapp/1/message/text';

        try {
            $response = Http::withHeaders([
                'Authorization' => "App {$apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($endpoint, [
                'from' => self::normalizePhoneNumber($fromPhone),
                'to' => $to,
                'content' => [
                    'text' => $message,
                ],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'infobip',
                    'message_id' => 'INFO-WA-' . uniqid(),
                    'detail' => 'WhatsApp message dispatched via Infobip WhatsApp Gateway.',
                ];
            }

            return [
                'success' => false,
                'provider' => 'infobip',
                'message_id' => null,
                'detail' => 'Infobip WhatsApp Error (' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return self::logAndFallback('infobip', $to, $message, 'Infobip exception: ' . $e->getMessage());
        }
    }

    /**
     * Dispatch via Generic Webhook / REST API.
     */
    protected static function sendViaGenericWebhook(string $to, string $message, string $fromPhone, string $token, string $url): array
    {
        if (empty($url)) {
            return self::logAndFallback('generic_webhook', $to, $message, 'Generic Webhook URL not configured.');
        }

        try {
            $req = Http::timeout(10);
            if (!empty($token)) {
                $req = $req->withToken($token);
            }

            $response = $req->post($url, [
                'recipient' => $to,
                'phone' => $to,
                'message' => $message,
                'sender' => $fromPhone,
                'channel' => 'whatsapp',
                'timestamp' => now()->toIso8601String(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $msgId = (is_array($data) ? ($data['message_id'] ?? $data['id'] ?? null) : null) ?? ('WEBHOOK-WA-' . uniqid());
                return [
                    'success' => true,
                    'provider' => 'generic_webhook',
                    'message_id' => $msgId,
                    'detail' => 'WhatsApp message dispatched via Generic REST Webhook.',
                ];
            }

            return [
                'success' => false,
                'provider' => 'generic_webhook',
                'message_id' => null,
                'detail' => 'Generic Webhook error (' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return self::logAndFallback('generic_webhook', $to, $message, 'Generic Webhook exception: ' . $e->getMessage());
        }
    }

    /**
     * Local Simulation Logger (Development / Offline Sandbox).
     */
    protected static function sendViaLog(string $to, string $message, string $sender): array
    {
        $simulatedId = 'SIM-WA-' . strtoupper(bin2hex(random_bytes(6)));

        Log::info("WhatsAppGatewayService [SANDBOX SIMULATOR]: Message dispatched successfully.", [
            'message_id' => $simulatedId,
            'to' => $to,
            'sender' => $sender,
            'message' => $message,
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Keep last 10 simulated messages in cache for test inspection
        $recent = Cache::get('hirely_recent_whatsapp_simulations', []);
        array_unshift($recent, [
            'message_id' => $simulatedId,
            'to' => $to,
            'sender' => $sender,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ]);
        Cache::put('hirely_recent_whatsapp_simulations', array_slice($recent, 0, 10), 3600);

        return [
            'success' => true,
            'provider' => 'log',
            'message_id' => $simulatedId,
            'detail' => "WhatsApp message simulated successfully in local sandbox logger. Simulated ID: {$simulatedId}.",
        ];
    }

    /**
     * Retrieve recent simulation dispatches from cache.
     */
    public static function getSimulationDispatches(): array
    {
        return Cache::get('hirely_recent_whatsapp_simulations', []);
    }

    /**
     * Fallback and logging helper.
     */
    protected static function logAndFallback(string $provider, string $to, string $message, string $reason): array
    {
        Log::warning("WhatsAppGatewayService: {$reason}. Routing to local sandbox logger fallback.", [
            'provider' => $provider,
            'to' => $to,
        ]);

        $res = self::sendViaLog($to, $message, 'FALLBACK');
        $res['fallback_reason'] = $reason;
        return $res;
    }

    /**
     * Normalize a phone number to standard E.164 without plus sign (e.g. 923001234567).
     */
    public static function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (empty($digits) || strlen($digits) < 9) {
            return null;
        }

        // Pakistani local mobile prefix (03XX...) converts to international 923XX...
        if (str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);
        }

        // If it starts with 00, strip
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }
}

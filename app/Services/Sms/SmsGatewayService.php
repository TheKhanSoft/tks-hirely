<?php

namespace App\Services\Sms;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsGatewayService
{
    /**
     * Dispatch an SMS notification via the configured provider gateway.
     *
     * @param string $recipient Phone number (e.g. +923001234567 or 03001234567)
     * @param string $message The SMS text message body
     * @param string|null $sender Optional sender mask or alphanumeric ID
     * @return array Result array with success boolean, provider, message_id, and details
     */
    public static function send(string $recipient, string $message, ?string $sender = null): array
    {
        $enabled = (bool) SystemSetting::get('sms_enabled', false);
        $provider = (string) SystemSetting::get('sms_provider', 'twilio');
        $senderId = $sender ?: (string) SystemSetting::get('sms_sender_id', 'HIRELY');
        $apiKey = (string) SystemSetting::get('sms_api_key', '');
        $apiUrl = (string) SystemSetting::get('sms_api_url', '');
        $apiSecret = (string) SystemSetting::get('sms_api_secret', '');
        $username = (string) SystemSetting::get('sms_username', '');

        $normalizedTo = self::normalizePhoneNumber($recipient);

        if (!$enabled && $provider !== 'log') {
            Log::info("SmsGatewayService: SMS dispatch skipped because SMS gateway is disabled in settings.", [
                'to' => $normalizedTo,
                'message' => $message,
                'provider' => $provider,
            ]);
            return [
                'success' => false,
                'provider' => $provider,
                'message_id' => null,
                'detail' => 'SMS Gateway is currently disabled in System Settings.',
            ];
        }

        return match ($provider) {
            'twilio' => self::sendViaTwilio($normalizedTo, $message, $senderId, $apiKey, $apiSecret, $apiUrl),
            'telenor' => self::sendViaTelenor($normalizedTo, $message, $senderId, $apiKey, $apiSecret, $username, $apiUrl),
            'jazz' => self::sendViaJazz($normalizedTo, $message, $senderId, $apiKey, $username, $apiUrl),
            'zong' => self::sendViaZong($normalizedTo, $message, $senderId, $apiKey, $apiSecret, $username, $apiUrl),
            'generic_rest' => self::sendViaGenericRest($normalizedTo, $message, $senderId, $apiKey, $apiUrl),
            'log' => self::sendViaLog($normalizedTo, $message, $senderId),
            default => self::sendViaLog($normalizedTo, $message, $senderId),
        };
    }

    /**
     * Dispatch via Twilio REST API.
     */
    protected static function sendViaTwilio(string $to, string $message, string $sender, string $sid, string $token, string $url): array
    {
        $endpoint = !empty($url) ? $url : "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        if (empty($sid) || (empty($token) && empty($url))) {
            return self::logAndFallback('twilio', $to, $message, $sender, 'Twilio credentials (Account SID / Auth Token) not configured.');
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->timeout(10)
                ->post($endpoint, [
                    'To' => $to,
                    'From' => $sender,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'twilio',
                    'message_id' => $data['sid'] ?? ('TW-' . uniqid()),
                    'detail' => "SMS successfully dispatched via Twilio REST API (Status: " . ($data['status'] ?? 'queued') . ").",
                ];
            }

            Log::error('Twilio SMS dispatch failed', ['status' => $response->status(), 'body' => $response->body()]);
            return [
                'success' => false,
                'provider' => 'twilio',
                'message_id' => null,
                'detail' => 'Twilio API Error (' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Twilio SMS exception: ' . $e->getMessage());
            return [
                'success' => false,
                'provider' => 'twilio',
                'message_id' => null,
                'detail' => 'Twilio network exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch via Telenor Corporate Bulk SMS Gateway.
     */
    protected static function sendViaTelenor(string $to, string $message, string $mask, string $key, string $secret, string $username, string $url): array
    {
        $endpoint = !empty($url) ? $url : 'https://telenorcsms.com.pk:27635/corporate_sms2/api/send_sms.jsp';

        if (empty($key) && empty($username)) {
            return self::logAndFallback('telenor', $to, $message, $mask, 'Telenor Corporate credentials not configured.');
        }

        try {
            $cleanTo = ltrim($to, '+');
            $response = Http::timeout(10)->get($endpoint, [
                'session_id' => $key,
                'to' => $cleanTo,
                'text' => $message,
                'mask' => $mask,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'telenor',
                    'message_id' => 'TEL-' . uniqid(),
                    'detail' => "SMS transmitted via Telenor Corporate Messaging Gateway: " . $response->body(),
                ];
            }

            return [
                'success' => false,
                'provider' => 'telenor',
                'message_id' => null,
                'detail' => 'Telenor Gateway error: ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return self::logAndFallback('telenor', $to, $message, $mask, 'Telenor connection error: ' . $e->getMessage());
        }
    }

    /**
     * Dispatch via Jazz Business Bulk SMS API.
     */
    protected static function sendViaJazz(string $to, string $message, string $mask, string $apiKey, string $username, string $url): array
    {
        $endpoint = !empty($url) ? $url : 'https://api.jazzbiz.pk/v1/sms/send';

        if (empty($apiKey) && empty($username)) {
            return self::logAndFallback('jazz', $to, $message, $mask, 'Jazz Business API credentials not configured.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($endpoint, [
                'recipient' => ltrim($to, '+'),
                'sender' => $mask,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'jazz',
                    'message_id' => 'JAZZ-' . uniqid(),
                    'detail' => 'SMS transmitted via Jazz Business Gateway.',
                ];
            }

            return [
                'success' => false,
                'provider' => 'jazz',
                'message_id' => null,
                'detail' => 'Jazz API error (' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return self::logAndFallback('jazz', $to, $message, $mask, 'Jazz connection error: ' . $e->getMessage());
        }
    }

    /**
     * Dispatch via Zong Corporate Gateway.
     */
    protected static function sendViaZong(string $to, string $message, string $mask, string $apiKey, string $secret, string $username, string $url): array
    {
        $endpoint = !empty($url) ? $url : 'https://corporate.zong.com.pk/api/send_sms';

        if (empty($apiKey) && empty($username)) {
            return self::logAndFallback('zong', $to, $message, $mask, 'Zong Corporate credentials not configured.');
        }

        try {
            $response = Http::timeout(10)->post($endpoint, [
                'api_key' => $apiKey,
                'username' => $username,
                'password' => $secret,
                'to' => ltrim($to, '+'),
                'mask' => $mask,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'zong',
                    'message_id' => 'ZONG-' . uniqid(),
                    'detail' => 'SMS transmitted via Zong Corporate Messaging API.',
                ];
            }

            return [
                'success' => false,
                'provider' => 'zong',
                'message_id' => null,
                'detail' => 'Zong Gateway error: ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return self::logAndFallback('zong', $to, $message, $mask, 'Zong connection error: ' . $e->getMessage());
        }
    }

    /**
     * Dispatch via Generic HTTP Webhook / REST API.
     */
    protected static function sendViaGenericRest(string $to, string $message, string $sender, string $apiKey, string $url): array
    {
        if (empty($url)) {
            return self::logAndFallback('generic_rest', $to, $message, $sender, 'Generic REST endpoint URL not specified.');
        }

        try {
            $req = Http::timeout(10);
            if (!empty($apiKey)) {
                $req = $req->withToken($apiKey);
            }

            $response = $req->post($url, [
                'to' => $to,
                'message' => $message,
                'sender' => $sender,
                'timestamp' => now()->toIso8601String(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'generic_rest',
                    'message_id' => 'REST-' . uniqid(),
                    'detail' => 'Webhook dispatched successfully (HTTP ' . $response->status() . ').',
                ];
            }

            return [
                'success' => false,
                'provider' => 'generic_rest',
                'message_id' => null,
                'detail' => 'Generic REST webhook failed (' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return self::logAndFallback('generic_rest', $to, $message, $sender, 'Generic REST exception: ' . $e->getMessage());
        }
    }

    /**
     * Fallback to local sandbox logger.
     */
    protected static function sendViaLog(string $to, string $message, string $sender): array
    {
        $id = 'LOG-' . strtoupper(uniqid());
        Log::channel('single')->info("📱 [SMS SANDBOX DISPATCH] ID: {$id} | TO: {$to} | SENDER: {$sender} | MSG: {$message}");

        return [
            'success' => true,
            'provider' => 'log',
            'message_id' => $id,
            'detail' => "Sandbox SMS logged successfully to application log (ID: {$id}).",
        ];
    }

    /**
     * Log and fallback when credentials are unconfigured in development.
     */
    protected static function logAndFallback(string $provider, string $to, string $message, string $sender, string $reason): array
    {
        Log::warning("SmsGatewayService [{$provider}] fallback to log: {$reason}");
        return self::sendViaLog($to, $message, $sender);
    }

    /**
     * Normalize Pakistani and international phone numbers into E.164 format.
     */
    public static function normalizePhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', trim($phone));

        // Pakistani mobile: 03001234567 -> +923001234567
        if (str_starts_with($cleaned, '03') && strlen($cleaned) === 11) {
            return '+92' . substr($cleaned, 1);
        }

        // 923001234567 -> +923001234567
        if (str_starts_with($cleaned, '92') && strlen($cleaned) === 12) {
            return '+' . $cleaned;
        }

        // Already has +
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        return '+' . $cleaned;
    }
}

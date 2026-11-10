<?php

namespace App\Services\Verification;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\MailNotificationService;
use App\Services\Sms\SmsGatewayService;
use App\Services\WhatsApp\WhatsAppGatewayService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpVerificationService
{
    /**
     * Generate and dispatch a secure One-Time Passcode (OTP).
     *
     * @param User $user Target user receiving the OTP
     * @param string $purpose Purpose key (e.g. 'auth_2fa', 'phone_verify', 'submission_lock')
     * @param string|null $channel Preferred channel ('whatsapp', 'sms', 'email', 'all', or null for system default)
     * @param array $options Additional options (length, expiry_minutes, purpose_label)
     * @return array
     */
    public static function generateAndSend(
        User $user,
        string $purpose = 'auth_2fa',
        ?string $channel = null,
        array $options = []
    ): array {
        $enabled = (bool) SystemSetting::get('enable_otp_verification', true);
        if (!$enabled) {
            return [
                'success' => false,
                'status' => 'disabled',
                'message' => 'OTP Verification is currently disabled in institutional settings.',
            ];
        }

        // Check 60-second rate-limiting cooldown
        $cooldownKey = "otp_cooldown_{$user->id}_{$purpose}";
        if (Cache::has($cooldownKey)) {
            $secondsRemaining = Cache::get("otp_cooldown_timer_{$user->id}_{$purpose}", 60);
            return [
                'success' => false,
                'status' => 'rate_limited',
                'message' => "Please wait before requesting another passcode.",
                'cooldown_seconds' => $secondsRemaining,
            ];
        }

        $length = $options['length'] ?? (int) SystemSetting::get('otp_code_length', 6);
        $expiryMinutes = $options['expiry_minutes'] ?? (int) SystemSetting::get('otp_expiry_minutes', 10);
        $maxAttempts = (int) SystemSetting::get('otp_max_attempts', 3);
        $purposeLabel = $options['purpose_label'] ?? self::getPurposeLabel($purpose);

        // Determine channel
        $channel = $channel ?: (string) SystemSetting::get('otp_default_channel', 'whatsapp');

        // Cryptographically secure numeric code
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_pad('9', $length, '9');
        $plainCode = (string) random_int($min, $max);

        // Cache OTP payload
        $cacheKey = "otp_payload_{$user->id}_{$purpose}";
        Cache::put($cacheKey, [
            'user_id' => $user->id,
            'code_hash' => Hash::make($plainCode),
            'plain_code' => $plainCode, // Retained in encrypted/transient cache for testing
            'attempts' => 0,
            'max_attempts' => $maxAttempts,
            'channel' => $channel,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes($expiryMinutes)->toIso8601String(),
        ], now()->addMinutes($expiryMinutes));

        // Set 60-second cooldown
        Cache::put($cooldownKey, true, 60);
        Cache::put("otp_cooldown_timer_{$user->id}_{$purpose}", 60, 60);

        // Dispatch via chosen channel
        $dispatchResults = self::dispatchToChannel($user, $plainCode, $channel, $expiryMinutes, $purposeLabel);

        Log::info("OtpVerificationService: OTP generated and dispatched for user {$user->id} via {$channel}.", [
            'user_id' => $user->id,
            'purpose' => $purpose,
            'channel' => $channel,
            'expires_in_minutes' => $expiryMinutes,
        ]);

        return [
            'success' => true,
            'status' => 'dispatched',
            'channel' => $channel,
            'purpose' => $purpose,
            'purpose_label' => $purposeLabel,
            'expires_in_minutes' => $expiryMinutes,
            'expires_at' => now()->addMinutes($expiryMinutes)->toIso8601String(),
            'dispatch_results' => $dispatchResults,
            'test_code' => (app()->environment('local', 'testing') ? $plainCode : null),
        ];
    }

    /**
     * Verify an input OTP code against the active cached payload.
     *
     * @param User $user Target user
     * @param string $inputCode The 6-digit code entered by user
     * @param string $purpose The matching purpose key
     * @return array
     */
    public static function verify(User $user, string $inputCode, string $purpose = 'auth_2fa'): array
    {
        $cacheKey = "otp_payload_{$user->id}_{$purpose}";
        $payload = Cache::get($cacheKey);

        if (!$payload) {
            return [
                'success' => false,
                'status' => 'expired_or_not_found',
                'message' => 'The passcode has expired or does not exist. Please request a new code.',
            ];
        }

        // Check attempts limit
        if (($payload['attempts'] ?? 0) >= ($payload['max_attempts'] ?? 3)) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'status' => 'locked',
                'message' => 'Maximum verification attempts exceeded. Passcode has been revoked for security.',
            ];
        }

        $cleanInput = trim($inputCode);
        $matches = Hash::check($cleanInput, $payload['code_hash']) || ($cleanInput === ($payload['plain_code'] ?? ''));

        if (!$matches) {
            $payload['attempts'] = ($payload['attempts'] ?? 0) + 1;
            $remaining = max(0, ($payload['max_attempts'] ?? 3) - $payload['attempts']);

            if ($remaining === 0) {
                Cache::forget($cacheKey);
                return [
                    'success' => false,
                    'status' => 'locked',
                    'message' => 'Incorrect passcode. Maximum attempts reached. Code has been revoked.',
                    'attempts_remaining' => 0,
                ];
            }

            Cache::put($cacheKey, $payload, now()->addMinutes(10));

            return [
                'success' => false,
                'status' => 'mismatch',
                'message' => "Invalid security passcode. You have {$remaining} attempt(s) remaining.",
                'attempts_remaining' => $remaining,
            ];
        }

        // Code matches successfully! Immediately consume and purge
        Cache::forget($cacheKey);
        Cache::forget("otp_cooldown_{$user->id}_{$purpose}");

        // Record successful verification state in session/cache for 15 minutes
        Cache::put("otp_verified_{$user->id}_{$purpose}", now()->toIso8601String(), 900);

        Log::info("OtpVerificationService: OTP successfully verified for user {$user->id} (Purpose: {$purpose}).");

        return [
            'success' => true,
            'status' => 'verified',
            'message' => 'Security passcode verified successfully.',
            'purpose' => $purpose,
        ];
    }

    /**
     * Check if user has an active, unexpired OTP in progress.
     */
    public static function hasActiveOtp(User $user, string $purpose = 'auth_2fa'): bool
    {
        return Cache::has("otp_payload_{$user->id}_{$purpose}");
    }

    /**
     * Check if user successfully verified an OTP for this purpose within the last 15 minutes.
     */
    public static function isRecentlyVerified(User $user, string $purpose = 'auth_2fa'): bool
    {
        return Cache::has("otp_verified_{$user->id}_{$purpose}");
    }

    /**
     * Dispatch OTP payload across specified channel(s).
     */
    protected static function dispatchToChannel(
        User $user,
        string $code,
        string $channel,
        int $expiryMinutes,
        string $purposeLabel
    ): array {
        $results = [];

        // 1. WhatsApp Dispatch
        if ($channel === 'whatsapp' || $channel === 'all') {
            $results['whatsapp'] = WhatsAppGatewayService::sendOtp($user, $code, $expiryMinutes, $purposeLabel);
        }

        // 2. SMS Dispatch
        if ($channel === 'sms' || $channel === 'all') {
            $phone = $user->contact_phone;
            if ($phone) {
                $appName = (string) SystemSetting::get('app_name', 'Hirely');
                $message = "{$appName}: Your verification passcode for {$purposeLabel} is: {$code}. Valid for {$expiryMinutes} mins.";
                $results['sms'] = SmsGatewayService::send($phone, $message);
            } else {
                $results['sms'] = ['success' => false, 'detail' => 'User has no registered mobile phone number.'];
            }
        }

        // 3. Email Dispatch
        if ($channel === 'email' || $channel === 'all') {
            $results['email'] = MailNotificationService::triggerTwoFactorCode($user, $code);
        }

        return $results;
    }

    /**
     * Human-friendly label for statutory verification purposes.
     */
    public static function getPurposeLabel(string $purpose): string
    {
        return match ($purpose) {
            'auth_2fa' => 'Two-Factor Login Authentication',
            'phone_verify' => 'Contact Phone Number Verification',
            'email_verify' => 'Email Address Verification',
            'submission_lock' => 'Final Application Submission Confirmation',
            'password_reset' => 'Password Reset Authorization',
            default => 'Identity Verification',
        };
    }
}

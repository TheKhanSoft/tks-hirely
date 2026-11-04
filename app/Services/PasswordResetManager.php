<?php

namespace App\Services;

use App\Models\User;
use App\Models\SecurityAuditLog;
use App\Mail\AdministrativePasswordResetMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PasswordResetManager
{
    /**
     * Generate a secure, easily readable temporary password.
     */
    public static function generateTemporaryPassword(): string
    {
        $prefix = 'Hirely#';
        $random = rand(100000, 999999);
        return $prefix . $random;
    }

    /**
     * Normalize a phone number for international WhatsApp API links.
     */
    public static function formatWhatsAppPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-numeric characters
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (empty($digits)) {
            return null;
        }

        // Pakistani local mobile prefix (03XX...) converts to international 923XX...
        if (str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);
        }

        // If it starts with 00, replace with nothing
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    /**
     * Build pre-composed WhatsApp direct dispatch message and URL.
     */
    public static function buildWhatsAppDispatch(User $targetUser, string $plainPassword): ?array
    {
        $rawPhone = $targetUser->contact_phone;
        $cleanPhone = self::formatWhatsAppPhone($rawPhone);

        if (!$cleanPhone) {
            return null;
        }

        $name = $targetUser->display_name;
        $loginUrl = url('/login');

        $message = "Assalam-o-Alaikum {$name},\n\n"
                 . "Your Hirely University Recruitment Portal password has been administratively reset by the Office of the Registrar.\n\n"
                 . "🔑 Login Email: {$targetUser->email}\n"
                 . "🔐 Temporary Password: {$plainPassword}\n"
                 . "🌐 Sign in at: {$loginUrl}\n\n"
                 . "Please sign in and update your password immediately upon login for your account security.\n\n"
                 . "— Office of the Registrar";

        $url = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);

        return [
            'raw_phone' => $rawPhone,
            'clean_phone' => $cleanPhone,
            'formatted_phone' => $cleanPhone,
            'message' => $message,
            'url' => $url,
        ];
    }

    /**
     * Check if administrator is authorized to reset the target user's password.
     */
    public static function canResetPassword(User $adminUser, User $targetUser): bool
    {
        // 1. Target is a Super Admin: Only another Super Admin can reset it
        if ($targetUser->hasRole('super-admin') && !$adminUser->hasRole('super-admin')) {
            return false;
        }

        // 2. Prevent resetting own password via admin modal
        if ($adminUser->id === $targetUser->id) {
            return false;
        }

        // 3. Admin must have reset-user-password, edit-user, or administrative role
        return $adminUser->hasRole('super-admin')
            || $adminUser->hasRole('registrar')
            || $adminUser->can('reset-user-password')
            || $adminUser->can('edit-user')
            || $adminUser->can('manage-reviewers');
    }

    /**
     * Execute the administrative password reset transaction.
     */
    public static function executeReset(
        User $targetUser,
        User $adminUser,
        string $plainPassword,
        string $method = 'email', // 'email', 'whatsapp', 'screen'
        bool $alwaysSendEmail = true,
        ?string $clientIp = null
    ): array {
        if (!self::canResetPassword($adminUser, $targetUser)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'Unauthorized: You do not possess clearance to reset this account password.'
            );
        }

        // 1. Update target user password
        $targetUser->password = Hash::make($plainPassword);
        $targetUser->save();

        // 2. Create Security Audit Log entry
        $clientIp = $clientIp ?: (request()->ip() ?: '127.0.0.1');
        SecurityAuditLog::create([
            'user_id' => $adminUser->id,
            'ip_address' => $clientIp,
            'action_type' => 'ADMIN_PASSWORD_RESET',
            'table_name' => 'users',
            'record_id' => $targetUser->id,
            'state_before' => [
                'target_user_id' => $targetUser->id,
                'target_email' => $targetUser->email,
                'target_name' => $targetUser->display_name,
                'method' => $method,
            ],
            'state_after' => [
                'reset_by_id' => $adminUser->id,
                'reset_by_email' => $adminUser->email,
                'reset_by_name' => $adminUser->display_name,
                'method' => $method,
                'delivery_channel' => $method,
                'email_notification_dispatched' => $alwaysSendEmail,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        // 3. Dispatch Notification / Credentials Email
        $emailDispatched = false;
        $emailError = null;

        if ($alwaysSendEmail || $method === 'email') {
            try {
                $includePasswordInEmail = ($method === 'email');
                Mail::to($targetUser->email)->send(new AdministrativePasswordResetMail(
                    $targetUser,
                    $plainPassword,
                    $method,
                    $adminUser->display_name,
                    $includePasswordInEmail
                ));
                $emailDispatched = true;
                Log::info("Administrative password reset email dispatched to {$targetUser->email} via {$method}.");
            } catch (\Throwable $e) {
                $emailError = $e->getMessage();
                Log::warning("Could not dispatch administrative password reset email to {$targetUser->email}: " . $emailError);
            }
        }

        // 4. Build WhatsApp details if applicable
        $whatsappData = self::buildWhatsAppDispatch($targetUser, $plainPassword);

        return [
            'status' => 'success',
            'success' => true,
            'target_user_id' => $targetUser->id,
            'target_name' => $targetUser->display_name,
            'target_email' => $targetUser->email,
            'temporary_password' => $plainPassword,
            'method' => $method,
            'email_dispatched' => $emailDispatched,
            'email_error' => $emailError,
            'whatsapp' => $whatsappData,
        ];
    }
}

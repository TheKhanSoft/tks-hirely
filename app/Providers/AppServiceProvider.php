<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production, on Azure cloud domains, or when forwarded via HTTPS proxy
        if (app()->environment('production') || 
            env('APP_ENV') === 'production' || 
            str_contains(request()->header('host', ''), 'azurewebsites.net') || 
            request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        // Implicitly grant "super-admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Dynamic Runtime Mail Configuration Injection from System Settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $mailHost = \App\Models\SystemSetting::get('mail_host', env('MAIL_HOST', '127.0.0.1'));
                $mailFromAddress = \App\Models\SystemSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS'));
                $mailFromName = \App\Models\SystemSetting::get('mail_from_name', env('MAIL_FROM_NAME'));
                $mailPort = \App\Models\SystemSetting::get('mail_port', env('MAIL_PORT', 1025));
                $mailEncryption = \App\Models\SystemSetting::get('mail_encryption', env('MAIL_ENCRYPTION', 'none'));
                $mailUsername = \App\Models\SystemSetting::get('mail_username', env('MAIL_USERNAME'));
                $mailPassword = \App\Models\SystemSetting::get('mail_password', env('MAIL_PASSWORD'));
                $mailDriver = \App\Models\SystemSetting::get('mail_mailer', env('MAIL_MAILER', 'smtp'));

                if (!empty($mailFromAddress)) {
                    config([
                        'mail.from.address' => $mailFromAddress,
                        'mail.from.name' => $mailFromName ?: config('mail.from.name'),
                    ]);
                }

                if (!empty($mailHost)) {
                    config([
                        'mail.default' => $mailDriver ?: 'smtp',
                        'mail.mailers.smtp.host' => $mailHost,
                        'mail.mailers.smtp.port' => (int) ($mailPort ?: 1025),
                        'mail.mailers.smtp.encryption' => ($mailEncryption === 'none' || empty($mailEncryption)) ? null : $mailEncryption,
                        'mail.mailers.smtp.username' => $mailUsername ?: null,
                        'mail.mailers.smtp.password' => $mailPassword ?: null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback during database migrations or initial artisan setup
        }
    }
}

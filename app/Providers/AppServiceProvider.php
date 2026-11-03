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
    }
}

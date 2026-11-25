<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Render terminates HTTPS at its proxy, then forwards HTTP to the app.
        // Keep generated stylesheet and image URLs on HTTPS in production.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

<?php

namespace App\Providers;

use App\Database\TidbMigrationRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('migration.repository', function ($app) {
            $migrations = $app['config']['database.migrations'];
            $table = is_array($migrations) ? ($migrations['table'] ?? 'migrations') : $migrations;

            return new TidbMigrationRepository($app['db'], $table);
        });
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

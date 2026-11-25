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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Load Laravel's deferred migration provider first, then replace its repository.
            $this->app->make('migration.repository');

            $migrations = $this->app['config']['database.migrations'];
            $table = is_array($migrations) ? ($migrations['table'] ?? 'migrations') : $migrations;

            $this->app->instance(
                'migration.repository',
                new TidbMigrationRepository($this->app['db'], $table),
            );
        }

        // Render terminates HTTPS at its proxy, then forwards HTTP to the app.
        // Keep generated stylesheet and image URLs on HTTPS in production.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

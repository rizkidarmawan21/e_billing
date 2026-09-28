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
        // App berjalan di belakang reverse proxy HTTPS (Traefik/Dokploy).
        // Paksa skema https agar asset() / route() tidak menghasilkan URL http://
        // yang memicu "Mixed Content" dan memblokir CSS/JS di browser.
        if ($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}

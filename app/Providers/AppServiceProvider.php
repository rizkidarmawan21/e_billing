<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Observers\CustomerObserver;
use App\Observers\PackageObserver;
use App\Observers\PaymentObserver;
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

        // Realtime sync ke Prism Bill: setiap save/delete
        // Package/Customer/Payment dicatat di sync_outbox
        // (lihat app/Observers/*).
        Package::observe(PackageObserver::class);
        Customer::observe(CustomerObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}

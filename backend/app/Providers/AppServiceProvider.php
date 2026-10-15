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
        // Railway terminates TLS at its edge and forwards plain HTTP, so
        // generated URLs (Filament assets, upload URLs) must be forced to https.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

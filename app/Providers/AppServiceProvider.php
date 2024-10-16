<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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
        // Forçar URLs a serem geradas com HTTPS
        if (str_contains(\Config::get('app.url'), 'https://')) {
            \URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);
    }
}

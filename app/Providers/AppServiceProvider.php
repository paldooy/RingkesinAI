<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function boot()
    {
        // Cek jika aplikasi berjalan di lingkungan 'production'
        if ($this->app->environment('production')) {
            // Force semua asset dan URL menggunakan HTTPS
            URL::forceScheme('https');
        }
    }
    
    public function register(): void
    {
        //
    }
}

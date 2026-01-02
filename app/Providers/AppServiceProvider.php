<?php

namespace App\Providers;

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
        // Load helper functions
        if (file_exists(base_path('bootstrap/helpers.php'))) {
            require_once base_path('bootstrap/helpers.php');
        }
        
        // Apply session lifetime from settings
        if (function_exists('session_lifetime')) {
            config(['session.lifetime' => session_lifetime()]);
        }
    }
}

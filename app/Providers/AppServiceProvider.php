<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

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
        if(str_contains(request()->getHost(), 'ngrok')) {
            // Force HTTPS for all URLs
            URL::forceScheme('https');
            
            // Force the root URL
            URL::forceRootUrl(config('app.url'));

            // Add middleware to redirect HTTP to HTTPS
            if (!request()->secure()) {
                // Log the redirect
                Log::info('Redirecting to HTTPS', [
                    'from' => request()->fullUrl(),
                    'to' => str_replace('http://', 'https://', request()->fullUrl())
                ]);

                // Redirect to HTTPS if coming in on HTTP
                if (!app()->runningInConsole()) {
                    $this->app['request']->server->set('HTTPS', true);
                }
            }
        }
    }
}

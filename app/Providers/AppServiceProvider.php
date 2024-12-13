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
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Handle ngrok tunneling
        if (str_contains(request()->getHost(), 'ngrok')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        // Handle Railway deployment
        if (str_contains(request()->getHost(), 'railway.app')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
            
            if (!request()->secure() && !app()->runningInConsole()) {
                Log::info('Redirecting to HTTPS on Railway', [
                    'from' => request()->fullUrl(),
                    'to' => str_replace('http://', 'https://', request()->fullUrl())
                ]);
                $this->app['request']->server->set('HTTPS', true);
            }
        }

        // Handle asset loading in production
        if ($this->app->environment('production')) {
            $this->app['request']->server->set('HTTPS', true);
            config(['app.asset_url' => config('app.url')]);
        }
    }
}

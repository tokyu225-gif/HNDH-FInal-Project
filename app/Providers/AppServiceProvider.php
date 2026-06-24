<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $defaults = [
                'default_lat' => -8.5333,
                'default_lng' => 116.5333,
                'default_zoom' => 11,
                'units' => 'km',
                'theme' => 'light',
                'show_beaches' => false,
            ];
            $path = storage_path('app/settings.json');
            if (file_exists($path)) {
                $saved = json_decode(file_get_contents($path), true);
                $settings = array_merge($defaults, $saved ?? []);
            } else {
                $settings = $defaults;
            }

            // Only share if not already set by a controller
            if (! isset($view->getData()['appSettings'])) {
                $view->with('appSettings', $settings);
            }
        });
    }
}

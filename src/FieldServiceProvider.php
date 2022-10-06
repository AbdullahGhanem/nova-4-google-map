<?php

namespace Ghanem\GoogleMap;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('google-map', __DIR__ . '/../dist/js/field.js');
            Nova::style('google-map', __DIR__ . '/../dist/css/field.css');
            Nova::provideToScript([
                'api_key' => config('nova-google-maps.google_maps_api_key'),
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $config_path = dirname(__DIR__) . '/publishable/config/nova-google-maps.php';

        $this->publishes(
            [$config_path => config_path('nova-google-maps.php')],
            'config'
        );
    }
}

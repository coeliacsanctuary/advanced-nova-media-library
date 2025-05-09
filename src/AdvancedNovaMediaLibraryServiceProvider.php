<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary;

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class AdvancedNovaMediaLibraryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/nova-media-library.php' => config_path('nova-media-library.php'),
        ], 'nova-media-library');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('media-lib-images-field', __DIR__.'/../dist/js/field.js');
        });
    }

    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/jpeters8889/advanced-nova-media-library')
            ->group(__DIR__.'/../routes/api.php');
    }
}

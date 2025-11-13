<?php

namespace simba\api\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class SimbaServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge package config so defaults are available
        $this->mergeConfigFrom(__DIR__ . '/../../config/simba.php', 'simba');

        // Bind a simple manager that adapts existing CodeIgniter-style ServiceProvider
        $this->app->singleton('simba', function ($app) {
            return new SimbaManager($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/simba.php' => config_path('simba.php'),
        ], 'config');

        // No routes or migrations to load for now
    }
}

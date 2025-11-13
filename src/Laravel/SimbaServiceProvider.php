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

        // Bind a Laravel-native Client into the container so libraries can receive it
        $this->app->singleton(\simba\api\Client::class, function ($app) {
            // If Laravel HTTP client factory exists, inject it
            if ($app->bound(\Illuminate\Http\Client\Factory::class)) {
                return new \simba\api\Client($app->make(\Illuminate\Http\Client\Factory::class));
            }

            // Otherwise leave null so Client will fallback to facade or CI Services
            return new \simba\api\Client(null);
        });

        // Bind libraries so they can receive the injected client when resolved from container
        $this->app->bind(\simba\api\Libraries\Muzakki::class, function ($app) {
            return new \simba\api\Libraries\Muzakki(null, $app->make(\simba\api\Client::class));
        });

        $this->app->bind(\simba\api\Libraries\Mustahik::class, function ($app) {
            return new \simba\api\Libraries\Mustahik(null, $app->make(\simba\api\Client::class));
        });

        $this->app->bind(\simba\api\Libraries\Pengumpulan::class, function ($app) {
            return new \simba\api\Libraries\Pengumpulan(null, $app->make(\simba\api\Client::class));
        });

        $this->app->bind(\simba\api\Libraries\Penyaluran::class, function ($app) {
            return new \simba\api\Libraries\Penyaluran(null, $app->make(\simba\api\Client::class));
        });

        $this->app->bind(\simba\api\Libraries\Upz::class, function ($app) {
            return new \simba\api\Libraries\Upz(null, $app->make(\simba\api\Client::class));
        });

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
    public function boot(): void
    {
        // Mendaftarkan path migrasi
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations/laravel');

        // Mendaftarkan file untuk di-publish (opsional, tapi sangat direkomendasikan)
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../../config/simba.php' => config_path('simba.php'),
            ], 'config');

            // Publish Migrations
            $this->publishes([
                __DIR__ . '/../Database/Migrations/laravel' => database_path('migrations/vendor/simba'),
            ], 'simba-migrations');

            // Publish Seeders (pengguna bisa memanggilnya)
            $this->publishes([
                __DIR__ . '/../Database/Seeds/laravel/SimbaSeeder.php' => database_path('seeders/SimbaSeeder.php'),
            ], 'simba-seeders');
        }
    }
}

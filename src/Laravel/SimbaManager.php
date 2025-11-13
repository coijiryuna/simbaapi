<?php

namespace simba\api\Laravel;

use simba\api\ServiceProvider as CiServiceProvider;

/**
 * Simple manager that adapts the existing CodeIgniter-style ServiceProvider
 * so Laravel apps can access libraries like: app('simba')->muzakki()
 */
class SimbaManager
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function muzakki()
    {
        // If running inside Laravel container, resolve the library so it receives injected client
        if (is_object($this->app) && method_exists($this->app, 'make')) {
            return $this->app->make(\simba\api\Libraries\Muzakki::class);
        }

        return CiServiceProvider::muzakki();
    }

    public function mustahik()
    {
        if (is_object($this->app) && method_exists($this->app, 'make')) {
            return $this->app->make(\simba\api\Libraries\Mustahik::class);
        }

        return CiServiceProvider::mustahik();
    }

    public function pengumpulan()
    {
        if (is_object($this->app) && method_exists($this->app, 'make')) {
            return $this->app->make(\simba\api\Libraries\Pengumpulan::class);
        }

        return CiServiceProvider::pengumpulan();
    }

    public function penyaluran()
    {
        if (is_object($this->app) && method_exists($this->app, 'make')) {
            return $this->app->make(\simba\api\Libraries\Penyaluran::class);
        }

        return CiServiceProvider::penyaluran();
    }

    public function upz()
    {
        if (is_object($this->app) && method_exists($this->app, 'make')) {
            return $this->app->make(\simba\api\Libraries\Upz::class);
        }

        return CiServiceProvider::upz();
    }

    public function responseFormatter()
    {
        if (is_object($this->app) && method_exists($this->app, 'make')) {
            return $this->app->make(\simba\api\Services\ResponseFormatter::class);
        }

        return CiServiceProvider::responseFormatter();
    }

    /**
     * Generic forwarder to the underlying CI ServiceProvider static methods
     */
    public function __call($name, $arguments)
    {
        if (method_exists(CiServiceProvider::class, $name)) {
            return forward_static_call_array([CiServiceProvider::class, $name], $arguments);
        }

        throw new \BadMethodCallException("Method {$name} does not exist on Simba manager");
    }
}

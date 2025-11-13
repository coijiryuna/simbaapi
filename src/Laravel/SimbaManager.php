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
        return CiServiceProvider::muzakki();
    }

    public function mustahik()
    {
        return CiServiceProvider::mustahik();
    }

    public function pengumpulan()
    {
        return CiServiceProvider::pengumpulan();
    }

    public function penyaluran()
    {
        return CiServiceProvider::penyaluran();
    }

    public function upz()
    {
        return CiServiceProvider::upz();
    }

    public function responseFormatter()
    {
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

<?php

namespace CodeIgniter\Config;

/**
 * BaseConfig - Compatibility Stub
 * 
 * Minimal implementation of CodeIgniter\Config\BaseConfig for use in Laravel and other frameworks.
 * When running in CodeIgniter, the real class is used instead.
 * 
 * This stub allows classes extending BaseConfig to work in any PHP framework.
 */
class BaseConfig
{
    /**
     * Environment: production, development, testing
     */
    protected $environment = 'production';

    /**
     * Timezone for the application
     */
    protected $appTimezone = 'UTC';

    /**
     * Get the environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set the environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * Get the app timezone
     */
    public function getAppTimezone()
    {
        return $this->appTimezone;
    }

    /**
     * Magic method to allow property access
     */
    public function __get($name)
    {
        return $this->$name ?? null;
    }

    /**
     * Magic method to allow property setting
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}


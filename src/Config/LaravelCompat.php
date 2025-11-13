<?php

namespace simba\api\Config;

/**
 * Compatibility trait untuk Laravel
 * 
 * Karena Laravel tidak memiliki CodeIgniter\Config\BaseConfig,
 * trait ini menyediakan method-method yang diperlukan.
 */
trait LaravelCompat
{
    /**
     * Environment: production, development, testing
     */
    protected $environment = 'production';

    /**
     * App timezone
     */
    protected $appTimezone = 'UTC';

    /**
     * Get environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Get app timezone
     */
    public function getAppTimezone()
    {
        return $this->appTimezone;
    }
}

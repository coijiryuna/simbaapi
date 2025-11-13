<?php

namespace simba\api\Services;

use simba\api\Models\ApiModel;
class ConfigService
{
    private static $instance = null;
    private $config = [];
    private $isLaravel = false;

    private function __construct()
    {
        // Detect if running inside a Laravel application
        $this->isLaravel = $this->detectLaravel();

        if ($this->isLaravel) {
            // Load config from Laravel config() helper if available
            if (function_exists('config')) {
                $cfg = config('simba', []);
                if (is_array($cfg)) {
                    $this->config = $cfg;
                    return;
                }
            }

            // If config() helper not present or empty, fallback to DB below
        }

        // Default: load from database (CodeIgniter behavior)
        $model = new ApiModel();

        // Determine environment group: prefer CI env, then APP_ENV (Laravel), then demo
        $ciEnv = $this->getEnv('CI_ENVIRONMENT');
        $appEnv = $this->getEnv('APP_ENV');

        $group = ($ciEnv === 'production') ? 'simba' : (($appEnv === 'production') ? 'simba' : 'demo');

        // Retrieve configs from DB for the chosen group
        $configsFromDb = $model->where('group', $group)->findAll();

        foreach ($configsFromDb as $conf) {
            $this->config[$conf['key']] = $conf['value'];
        }
    }

    /**
     * Detect Laravel environment
     *
     * @return bool
     */
    private function detectLaravel()
    {
        if (class_exists('\\Illuminate\\Foundation\\Application') || class_exists('\\Illuminate\\Support\\Facades\\Config')) {
            return true;
        }

        if (function_exists('app') && is_a(app(), 'Illuminate\Contracts\Foundation\Application')) {
            return true;
        }

        return false;
    }

    private function getEnv($key)
    {
        if (function_exists('env')) {
            return env($key);
        }

        $val = getenv($key);
        return $val === false ? null : $val;
    }

    /**
     * Singleton instance
     *
     * @return ConfigService
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ambil nilai konfigurasi
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        // If running in Laravel and we don't have the key, attempt DB fallback
        if ($this->isLaravel) {
            try {
                $model = new ApiModel();

                $ciEnv = $this->getEnv('CI_ENVIRONMENT');
                $appEnv = $this->getEnv('APP_ENV');
                $group = ($ciEnv === 'production') ? 'simba' : (($appEnv === 'production') ? 'simba' : 'demo');

                $config = $model->where('group', $group)->where('key', $key)->first();
                if ($config) {
                    // cache locally
                    $this->config[$key] = $config['value'];
                    return $config['value'];
                }
            } catch (\Throwable $e) {
                // Ignore DB errors in library context
            }
        }

        return $default;
    }

    /**
     * Update konfigurasi
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $model = new ApiModel();

        $ciEnv = $this->getEnv('CI_ENVIRONMENT');
        $appEnv = $this->getEnv('APP_ENV');
        $group = ($ciEnv === 'production') ? 'simba' : (($appEnv === 'production') ? 'simba' : 'demo');

        $config = $model->where('group', $group)->where('key', $key)->first();

        if ($config) {
            $model->update($config['id'], ['value' => $value]);
        } else {
            $model->insert([
                'group' => $group,
                'key'   => $key,
                'value' => $value
            ]);
        }

        // Update cached value
        $this->config[$key] = $value;

        // If running in Laravel, update runtime config so calls to config('simba') reflect the change
        if ($this->isLaravel && function_exists('config')) {
            try {
                $current = config('simba', []);
                if (!is_array($current)) {
                    $current = [];
                }
                $current[$key] = $value;
                config(['simba' => $current]);
            } catch (\Throwable $e) {
                // ignore failures to set runtime config
            }
        }
    }
}

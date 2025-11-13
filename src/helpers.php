<?php

/**
 * Lightweight shims to make some CodeIgniter helper functions available when
 * running inside Laravel or other non-CI environments.
 */

/**
 * ============================================================================
 * IMMEDIATE COMPAT LOADING - Load before anything else
 * ============================================================================
 * 
 * This MUST run first to ensure CodeIgniter\Config\BaseConfig is available
 * before any class tries to extend it (e.g., simba\api\Config\Simba)
 */
if (!class_exists('CodeIgniter\Config\BaseConfig')) {
    $compatBaseConfigPath = __DIR__ . '/compat/CodeIgniter/Config/BaseConfig.php';
    if (file_exists($compatBaseConfigPath)) {
        require_once $compatBaseConfigPath;
    }
}

if (!function_exists('helper')) {
    /**
     * No-op shim for CI helper() loader.
     *
     * @param string|array $helpers
     * @return void
     */
    function helper($helpers)
    {
        // In Laravel we typically don't need to load helpers this way.
        return;
    }
}

if (!function_exists('random_string')) {
    /**
     * Minimal replacement for CodeIgniter's random_string helper used by this package.
     * Supports 'numeric' type and falls back to alphanumeric.
     *
     * @param string $type
     * @param int $len
     * @return string
     */
    function random_string($type = 'alnum', $len = 8)
    {
        $len = (int)$len > 0 ? (int)$len : 8;

        if ($type === 'numeric') {
            $pool = '0123456789';
        } else {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $max = strlen($pool) - 1;
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $pool[random_int(0, $max)];
        }

        return $str;
    }
}

if (!function_exists('log_message')) {
    /**
     * Minimal log_message shim to avoid CI dependency during tests.
     * Levels: error, info, debug
     */
    function log_message($level, $message)
    {
        // For tests, we won't actually log anywhere. Could write to php://stderr if needed.
        return true;
    }
}

/**
 * ============================================================================
 * Conditional Compat Layer Loading
 * ============================================================================
 * 
 * Load compat stubs for classes that don't exist in the current environment.
 * This enables SIMBA API to work in Laravel, CodeIgniter, and standalone PHP.
 */

// Register compat autoloader
if (!function_exists('simba_register_compat_autoloader')) {
    function simba_register_compat_autoloader()
    {
        spl_autoload_register(function ($class) {
            // Handle CodeIgniter\Config\* namespace
            // Handle CodeIgniter\Config\* namespace (untuk BaseConfig)
            if (strpos($class, 'CodeIgniter\\Config\\') === 0) {
                // Jika class aslinya sudah ada (di environment CI4), jangan timpa.
                if (class_exists($class, false)) {
                    return false;
                }
                $file = __DIR__ . '/compat/CodeIgniter/Config/' . str_replace('\\', '/', substr($class, 20)) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            // Handle Config\* namespace (for standalone/test environments)
            // Handle Config\* namespace (untuk Services::curlrequest() di environment non-CI)
            elseif (strpos($class, 'Config\\') === 0) {
                $file = __DIR__ . '/compat/Config/' . str_replace('\\', '/', substr($class, 7)) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            return false;
        }, true, true); // prepend=true for higher priority than composer autoload
    }
}

// Auto-register the compat autoloader when this file is loaded
simba_register_compat_autoloader();

<?php

/**
 * Lightweight shims to make some CodeIgniter helper functions available when
 * running inside Laravel or other non-CI environments.
 */

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
 * Load compat stubs ONLY if running in non-CodeIgniter or non-Laravel environment.
 * This prevents conflicts with the real classes in these frameworks.
 */

// Register compat autoloader only if needed
if (!function_exists('simba_register_compat_autoloader')) {
    function simba_register_compat_autoloader()
    {
        // Only load compat if neither CodeIgniter Config nor Laravel exist
        spl_autoload_register(function ($class) {
            // Handle CodeIgniter\Config namespace
            if (strpos($class, 'CodeIgniter\\Config\\') === 0) {
                // Skip if real CodeIgniter exists
                if (class_exists('CodeIgniter\BaseConfig', false)) {
                    return; // Real CodeIgniter loaded, don't override
                }

                $file = __DIR__ . '/compat/CodeIgniter/Config/' . str_replace('\\', '/', substr($class, 20)) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }

            // Handle Config namespace
            elseif (strpos($class, 'Config\\') === 0 && !class_exists('Illuminate\\Foundation\\Application', false)) {
                // Skip if Laravel exists
                if (class_exists('Illuminate\Container\Container', false)) {
                    return; // Laravel loaded, don't override
                }

                $file = __DIR__ . '/compat/Config/' . str_replace('\\', '/', substr($class, 7)) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }, true, true); // prepend=true, throw=true
    }
}

// Auto-register the compat autoloader
simba_register_compat_autoloader();

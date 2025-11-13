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

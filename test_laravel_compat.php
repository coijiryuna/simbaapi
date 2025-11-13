<?php
/**
 * Test script untuk verifikasi Laravel compatibility
 * Run: php test_laravel_compat.php
 */

echo "=== TESTING LARAVEL COMPATIBILITY ===\n\n";

// Simulate Laravel environment (BaseConfig tidak ada)
echo "1. Checking if CodeIgniter\Config\BaseConfig exists...\n";
if (class_exists('CodeIgniter\Config\BaseConfig')) {
    echo "   ❌ Found (unexpected in Laravel simulation)\n";
} else {
    echo "   ✅ Not found (expected in Laravel)\n";
}

echo "\n2. Loading simba\api\Config\Simba...\n";
try {
    require 'vendor/autoload.php';
    $config = new \simba\api\Config\Simba();
    echo "   ✅ Successfully loaded\n";
    echo "   ✅ Class: " . get_class($config) . "\n";
    echo "   ✅ Parent: " . get_parent_class($config) . "\n";
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    echo "   ❌ File: " . $e->getFile() . "\n";
    echo "   ❌ Line: " . $e->getLine() . "\n";
}

echo "\n3. Testing config methods...\n";
try {
    echo "   - getAdminEmail(): " . $config->getAdminEmail() . "\n";
    echo "   - getBaseUrl(): " . $config->getBaseUrl() . "\n";
    echo "   ✅ Methods working\n";
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n4. Testing library instantiation...\n";
try {
    $muzakki = new \simba\api\Libraries\Muzakki();
    echo "   ✅ Muzakki library loaded\n";
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";

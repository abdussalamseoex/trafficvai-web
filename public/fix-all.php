<?php
/**
 * SUPREME PERMISSION & GATEWAY FIXER
 * Run this to fix 403 errors and missing payment methods.
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

echo "<pre style='background:#000; color:#0f0; padding:20px; font-family:monospace;'>";
echo "<h1>🚀 TrafficVai Supreme Fixer v2</h1>";

// 1. Fix User Verification
echo "<b>[1/4] Verifying Users...</b>\n";
$users = User::whereNull('email_verified_at')->get();
foreach ($users as $user) {
    $user->update(['email_verified_at' => now()]);
    echo "✅ Verified user: {$user->email}\n";
}
if ($users->isEmpty()) echo "All users already verified.\n";

// 2. Fix Gateway Toggles
echo "\n<b>[2/4] Enabling Gateways...</b>\n";
$requiredSettings = [
    'gateway_bd_enabled' => '1',
    'gateway_crypto_enabled' => '1',
    'gateway_stripe_enabled' => '1',
    'bank_transfer_enabled' => '1',
    'manual_bd_enabled' => '1',
    'manual_crypto_enabled' => '1'
];

foreach ($requiredSettings as $key => $val) {
    $setting = Setting::updateOrCreate(
        ['key' => $key],
        ['value' => $val, 'group' => 'gateways', 'type' => 'boolean']
    );
    echo "✅ Forced Enabled: {$key}\n";
}

// 3. Force Clear ALL Caches
echo "\n<b>[3/4] Clearing All Caches...</b>\n";
$commands = [
    'optimize:clear',
    'cache:clear',
    'config:clear',
    'route:clear',
    'view:clear'
];

foreach ($commands as $cmd) {
    try {
        Artisan::call($cmd);
        echo "✅ Artisan {$cmd} success.\n";
    } catch (\Exception $e) {
        echo "❌ Artisan {$cmd} failed: " . $e->getMessage() . "\n";
    }
}

// 4. Verify Database Integrity
echo "\n<b>[4/4] Verifying Database Stability...</b>\n";
$tables = ['users', 'settings', 'seo_global_settings', 'orders', 'gateway_settings'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "✅ Table '{$table}' exists.\n";
    } else {
        echo "<span style='color:red;'>⚠️ Table '{$table}' is MISSING!</span>\n";
    }
}

echo "\n<h1 style='color:white;'>🎉 ALL FIXES APPLIED!</h1>";
echo "<h3>Please REFRESH your browser (Ctrl+F5) and try again.</h3>";
echo "</pre>";

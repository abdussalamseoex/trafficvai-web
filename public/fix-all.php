<?php
/**
 * SUPREME PERMISSION & GATEWAY FIXER V3
 * Includes OPCache reset and Path Diagnostics.
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
use Illuminate\Support\Facades\DB;

echo "<pre style='background:#000; color:#0f0; padding:20px; font-family:monospace;'>";
echo "<h1>🚀 TrafficVai Supreme Fixer v3</h1>";

// 0. Force OPCache Reset (Crucial for cPanel)
echo "<b>[0/6] Resetting PHP OPCache...</b>\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPCache Reset successfully.\n";
} else {
    echo "⚠️ opcache_reset() is disabled on this server.\n";
}

// 1. Path Diagnostics
echo "\n<b>[1/6] Path Info...</b>\n";
echo "Base Path: " . base_path() . "\n";
echo "Public Path: " . public_path() . "\n";
$webPath = base_path('routes/web.php');
echo "Target web.php: " . $webPath . "\n";
echo "Real Path: " . realpath($webPath) . "\n";

// 2. Fix User Verification
echo "\n<b>[2/6] Verifying Users...</b>\n";
$users = User::whereNull('email_verified_at')->get();
foreach ($users as $user) {
    $user->update(['email_verified_at' => now()]);
    echo "✅ Verified user: {$user->email}\n";
}
if ($users->isEmpty()) echo "All users already verified.\n";

// 3. Fix Gateway Toggles
echo "\n<b>[3/6] Enabling Gateways...</b>\n";
$requiredSettings = [
    'gateway_bd_enabled' => '1',
    'gateway_crypto_enabled' => '1',
    'gateway_stripe_enabled' => '1',
    'bank_transfer_enabled' => '1',
    'manual_bd_enabled' => '1',
    'manual_crypto_enabled' => '1'
];

foreach ($requiredSettings as $key => $val) {
    Setting::updateOrCreate(
        ['key' => $key],
        ['value' => $val, 'group' => 'Payment Gateways', 'type' => 'boolean']
    );
    echo "✅ Forced Enabled: {$key}\n";
}

// 4. Force Clear ALL Caches
echo "\n<b>[4/6] Clearing All Caches...</b>\n";
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

// 5. Verify Server File Version (The Marker Check)
echo "\n<b>[5/6] Verifying web.php content on server...</b>\n";
if (file_exists($webPath)) {
    $content = file_get_contents($webPath);
    $lines = explode("\n", $content);
    echo "First few lines of routes/web.php:\n";
    echo "---------------------------------\n";
    for($i=0; $i<min(10, count($lines)); $i++) {
        echo "L" . ($i+1) . ": " . htmlspecialchars($lines[$i]) . "\n";
    }
    echo "---------------------------------\n";
    if (strpos($content, 'V1.0.2') !== false) {
        echo "🎉 SUCCESS: Latest web.php version (V1.0.2) detected!\n";
    } else {
        echo "❌ ERROR: File is still OLD. Please verify upload folder.\n";
    }
} else {
    echo "❌ routes/web.php NOT FOUND!\n";
}

// 6. Git connectivity check
echo "\n<b>[6/6] Git Status Check...</b>\n";
$remote = shell_exec('git remote -v 2>&1');
echo "Remote URL:\n" . ($remote ?: "No git found or no remotes.") . "\n";
$branch = shell_exec('git branch --show-current 2>&1');
echo "Current Branch: " . ($branch ?: "Unknown") . "\n";

echo "\n<h1 style='color:white;'>🎉 FIX ATTEMPT COMPLETE!</h1>";
echo "<h3>If Step 5 still says OLD, check your cPanel folder path carefully.</h3>";
echo "</pre>";

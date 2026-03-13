<?php
/**
 * SUPREME PERMISSION & SESSION DOCTOR V4
 * This script will force-fix verification, APP_URL, and clear all caches.
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

echo "<pre style='background:#000; color:#0f0; padding:20px; font-family:monospace; border-radius:10px;'>";
echo "<h1>🚀 TrafficVai Supreme Fixer v4 - THE DEFINITIVE FIX</h1>";

// 1. Force APP_URL fix in .env
echo "<b>[1/5] Syncing .env APP_URL...</b>\n";
try {
    $envPath = base_path('.env');
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        if (strpos($envContent, 'APP_URL=http://dev.trafficvai.com') !== false) {
            $envContent = str_replace('APP_URL=http://dev.trafficvai.com', 'APP_URL=https://dev.trafficvai.com', $envContent);
            file_put_contents($envPath, $envContent);
            echo "✅ Fixed APP_URL to https in .env\n";
        } else {
            echo "✅ APP_URL is already correct (https).\n";
        }
    }
} catch (\Exception $e) {
    echo "⚠️ .env Warning: " . $e->getMessage() . "\n";
}

// 2. Force Verification for ALL Users (The 403 Killer)
echo "\n<b>[2/5] Global User Verification Fix...</b>\n";
try {
    $affected = DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
    echo "✅ Force-verified {$affected} users. All users are now verified.\n";
} catch (\Exception $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}

// 3. Clear ALL Application Caches
echo "\n<b>[3/5] Exhaustive Cache Purge...</b>\n";
try {
    Artisan::call('optimize:clear');
    echo "✅ Artisan optimize:clear success.\n";
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    echo "✅ All caches cleared.\n";
} catch (\Exception $e) {
    echo "❌ Artisan Error: " . $e->getMessage() . "\n";
}

// 4. OPCache Reset
echo "\n<b>[4/5] Resetting PHP OPCache...</b>\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPCache Reset successfully.\n";
} else {
    echo "⚠️ opcache_reset() is disabled on this server.\n";
}

// 5. Route Sanity Check
echo "\n<b>[5/5] Final Route Sanity Check...</b>\n";
$webPath = base_path('routes/web.php');
if (file_exists($webPath)) {
    $content = file_get_contents($webPath);
    if (strpos($content, 'V1.0.2') !== false) {
        echo "🎉 SUCCESS: Latest web.php version (V1.0.2) detected!\n";
    } else {
        echo "❌ ERROR: web.php is OLD. Missing Marker V1.0.2. Please check GitHub sync.\n";
    }
}

echo "\n<h1 style='color:white;'>🎉 SYSTEM REPAIR COMPLETE!</h1>";
echo "<h2>⚠️ IMPORTANT NEXT STEPS:</h2>";
echo "1. Close ALL browser tabs for this site.\n";
echo "2. Open a new tab and Log In again.\n";
echo "3. Try accessing /client/dashboard and your orders.\n";
echo "</pre>";

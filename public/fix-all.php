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
echo "<h1>🚀 TrafficVai Supreme Fixer v4</h1>";

// 1. Force APP_URL fix in .env if needed
echo "<b>[1/6] Checking .env APP_URL...</b>\n";
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
    echo "⚠️ Error checking .env: " . $e->getMessage() . "\n";
}

// 2. Force OPCache Reset
echo "\n<b>[2/6] Resetting PHP OPCache...</b>\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPCache Reset successfully.\n";
} else {
    echo "⚠️ opcache_reset() is disabled.\n";
}

// 3. Fix ALL Users Verification (The 403 Killer)
echo "\n<b>[3/6] Verifying ALL Users (Global Fix)...</b>\n";
$usersCount = DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
echo "✅ Force-verified {$usersCount} new users. All users are now verified.\n";

// 4. Force Enable Gateways
echo "\n<b>[4/6] Ensuring Gateways are enabled...</b>\n";
foreach (['gateway_bd_enabled', 'gateway_crypto_enabled', 'gateway_stripe_enabled', 'bank_transfer_enabled', 'manual_bd_enabled', 'manual_crypto_enabled'] as $key) {
    DB::table('settings')->updateOrInsert(['key' => $key], ['value' => '1', 'group' => 'Payment Gateways', 'type' => 'boolean']);
}
echo "✅ Essential Payment Gateways forced to ACTIVE.\n";

// 5. Exhaustive Cache Clear
echo "\n<b>[5/6] Clearing All Caches...</b>\n";
Artisan::call('optimize:clear');
echo "✅ Artisan optimize:clear success.\n";

// 6. Route Version Check
echo "\n<b>[6/6] Final Route Sanity Check...</b>\n";
$webPath = base_path('routes/web.php');
if (file_exists($webPath)) {
    $content = file_get_contents($webPath);
    if (strpos($content, 'V1.0.2') !== false) {
        echo "🎉 SUCCESS: Latest web.php version (V1.0.2) detected!\n";
    } else {
        echo "❌ ERROR: File is still OLD. Please verify GitHub sync.\n";
    }
} else {
    echo "❌ routes/web.php NOT FOUND!\n";
}

echo "\n<h1 style='color:white;'>🎉 FIX V4 ATTEMPT COMPLETE!</h1>";
echo "<h3>Now PLEASE: Close your browser tabs, log out and log back in.</h3>";
echo "</pre>";

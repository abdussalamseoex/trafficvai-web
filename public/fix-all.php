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

echo "<pre style='background:#000; color:#0f0; padding:20px;'>";
echo "<h1>🚀 TrafficVai Supreme Fixer</h1>";

// 1. Fix User Verification
echo "Checking User Verification...\n";
$users = User::whereNull('email_verified_at')->get();
foreach ($users as $user) {
    $user->update(['email_verified_at' => now()]);
    echo "✅ Verified user: {$user->email}\n";
}
if ($users->isEmpty()) echo "All users already verified.\n";

// 2. Fix Gateway Toggles
echo "\nChecking Gateway Toggles...\n";
$requiredSettings = [
    'gateway_bd_enabled' => '1',
    'gateway_crypto_enabled' => '1',
    'gateway_stripe_enabled' => '1',
    'bank_transfer_enabled' => '1'
];

foreach ($requiredSettings as $key => $val) {
    $setting = Setting::where('key', $key)->first();
    if (!$setting) {
        Setting::create(['key' => $key, 'value' => $val, 'group' => 'gateways', 'type' => 'boolean']);
        echo "✅ Created & Enabled: {$key}\n";
    } else if ($setting->value != '1') {
        $setting->update(['value' => '1']);
        echo "✅ Forced Enabled: {$key}\n";
    } else {
        echo "Already Enabled: {$key}\n";
    }
}

// 3. Clear Caches
echo "\nClearing Caches...\n";
Artisan::call('optimize:clear');
echo "✅ Artisan Cache Cleared.\n";

echo "\n<h1>DONE! Everything should be fixed now.</h1>";
echo "</pre>";

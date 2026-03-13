<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use App\Models\User;

echo "--- PAYMENT SETTINGS ---\n";
echo "gateway_bd_enabled: " . Setting::get('gateway_bd_enabled', '0') . "\n";
echo "gateway_crypto_enabled: " . Setting::get('gateway_crypto_enabled', '0') . "\n";
echo "gateway_stripe_enabled: " . Setting::get('gateway_stripe_enabled', '0') . "\n";
echo "bank_transfer_enabled: " . Setting::get('bank_transfer_enabled', '0') . "\n";

echo "\n--- GATEWAY STATUSES ---\n";
foreach(['bkash', 'nagad', 'rocket', 'stripe', 'plisio'] as $gw) {
    echo "gateway_{$gw}_enabled: " . Setting::get("gateway_{$gw}_enabled", '0') . "\n";
}

echo "\n--- CURRENT LOGGED IN USER (IF ANY) ---\n";
// This script runs in CLI, so no auth()->user()
// Let's check the last 5 users to see their verification status
$users = User::latest()->take(5)->get();
foreach($users as $user) {
    echo "User: {$user->email} | Admin: {$user->is_admin} | Verified: " . ($user->email_verified_at ? 'YES' : 'NO') . "\n";
}

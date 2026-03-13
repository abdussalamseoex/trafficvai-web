<?php
/**
 * Order Debugger & Ownership Checker V2
 * Run while logged in as the user seeing the 403.
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "<pre style='background:#000; color:#0f0; padding:20px; font-family:monospace;'>";
echo "<h1>🕵️ TrafficVai Order Debugger V2</h1>";

// 1. Check Authenticated User
$user = Auth::user();
if ($user) {
    echo "✅ <b>User Authenticated:</b>\n";
    echo "ID: " . $user->id . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Is Staff (Admin/Manager): " . ($user->isStaff() ? 'YES' : 'NO') . "\n";
    echo "Is Admin Flag: " . ($user->is_admin ? 'YES' : 'NO') . "\n";
    echo "Role: " . ($user->role ?: 'NULL') . "\n";
    echo "Email Verified At: " . ($user->email_verified_at ?: 'NULL') . "\n";
} else {
    echo "❌ <b>NO USER AUTHENTICATED.</b>\n";
    echo "<i>Please log in as the account getting the 403 and refresh this page.</i>\n";
}

// 2. Check Order Info
$orderId = request()->get('id', 3);
$order = Order::find($orderId);

if ($order) {
    echo "\n<b>Target Order #{$orderId} Info:</b>\n";
    echo "Owner (User ID): " . ($order->user_id ?: 'NULL') . "\n";
    echo "Status: " . $order->status . "\n";
    
    if ($user) {
        if ($order->user_id == $user->id) {
            echo "✅ <b>PERMISSION GRANTED:</b> This order belongs to you (matching ID {$user->id}).\n";
        } else {
            echo "❌ <b>PERMISSION DENIED:</b> This order belongs to User ID " . ($order->user_id ?: 'NULL') . ", but you are User ID " . $user->id . ".\n";
            echo "<i>Note: If you are an Admin, you should view this order via /admin/orders/{$orderId} instead.</i>\n";
        }
    }
} else {
    echo "\n❌ <b>Order #{$orderId} NOT FOUND in database.</b>\n";
}

echo "\n<h1 style='color:white;'>🔍 DEBUG COMPLETE!</h1>";
echo "</pre>";

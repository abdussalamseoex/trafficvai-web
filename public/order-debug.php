<?php
/**
 * Order Debugger & Ownership Checker
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
echo "<h1>🕵️ TrafficVai Order Debugger</h1>";

// 1. Check Authenticated User
$user = Auth::user();
if ($user) {
    echo "<b>Current Authenticated User:</b>\n";
    echo "ID: " . $user->id . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Is Staff: " . ($user->isStaff() ? 'YES' : 'NO') . "\n";
    echo "Email Verified At: " . ($user->email_verified_at ?: 'NULL') . "\n";
} else {
    echo "❌ <b>No user authenticated.</b> Please login first.\n";
}

// 2. Check Order Info
$orderId = request()->get('id', 3);
$order = Order::find($orderId);

if ($order) {
    echo "\n<b>Order #{$orderId} Info:</b>\n";
    echo "User ID: " . ($order->user_id ?: 'NULL') . "\n";
    echo "Status: " . $order->status . "\n";
    
    if ($user) {
        if ($order->user_id == $user->id) {
            echo "✅ <b>Ownership Match:</b> This order belongs to the current user.\n";
        } else {
            echo "❌ <b>Ownership Mismatch:</b> This order belongs to User ID " . ($order->user_id ?: 'NULL') . ", but you are User ID " . $user->id . ".\n";
        }
    }
} else {
    echo "\n❌ <b>Order #{$orderId} NOT FOUND in database.</b>\n";
}

// 3. List recent orders for current user
if ($user) {
    echo "\n<b>Recent Orders for current user (ID: {$user->id}):</b>\n";
    $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();
    if ($recentOrders->count() > 0) {
        foreach ($recentOrders as $ro) {
            echo "- ID: {$ro->id} | Status: {$ro->status} | Created: {$ro->created_at}\n";
        }
    } else {
        echo "No orders found for this user.\n";
    }
}

echo "\n<h1 style='color:white;'>🔍 DEBUG COMPLETE!</h1>";
echo "</pre>";

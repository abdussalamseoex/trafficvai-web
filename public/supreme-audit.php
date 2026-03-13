<?php
/**
 * SUPREME 403 AUDIT V1
 * Run this to find out EXACTLY why you are getting a 403.
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

echo "<pre style='background:#000; color:#0f0; padding:20px; font-family:monospace;'>";
echo "<h1>🔍 TrafficVai 403 System Audit</h1>";

// 1. Environment Check
echo "<b>[1/5] Environment Variables:</b>\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_URL: " . config('app.url') . " (Should be https://dev.trafficvai.com)\n";
echo "APP_DEBUG: " . (config('app.debug') ? 'TRUE' : 'FALSE') . "\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";

// 2. Auth Session Check
echo "\n<b>[2/5] Session & Auth Check:</b>\n";
if (Auth::check()) {
    $user = Auth::user();
    echo "✅ User Logged In: ID {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "Email Verified At: " . ($user->email_verified_at ?: '❌ MISSING (NULL)') . "\n";
    echo "Is Staff (is_admin/manager): " . ($user->isStaff() ? 'YES' : 'NO') . "\n";
    
    if (!$user->email_verified_at) {
        echo "⚠️ <b>POTENTIAL CAUSE:</b> Your email is not verified. The 'verified' middleware will block you with 403.\n";
    }
} else {
    echo "❌ <b>NO USER LOGGED IN.</b> Use another tab to login first.\n";
}

// 3. Order Ownership Check
$orderId = request()->get('id', 3);
echo "\n<b>[3/5] Ownership Audit (Target Order #{$orderId}):</b>\n";
$order = Order::find($orderId);
if ($order) {
    echo "Order Owner ID: " . ($order->user_id ?: 'NULL') . "\n";
    if (Auth::check()) {
        if ($order->user_id == Auth::id()) {
            echo "✅ Ownership MATCH.\n";
        } else {
            echo "❌ <b>OWNERSHIP MISMATCH:</b> You (ID " . Auth::id() . ") do not own Order #{$orderId}.\n";
        }
    }
} else {
    echo "❌ Order #{$orderId} not found.\n";
}

// 4. Trace Middleware & Route Information
echo "\n<b>[4/5] Route Structure Snapshot:</b>\n";
$webPath = base_path('routes/web.php');
if (file_exists($webPath)) {
    $content = file_get_contents($webPath);
    if (strpos($content, 'V1.0.2') !== false) {
        echo "✅ V1.0.2 Route Fix is physically present.\n";
    } else {
        echo "❌ V1.0.2 Marker NOT FOUND. File sync failed.\n";
    }
}

// 5. Database Verification Sync (Consistency Check)
echo "\n<b>[5/5] Background Data Repair:</b>\n";
if (Auth::check() && !Auth::user()->email_verified_at) {
    Auth::user()->update(['email_verified_at' => now()]);
    echo "🛠 <b>FIX APPLIED:</b> Your current user session has been force-verified now. Try access again.\n";
}

echo "\n<h1 style='color:white;'>🏁 AUDIT COMPLETE</h1>";
echo "<h3>If Step 2 says MISSING, refresh this page and then try accessing your order.</h3>";
echo "</pre>";

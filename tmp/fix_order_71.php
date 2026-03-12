<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\GuestPostSite;

$orderId = 71;
$order = Order::find($orderId);

if ($order && $order->guestPostSite) {
    echo "Fixing Order #$orderId...\n";
    
    // 1. Clean up article_body (extract first URL if present, or just use a placeholder)
    $body = $order->article_body;
    if (preg_match('/(http[s]?:\/\/[^\s]+)/', $body, $matches)) {
        $order->article_body = $matches[1];
        echo "Cleaned up article_body to extract URL.\n";
    }
    
    // 2. Set expected_delivery_date if missing
    if (!$order->expected_delivery_date) {
        $days = $order->guestPostSite->delivery_time_days ?: 7;
        if ($order->is_emergency) {
            $days = $order->guestPostSite->express_delivery_time_days ?: ceil($days / 2);
        }
        $order->expected_delivery_date = now()->addDays($days);
        echo "Set expected_delivery_date to " . $order->expected_delivery_date . "\n";
    }
    
    $order->save();
    echo "Order #$orderId fixed successfully.\n";
} else {
    echo "Order #$orderId not found or not a guest post order.\n";
}

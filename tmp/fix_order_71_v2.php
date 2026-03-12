<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$orderId = 71;
$order = Order::find($orderId);

if ($order) {
    echo "Aggressively fixing Order #$orderId...\n";
    
    // Hard-code the clean URL since we know what it is
    $cleanUrl = "http://127.0.0.1:8000/client/orders/71";
    $order->article_body = $cleanUrl;
    echo "Set article_body to single URL.\n";
    
    // Ensure timer is set
    if (!$order->expected_delivery_date) {
        $order->expected_delivery_date = now()->addDays(7);
        echo "Set expected_delivery_date.\n";
    }
    
    $order->save();
    echo "Order #$orderId fixed successfully.\n";
} else {
    echo "Order #$orderId not found.\n";
}

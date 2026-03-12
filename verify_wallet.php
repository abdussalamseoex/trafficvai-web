<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;

$order = Order::latest()->first();
echo "Latest Order:\n";
echo "ID: " . $order->id . "\n";
echo "Method: " . $order->payment_method . "\n";
echo "Status: " . $order->payment_status . "\n";
echo "Total: " . $order->total_amount . "\n";

$transaction = Transaction::latest()->where('user_id', $order->user_id)->first();
echo "\nLatest Transaction for User #{$order->user_id}:\n";
if ($transaction) {
    echo "ID: " . $transaction->id . "\n";
    echo "Type: " . $transaction->type . "\n";
    echo "Amount: " . $transaction->amount . "\n";
    echo "Description: " . $transaction->description . "\n";
}
else {
    echo "No transaction found.\n";
}

$wallet = Wallet::where('user_id', $order->user_id)->first();
echo "\nCurrent Wallet Balance: " . ($wallet->balance ?? 0) . "\n";

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Package;
use App\Models\Order;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\WalletService;
use Illuminate\Http\Request;

$user = User::find(2);

auth()->login($user);

// Credit user first to ensure enough balance
$walletService = app(WalletService::class);
$walletService->credit($user, 500, 'Test', 'Simulated credit for testing');

$package = Package::first();
echo "Testing with Package: " . $package->name . " (Price: " . $package->price . ")\n";
echo "Initial Balance: " . $user->fresh()->balance . "\n";

// Create Request mock
$request = new Request([
    'payment_method' => 'wallet',
    'project_id' => null,
    'is_emergency' => 'standard',
]);

// Simulate Controller logic
try {
    $totalAmount = $package->price;
    $order = Order::create([
        'user_id' => $user->id,
        'package_id' => $package->id,
        'status' => 'pending_requirements',
        'total_amount' => $totalAmount,
    ]);

    echo "Order created with ID: " . $order->id . ". Status: " . $order->status . "\n";

    $paymentMethod = $request->input('payment_method', 'stripe');
    echo "Resolving driver for: " . $paymentMethod . "\n";

    $manager = app(PaymentGatewayManager::class);
    $response = $manager::resolve($paymentMethod)->processPayment($order);

    echo "Payment processed. Response type: " . get_class($response) . "\n";

    // Check if it's a redirect to success or back with error
    if ($response->isRedirect()) {
        $targetUrl = $response->getTargetUrl();
        echo "Redirect to: " . $targetUrl . "\n";
        if (strpos($targetUrl, 'orders/') !== false) {
            echo "SUCCESS: Redirected to order page.\n";
        }
        else {
            echo "FAILURE: Redirected elsewhere (likely back).\n";
        }
    }

    $order->refresh();
    echo "Final Order Status (Payment): " . $order->payment_status . "\n";
    echo "Final Order Method: " . $order->payment_method . "\n";
    echo "Final Wallet Balance: " . $user->fresh()->balance . "\n";

}
catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

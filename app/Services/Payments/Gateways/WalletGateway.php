<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Services\Payments\WalletService;
use Exception;

class WalletGateway implements PaymentGatewayInterface
{
    protected $walletService;

    public function __construct()
    {
        $this->walletService = app(WalletService::class);
    }

    public function processPayment($order)
    {
        try {
            // Backend Validation: Ensure balance is sufficient
            // WalletService::debit performs the check and decrements atomically
            $packageName = $order->package->name ?? 'Service';
            $transaction = $this->walletService->debit(
                $order->user,
                $order->total_amount,
                'order',
                "Payment for Order #{$order->id} ({$packageName})",
            ['order_id' => $order->id]
            );

            // Update Order Status
            $hasRequirements = $order->requirements()->count() > 0 || !empty($order->guest_post_url);
            $order->update([
                'payment_status' => 'paid',
                'status' => $hasRequirements ? 'processing' : 'pending_requirements',
                'payment_method' => 'wallet',
                'transaction_id' => $transaction->id,
            ]);

            return redirect()->route('client.orders.show', $order->id)
                ->with('success', 'Payment successful! Your order is now being processed.');

        }
        catch (Exception $e) {
            $message = $e->getMessage();
            if ($message === 'Insufficient wallet balance.') {
                $message = 'Insufficient balance. <a href="' . route('client.payments.topup') . '" class="underline font-bold">Top up your wallet here</a> to complete this order.';
            }
            return redirect()->back()
                ->with('error', $message);
        }
    }

    public function verifyPayment(\Illuminate\Http\Request $request): bool
    {
        return true; // Internally processed
    }
}

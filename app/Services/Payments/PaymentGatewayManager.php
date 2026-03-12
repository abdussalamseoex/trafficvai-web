<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;
use App\Services\Payments\Gateways\StripeGateway;
use App\Services\Payments\Gateways\BankTransferGateway;
use App\Services\Payments\Gateways\WalletGateway;
use App\Services\Payments\Gateways\PlisioGateway;

class PaymentGatewayManager
{
    /**
     * Resolve the requested payment gateway.
     *
     * @param string $driver
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public static function resolve(string $driver): PaymentGatewayInterface
    {
        // Check if enabled in settings
        $isEnabled = \App\Models\Setting::get("gateway_{$driver}_enabled", '0') == '1';

        // Backward compatibility for old manual check
        if ($driver === 'bank_transfer') {
            $isEnabled = $isEnabled || \App\Models\Setting::get('bank_transfer_enabled', '0') == '1';
        }

        if (!$isEnabled) {
            throw new InvalidArgumentException("Payment driver [{$driver}] is either disabled or not supported.");
        }

        // Explicitly handle Bangladesh manual gateways which use a common "Manual" driver logic but different configs
        if (in_array($driver, ['bkash', 'nagad', 'rocket'])) {
            return new \App\Services\Payments\Gateways\ManualBDGateway($driver);
        }

        return match ($driver) {
                'stripe' => new StripeGateway(),
                'bank_transfer' => new BankTransferGateway(),
                'wallet' => new WalletGateway(),
                'sslcommerz' => new \App\Services\Payments\Gateways\SSLCommerzGateway(),
                'plisio' => new PlisioGateway(),
                default => throw new InvalidArgumentException("Payment driver [{$driver}] is not implemented yet."),
            };
    }

    /**
     * Get a list of all globally enabled payment gateways, grouped by category
     * 
     * @return array
     */
    public static function getEnabledGateways(): array
    {
        $allGateways = config('payment_gateways');
        $enabled = [
            'global' => [],
            'crypto' => [],
            'bangladesh' => []
        ];

        foreach ($allGateways as $category => $gateways) {
            foreach ($gateways as $slug => $gateway) {
                $isSetEnabled = \App\Models\Setting::get("gateway_{$slug}_enabled", '0') == '1';

                // Bank transfer legacy fallback
                if ($slug == 'bank_transfer' && \App\Models\Setting::get("bank_transfer_enabled", '0') == '1') {
                    $isSetEnabled = true;
                }

                if ($isSetEnabled) {
                    $enabled[$category][$slug] = $gateway;
                }
            }
        }
        return $enabled;
    }

    /**
     * Handle potential wallet partial payment.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public static function processPotentialWalletPayment(\Illuminate\Http\Request $request, \App\Models\Order $order)
    {
        $paymentMethod = $request->input('payment_method', 'stripe');
        $useWallet = $request->input('use_wallet', '0') == '1';

        if ($useWallet && $paymentMethod !== 'wallet') {
            $user = auth()->user();
            if ($user->balance > 0) {
                $totalAmount = $order->total_amount;
                $deduction = min($totalAmount, $user->balance);

                $walletService = app(\App\Services\Payments\WalletService::class);
                $transaction = $walletService->debit(
                    $user,
                    $deduction,
                    'order_partial',
                    "Partial payment for Order #{$order->id}",
                ['order_id' => $order->id]
                );

                $order->update([
                    'wallet_amount' => $deduction,
                    'total_amount' => $totalAmount - $deduction,
                    'payment_notes' => "Partial payment of $" . number_format($deduction, 2) . " from Wallet. Balance remaining: $" . number_format($totalAmount - $deduction, 2)
                ]);

                if ($order->total_amount <= 0) {
                    $hasRequirements = $order->requirements()->count() > 0 || !empty($order->guest_post_url);
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => $hasRequirements ? 'processing' : 'pending_requirements',
                        'payment_method' => 'wallet',
                        'transaction_id' => $transaction->id
                    ]);
                    return redirect()->route('client.orders.show', $order->id)
                        ->with('success', 'Order placed successfully using your wallet balance!');
                }
            }
        }

        return null;
    }
}

<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    /**
     * Process the payment and return a redirect response to the Stripe Checkout session.
     *
     * @param mixed $data
     * @return mixed
     */
    public function processPayment($data)
    {
        try {
            \Stripe\Stripe::setApiKey(\App\Models\Setting::get('stripe_secret_key', config('payments.stripe.secret')));

            $domain = config('app.url');

            $isOrder = $data instanceof \App\Models\Order;
            $isTopup = $data instanceof \App\Models\TopupRequest;

            $amount = $isOrder ? $data->total_amount : ($isTopup ? $data->amount : $data['amount']);
            $description = $isOrder ? 'TrafficVai Order #' . $data->id : ($isTopup ? 'Wallet Top-up #' . $data->id : ($data['description'] ?? 'Wallet Top-up'));
            $currency = \App\Models\Setting::get('stripe_currency', config('payments.stripe.currency', 'usd'));

            $successUrl = $isOrder
                ? $domain . '/client/orders/' . $data->id . '?session_id={CHECKOUT_SESSION_ID}'
                : ($isTopup ? $domain . '/client/payments?session_id={CHECKOUT_SESSION_ID}' : ($data['return_url'] ?? $domain . '/client/payments?session_id={CHECKOUT_SESSION_ID}'));

            $cancelUrl = $isOrder
                ? $domain . '/client/orders/' . $data->id
                : ($isTopup ? $domain . '/client/payments/topup' : ($data['cancel_url'] ?? $domain . '/client/payments/topup'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                        'price_data' => [
                            'currency' => $currency,
                            'product_data' => [
                                'name' => $description,
                            ],
                            'unit_amount' => (int)round($amount * 100),
                        ],
                        'quantity' => 1,
                    ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $isOrder ? 'ORDER_' . $data->id : ($isTopup ? 'TOPUP_' . $data->id : 'TOPUP_' . auth()->id()),
                'metadata' => [
                    'type' => $isOrder ? 'order' : 'topup',
                    'order_id' => $isOrder ? (string)$data->id : null,
                    'topup_id' => $isTopup ? (string)$data->id : null,
                    'user_id' => (string)auth()->id(),
                ],
            ]);

            if ($isOrder) {
                $data->update([
                    'payment_method' => 'stripe',
                    'payment_status' => 'pending',
                    'transaction_id' => $session->id,
                    'payment_url' => $session->url,
                ]);
            }
            elseif ($isTopup) {
                $data->update([
                    'transaction_id' => $session->id,
                    'meta' => array_merge($data->meta ?? [], [
                        'stripe_session_id' => $session->id,
                        'stripe_url' => $session->url
                    ])
                ]);
            }

            return redirect()->away($session->url);
        }
        catch (\Exception $e) {
            Log::error('Stripe Exception: ' . $e->getMessage());
            if ($isOrder) {
                return redirect()->route('client.orders.show', $data)->with('error', 'Unable to process payment with Stripe at this time.');
            }
            return back()->with('error', 'Unable to process payment with Stripe at this time.');
        }
    }

    /**
     * Verify the payment callback/webhook/status.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyPayment(Request $request): bool
    {
        // Validation logic can be placed here if catching webhooks
        return true;
    }
}

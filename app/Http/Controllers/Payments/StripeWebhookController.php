<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = \App\Models\Setting::get('stripe_webhook_secret', config('payments.stripe.webhook_secret'));

        $event = null;

        try {
            // Note: If you don't have webhook_secret configured yet (e.g. testing locally),
            // you might skip this or let it fail gently.
            if ($endpointSecret) {
                $event = Webhook::constructEvent(
                    $payload, $sigHeader, $endpointSecret
                );
            }
            else {
                // Testing fallback if no secret is set
                $event = \Stripe\Event::constructFrom(json_decode($payload, true));
            }
        }
        catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook Error: Invalid payload');
            return response('Invalid payload', 400);
        }
        catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Error: Invalid signature');
            return response('Invalid signature', 400);
        }

        // Handle the checkout.session.completed event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata;

            if (($metadata->type ?? 'order') === 'topup') {
                $userId = $metadata->user_id ?? null;
                $user = \App\Models\User::find($userId);

                if ($user) {
                    $amount = $session->amount_total / 100;
                    $walletService = app(\App\Services\Payments\WalletService::class);

                    // Simple idempotency check
                    $exists = \App\Models\Transaction::where('meta->session_id', $session->id)->exists();

                    if (!$exists) {
                        $walletService->credit(
                            $user,
                            $amount,
                            'topup',
                            "Auto Top-up via Stripe (Ref: {$session->id})",
                        ['session_id' => $session->id]
                        );
                        Log::info("Wallet for User #{$user->id} credited with ${$amount} via Stripe Webhook.");

                        try {
                            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WalletTopupReceipt([
                                'user_name' => $user->name,
                                'amount' => $amount,
                                'payment_method' => 'stripe',
                                'transaction_id' => $session->id,
                                'date' => now()->format('M d, Y h:i A')
                            ]));
                        }
                        catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Mail Error (Stripe Webhook): ' . $e->getMessage());
                        }

                        // Check if this was an invoice settlement
                        $invoiceId = $metadata->invoice_id ?? null;
                        if ($invoiceId) {
                            $invoice = \App\Models\Invoice::find($invoiceId);
                            if ($invoice) {
                                app(\App\Services\InvoiceService::class)->settle($invoice, 'stripe', $session->id, "Stripe Webhook Completion");
                            }
                        }
                    }
                }
            }
            else {
                $orderId = $metadata->order_id ?? null;
                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order && $order->payment_status !== 'paid') {
                        $hasRequirements = $order->requirements()->count() > 0 || !empty($order->guest_post_url);
                        $order->update([
                            'status' => $hasRequirements ? 'processing' : 'pending_requirements',
                            'payment_status' => 'paid',
                            'transaction_id' => $session->payment_intent ?? $session->id,
                        ]);

                        Log::info("Payment for Order #{$order->id} completed via Stripe Webhook.");
                    }
                }
            }
        }

        return response('Webhook handled', 200);
    }
}

<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(\App\Models\Order $order): void
    {
        // Notify Admin of new order
        $this->notificationService->notifyAdmin(
            "New Order #{$order->id} Received",
            "A new order has been placed by {$order->user->name} Amount: {$order->total_amount}",
            route('admin.orders.show', $order),
            'admin_new_order',
            $order->id
        );
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(\App\Models\Order $order): void
    {
        // 1. Handle Order Status Change
        if ($order->wasChanged('status')) {
            $this->notificationService->send('order_status_updated', $order->user, [
                'order_id' => $order->id,
                'user_name' => $order->user->name,
                'status' => str_replace('_', ' ', $order->status),
                'previous_status' => str_replace('_', ' ', $order->getOriginal('status') ?? 'N/A'),
                'link' => route('client.orders.show', $order)
            ]);
        }

        // 2. Handle Payment Status Change
        if ($order->wasChanged('payment_status')) {
            switch ($order->payment_status) {
                case 'paid':
                    $this->notificationService->send('payment_approved', $order->user, [
                        'order_id' => $order->id,
                        'user_name' => $order->user->name,
                        'amount' => $order->total_amount,
                        'link' => route('client.orders.show', $order)
                    ]);
                    break;
                case 'failed':
                    $this->notificationService->send('payment_failed', $order->user, [
                        'order_id' => $order->id,
                        'user_name' => $order->user->name,
                        'link' => route('client.orders.show', $order)
                    ]);
                    break;
                case 'refunded':
                    $this->notificationService->send('payment_refunded', $order->user, [
                        'order_id' => $order->id,
                        'user_name' => $order->user->name,
                        'amount' => $order->total_amount,
                        'link' => route('client.orders.show', $order)
                    ]);
                    break;
            }
        }

        // 3. Handle Payment Proof Submission (Client updates proof fields)
        if (($order->wasChanged('payment_proof') && $order->payment_proof) || ($order->wasChanged('transaction_id') && $order->transaction_id)) {
            // Only if payment_status is still pending/initial
            if ($order->payment_status === 'pending') {
                $this->notificationService->notifyAdmin(
                    "Payment Proof Submitted for Order #{$order->id}",
                    "User {$order->user->name} has submitted payment proof for verification.",
                    route('admin.orders.show', $order),
                    'admin_payment_proof',
                    $order->id
                );
            }
        }
    }

    /**
     * Handle the Order "saving" event.
     */
    public function saving(\App\Models\Order $order): void
    {
        if (!$order->subtotal_amount || $order->subtotal_amount <= 0) {
            $order->subtotal_amount = (float)$order->total_amount + (float)$order->wallet_amount + (float)$order->discount_amount;
        }
    }
}

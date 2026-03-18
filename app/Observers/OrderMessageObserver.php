<?php

namespace App\Observers;

use App\Models\OrderMessage;

class OrderMessageObserver
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the OrderMessage "created" event.
     */
    public function created(\App\Models\OrderMessage $orderMessage): void
    {
        $order = $orderMessage->order;
        $sender = $orderMessage->user;

        if ($sender->is_admin) {
            // Notify Client
            $this->notificationService->send('new_message_client', $order->user, [
                'order_id' => $order->id,
                'sender_name' => 'Admin',
                'message_preview' => substr($orderMessage->message, 0, 100) . '...',
                'link' => route('client.orders.show', $order)
            ]);
        }
        else {
            // Notify Admin
            $this->notificationService->notifyAdmin(
                "New Message on Order #{$order->id}",
                "Client {$sender->name} sent a message: " . substr($orderMessage->message, 0, 100),
                route('admin.orders.show', $order),
                'admin_notification',
                $order->id
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'order_status_updated',
                'name' => 'Order Status Updated',
                'subject' => 'Update on your Order #{order_id}',
                'body' => '<p>Hello {user_name},</p><p>Your order <strong>#{order_id}</strong> has been updated to: <strong>{status}</strong>.</p><p><a href="{link}">Click here to view your order.</a></p>',
                'type' => 'order',
                'variables_hint' => ['order_id', 'user_name', 'status', 'link']
            ],
            [
                'slug' => 'payment_approved',
                'name' => 'Payment Approved',
                'subject' => 'Payment Received for Order #{order_id}',
                'body' => '<p>Hello {user_name},</p><p>We have successfully received your payment of <strong>${amount}</strong> for order <strong>#{order_id}</strong>.</p><p><a href="{link}">View Order Details</a></p>',
                'type' => 'payment',
                'variables_hint' => ['order_id', 'user_name', 'amount', 'link']
            ],
            [
                'slug' => 'payment_failed',
                'name' => 'Payment Failed',
                'subject' => 'Payment Failed for Order #{order_id}',
                'body' => '<p>Hello {user_name},</p><p>Unfortunately, the payment attempt for your order <strong>#{order_id}</strong> has failed. Please try again or use a different payment method.</p><p><a href="{link}">Return to Checkout</a></p>',
                'type' => 'payment',
                'variables_hint' => ['order_id', 'user_name', 'link']
            ],
            [
                'slug' => 'payment_refunded',
                'name' => 'Payment Refunded',
                'subject' => 'Refund Processed for Order #{order_id}',
                'body' => '<p>Hello {user_name},</p><p>A refund of <strong>${amount}</strong> has been processed for your order <strong>#{order_id}</strong>.</p><p><a href="{link}">View Order Details</a></p>',
                'type' => 'payment',
                'variables_hint' => ['order_id', 'user_name', 'amount', 'link']
            ],
            [
                'slug' => 'new_message_client',
                'name' => 'New Message from Support',
                'subject' => 'New Message for Order #{order_id}',
                'body' => '<p>Hello,</p><p>You have received a new message regarding your order <strong>#{order_id}</strong>:</p><blockquote>{message_preview}</blockquote><p><a href="{link}">Reply to Message</a></p>',
                'type' => 'message',
                'variables_hint' => ['order_id', 'message_preview', 'link']
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\EmailTemplate::updateOrCreate(['slug' => $template['slug']], $template);
        }
    }
}

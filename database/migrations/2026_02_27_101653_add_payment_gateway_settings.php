<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['key' => 'stripe_enabled', 'value' => '0', 'group' => 'Payment Gateways', 'type' => 'boolean'],
            ['key' => 'stripe_public_key', 'value' => '', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'stripe_secret_key', 'value' => '', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'stripe_webhook_secret', 'value' => '', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'bank_transfer_enabled', 'value' => '1', 'group' => 'Payment Gateways', 'type' => 'boolean'],
            ['key' => 'bank_name', 'value' => 'Standard Chartered Bank', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'bank_account_name', 'value' => 'TrafficVai Solutions LLC', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'bank_account_number', 'value' => '394857293049', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'bank_routing_swift', 'value' => 'SCBKUS33XXX', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'bank_reference_prefix', 'value' => 'ORDER-', 'group' => 'Payment Gateways', 'type' => 'text'],
            ['key' => 'bank_transfer_instructions', 'value' => 'After making the transfer, please send a message via the Order Messaging system below. Include your transfer receipt or reference number so our team can verify your payment and activate your order.', 'group' => 'Payment Gateways', 'type' => 'textarea'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
            ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'stripe_enabled', 'stripe_public_key', 'stripe_secret_key', 'stripe_webhook_secret',
            'bank_transfer_enabled', 'bank_name', 'bank_account_name', 'bank_account_number',
            'bank_routing_swift', 'bank_reference_prefix', 'bank_transfer_instructions'
        ];

        \App\Models\Setting::whereIn('key', $keys)->delete();
    }
};

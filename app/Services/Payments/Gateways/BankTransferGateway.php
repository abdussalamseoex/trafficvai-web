<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;

class BankTransferGateway implements PaymentGatewayInterface
{
    /**
     * Process the payment and return a redirect response or checkout URL/session.
     *
     * @param mixed $data
     * @return mixed
     */
    public function processPayment($data)
    {
        if ($data instanceof Order) {
            // For bank transfer, we just mark as pending and show instructions.
            $data->update([
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'payment_url' => null,
                'transaction_id' => null,
            ]);

            return redirect()->route('client.orders.show', $data)->with('success', 'Order placed successfully. Please complete the bank transfer. Instructions have been sent to your email.');
        }

        // For other types (like manual top-up, though usually handled by separate flow)
        return back()->with('info', 'Please follow the manual payment instructions.');
    }

    /**
     * Verify the payment callback/webhook/status.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyPayment(Request $request): bool
    {
        // Bank transfers are verified manually by admin.
        return true;
    }
}

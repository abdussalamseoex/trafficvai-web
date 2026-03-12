<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Process the payment and return a redirect response or checkout URL/session.
     *
     * @param mixed $data
     * @return mixed
     */
    public function processPayment($data);

    /**
     * Verify the payment callback/webhook/status.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyPayment(Request $request): bool;
}

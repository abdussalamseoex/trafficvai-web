<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;

class SSLCommerzGateway implements PaymentGatewayInterface
{
    /**
     * Process the payment and return a redirect response or checkout URL/session.
     *
     * @param mixed $data
     * @return mixed
     */
    public function processPayment($data)
    {
        // TODO: Implement SSLCOMMERZ API Integration
        // For now, redirects to a placeholder or throws implementation error until API is ready
        throw new \Exception("SSLCOMMERZ API Integration is currently in development. Please use manual payment methods for now.");
    }

    /**
     * Verify the payment callback/webhook/status.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyPayment(Request $request): bool
    {
        return false;
    }
}

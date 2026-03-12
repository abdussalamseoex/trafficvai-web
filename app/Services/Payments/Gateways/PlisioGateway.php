<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\TopupRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlisioGateway implements PaymentGatewayInterface
{
    /**
     * Process the payment via Plisio.
     *
     * @param Order|TopupRequest $data
     * @return mixed
     */
    public function processPayment($data)
    {
        $apiKey = Setting::get('gateway_plisio_api_key', '');
        if (empty($apiKey)) {
            Log::error('Plisio Gateway: API Key is missing.');
            return back()->with('error', 'Plisio payment gateway is not properly configured.');
        }

        // Determine transaction reference and amount based on object type
        if ($data instanceof Order) {
            $amount = $data->total_amount;
            $orderNumber = 'ORDER_' . $data->id;
            $orderName = 'Order #' . $data->id;
        }
        elseif ($data instanceof TopupRequest) {
            $amount = $data->amount;
            $orderNumber = 'TOPUP_' . $data->id;
            $orderName = 'Wallet Topup #' . $data->id;
        }
        else {
            Log::error('Plisio Gateway: Invalid data type provided.');
            return back()->with('error', 'Invalid payment payment request.');
        }

        $currency = 'USD'; // Plisio supports USD fiat source

        try {
            // Call Plisio API to create a new invoice
            $http = Http::timeout(30);
            if (app()->environment('local')) {
                $http = $http->withoutVerifying();
            }

            $successUrl = url('/client/dashboard?payment=success');
            $cancelUrl = url('/client/dashboard?payment=cancelled');

            if ($data instanceof Order) {
                $successUrl = route('client.orders.show', $data) . '?payment=success';
                $cancelUrl = route('client.orders.show', $data) . '?payment=cancelled';
            } elseif ($data instanceof TopupRequest) {
                $successUrl = route('client.payments.index') . '?payment=success';
                $cancelUrl = route('client.payments.topup') . '?payment=cancelled';
            }

            // Ensure payment method is saved
            $data->update(['payment_method' => 'plisio']);

            $response = $http->get('https://api.plisio.net/api/v1/invoices/new', [
                'api_key' => $apiKey,
                'source_currency' => $currency,
                'source_amount' => $amount,
                'order_number' => $orderNumber,
                'order_name' => $orderName,
                'callback_url' => url('/plisio/callback'),
                'success_invoice_url' => $successUrl,
                'cancel_invoice_url' => $cancelUrl,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === 'success') {
                $invoiceUrl = $result['data']['invoice_url'] ?? null;
                if ($invoiceUrl) {
                    return redirect()->away($invoiceUrl);
                }
            }

            // Handle API error
            $errorMessage = $result['data']['message'] ?? 'Failed to create Plisio invoice.';
            Log::error('Plisio API Error: ' . json_encode($result));
            return back()->with('error', 'Payment Gateway Error: ' . $errorMessage);

        }
        catch (\Exception $e) {
            Log::error('Plisio Gateway Exception: ' . $e->getMessage());
            return back()->with('error', 'Unable to connect to the payment gateway.');
        }
    }

    /**
     * Verify the payment.
     *
     * Note: Plisio uses an asynchronous webhook (callback) for verification.
     * This verify method may be called synchronously after redirect, but the true verification
     * happens in the PlisioCallbackController.
     *
     * @param mixed $request
     * @return bool
     */
    public function verifyPayment(\Illuminate\Http\Request $request): bool
    {
        // For Plisio, synchronous verification after return is usually just a visual confirmation.
        // The actual verification and status update must be handled by the async webhook.
        return true;
    }
}

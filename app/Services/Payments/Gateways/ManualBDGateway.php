<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;

class ManualBDGateway implements PaymentGatewayInterface
{
    protected $driver;

    public function __construct(string $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Process the payment and return a redirect response or checkout URL/session.
     *
     * @param mixed $data
     * @return mixed
     */
    public function processPayment($data)
    {
        if ($data instanceof Order) {
            $data->update([
                'payment_method' => $this->driver,
                'payment_status' => 'pending',
                'payment_url' => null,
                'transaction_id' => null,
            ]);

            $number = \App\Models\Setting::get("gateway_{$this->driver}_account_number");
            $type = \App\Models\Setting::get("gateway_{$this->driver}_account_type");
            $details = "Method: " . strtoupper($this->driver) . "\nNumber: {$number}\nType: {$type}";

            $instructions = \App\Models\Setting::get("gateway_{$this->driver}_instructions");
            $config = config("payment_gateways.bangladesh.{$this->driver}");

            return view('client.payments.manual_instructions', [
                'amount' => $data->total_amount,
                'method' => $this->driver,
                'details' => $details,
                'instructions' => $instructions,
                'order' => $data,
                'gateway' => $config
            ]);
        }

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
        // Verified manually by admin via proof upload
        return true;
    }
}

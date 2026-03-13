<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TopupRequest;
use App\Models\Setting;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $transactions = $user->transactions()->latest()->paginate(10);
        $pendingTopups = TopupRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id], ['balance' => 0.00]);

        return view('client.payments.index', compact('transactions', 'wallet', 'pendingTopups'));
    }

    public function topup()
    {
        $gateways = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();

        // Check if crypto is enabled
        $cryptoEnabled = \App\Models\Setting::get('gateway_crypto_enabled', '0') == '1';
        $bdEnabled = \App\Models\Setting::get('gateway_bd_enabled', '0') == '1';

        return view('client.payments.topup', compact('gateways', 'cryptoEnabled', 'bdEnabled'));
    }

    public function processTopup(Request $request, PaymentGatewayManager $gatewayManager)
    {
        $allGateways = array_merge(
            array_keys(config('payment_gateways.global')),
            array_keys(config('payment_gateways.crypto')),
            array_keys(config('payment_gateways.bangladesh'))
        );

        $request->validate([
            'amount' => 'required|numeric|min:5',
            'payment_method' => 'required|in:' . implode(',', $allGateways),
        ]);

        try {
            $gateway = $gatewayManager->resolve($request->payment_method);

            // Create TopupRequest for ALL methods with 'initiated' status
            $topup = TopupRequest::create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => 'initiated'
            ]);

            // Handle manual redirection for bKash, Nagad, Rocket, Bank Transfer
            if (in_array($request->payment_method, ['bkash', 'nagad', 'rocket', 'bank_transfer'])) {

                // Format details for manual methods (bKash/Nagad/Rocket have separate fields)
                if (in_array($request->payment_method, ['bkash', 'nagad', 'rocket'])) {
                    $number = Setting::get("gateway_{$request->payment_method}_account_number");
                    $type = Setting::get("gateway_{$request->payment_method}_account_type");
                    $details = "Method: " . strtoupper($request->payment_method) . "\nNumber: {$number}\nType: {$type}";
                }
                else {
                    $details = Setting::get("gateway_{$request->payment_method}_details") ?? Setting::get("{$request->payment_method}_details");
                }

                $instructions = Setting::get("gateway_{$request->payment_method}_instructions") ?? Setting::get("{$request->payment_method}_instructions");
                $config = config("payment_gateways.bangladesh.{$request->payment_method}") ?? config("payment_gateways.global.{$request->payment_method}");

                // Redirect to a specific top-up manual instructions page
                return view('client.payments.manual_instructions', [
                    'amount' => $request->amount,
                    'method' => $request->payment_method,
                    'details' => $details,
                    'instructions' => $instructions,
                    'topup' => $topup,
                    'gateway' => $config
                ]);
            }

            // Auto gateways (Stripe, etc.)
            return $gateway->processPayment($topup);
        }
        catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function submitManualProof(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'proof' => 'nullable|string|max:1000',
            'transaction_id' => 'nullable|string|max:255',
            'sender_number' => 'nullable|string|max:255',
            'payment_method' => 'required|string',
            'topup_id' => 'required|exists:topup_requests,id'
        ]);

        $topup = TopupRequest::findOrFail($request->topup_id);

        // Ensure user owns this request
        if ($topup->user_id !== auth()->id() && !auth()->user()->isStaff()) {
            abort(403);
        }

        $topup->update([
            'payment_method' => $request->payment_method,
            'proof' => $request->proof,
            'transaction_id' => $request->transaction_id,
            'sender_number' => $request->sender_number,
            'status' => 'pending'
        ]);

        return redirect()->route('client.payments.index')->with('success', 'Your payment info has been submitted. An administrator will verify it shortly.');
    }
}

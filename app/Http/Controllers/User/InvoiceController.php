<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\TopupRequest;
use App\Services\InvoiceService;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\WalletService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoiceService;
    protected $walletService;

    public function __construct(InvoiceService $invoiceService, WalletService $walletService)
    {
        $this->invoiceService = $invoiceService;
        $this->walletService = $walletService;
    }

    /**
     * Display a listing of the user's invoices (orders).
     */
    public function index()
    {
        // ... (existing index code)
        $orders = Order::with(['package', 'guestPostSite'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.invoices.index', compact('orders'));
    }

    /**
     * Display the specified invoice.
     * Handles both custom Invoice models and Order receipts.
     */
    public function show($invoice)
    {
        // First check if it's a custom Invoice (admin-issued)
        $customInvoice = Invoice::where('id', $invoice)
            ->where('user_id', auth()->id())
            ->with('items', 'user')
            ->first();

        if ($customInvoice) {
            $gateways = PaymentGatewayManager::getEnabledGateways();
            return view('client.invoices.invoice_show', [
                'invoice' => $customInvoice,
                'gateways' => $gateways
            ]);
        }

        // Otherwise treat as an Order receipt
        $order = Order::where('id', $invoice)
            ->where('user_id', auth()->id())
            ->with(['package', 'guestPostSite'])
            ->firstOrFail();

        return view('client.invoices.show', ['invoice' => $order]);
    }

    /**
     * Download the invoice as a PDF.
     */
    public function download($invoice)
    {
        // ... (existing download code remains SAME)
        $customInvoice = Invoice::where('id', $invoice)
            ->where('user_id', auth()->id())
            ->with(['items', 'user'])
            ->first();

        if ($customInvoice) {
            return response()->view('invoices.pdf', ['invoice' => $customInvoice, 'print' => 1])
                ->header('Content-Type', 'text/html');
        }

        return redirect()->route('client.invoices.show', $invoice)
            ->with('info', 'Please use your browser\'s print function (Ctrl+P) on the receipt page to save as PDF.');
    }

    /**
     * Pay the invoice using selected payment method.
     */
    public function pay(Request $request, Invoice $invoice, PaymentGatewayManager $gatewayManager)
    {
        // Ensure user owns the invoice
        if ($invoice->user_id != auth()->id()) {
            abort(403);
        }

        // Ensure invoice is unpaid
        if ($invoice->status !== 'unpaid') {
            return back()->with('error', 'Only unpaid invoices can be paid.');
        }

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $method = $request->payment_method;

        // 1. Wallet Payment
        if ($method === 'wallet') {
            try {
                $user = auth()->user();
                
                // Debit from wallet
                $this->walletService->debit(
                    $user, 
                    (float) $invoice->total, 
                    'invoice_payment', 
                    "Payment for Invoice #{$invoice->invoice_number}",
                    ['invoice_id' => $invoice->id]
                );

                // Settle invoice
                $this->invoiceService->settle($invoice, 'wallet', 'WAL-INV-' . $invoice->id);

                return redirect()->route('client.invoices.show', $invoice->id)
                    ->with('success', 'Invoice paid successfully using your wallet balance!');
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        // 2. Gateway Payment (via TopupRequest)
        try {
            // Create a TopupRequest record that will settle the invoice on completion
            $topup = TopupRequest::create([
                'user_id' => auth()->id(),
                'amount' => $invoice->total,
                'payment_method' => $method,
                'status' => 'pending',
                'meta' => [
                    'invoice_id' => $invoice->id,
                    'type' => 'invoice_settlement'
                ]
            ]);

            // Process payment via gateway manager
            $gateway = PaymentGatewayManager::resolve($method);
            return $gateway->processPayment($topup);

        } catch (\Exception $e) {
            return back()->with('error', 'Gateway error: ' . $e->getMessage());
        }
    }
}

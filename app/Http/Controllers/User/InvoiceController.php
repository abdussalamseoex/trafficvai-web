<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the user's invoices (orders).
     */
    public function index()
    {
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
            return view('client.invoices.invoice_show', ['invoice' => $customInvoice]);
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
        // Check custom invoice first
        $customInvoice = Invoice::where('id', $invoice)
            ->where('user_id', auth()->id())
            ->first();

        if ($customInvoice) {
            return redirect()->route('client.invoices.show', $invoice)
                ->with('info', 'Use your browser\'s print function to save as PDF.');
        }

        return redirect()->route('client.invoices.show', $invoice)
            ->with('info', 'PDF downloads are currently generating. Please use the web receipt for now.');
    }
}

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
        // Try to find as custom invoice first
        $customInvoice = Invoice::where('id', $invoice)
            ->where('user_id', auth()->id())
            ->with(['items', 'user'])
            ->first();

        if ($customInvoice) {
            return response()->view('invoices.pdf', ['invoice' => $customInvoice, 'print' => 1])
                ->header('Content-Type', 'text/html');
        }

        // For Order receipts, we don't have a printable template yet, 
        // fallback to redirecting back to the show page with instructions
        return redirect()->route('client.invoices.show', $invoice)
            ->with('info', 'Please use your browser\'s print function (Ctrl+P) on the receipt page to save as PDF.');
    }
}

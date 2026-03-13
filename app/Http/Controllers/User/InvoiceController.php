<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
     * Display the specified invoice (order receipt).
     */
    public function show(Order $invoice)
    {
        // Ensure the user owns this invoice
        if ($invoice->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load(['package', 'guestPostSite']);

        return view('client.invoices.show', compact('invoice'));
    }

    /**
     * Download the invoice as a PDF (placeholder for future PDF generation).
     */
    public function download(Order $invoice)
    {
        // Ensure the user owns this invoice
        if ($invoice->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // For now, just render the show view. In the future, use dompdf or snappy.
        // return \PDF::loadView('client.invoices.pdf', compact('invoice'))->download('invoice-'.$invoice->order_number.'.pdf');

        return redirect()->route('client.invoices.show', $invoice->id)
            ->with('info', 'PDF downloads are currently generating. Please use the web receipt for now.');
    }
}

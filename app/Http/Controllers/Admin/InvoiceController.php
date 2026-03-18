<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of all invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('user')->latest();

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(20);
        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request)
    {
        $clients = User::where('is_admin', false)->orderBy('name')->get();
        $invoiceServices = \App\Models\InvoiceService::orderBy('name')->get();
        
        $order = null;
        if ($request->has('order_id')) {
            $order = \App\Models\Order::with('package.service', 'user')->find($request->order_id);
        }

        return view('admin.invoices.create', compact('clients', 'invoiceServices', 'order'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'type'          => 'nullable|in:custom,renewal',
            'order_id'      => 'nullable|exists:orders,id',
            'currency'      => 'required|string|max:10',
            'due_date'      => 'nullable|date',
            'status'        => 'required|in:draft,unpaid,paid,cancelled,overdue',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value'=> 'nullable|numeric|min:0',
            'tax_rate'      => 'nullable|numeric|min:0|max:100',
            'notes'         => 'nullable|string',
            'terms'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        [$subtotal, $taxAmount, $total] = $this->calculateTotals($request);

        $invoice = Invoice::create([
            'user_id'        => $request->user_id,
            'type'           => $request->type ?? 'custom',
            'order_id'       => $request->order_id,
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'currency'       => $request->currency,
            'subtotal'       => $subtotal,
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value,
            'tax_rate'       => $request->tax_rate,
            'tax_amount'     => $taxAmount,
            'total'          => $total,
            'status'         => $request->status,
            'due_date'       => $request->due_date,
            'notes'          => $request->notes,
            'terms'          => $request->terms,
        ]);

        foreach ($request->items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $lineTotal,
            ]);
        }
        
        // Load relationships for mail
        $invoice->load(['user', 'items']);
        
        try {
            Mail::to($invoice->user->email)->send(new \App\Mail\InvoiceCreated($invoice));
            
            app(\App\Services\NotificationService::class)->send(
                'invoice_created',
                $invoice->user,
                ['link' => route('client.invoices.show', $invoice)]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Invoice Creation Notification Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    /**
     * Display a specific invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'items']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing an invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['user', 'items']);
        $clients = User::where('is_admin', false)->orderBy('name')->get();
        $invoiceServices = \App\Models\InvoiceService::orderBy('name')->get();
        $order = null;
        if ($invoice->order_id) {
            $order = \App\Models\Order::find($invoice->order_id);
        }
        return view('admin.invoices.edit', compact('invoice', 'clients', 'invoiceServices', 'order'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'type'          => 'nullable|in:custom,renewal',
            'order_id'      => 'nullable|exists:orders,id',
            'currency'      => 'required|string|max:10',
            'due_date'      => 'nullable|date',
            'status'        => 'required|in:draft,unpaid,paid,cancelled,overdue',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value'=> 'nullable|numeric|min:0',
            'tax_rate'      => 'nullable|numeric|min:0|max:100',
            'notes'         => 'nullable|string',
            'terms'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        [$subtotal, $taxAmount, $total] = $this->calculateTotals($request);

        $invoice->update([
            'user_id'        => $request->user_id,
            'type'           => $request->type ?? 'custom',
            'order_id'       => $request->order_id,
            'currency'       => $request->currency,
            'subtotal'       => $subtotal,
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value,
            'tax_rate'       => $request->tax_rate,
            'tax_amount'     => $taxAmount,
            'total'          => $total,
            'status'         => $request->status,
            'due_date'       => $request->due_date,
            'notes'          => $request->notes,
            'terms'          => $request->terms,
        ]);

        $invoice->items()->delete();
        foreach ($request->items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $lineTotal,
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted.');
    }

    /**
     * Download the invoice as a printable HTML page (PDF via browser print).
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['user', 'items']);
        return response()->view('invoices.pdf', ['invoice' => $invoice, 'print' => 1])
            ->header('Content-Type', 'text/html');
    }

    /**
     * Send invoice to client via email (without PDF attachment).
     */
    public function sendEmail(Invoice $invoice)
    {
        $invoice->load(['user', 'items']);
        Mail::to($invoice->user->email)->send(new \App\Mail\InvoiceCreated($invoice));
        return back()->with('success', 'Invoice emailed to ' . $invoice->user->email . '.');
    }

    /**
     * Update just the status of an invoice.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:draft,unpaid,paid,cancelled,overdue']);
        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    /**
     * Helper to calculate totals from request data.
     */
    private function calculateTotals(Request $request): array
    {
        $subtotal = collect($request->items)->sum(fn($item) => $item['quantity'] * $item['unit_price']);

        \Illuminate\Support\Facades\Log::info("Calculating totals. Items count: " . count($request->items) . ", Subtotal: " . $subtotal);

        // Apply discount
        $discountAmount = 0;
        if ($request->discount_type === 'percentage' && $request->discount_value > 0) {
            $discountAmount = $subtotal * ($request->discount_value / 100);
        } elseif ($request->discount_type === 'fixed' && $request->discount_value > 0) {
            $discountAmount = min($subtotal, $request->discount_value);
        }

        $afterDiscount = $subtotal - $discountAmount;

        // Apply tax
        $taxAmount = 0;
        if ($request->tax_rate > 0) {
            $taxAmount = $afterDiscount * ($request->tax_rate / 100);
        }

        $total = $afterDiscount + $taxAmount;

        \Illuminate\Support\Facades\Log::info("Final totals: Subtotal=$subtotal, Tax=$taxAmount, Total=$total");

        return [$subtotal, $taxAmount, $total];
    }
}

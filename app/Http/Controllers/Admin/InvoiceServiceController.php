<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceService;
use Illuminate\Http\Request;

class InvoiceServiceController extends Controller
{
    public function index()
    {
        $services = InvoiceService::latest()->paginate(20);
        return view('admin.invoice-services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.invoice-services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        InvoiceService::create($validated);

        return redirect()->route('admin.invoice-services.index')->with('success', 'Invoice Service created successfully.');
    }

    public function edit(InvoiceService $invoiceService)
    {
        return view('admin.invoice-services.edit', compact('invoiceService'));
    }

    public function update(Request $request, InvoiceService $invoiceService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $invoiceService->update($validated);

        return redirect()->route('admin.invoice-services.index')->with('success', 'Invoice Service updated successfully.');
    }

    public function destroy(InvoiceService $invoiceService)
    {
        $invoiceService->delete();

        return redirect()->route('admin.invoice-services.index')->with('success', 'Invoice Service deleted successfully.');
    }
}

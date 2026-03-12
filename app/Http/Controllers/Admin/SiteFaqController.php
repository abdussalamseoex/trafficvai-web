<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faqs = \App\Models\SiteFaq::orderBy('sort_order')->latest()->paginate(20);
        return view('admin.site-faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.site-faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        \App\Models\SiteFaq::create($validated);

        return redirect()->route('admin.site-faqs.index')->with('success', 'FAQ created successfully.');
    }

    public function edit(\App\Models\SiteFaq $siteFaq)
    {
        return view('admin.site-faqs.edit', compact('siteFaq'));
    }

    public function update(Request $request, \App\Models\SiteFaq $siteFaq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $siteFaq->update($validated);

        return redirect()->route('admin.site-faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(\App\Models\SiteFaq $siteFaq)
    {
        $siteFaq->delete();
        return redirect()->route('admin.site-faqs.index')->with('success', 'FAQ deleted successfully.');
    }
}

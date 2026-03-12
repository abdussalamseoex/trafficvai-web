<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuestPostSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sites = \App\Models\GuestPostSite::latest()->get();
        return view('admin.guest_posts.index', compact('sites'));
    }

    public function create()
    {
        return view('admin.guest_posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'niche' => 'required|string|max:255',
            'da' => 'nullable|integer|min:0|max:100',
            'dr' => 'nullable|integer|min:0|max:100',
            'traffic' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'price_creation_placement' => 'nullable|numeric|min:0',
            'price_link_insertion' => 'nullable|numeric|min:0',
            'delivery_time_days' => 'nullable|integer|min:1',
            'express_delivery_time_days' => 'nullable|integer|min:1',
            'word_count' => 'nullable|integer|min:100',
            'description' => 'nullable|string',
            'sample_post_url' => 'nullable|url',
            'is_active' => 'boolean',
            'link_type' => 'required|string|max:50',
            'max_links_allowed' => 'required|integer|min:1',
            'is_sponsored' => 'boolean',
            'language' => 'required|string|max:50',
            'service_type' => 'required|string|max:50',
            'spam_score' => 'nullable|integer|min:0|max:100'
        ]);

        \App\Models\GuestPostSite::create($validated);
        return redirect()->route('admin.guest-posts.index')->with('success', 'Guest Post Site created successfully.');
    }

    public function show(string $id)
    {
    // Not used
    }

    public function edit(string $id)
    {
        $site = \App\Models\GuestPostSite::findOrFail($id);
        return view('admin.guest_posts.edit', ['guestPost' => $site]);
    }

    public function update(Request $request, string $id)
    {
        $site = \App\Models\GuestPostSite::findOrFail($id);
        $validated = $request->validate([
            'url' => 'required|url',
            'niche' => 'required|string|max:255',
            'da' => 'nullable|integer|min:0|max:100',
            'dr' => 'nullable|integer|min:0|max:100',
            'traffic' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'price_creation_placement' => 'nullable|numeric|min:0',
            'price_link_insertion' => 'nullable|numeric|min:0',
            'delivery_time_days' => 'nullable|integer|min:1',
            'express_delivery_time_days' => 'nullable|integer|min:1',
            'word_count' => 'nullable|integer|min:100',
            'description' => 'nullable|string',
            'sample_post_url' => 'nullable|url',
            'is_active' => 'boolean',
            'link_type' => 'required|string|max:50',
            'max_links_allowed' => 'required|integer|min:1',
            'is_sponsored' => 'boolean',
            'language' => 'required|string|max:50',
            'service_type' => 'required|string|max:50',
            'spam_score' => 'nullable|integer|min:0|max:100'
        ]);

        $site->update($validated);
        return redirect()->route('admin.guest-posts.index')->with('success', 'Guest Post Site updated successfully.');
    }

    public function destroy(string $id)
    {
        $site = \App\Models\GuestPostSite::findOrFail($id);
        $site->delete();
        return redirect()->route('admin.guest-posts.index')->with('success', 'Guest Post Site deleted successfully.');
    }
}

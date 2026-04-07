<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuestPostSiteController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,txt|max:10240'
        ]);

        $file = $request->file('import_file');
        
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        if(!$header) {
            return redirect()->back()->with('error', 'Invalid CSV file format.');
        }

        $header = array_map('strtolower', $header);
        $header = array_map('trim', $header);
        $header = array_map(function($h) {
            return str_replace([' ', '-'], '_', $h);
        }, $header);

        $successCount = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($header) !== count($row)) {
                continue;
            }
            $data = array_combine($header, $row);
            
            if(!isset($data['url']) || empty($data['url'])) continue;
            
            $niche = isset($data['niche']) && !empty($data['niche'])
                ? array_map('trim', explode(',', $data['niche'])) 
                : ['General'];
            
            \App\Models\GuestPostSite::updateOrCreate(
                ['url' => $data['url']],
                [
                    'niche' => $niche,
                    'da' => isset($data['da']) && is_numeric($data['da']) ? (int)$data['da'] : null,
                    'dr' => isset($data['dr']) && is_numeric($data['dr']) ? (int)$data['dr'] : null,
                    'traffic' => isset($data['traffic']) && is_numeric($data['traffic']) ? (int)$data['traffic'] : null,
                    'price' => isset($data['price']) && is_numeric($data['price']) ? (float)$data['price'] : 0.00,
                    'is_active' => isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
                    'link_type' => !empty($data['link_type']) ? $data['link_type'] : 'DoFollow',
                    'max_links_allowed' => isset($data['max_links_allowed']) && is_numeric($data['max_links_allowed']) ? (int)$data['max_links_allowed'] : 1,
                    'is_sponsored' => isset($data['is_sponsored']) ? filter_var($data['is_sponsored'], FILTER_VALIDATE_BOOLEAN) : false,
                    'language' => !empty($data['language']) ? $data['language'] : 'English',
                    'service_type' => !empty($data['service_type']) ? $data['service_type'] : 'Guest Post',
                    'spam_score' => isset($data['spam_score']) && is_numeric($data['spam_score']) ? (int)$data['spam_score'] : null,
                    'price_creation_placement' => isset($data['price_creation_placement']) && is_numeric($data['price_creation_placement']) ? (float)$data['price_creation_placement'] : null,
                    'price_link_insertion' => isset($data['price_link_insertion']) && is_numeric($data['price_link_insertion']) ? (float)$data['price_link_insertion'] : null,
                    'delivery_time_days' => isset($data['delivery_time_days']) && is_numeric($data['delivery_time_days']) ? (int)$data['delivery_time_days'] : null,
                ]
            );
            $successCount++;
        }
        
        fclose($handle);

        return redirect()->back()->with('success', "Successfully imported {$successCount} sites.");
    }

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
            'niche' => 'required|array|min:1',
            'niche.*' => 'string|max:255',
            'da' => 'nullable|integer|min:0|max:100',
            'dr' => 'nullable|integer|min:0|max:100',
            'traffic' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'price_creation_placement' => 'nullable|numeric|min:0',
            'price_link_insertion' => 'nullable|numeric|min:0',
            'delivery_time_days' => 'nullable|integer|min:1',
            'express_delivery_time_days' => 'nullable|integer|min:1',
            'express_delivery_price' => 'nullable|numeric|min:0',
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

    public function show(\App\Models\GuestPostSite $guestPost)
    {
    // Not used
    }

    public function edit(\App\Models\GuestPostSite $guestPost)
    {
        return view('admin.guest_posts.edit', ['guestPost' => $guestPost]);
    }

    public function update(Request $request, \App\Models\GuestPostSite $guestPost)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'niche' => 'required|array|min:1',
            'niche.*' => 'string|max:255',
            'da' => 'nullable|integer|min:0|max:100',
            'dr' => 'nullable|integer|min:0|max:100',
            'traffic' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'price_creation_placement' => 'nullable|numeric|min:0',
            'price_link_insertion' => 'nullable|numeric|min:0',
            'delivery_time_days' => 'nullable|integer|min:1',
            'express_delivery_time_days' => 'nullable|integer|min:1',
            'express_delivery_price' => 'nullable|numeric|min:0',
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

        $guestPost->update($validated);
        return redirect()->route('admin.guest-posts.index')->with('success', 'Guest Post Site updated successfully.');
    }

    public function destroy(\App\Models\GuestPostSite $guestPost)
    {
        $guestPost->delete();
        return redirect()->route('admin.guest-posts.index')->with('success', 'Guest Post Site deleted successfully.');
    }
}

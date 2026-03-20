<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.site-settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $settings = $request->except(['_token', 'site_logo', 'site_favicon']);

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => 'storage/' . $path]
            );
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => 'storage/' . $path]
            );
            
            // Auto-clone to public/favicon.ico to prevent server 404 intercepts
            try {
                copy(storage_path('app/public/' . $path), public_path('favicon.ico'));
            } catch (\Exception $e) {
                // Silently ignore if permissions fail
            }
        }

        // Initialize empty arrays for dynamic lists if they were completely removed in the UI
        $dynamicArrayKeys = [
            'header_menu',
            'header_services_menu',
            'footer_col_1_links',
            'footer_col_2_links',
            'footer_col_3_links'
        ];

        foreach ($dynamicArrayKeys as $arrayKey) {
            if (!isset($settings[$arrayKey])) {
                $settings[$arrayKey] = [];
            }
        }

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\HandlesSeoMetadata;

class ServiceController extends Controller
{
    use HandlesSeoMetadata;
    public function index()
    {
        $services = \App\Models\Service::where('service_type', 'seo')->withCount('packages', 'requirements')->latest()->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->where('type', 'service')->get();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'slug' => 'required|string|max:255|unique:services',
            'description' => 'nullable|string',
            'hero_image' => 'nullable|image|max:4096',
            'hero_video_url' => 'nullable|url|max:500',
            'sample_link' => 'nullable|url|max:500',
            'requirements' => 'nullable|array',
            'requirements.*.name' => 'required|string|max:255',
            'requirements.*.type' => 'required|string|in:text,url,textarea,file',
            'requirements.*.is_required' => 'boolean',
            'packages' => 'required|array|size:3',
            'packages.*.name' => 'required|string|max:255',
            'packages.*.description' => 'nullable|string',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.emergency_fee' => 'nullable|numeric|min:0',
            'packages.*.turnaround_time_days' => 'nullable|integer|min:1',
            'packages.*.express_turnaround_time_days' => 'nullable|integer|min:1',
            'packages.*.features' => 'nullable|array',
            'packages.*.features.*' => 'required|string',
            'addons' => 'nullable|array',
            'addons.*.name' => 'required|string|max:255',
            'addons.*.description' => 'nullable|string',
            'addons.*.price' => 'required|numeric|min:0',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required|string|max:500',
            'faqs.*.answer' => 'required|string',
        ]);

        // Handle hero image upload
        $heroImagePath = null;
        if ($request->hasFile('hero_image')) {
            $heroImagePath = $request->file('hero_image')->store('services', 'public');
        }

        $service = \App\Models\Service::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'faqs' => $validated['faqs'] ?? null,
            'hero_image' => $heroImagePath,
            'hero_video_url' => $validated['hero_video_url'] ?? null,
            'sample_link' => $validated['sample_link'] ?? null,
        ]);

        // Requirements
        if (!empty($validated['requirements'])) {
            foreach ($validated['requirements'] as $req) {
                $service->requirements()->create([
                    'name' => $req['name'],
                    'type' => $req['type'],
                    'is_required' => $req['is_required'] ?? false,
                ]);
            }
        }

        // Packages
        foreach ($validated['packages'] as $pkg) {
            $service->packages()->create([
                'name' => $pkg['name'],
                'description' => $pkg['description'] ?? null,
                'price' => $pkg['price'],
                'emergency_fee' => $pkg['emergency_fee'] ?? null,
                'turnaround_time_days' => $pkg['turnaround_time_days'] ?? null,
                'express_turnaround_time_days' => $pkg['express_turnaround_time_days'] ?? null,
                'features' => $pkg['features'] ?? [],
            ]);
        }

        // Addons
        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $addon) {
                $service->addons()->create([
                    'name' => $addon['name'],
                    'description' => $addon['description'] ?? null,
                    'price' => $addon['price'],
                ]);
            }
        }

        $this->syncSeoMetadata($service, $request);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(\App\Models\Service $service)
    {
        $service->load('requirements');
        $categories = \App\Models\Category::where('is_active', true)->where('type', 'service')->get();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, \App\Models\Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'slug' => 'required|string|max:255|unique:services,slug,' . $service->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'hero_image' => 'nullable|image|max:4096',
            'hero_video_url' => 'nullable|url|max:500',
            'sample_link' => 'nullable|url|max:500',
            'requirements' => 'nullable|array',
            'requirements.*.id' => 'nullable|exists:service_requirements,id',
            'requirements.*.name' => 'required|string|max:255',
            'requirements.*.type' => 'required|string|in:text,url,textarea,file',
            'requirements.*.is_required' => 'boolean',
            'packages' => 'required|array|size:3',
            'packages.*.id' => 'nullable|exists:packages,id',
            'packages.*.name' => 'required|string|max:255',
            'packages.*.description' => 'nullable|string',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.emergency_fee' => 'nullable|numeric|min:0',
            'packages.*.turnaround_time_days' => 'nullable|integer|min:1',
            'packages.*.express_turnaround_time_days' => 'nullable|integer|min:1',
            'packages.*.features' => 'nullable|array',
            'packages.*.features.*' => 'required|string',
            'addons' => 'nullable|array',
            'addons.*.id' => 'nullable|exists:addons,id',
            'addons.*.name' => 'required|string|max:255',
            'addons.*.description' => 'nullable|string',
            'addons.*.price' => 'required|numeric|min:0',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required|string|max:500',
            'faqs.*.answer' => 'required|string',
        ]);

        // Handle hero image upload
        $heroImagePath = $service->hero_image;
        if ($request->hasFile('hero_image')) {
            // Delete old image if exists
            if ($service->hero_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->hero_image);
            }
            $heroImagePath = $request->file('hero_image')->store('services', 'public');
        }
        elseif ($request->boolean('remove_hero_image')) {
            if ($service->hero_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->hero_image);
            }
            $heroImagePath = null;
        }

        $service->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'faqs' => $validated['faqs'] ?? null,
            'hero_image' => $heroImagePath,
            'hero_video_url' => $validated['hero_video_url'] ?? null,
            'sample_link' => $validated['sample_link'] ?? null,
        ]);

        // Sync requirements
        $existingReqIds = [];
        if (!empty($validated['requirements'])) {
            foreach ($validated['requirements'] as $req) {
                if (isset($req['id']) && $req['id'] !== '') {
                    $requirement = $service->requirements()->find($req['id']);
                    if ($requirement) {
                        $requirement->update([
                            'name' => $req['name'],
                            'type' => $req['type'],
                            'is_required' => $req['is_required'] ?? false,
                        ]);
                        $existingReqIds[] = $requirement->id;
                    }
                }
                else {
                    $newReq = $service->requirements()->create([
                        'name' => $req['name'],
                        'type' => $req['type'],
                        'is_required' => $req['is_required'] ?? false,
                    ]);
                    $existingReqIds[] = $newReq->id;
                }
            }
        }
        $service->requirements()->whereNotIn('id', $existingReqIds)->delete();

        // Sync Packages
        $existingPkgIds = [];
        foreach ($validated['packages'] as $pkg) {
            if (isset($pkg['id']) && $pkg['id'] !== '') {
                $package = $service->packages()->find($pkg['id']);
                if ($package) {
                    $package->update([
                        'name' => $pkg['name'],
                        'description' => $pkg['description'] ?? null,
                        'price' => $pkg['price'],
                        'emergency_fee' => $pkg['emergency_fee'] ?? null,
                        'turnaround_time_days' => $pkg['turnaround_time_days'] ?? null,
                        'express_turnaround_time_days' => $pkg['express_turnaround_time_days'] ?? null,
                        'features' => $pkg['features'] ?? [],
                    ]);
                    $existingPkgIds[] = $package->id;
                }
            }
            else {
                $newPkg = $service->packages()->create([
                    'name' => $pkg['name'],
                    'description' => $pkg['description'] ?? null,
                    'price' => $pkg['price'],
                    'emergency_fee' => $pkg['emergency_fee'] ?? null,
                    'turnaround_time_days' => $pkg['turnaround_time_days'] ?? null,
                    'express_turnaround_time_days' => $pkg['express_turnaround_time_days'] ?? null,
                    'features' => $pkg['features'] ?? [],
                ]);
                $existingPkgIds[] = $newPkg->id;
            }
        }
        $service->packages()->whereNotIn('id', $existingPkgIds)->delete();

        // Sync Addons
        $existingAddonIds = [];
        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $ad) {
                if (isset($ad['id']) && $ad['id'] !== '') {
                    $addon = $service->addons()->find($ad['id']);
                    if ($addon) {
                        $addon->update([
                            'name' => $ad['name'],
                            'description' => $ad['description'] ?? null,
                            'price' => $ad['price'],
                        ]);
                        $existingAddonIds[] = $addon->id;
                    }
                }
                else {
                    $newAddon = $service->addons()->create([
                        'name' => $ad['name'],
                        'description' => $ad['description'] ?? null,
                        'price' => $ad['price'],
                    ]);
                    $existingAddonIds[] = $newAddon->id;
                }
            }
        }
        $service->addons()->whereNotIn('id', $existingAddonIds)->delete();

        $this->syncSeoMetadata($service, $request);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(\App\Models\Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\HandlesSeoMetadata;

class CampaignController extends Controller
{
    use HandlesSeoMetadata;
    /**
     * Get a human-readable title for the given type slug
     */
    private function getTitle($type)
    {
        return Str::title(str_replace('-', ' ', $type));
    }

    public function index($type)
    {
        $services = \App\Models\Service::where('service_type', $type)->withCount('packages', 'requirements')->latest()->get();
        $title = $this->getTitle($type);
        return view('admin.campaigns.index', compact('services', 'type', 'title'));
    }

    public function create($type)
    {
        $categories = \App\Models\Category::where('is_active', true)->where('type', 'service')->get();
        $title = $this->getTitle($type);
        return view('admin.campaigns.create', compact('categories', 'type', 'title'));
    }

    public function store(Request $request, $type)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'slug' => 'required|string|max:255|unique:services',
            'description' => 'nullable|string',
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
            'hero_image' => 'nullable|image|max:4096',
            'hero_video_url' => 'nullable|url|max:500',
            'sample_link' => 'nullable|url|max:500',
        ]);

        $heroImagePath = null;
        if ($request->hasFile('hero_image')) {
            $heroImagePath = $request->file('hero_image')->store('services', 'public');
        }

        $service = \App\Models\Service::create([
            'service_type' => $type,
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

        if (!empty($validated['requirements'])) {
            foreach ($validated['requirements'] as $req) {
                $service->requirements()->create([
                    'name' => $req['name'],
                    'type' => $req['type'],
                    'is_required' => $req['is_required'] ?? false,
                ]);
            }
        }

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

        $title = $this->getTitle($type);
        return redirect()->route('admin.campaigns.index', $type)->with('success', $title . ' package created successfully.');
    }

    public function edit($type, \App\Models\Service $campaign)
    {
        $campaign->load('requirements');
        $categories = \App\Models\Category::where('is_active', true)->where('type', 'service')->get();
        $title = $this->getTitle($type);
        $service = $campaign; // matching variable name
        return view('admin.campaigns.edit', compact('service', 'categories', 'type', 'title'));
    }

    public function update(Request $request, $type, \App\Models\Service $campaign)
    {
        $service = $campaign;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'slug' => 'required|string|max:255|unique:services,slug,' . $service->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
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
            'hero_image' => 'nullable|image|max:4096',
            'hero_video_url' => 'nullable|url|max:500',
            'sample_link' => 'nullable|url|max:500',
        ]);

        $heroImagePath = $service->hero_image;
        if ($request->hasFile('hero_image')) {
            if ($service->hero_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->hero_image);
            }
            $heroImagePath = $request->file('hero_image')->store('services', 'public');
        } elseif ($request->boolean('remove_hero_image')) {
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

        // Code exactly the same as WebsiteTrafficController for requirements/packages/addons
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
                    /** @var \App\Models\ServiceRequirement $newReq */
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
                /** @var \App\Models\Package $newPkg */
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
                    /** @var \App\Models\Addon $newAddon */
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

        $title = $this->getTitle($type);
        return redirect()->route('admin.campaigns.index', $type)->with('success', $title . ' package updated successfully.');
    }

    public function destroy($type, \App\Models\Service $campaign)
    {
        $campaign->delete();
        $title = $this->getTitle($type);
        return redirect()->route('admin.campaigns.index', $type)->with('success', $title . ' package deleted successfully.');
    }
}

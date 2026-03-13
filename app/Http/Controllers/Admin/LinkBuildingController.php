<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class LinkBuildingController extends Controller
{
    public function index()
    {
        $services = Service::where('service_type', 'link-building')
            ->withCount('packages', 'requirements')
            ->latest()
            ->get();
        return view('admin.link-building.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->where('type', 'service')->get();
        return view('admin.link-building.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                                  => 'required|string|max:255',
            'category_id'                           => 'nullable|exists:categories,id',
            'slug'                                  => 'required|string|max:255|unique:services',
            'description'                           => 'nullable|string',
            'requirements'                          => 'nullable|array',
            'requirements.*.name'                   => 'required|string|max:255',
            'requirements.*.type'                   => 'required|string|in:text,url,textarea,file',
            'requirements.*.is_required'            => 'boolean',
            'packages'                              => 'required|array|size:3',
            'packages.*.name'                       => 'required|string|max:255',
            'packages.*.description'                => 'nullable|string',
            'packages.*.price'                      => 'required|numeric|min:0',
            'packages.*.emergency_fee'              => 'nullable|numeric|min:0',
            'packages.*.turnaround_time_days'       => 'nullable|integer|min:1',
            'packages.*.express_turnaround_time_days' => 'nullable|integer|min:1',
            'packages.*.features'                   => 'nullable|array',
            'packages.*.features.*'                 => 'required|string',
            'addons'                                => 'nullable|array',
            'addons.*.name'                         => 'required|string|max:255',
            'addons.*.description'                  => 'nullable|string',
            'addons.*.price'                        => 'required|numeric|min:0',
            'faqs'                                  => 'nullable|array',
            'faqs.*.question'                       => 'required|string|max:500',
            'faqs.*.answer'                         => 'required|string',
        ]);

        $service = Service::create([
            'service_type' => 'link-building',
            'name'         => $validated['name'],
            'category_id'  => $validated['category_id'] ?? null,
            'slug'         => $validated['slug'],
            'description'  => $validated['description'] ?? null,
            'is_active'    => true,
            'faqs'         => $validated['faqs'] ?? null,
        ]);

        if (!empty($validated['requirements'])) {
            foreach ($validated['requirements'] as $req) {
                $service->requirements()->create([
                    'name'        => $req['name'],
                    'type'        => $req['type'],
                    'is_required' => $req['is_required'] ?? false,
                ]);
            }
        }

        foreach ($validated['packages'] as $pkg) {
            $service->packages()->create([
                'name'                        => $pkg['name'],
                'description'                 => $pkg['description'] ?? null,
                'price'                       => $pkg['price'],
                'emergency_fee'               => $pkg['emergency_fee'] ?? null,
                'turnaround_time_days'        => $pkg['turnaround_time_days'] ?? null,
                'express_turnaround_time_days'=> $pkg['express_turnaround_time_days'] ?? null,
                'features'                    => $pkg['features'] ?? [],
            ]);
        }

        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $addon) {
                $service->addons()->create([
                    'name'        => $addon['name'],
                    'description' => $addon['description'] ?? null,
                    'price'       => $addon['price'],
                ]);
            }
        }

        return redirect()->route('admin.link-building.index')->with('success', 'Link Building service created successfully.');
    }

    public function edit(Service $linkBuilding)
    {
        $linkBuilding->load('requirements');
        $categories = Category::where('is_active', true)->where('type', 'service')->get();
        $service = $linkBuilding;
        return view('admin.link-building.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $linkBuilding)
    {
        $service = $linkBuilding;
        $validated = $request->validate([
            'name'                                  => 'required|string|max:255',
            'category_id'                           => 'nullable|exists:categories,id',
            'slug'                                  => 'required|string|max:255|unique:services,slug,' . $service->id,
            'description'                           => 'nullable|string',
            'is_active'                             => 'boolean',
            'requirements'                          => 'nullable|array',
            'requirements.*.id'                     => 'nullable|exists:service_requirements,id',
            'requirements.*.name'                   => 'required|string|max:255',
            'requirements.*.type'                   => 'required|string|in:text,url,textarea,file',
            'requirements.*.is_required'            => 'boolean',
            'packages'                              => 'required|array|size:3',
            'packages.*.id'                         => 'nullable|exists:packages,id',
            'packages.*.name'                       => 'required|string|max:255',
            'packages.*.description'                => 'nullable|string',
            'packages.*.price'                      => 'required|numeric|min:0',
            'packages.*.emergency_fee'              => 'nullable|numeric|min:0',
            'packages.*.turnaround_time_days'       => 'nullable|integer|min:1',
            'packages.*.express_turnaround_time_days' => 'nullable|integer|min:1',
            'packages.*.features'                   => 'nullable|array',
            'packages.*.features.*'                 => 'required|string',
            'addons'                                => 'nullable|array',
            'addons.*.id'                           => 'nullable|exists:addons,id',
            'addons.*.name'                         => 'required|string|max:255',
            'addons.*.description'                  => 'nullable|string',
            'addons.*.price'                        => 'required|numeric|min:0',
            'faqs'                                  => 'nullable|array',
            'faqs.*.question'                       => 'required|string|max:500',
            'faqs.*.answer'                         => 'required|string',
        ]);

        $service->update([
            'name'        => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'slug'        => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->has('is_active'),
            'faqs'        => $validated['faqs'] ?? null,
        ]);

        $existingReqIds = [];
        if (!empty($validated['requirements'])) {
            foreach ($validated['requirements'] as $req) {
                if (isset($req['id']) && $req['id'] !== '') {
                    $requirement = $service->requirements()->find($req['id']);
                    if ($requirement) {
                        $requirement->update([
                            'name'        => $req['name'],
                            'type'        => $req['type'],
                            'is_required' => $req['is_required'] ?? false,
                        ]);
                        $existingReqIds[] = $requirement->id;
                    }
                } else {
                    $newReq = $service->requirements()->create([
                        'name'        => $req['name'],
                        'type'        => $req['type'],
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
                        'name'                         => $pkg['name'],
                        'description'                  => $pkg['description'] ?? null,
                        'price'                        => $pkg['price'],
                        'emergency_fee'                => $pkg['emergency_fee'] ?? null,
                        'turnaround_time_days'         => $pkg['turnaround_time_days'] ?? null,
                        'express_turnaround_time_days' => $pkg['express_turnaround_time_days'] ?? null,
                        'features'                     => $pkg['features'] ?? [],
                    ]);
                    $existingPkgIds[] = $package->id;
                }
            } else {
                $newPkg = $service->packages()->create([
                    'name'                         => $pkg['name'],
                    'description'                  => $pkg['description'] ?? null,
                    'price'                        => $pkg['price'],
                    'emergency_fee'                => $pkg['emergency_fee'] ?? null,
                    'turnaround_time_days'         => $pkg['turnaround_time_days'] ?? null,
                    'express_turnaround_time_days' => $pkg['express_turnaround_time_days'] ?? null,
                    'features'                     => $pkg['features'] ?? [],
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
                            'name'        => $ad['name'],
                            'description' => $ad['description'] ?? null,
                            'price'       => $ad['price'],
                        ]);
                        $existingAddonIds[] = $addon->id;
                    }
                } else {
                    $newAddon = $service->addons()->create([
                        'name'        => $ad['name'],
                        'description' => $ad['description'] ?? null,
                        'price'       => $ad['price'],
                    ]);
                    $existingAddonIds[] = $newAddon->id;
                }
            }
        }
        $service->addons()->whereNotIn('id', $existingAddonIds)->delete();

        return redirect()->route('admin.link-building.index')->with('success', 'Link Building service updated successfully.');
    }

    public function destroy(Service $linkBuilding)
    {
        $linkBuilding->delete();
        return redirect()->route('admin.link-building.index')->with('success', 'Link Building service deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SeoGlobalSetting;
use App\Services\SeoService;

class SeoSettingsController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index()
    {
        $settings = $this->seoService->getGlobalSettings();
        return view('admin.seo.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string',
            'twitter_handle' => 'nullable|string',
            'default_og_image' => 'nullable|image|max:2048',
        ]);

        $settings = SeoGlobalSetting::first() ?? new SeoGlobalSetting();
        $data = $request->except(['default_og_image', '_token']);

        if ($request->hasFile('default_og_image')) {
            $data['default_og_image'] = $request->file('default_og_image')->store('seo', 'public');
        }

        $settings->fill($data)->save();

        return redirect()->back()->with('success', 'Global SEO settings updated.');
    }

    public function analytics()
    {
        $settings = $this->seoService->getGlobalSettings();
        return view('admin.seo.analytics', compact('settings'));
    }

    public function updateAnalytics(Request $request)
    {
        $settings = SeoGlobalSetting::first() ?? new SeoGlobalSetting();
        $settings->fill($request->only(['ga_code', 'gsc_verification', 'header_scripts', 'footer_scripts']))->save();
        return redirect()->back()->with('success', 'Analytics & Scripts updated.');
    }

    public function robots()
    {
        $settings = $this->seoService->getGlobalSettings();
        return view('admin.seo.robots', compact('settings'));
    }

    public function updateRobots(Request $request)
    {
        $settings = SeoGlobalSetting::first() ?? new SeoGlobalSetting();
        $settings->robots_txt = $request->robots_txt;
        $settings->save();
        return redirect()->back()->with('success', 'Robots.txt updated.');
    }
}

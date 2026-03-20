<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Redirect to canonical URL if accessed via /page/ prefix but has a prettier URL
        $seoService = app(\App\Services\SeoService::class);
        $canonicalUrl = $seoService->getEntityUrl($page);
        $currentUrl = request()->url();
        
        if ($currentUrl !== $canonicalUrl && str_contains(request()->getPathInfo(), '/page/')) {
            return redirect($canonicalUrl, 301);
        }

        return view('pages.show', compact('page'));
    }
}

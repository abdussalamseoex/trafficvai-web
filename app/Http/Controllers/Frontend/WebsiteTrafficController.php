<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteTrafficController extends Controller
{
    public function index()
    {
        $page = \App\Models\Page::where('slug', 'website-traffic')->first();
        $categories = \App\Models\Category::where('is_active', true)
            ->with(['services' => function ($query) {
            $query->where('is_active', true)->where('service_type', 'traffic')->with('packages');
        }])
            ->get();

        $uncategorizedServices = \App\Models\Service::where('is_active', true)
            ->where('service_type', 'traffic')
            ->whereNull('category_id')
            ->with('packages')
            ->get();

        return view('traffic.index', compact('categories', 'uncategorizedServices', 'page'));
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteTrafficController extends Controller
{
    public function index()
    {
        $page = \App\Models\Page::where('slug', 'website-traffic')->first();

        $bdtRate = (float) \App\Models\Setting::get('bdt_exchange_rate', 120);
        if ($bdtRate <= 0) $bdtRate = 120;

        $pointBundles = [
            ['name' => 'Starter',  'points' => 5000,   'usd' => 5.00,  'popular' => false, 'color' => 'indigo'],
            ['name' => 'Growth',   'points' => 15000,  'usd' => 13.50, 'popular' => true,  'color' => 'orange'],
            ['name' => 'Pro',      'points' => 35000,  'usd' => 28.00, 'popular' => false, 'color' => 'indigo'],
            ['name' => 'Scale',    'points' => 100000, 'usd' => 70.00, 'popular' => false, 'color' => 'indigo'],
        ];

        return view('traffic.index', compact('page', 'bdtRate', 'pointBundles'));
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SiteFaq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch published FAQs, order by sort_order
        $faqs = SiteFaq::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Group by category if needed, or just send flat list if we want a simple accordion
        // For a better UI, grouping by category is great.
        $faqsByCategory = $faqs->groupBy('category');

        return view('client.faq.index', compact('faqsByCategory'));
    }
}

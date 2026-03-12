<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Fetch active campaigns/orders to display their names in the dashboard
        $activeCampaigns = Order::with(['package', 'guestPostSite'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['processing', 'completed']) // Assuming 'completed' means ongoing for SEO, or actively working
            ->latest()
            ->take(5)
            ->get();

        return view('client.reports.index', compact('user', 'activeCampaigns'));
    }
}

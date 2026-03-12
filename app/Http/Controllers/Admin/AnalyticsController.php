<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalOrders = \App\Models\Order::count();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total_amount');
        $totalUsers = \App\Models\User::where('is_admin', false)->count();
        $totalLeads = \App\Models\Lead::count();

        $recentOrders = \App\Models\Order::with(['user', 'package.service', 'guestPostSite'])->latest()->limit(5)->get();

        // Basic Monthly Growth (current month vs previous)
        $thisMonthRevenue = \App\Models\Order::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('total_amount');
        $lastMonthRevenue = \App\Models\Order::where('status', 'completed')->whereMonth('created_at', now()->subMonth()->month)->sum('total_amount');

        return view('admin.analytics.index', compact(
            'totalOrders', 'totalRevenue', 'totalUsers', 'totalLeads',
            'recentOrders', 'thisMonthRevenue', 'lastMonthRevenue'
        ));
    }
}

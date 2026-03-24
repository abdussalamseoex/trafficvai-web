<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = \App\Models\Order::count();
        $totalRevenue = \App\Models\Order::where('payment_status', 'paid')->sum('total_amount');
        $totalServices = \App\Models\Service::count();
        $totalGuestPosts = \App\Models\GuestPostSite::count();
        $recentOrders = \App\Models\Order::with(['user', 'package.service', 'guestPostSite'])
            ->withCount(['messages as unread_messages_count' => function ($query) {
            $query->where('is_read', false)
                ->whereHas('user', function ($q) {
                $q->where('is_admin', false);
            }
            );
        }])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'totalServices', 'totalGuestPosts', 'recentOrders'));
    }
}

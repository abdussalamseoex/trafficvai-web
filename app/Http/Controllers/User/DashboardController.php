<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $totalOrders = $user->orders()->count();
        $activeOrders = $user->orders()->whereIn('status', ['pending_requirements', 'processing'])->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();
        $recentOrders = $user->orders()
            ->withCount(['messages as unread_messages_count' => function ($query) {
            $query->where('is_read', false)
                ->whereHas('user', function ($q) {
                $q->where('is_admin', true);
            }
            );
        }])
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard', compact('totalOrders', 'activeOrders', 'completedOrders', 'recentOrders'));
    }
}

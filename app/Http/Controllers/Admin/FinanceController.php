<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Display the financial analytics dashboard.
     */
    public function index()
    {
        // 1. Calculate Monthly Recurring Revenue (MRR)
        // Assume active recurring orders have a 'package' and are not 'cancelled'. (Adjust based on your business logic)
        $mrr = Order::whereHas('package')
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->sum('amount'); // In a real scenario, MRR might be derived only from subscriptions.

        // Total all-time revenue
        $totalRevenue = Order::whereIn('status', ['completed', 'processing'])->sum('amount');

        // 2. Client Growth Rate
        $newClientsThisMonth = User::where('is_admin', false)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $newClientsLastMonth = User::where('is_admin', false)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $growthRate = 0;
        if ($newClientsLastMonth > 0) {
            $growthRate = (($newClientsThisMonth - $newClientsLastMonth) / $newClientsLastMonth) * 100;
        }
        elseif ($newClientsThisMonth > 0) {
            $growthRate = 100; // 100% growth if there were 0 last month and some this month
        }

        // 3. Top Selling Services (Past 6 Months)
        $topPackages = Order::whereNotNull('package_id')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select('package_id', DB::raw('count(*) as total'))
            ->groupBy('package_id')
            ->orderBy('total', 'desc')
            ->with('package.service')
            ->take(5)
            ->get();

        // 4. Revenue over the last 6 months (for Chart)
        $monthlyRevenue = [];
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $monthlyRevenue[] = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->whereIn('status', ['completed', 'processing'])
                ->sum('amount');
        }

        return view('admin.finance.index', compact(
            'mrr',
            'totalRevenue',
            'newClientsThisMonth',
            'growthRate',
            'topPackages',
            'months',
            'monthlyRevenue'
        ));
    }
}

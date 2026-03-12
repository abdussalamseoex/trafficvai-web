<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateReferral;
use App\Models\ReferralCode;
use App\Models\Setting;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    /**
     * Admin overview of all affiliates.
     */
    public function index()
    {
        // Overall stats
        $totalAffiliates = ReferralCode::count();
        $totalClicks = ReferralCode::sum('clicks');
        $totalSignups = AffiliateReferral::whereNotNull('referred_user_id')->count();
        $totalCommission = AffiliateReferral::sum('commission_amount');
        $totalOrders = AffiliateReferral::whereNotNull('order_id')->count();

        // All affiliates with eager loaded relationships
        $affiliates = ReferralCode::with(['user', 'referrals'])
            ->withCount([
            'referrals as signups_count' => function ($q) {
            $q->whereNotNull('referred_user_id');
        },
            'referrals as orders_count' => function ($q) {
            $q->whereNotNull('order_id');
        },
        ])
            ->withSum('referrals as total_commission', 'commission_amount')
            ->orderByDesc('clicks')
            ->paginate(25);

        return view('admin.affiliates.index', compact(
            'affiliates',
            'totalAffiliates',
            'totalClicks',
            'totalSignups',
            'totalCommission',
            'totalOrders'
        ));
    }

    /**
     * Detailed view for a single affiliate.
     */
    public function show(ReferralCode $affiliate)
    {
        $affiliate->load(['user', 'referrals.referredUser', 'referrals.order']);

        $stats = [
            'clicks' => $affiliate->clicks,
            'signups' => $affiliate->referrals->whereNotNull('referred_user_id')->count(),
            'orders' => $affiliate->referrals->whereNotNull('order_id')->count(),
            'total_commission' => $affiliate->referrals->sum('commission_amount'),
        ];

        return view('admin.affiliates.show', compact('affiliate', 'stats'));
    }

    /**
     * Save global commission settings.
     */
    public function settings(Request $request)
    {
        $request->validate([
            'affiliate_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'affiliate_min_payout' => ['required', 'numeric', 'min:0'],
        ]);

        Setting::updateOrCreate(
        ['key' => 'affiliate_commission_rate'],
        ['value' => $request->affiliate_commission_rate, 'group' => 'general', 'type' => 'text']
        );

        Setting::updateOrCreate(
        ['key' => 'affiliate_min_payout'],
        ['value' => $request->affiliate_min_payout, 'group' => 'general', 'type' => 'text']
        );

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Commission settings saved successfully.');
    }
}

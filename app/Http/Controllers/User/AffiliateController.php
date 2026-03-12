<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AffiliateReferral;
use App\Models\ReferralCode;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    /**
     * Display the affiliate dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Get or create the user's referral code
        $referralCode = ReferralCode::getOrCreateForUser($user);

        $referralLink = url('/ref/' . $referralCode->code);

        // Load real stats
        $signups = AffiliateReferral::where('referral_code_id', $referralCode->id)
            ->whereNotNull('referred_user_id')
            ->count();

        $ordersCount = AffiliateReferral::where('referral_code_id', $referralCode->id)
            ->whereNotNull('order_id')
            ->count();

        $earnings = AffiliateReferral::where('referral_code_id', $referralCode->id)
            ->sum('commission_amount');

        $conversionRate = $referralCode->clicks > 0
            ? round(($signups / $referralCode->clicks) * 100, 1) . '%'
            : '0%';

        $stats = [
            'clicks' => $referralCode->clicks,
            'signups' => $signups,
            'orders' => $ordersCount,
            'earnings' => $earnings,
            'conversion_rate' => $conversionRate,
        ];

        $recentReferrals = AffiliateReferral::where('referral_code_id', $referralCode->id)
            ->with(['referredUser', 'order'])
            ->latest()
            ->take(20)
            ->get();

        return view('client.affiliate.index', compact('user', 'referralLink', 'stats', 'recentReferrals'));
    }
}

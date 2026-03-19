<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuestPostController extends Controller
{
    public function index()
    {
        $sites = \App\Models\GuestPostSite::where('is_active', true)->get();
        $gateways = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
        $activeCoupons = \App\Models\Coupon::where('status', true)
            ->where('is_global', true)
            ->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
            ->where(function ($query) {
            $query->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
        })
            ->get();
        return view('guest_posts.index', compact('sites', 'gateways', 'activeCoupons'));
    }

    public function checkout(Request $request, \App\Models\GuestPostSite $guestPost)
    {
        return redirect()->route('client.guest_posts.index', [
            'site_id' => $guestPost->id
        ]);
    }
}

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
        $tier = $request->input('service_tier', 'placement');
        $price = $guestPost->price;
        if ($tier === 'creation_placement')
            $price = $guestPost->price_creation_placement;
        if ($tier === 'link_insertion')
            $price = $guestPost->price_link_insertion;

        // Add emergency fee if selected
        if ($request->input('is_emergency') == '1') {
            $price += 50;
        }

        $subtotalAmount = $price;
        $discountAmount = 0;
        $couponId = null;

        if ($request->filled('coupon_code')) {
            $coupon = \App\Models\Coupon::where('code', $request->input('coupon_code'))->first();
            if ($coupon && $coupon->isValid()) {
                if ($coupon->is_global) {
                    $couponId = $coupon->id;
                    if ($coupon->type === 'percentage') {
                        $discountAmount = ($subtotalAmount * $coupon->value) / 100;
                    }
                    else {
                        $discountAmount = $coupon->value;
                    }
                    $price = max(0, $subtotalAmount - $discountAmount);
                    $coupon->increment('used_count');
                }
            }
        }

        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'project_id' => $request->input('project_id'),
            'guest_post_site_id' => $guestPost->id,
            'package_id' => null,
            'status' => 'pending_payment',
            'subtotal_amount' => $subtotalAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $price,
            'coupon_id' => $couponId,
            'service_tier' => $tier,
            'is_emergency' => $request->input('is_emergency') == '1',
        ]);

        $paymentMethod = $request->input('payment_method', 'stripe');

        $partialResponse = \App\Services\Payments\PaymentGatewayManager::processPotentialWalletPayment($request, $order);
        if ($partialResponse)
            return $partialResponse;

        return \App\Services\Payments\PaymentGatewayManager::resolve($paymentMethod)->processPayment($order);
    }
}

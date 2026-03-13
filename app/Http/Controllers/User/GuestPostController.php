<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GuestPostSite;
use App\Models\Order;
use Illuminate\Http\Request;

class GuestPostController extends Controller
{
    public function index(Request $request)
    {
        $query = GuestPostSite::query();

        // Advanced Filtering
        if ($request->filled('min_da')) {
            $query->where('da', '>=', $request->min_da);
        }
        if ($request->filled('max_da')) {
            $query->where('da', '<=', $request->max_da);
        }

        if ($request->filled('min_dr')) {
            $query->where('dr', '>=', $request->min_dr);
        }
        if ($request->filled('max_dr')) {
            $query->where('dr', '<=', $request->max_dr);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_traffic')) {
            $query->where('traffic', '>=', $request->min_traffic);
        }

        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('niche', 'like', '%' . $request->category . '%');
        }

        if ($request->filled('link_type') && $request->link_type !== 'All') {
            $query->where('link_type', $request->link_type);
        }

        if ($request->filled('max_links_allowed')) {
            $query->where('max_links_allowed', '>=', $request->max_links_allowed);
        }

        if ($request->filled('is_sponsored') && $request->is_sponsored !== 'All') {
            $is_sponsored = $request->is_sponsored === 'Yes' ? 1 : 0;
            $query->where('is_sponsored', $is_sponsored);
        }

        if ($request->filled('language') && $request->language !== 'All') {
            $query->where('language', $request->language);
        }

        if ($request->filled('service_type') && $request->service_type !== 'All') {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('max_spam_score')) {
            $query->where('spam_score', '<=', $request->max_spam_score);
        }

        $sites = $query->latest()->paginate(24)->withQueryString();

        $activeCoupons = \App\Models\Coupon::where('status', true)
            ->where('is_global', true)
            ->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
            ->where(function ($query) {
            $query->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
        })
            ->get();

        return view('client.guest-posts.index', compact('sites', 'activeCoupons'));
    }

    public function show(GuestPostSite $guestPost)
    {
        $activeCoupons = \App\Models\Coupon::where('status', true)
            ->where('is_global', true)
            ->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
            ->where(function ($query) {
            $query->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
        })
            ->get();

        $gateways = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
        $cryptoEnabled = \App\Models\Setting::get('gateway_crypto_enabled', '0') == '1';
        $bdEnabled = \App\Models\Setting::get('gateway_bd_enabled', '0') == '1';

        return view('client.guest-posts.show', compact('guestPost', 'gateways', 'cryptoEnabled', 'bdEnabled', 'activeCoupons'));
    }

    public function checkout(Request $request, GuestPostSite $guestPost)
    {
        $request->validate([
            'service_tier' => 'required|in:placement,creation_placement,link_insertion',
            'is_emergency' => 'nullable|boolean',
        ]);

        $totalAmount = $guestPost->price;
        $serviceName = 'Guest Post Placement';

        if ($request->service_tier === 'creation_placement' && $guestPost->price_creation_placement) {
            $totalAmount = $guestPost->price_creation_placement;
            $serviceName = 'Guest Post Creation & Placement';
        }
        elseif ($request->service_tier === 'link_insertion' && $guestPost->price_link_insertion) {
            $totalAmount = $guestPost->price_link_insertion;
            $serviceName = 'Guest Post Link Insertion';
        }

        $isEmergency = $request->input('is_emergency', false);
        if ($isEmergency) {
            $totalAmount += ($guestPost->express_delivery_price ?? 50); // Dynamic emergency fee for guest posts
        }

        $subtotalAmount = $totalAmount;
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
                    $totalAmount = max(0, $subtotalAmount - $discountAmount);
                    $coupon->increment('used_count');
                }
            }
        }

        $paymentMethod = $request->input('payment_method', 'stripe');
        $order = Order::create([
            'user_id' => auth()->id(),
            'project_id' => $request->input('project_id'),
            'guest_post_site_id' => $guestPost->id,
            'status' => 'pending_payment',
            'subtotal_amount' => $subtotalAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'coupon_id' => $couponId,
            'service_tier' => $request->service_tier,
            'is_emergency' => $isEmergency,
            'payment_method' => $paymentMethod,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)->send(new \App\Mail\OrderConfirmation([
                'id' => $order->id,
                'user_name' => auth()->user()->name,
                'title' => 'Guest Post: ' . $guestPost->site_name . ' (' . $serviceName . ')',
                'amount' => $totalAmount,
                'status' => 'Pending',
                'date' => now()->format('M d, Y h:i A')
            ]));
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Mail Error (GuestPost): ' . $e->getMessage());
        }

        $paymentMethod = $request->input('payment_method', 'stripe');

        $partialResponse = \App\Services\Payments\PaymentGatewayManager::processPotentialWalletPayment($request, $order);
        if ($partialResponse)
            return $partialResponse;

        return \App\Services\Payments\PaymentGatewayManager::resolve($paymentMethod)->processPayment($order);
    }
}

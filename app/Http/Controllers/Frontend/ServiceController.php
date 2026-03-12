<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->where('type', 'service')
            ->with(['services' => function ($query) {
            $query->where('is_active', true)->where('service_type', 'seo')->with('packages');
        }])
            ->get();

        // Also fetch services without a category just in case
        $uncategorizedServices = \App\Models\Service::where('is_active', true)
            ->where('service_type', 'seo')
            ->whereNull('category_id')
            ->with('packages')
            ->get();

        return view('services.index', compact('categories', 'uncategorizedServices'));
    }

    public function category(\App\Models\Category $category)
    {
        abort_unless($category->is_active, 404);
        $category->load(['services' => function ($query) {
            $query->where('is_active', true)->with('packages', 'addons');
        }, 'children' => function ($query) {
            $query->where('is_active', true);
        }]);

        return view('services.category', compact('category'));
    }

    public function show(\App\Models\Service $service)
    {
        abort_unless($service->is_active, 404);
        $service->load(['packages', 'addons']);

        $activeCoupons = \App\Models\Coupon::where('status', true)
            ->where(function ($query) use ($service) {
            $query->where('is_global', true)
                ->orWhere('service_id', $service->id);
        })
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

        return view('services.show', compact('service', 'activeCoupons', 'gateways', 'cryptoEnabled', 'bdEnabled'));
    }

    public function checkout(Request $request, \App\Models\Package $package)
    {
        $addonIds = $request->input('addons', []);
        $addons = \App\Models\Addon::whereIn('id', $addonIds)
            ->where('service_id', $package->service_id)
            ->get();

        $subtotalAmount = $package->price + $addons->sum('price');
        $totalAmount = $subtotalAmount;
        $discountAmount = 0;
        $couponId = null;

        if ($request->filled('coupon_code')) {
            $coupon = \App\Models\Coupon::where('code', $request->input('coupon_code'))->first();
            if ($coupon && $coupon->isValid()) {
                if ($coupon->is_global || $coupon->service_id == $package->service_id) {
                    $couponId = $coupon->id;
                    if ($coupon->type === 'percentage') {
                        $discountAmount = ($subtotalAmount * $coupon->value) / 100;
                    }
                    else {
                        $discountAmount = $coupon->value;
                    }
                    $totalAmount = max(0, $subtotalAmount - $discountAmount);

                    // Increment usage
                    $coupon->increment('used_count');
                }
            }
        }

        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'project_id' => $request->input('project_id'),
            'package_id' => $package->id,
            'status' => 'pending_payment',
            'subtotal_amount' => $subtotalAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'coupon_id' => $couponId,
        ]);

        foreach ($addons as $addon) {
            \App\Models\OrderAddon::create([
                'order_id' => $order->id,
                'addon_id' => $addon->id,
                'price' => $addon->price,
            ]);
        }

        $paymentMethod = $request->input('payment_method', 'stripe');

        $partialResponse = \App\Services\Payments\PaymentGatewayManager::processPotentialWalletPayment($request, $order);
        if ($partialResponse)
            return $partialResponse;

        return \App\Services\Payments\PaymentGatewayManager::resolve($paymentMethod)->processPayment($order);
    }

    public function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'service_id' => 'nullable|numeric'
        ]);

        $coupon = \App\Models\Coupon::where('code', $request->code)->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon.']);
        }

        if ($request->filled('service_id') && !$coupon->is_global && $coupon->service_id != $request->service_id) {
            return response()->json(['valid' => false, 'message' => 'Coupon not valid for this service.']);
        }

        if (!$request->filled('service_id') && !$coupon->is_global) {
            return response()->json(['valid' => false, 'message' => 'This coupon is only valid for specific services.']);
        }

        return response()->json([
            'valid' => true,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'message' => 'Coupon applied successfully!'
        ]);
    }
}

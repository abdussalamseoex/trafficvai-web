<?php

namespace App\Http\Controllers\User;

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

        $uncategorizedServices = \App\Models\Service::where('is_active', true)
            ->where('service_type', 'seo')
            ->whereNull('category_id')
            ->with('packages')
            ->get();

        return view('client.services.index', compact('categories', 'uncategorizedServices'));
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

        return view('client.services.show', compact('service', 'activeCoupons', 'gateways', 'cryptoEnabled', 'bdEnabled'));
    }

    public function checkout(Request $request, \App\Models\Package $package)
    {
        $addonIds = $request->input('addons', []);
        $addons = \App\Models\Addon::whereIn('id', $addonIds)
            ->where('service_id', $package->service_id)
            ->get();

        $subtotalAmount = $package->price + $addons->sum('price');
        $isEmergency = $request->input('is_emergency') == 'express';

        if ($isEmergency && $package->emergency_fee) {
            $subtotalAmount += $package->emergency_fee;
        }

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
                    $coupon->increment('used_count');
                }
            }
        }

        $paymentMethod = $request->input('payment_method', 'stripe');
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'project_id' => $request->input('project_id'),
            'package_id' => $package->id,
            'status' => 'pending_payment',
            'subtotal_amount' => $subtotalAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'coupon_id' => $couponId,
            'is_emergency' => $isEmergency,
            'payment_method' => $paymentMethod,
        ]);

        try {
            app(\App\Services\NotificationService::class)->send('order_confirmation', auth()->user(), [
                'order_id' => $order->id,
                'title' => 'Order Confirmation: ' . $package->service->title . ' - ' . $package->name,
                'amount' => $totalAmount,
                'status' => 'Pending Payment',
                'link' => route('client.orders.show', $order)
            ]);
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Mail Error (Service): ' . $e->getMessage());
        }

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
}

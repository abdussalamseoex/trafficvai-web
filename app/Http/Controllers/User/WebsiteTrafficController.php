<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteTrafficController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->where('type', 'service')
            ->with(['services' => function ($query) {
            $query->where('is_active', true)->where('service_type', 'traffic')->with('packages');
        }])
            ->get();

        $uncategorizedServices = \App\Models\Service::where('is_active', true)
            ->where('service_type', 'traffic')
            ->whereNull('category_id')
            ->with('packages')
            ->get();

        return view('client.traffic.index', compact('categories', 'uncategorizedServices'));
    }

    public function show(\App\Models\Service $service)
    {
        abort_unless($service->is_active, 404);
        $service->load(['packages', 'addons']);
        $gateways = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
        $cryptoEnabled = \App\Models\Setting::get('gateway_crypto_enabled', '0') == '1';
        $bdEnabled = \App\Models\Setting::get('gateway_bd_enabled', '0') == '1';

        return view('client.services.show', compact('service', 'gateways', 'cryptoEnabled', 'bdEnabled'));
    }

    public function checkout(Request $request, \App\Models\Package $package)
    {
        $addonIds = $request->input('addons', []);
        $addons = \App\Models\Addon::whereIn('id', $addonIds)
            ->where('service_id', $package->service_id)
            ->get();

        $totalAmount = $package->price + $addons->sum('price');
        $isEmergency = $request->input('is_emergency') == 'express';

        if ($isEmergency && $package->emergency_fee) {
            $totalAmount += $package->emergency_fee;
        }

        $paymentMethod = $request->input('payment_method', 'stripe');
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'project_id' => $request->input('project_id'),
            'package_id' => $package->id,
            'status' => 'pending_payment',
            'total_amount' => $totalAmount,
            'is_emergency' => $isEmergency,
            'payment_method' => $paymentMethod,
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
}

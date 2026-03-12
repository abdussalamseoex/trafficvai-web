<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class GatewaySettingController extends Controller
{
    public function index()
    {
        $settings = Setting::where('group', 'Payment Gateways')->get()->keyBy('key');
        $gatewaysConfig = config('payment_gateways');
        return view('admin.gateway-settings.index', compact('settings', 'gatewaysConfig'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');
        $gatewaysConfig = config('payment_gateways');

        // Dynamically process checkboxes for enabled state
        foreach ($gatewaysConfig as $category => $gateways) {
            foreach ($gateways as $slug => $gateway) {
                if ($slug === 'wallet')
                    continue;

                $data["gateway_{$slug}_enabled"] = $request->has("gateway_{$slug}_enabled") ? '1' : '0';
            }
        }

        // Backward compatibility for Stripe and Bank Transfer keys
        if (isset($data['gateway_stripe_enabled'])) {
            $data['stripe_enabled'] = $data['gateway_stripe_enabled'];
        }
        if (isset($data['gateway_bank_transfer_enabled'])) {
            $data['bank_transfer_enabled'] = $data['gateway_bank_transfer_enabled'];
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => 'Payment Gateways', 'type' => 'text']
            );
        }

        return redirect()->back()->with('success', 'Gateway configurations updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    private function getTitle($type)
    {
        return Str::title(str_replace('-', ' ', $type));
    }

    public function index($type)
    {
        $page = \App\Models\Page::where('slug', $type)->first();
        $types = [$type];
        if ($type === 'seo-campaigns') {
            $types = [
                'keyword-research', 'on-page-seo', 'technical-seo',
                'local-seo', 'content-seo',
                'seo-audit', 'monthly-seo', 'e-commerce-seo'
            ];
        }

        $categories = \App\Models\Category::where('is_active', true)
            ->with(['services' => function ($query) use ($types) {
            $query->where('is_active', true)->whereIn('service_type', $types)->with('packages');
        }])
            ->get();

        $uncategorizedServices = \App\Models\Service::where('is_active', true)
            ->whereIn('service_type', $types)
            ->whereNull('category_id')
            ->with('packages')
            ->get();

        $title = $this->getTitle($type);

        return view('campaigns.index', compact('categories', 'uncategorizedServices', 'type', 'title', 'page'));
    }

    public function show($type, \App\Models\Service $service)
    {
        abort_unless($service->is_active, 404);
        $service->load(['packages', 'addons']);
        $title = $this->getTitle($type);

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

        return view('campaigns.show', compact('service', 'type', 'title', 'gateways', 'cryptoEnabled', 'bdEnabled', 'activeCoupons'));
    }

    public function checkout(Request $request, $type, \App\Models\Package $package)
    {
        $targetRoute = ($type === 'link-building') 
            ? route('client.link_building.show', $package->service->slug) 
            : (preg_match('/^(seo-campaigns|keyword-research|on-page-seo|technical-seo|local-seo|content-seo|seo-audit|monthly-seo|e-commerce-seo)$/', $type) 
                ? route('client.seo_campaigns.show', ['type' => $type, 'service' => $package->service->slug]) 
                : route('client.campaigns.show', ['type' => $type, 'service' => $package->service->slug]));

        return redirect($targetRoute . '?package_id=' . $package->id);
    }
}

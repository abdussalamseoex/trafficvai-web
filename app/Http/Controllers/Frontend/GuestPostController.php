<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GuestPostSite;
use Illuminate\Database\Eloquent\Builder;

class GuestPostController extends Controller
{
    public function index(Request $request)
    {
        $page = \App\Models\Page::where('slug', 'guest-posts')->first();
        
        /** @var Builder $query */
        $query = GuestPostSite::where('is_active', true);

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
            $query->whereJsonContains('niche', $request->category);
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

        if ($request->filled('ownership_type') && $request->ownership_type !== 'All') {
            $query->where('ownership_type', $request->ownership_type);
        }

        // Keyword Search (URL, Niche, or Description)
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->whereRaw('(url LIKE ? OR niche LIKE ? OR description LIKE ?)', ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]);
        }

        $sites = $query->orderByDesc('is_featured')->latest()->get();

        $gateways = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
        $activeCoupons = \App\Models\Coupon::where('status', true)
            ->where('is_private', false)
            ->where('is_global', true)
            ->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
            ->where(function ($query) {
            $query->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
        })
            ->get();
            
        return view('guest_posts.index', compact('sites', 'gateways', 'activeCoupons', 'page'));
    }

    public function checkout(Request $request, \App\Models\GuestPostSite $guestPost)
    {
        return redirect()->route('client.guest_posts.index', [
            'site_id' => $guestPost->id
        ]);
    }
}

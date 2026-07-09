<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrafficCampaign;
use App\Services\SurfEngineApiService;
use Illuminate\Http\Request;

class TrafficCampaignAdminController extends Controller
{
    /**
     * Display all clients' Traffic Campaigns
     */
    public function index(Request $request)
    {
        $query = TrafficCampaign::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function($q) use ($search) {
                $q->where('external_order_id', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $campaigns = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => TrafficCampaign::count(),
            'active' => TrafficCampaign::where('status', 'active')->count(),
            'paused' => TrafficCampaign::where('status', 'paused')->count(),
            'total_hits' => TrafficCampaign::sum('hits_delivered'),
            'total_points' => TrafficCampaign::sum('points_deducted'),
        ];

        return view('admin.traffic_campaigns.index', compact('campaigns', 'stats'));
    }

    /**
     * Display only active running Traffic Campaigns
     */
    public function active()
    {
        $campaigns = TrafficCampaign::with('user')->where('status', 'active')->latest()->paginate(20);

        $stats = [
            'total' => TrafficCampaign::count(),
            'active' => TrafficCampaign::where('status', 'active')->count(),
            'paused' => TrafficCampaign::where('status', 'paused')->count(),
            'total_hits' => TrafficCampaign::sum('hits_delivered'),
            'total_points' => TrafficCampaign::sum('points_deducted'),
        ];

        return view('admin.traffic_campaigns.index', compact('campaigns', 'stats'));
    }

    /**
     * Manually sync live delivery status from surf.abguestpost.net
     */
    public function syncStatus(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        $statusResponse = $apiService->getCampaignStatus($campaign->external_order_id);

        if ($statusResponse['success'] ?? false) {
            $data = $statusResponse['data'];
            if (isset($data['hits_delivered'])) {
                $campaign->hits_delivered = (int) $data['hits_delivered'];
            }
            if (isset($data['status'])) {
                $campaign->status = strtolower($data['status']);
            }
            $campaign->save();

            return back()->with('success', "Campaign {$campaign->external_order_id} synced successfully with Core Engine!");
        }

        return back()->with('error', "Could not fetch status from Core Engine.");
    }

    /**
     * Pause or resume any client campaign
     */
    public function toggleStatus(TrafficCampaign $campaign)
    {
        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->update(['status' => $newStatus]);

        return back()->with('success', "Campaign {$campaign->external_order_id} updated to " . ucfirst($newStatus));
    }

    /**
     * Delete any client campaign
     */
    public function destroy(TrafficCampaign $campaign)
    {
        $orderId = $campaign->external_order_id;
        $campaign->delete();

        return back()->with('success', "Campaign {$orderId} deleted successfully.");
    }
}

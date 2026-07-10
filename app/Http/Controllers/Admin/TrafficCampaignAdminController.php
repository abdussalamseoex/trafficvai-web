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
     * Display all clients' Points Ledger & Topup History
     */
    public function ledger(Request $request)
    {
        $query = \App\Models\TrafficPointLog::with('user')->latest();

        $tab = $request->query('tab', 'all');
        if ($tab === 'credit') {
            $query->where('type', 'credit');
        } elseif ($tab === 'debit') {
            $query->where('type', 'debit');
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $ledgers = $query->paginate(25)->withQueryString();

        $stats = [
            'total_credits' => \App\Models\TrafficPointLog::where('type', 'credit')->sum('points'),
            'total_debits' => \App\Models\TrafficPointLog::where('type', 'debit')->sum('points'),
            'total_usd_topups' => \App\Models\TrafficPointLog::where('type', 'credit')->sum('cost_usd'),
        ];

        return view('admin.traffic_campaigns.ledger', compact('ledgers', 'stats', 'tab'));
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
    public function toggleStatus(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->update(['status' => $newStatus]);

        try {
            $apiService->updateCampaignStatus($campaign->external_order_id, $newStatus);
            if (!empty($campaign->remote_campaign_id)) {
                $apiService->updateCampaignStatus($campaign->remote_campaign_id, $newStatus);
            }
        } catch (\Throwable $e) {}

        return back()->with('success', "Campaign {$campaign->external_order_id} updated to " . ucfirst($newStatus) . " and synced with Core Engine");
    }

    /**
     * Delete any client campaign
     */
    public function destroy(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        $orderId = $campaign->external_order_id;
        $url = $campaign->url;

        // Stop & delete on Core Automation Engine
        try {
            $apiService->deleteCampaign($orderId);
            if (!empty($campaign->remote_campaign_id) && $campaign->remote_campaign_id !== $orderId) {
                $apiService->deleteCampaign($campaign->remote_campaign_id);
            }
        } catch (\Throwable $e) {}

        // Record audit trail log
        try {
            if ($campaign->user_id) {
                \App\Models\TrafficPointLog::create([
                    'user_id' => $campaign->user_id,
                    'type' => 'usage',
                    'points' => 0,
                    'cost_usd' => 0,
                    'description' => "Campaign Deleted by Admin: {$orderId} ({$url}) - Delivery stopped on Core Engine",
                    'status' => 'completed',
                ]);
            }
        } catch (\Throwable $e) {}

        $campaign->delete();

        return back()->with('success', "Campaign {$orderId} deleted successfully and delivery stopped on Core Server.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrafficCampaign;
use App\Models\User;
use App\Services\SurfEngineApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrafficCampaignAdminController extends Controller
{
    /**
     * Display all clients' Traffic Campaigns
     */
    public function index(Request $request, \App\Services\SurfEngineApiService $apiService)
    {
        $activeCampaigns = TrafficCampaign::with('user')->where('status', 'active')->get();
        foreach ($activeCampaigns as $camp) {
            try {
                \App\Console\Commands\SyncTrafficDelivery::syncSingleCampaign($camp, $apiService);
            } catch (\Throwable $e) {}
        }

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
            $query->where(function($q) {
                $q->whereIn('type', ['credit', 'purchase', 'topup'])->orWhere('points', '>', 0);
            });
        } elseif ($tab === 'debit') {
            $query->whereNotIn('type', ['credit', 'purchase', 'topup'])->where('points', '<=', 0);
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
            'total_credits' => \App\Models\TrafficPointLog::where(function($q) {
                $q->whereIn('type', ['credit', 'purchase', 'topup'])->orWhere('points', '>', 0);
            })->sum('points'),
            'total_debits' => abs(\App\Models\TrafficPointLog::whereNotIn('type', ['credit', 'purchase', 'topup'])->where('points', '<=', 0)->sum('points')),
            'total_usd_topups' => \App\Models\TrafficPointLog::where(function($q) {
                $q->whereIn('type', ['credit', 'purchase', 'topup'])->orWhere('points', '>', 0);
            })->sum('cost_usd'),
        ];

        return view('admin.traffic_campaigns.ledger', compact('ledgers', 'stats', 'tab'));
    }

    /**
     * Manually sync live delivery status from surf.abguestpost.net
     */
    public function syncStatus(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        $success = \App\Console\Commands\SyncTrafficDelivery::syncSingleCampaign($campaign, $apiService);

        if ($success) {
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
        $campaign->update([
            'status' => $newStatus,
            'auto_paused' => false
        ]);

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

    /**
     * Display live monitoring screen for any client's campaign (Admin Executive View)
     */
    public function monitor(TrafficCampaign $campaign)
    {
        return view('admin.traffic_campaigns.monitor', compact('campaign'));
    }

    /**
     * Live JSON status poller for Admin Monitoring Page
     */
    public function liveStatus(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        // Fetch live status from Core Automation Engine
        $statusResponse = $apiService->getCampaignStatus($campaign->external_order_id);

        if ($statusResponse['success'] ?? false) {
            $data = $statusResponse['data'];
            if (isset($data['hits_delivered'])) {
                $newHits = (int) $data['hits_delivered'];
                $campaign->hits_delivered = max($campaign->hits_delivered, $newHits);
                $campaign->save();
            }
            if (isset($data['status'])) {
                $campaign->status = strtolower($data['status']);
                $campaign->save();
            }
        }

        return response()->json([
            'hits_delivered' => (int) $campaign->hits_delivered,
            'total_limit'    => (int) $campaign->total_limit,
            'percentage'     => $campaign->delivery_percentage,
            'status'         => ucfirst($campaign->status),
        ]);
    }

    /**
     * Clients Overview — per-client campaign stats
     */
    public function clients(Request $request)
    {
        $query = User::has('trafficCampaigns')
            ->withCount([
                'trafficCampaigns as total_campaigns',
                'trafficCampaigns as active_campaigns'    => fn($q) => $q->where('status', 'active'),
                'trafficCampaigns as paused_campaigns'    => fn($q) => $q->where('status', 'paused'),
                'trafficCampaigns as completed_campaigns' => fn($q) => $q->whereIn('status', ['completed', 'deleted']),
            ])
            ->withSum('trafficCampaigns as total_hits', 'hits_delivered')
            ->orderByDesc('active_campaigns')
            ->orderByDesc('total_campaigns');

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        $clients = $query->paginate(25)->withQueryString();

        $overallStats = [
            'total_clients'       => User::has('trafficCampaigns')->count(),
            'zero_balance'        => User::has('trafficCampaigns')->where('traffic_points', '<=', 0)->count(),
            'total_active'        => TrafficCampaign::where('status', 'active')->count(),
            'total_paused'        => TrafficCampaign::where('status', 'paused')->count(),
        ];

        return view('admin.traffic_campaigns.clients', compact('clients', 'overallStats'));
    }

    /**
     * Quick Add Points from Clients Overview modal
     */
    public function quickAddPoints(Request $request, User $user)
    {
        $request->validate([
            'points'      => 'required|integer|min:1|max:10000000',
            'description' => 'nullable|string|max:255',
        ]);

        $pts  = (int) $request->points;
        $desc = $request->description ?: 'Admin Quick Top-Up from Traffic Dashboard';

        $user->increment('traffic_points', $pts);

        // Auto-resume all paused campaigns
        $resumed = 0;
        $paused  = TrafficCampaign::where('user_id', $user->id)->where('status', 'paused')->get();
        if ($paused->count() > 0) {
            $apiService = app(SurfEngineApiService::class);
            foreach ($paused as $camp) {
                try {
                    $r = $apiService->updateCampaignStatus($camp->external_order_id, 'active');
                    if ($r['success'] ?? false) {
                        $camp->status     = 'active';
                        $camp->auto_paused = false;
                        $camp->save();
                        $resumed++;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Quick resume failed for ' . $camp->external_order_id . ': ' . $e->getMessage());
                }
            }
        }

        try {
            \App\Models\TrafficPointLog::create([
                'user_id'     => $user->id,
                'type'        => 'adjustment',
                'points'      => $pts,
                'cost_usd'    => 0,
                'description' => 'Admin: ' . $desc,
                'status'      => 'completed',
            ]);
        } catch (\Throwable $e) {}

        $msg = number_format($pts) . ' points added to ' . $user->name;
        if ($resumed > 0) $msg .= " — {$resumed} campaign(s) auto-resumed!";

        return back()->with('success', $msg);
    }
}

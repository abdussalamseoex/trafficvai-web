<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TrafficCampaign;
use App\Services\SurfEngineApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TrafficCampaignController extends Controller
{
    /**
     * Display the Ultra-Premium Sleek Dark Mode Traffic Campaign Builder Page
     */
    public function builder(Request $request)
    {
        $activeTab = $request->query('tab', 'direct'); // 'direct' or 'search'
        if (!in_array($activeTab, ['direct', 'search'])) {
            $activeTab = 'direct';
        }

        $wallet = auth()->user()->wallet;
        $balance = $wallet ? $wallet->balance : 0;

        return view('client.traffic_campaign.builder', compact('activeTab', 'balance'));
    }

    /**
     * Calculate points server-side based on exact master prompt formula
     */
    public static function calculateRequiredPoints(
        string $campaignType,
        int $duration,
        int $subPageVisits,
        int $subPageDuration,
        int $totalLimit,
        string $captchaMode
    ): int {
        $totalSeconds = $duration + ($subPageVisits * $subPageDuration);
        $isSearchPremium = ($campaignType === 'search' && $captchaMode === 'premium');
        $baseRate60s = $isSearchPremium ? 30.0 : 20.0;
        $pointsPerVisit = $baseRate60s * ($totalSeconds / 60.0);
        return (int) ceil($pointsPerVisit * $totalLimit);
    }

    /**
     * Launch new Traffic Campaign and inject into surf.abguestpost.net API
     */
    public function store(Request $request, SurfEngineApiService $apiService)
    {
        $validated = $request->validate([
            'campaign_type' => 'required|in:direct,search',
            'url' => 'required|url',
            'total_limit' => 'required|integer|min:10|max:100000',
            'hourly_limit' => 'required|integer|min:1|max:5000',
            'duration' => 'required|in:60,90,120',
            'sub_page_visits' => 'required|in:0,1,2,3',
            'sub_page_duration' => 'required|integer|min:0|max:120',
            'device_type' => 'required|in:desktop,mobile,All',
            'target_country' => 'required|string',
            'search_engine' => 'nullable|in:google,bing,yahoo',
            'keywords' => 'nullable|string',
            'max_page' => 'nullable|in:1,3,5,10',
            'captcha_mode' => 'nullable|in:normal,premium',
        ]);

        $campaignType = $validated['campaign_type'];
        $duration = (int) $validated['duration'];
        $subPageVisits = (int) $validated['sub_page_visits'];
        $subPageDuration = (int) ($validated['sub_page_duration'] ?? 30);
        $totalLimit = (int) $validated['total_limit'];
        $captchaMode = $validated['captcha_mode'] ?? 'normal';

        // Calculate required points
        $requiredPoints = self::calculateRequiredPoints(
            $campaignType,
            $duration,
            $subPageVisits,
            $subPageDuration,
            $totalLimit,
            $captchaMode
        );

        $user = auth()->user();
        $wallet = $user->wallet;
        $balance = $wallet ? (float) $wallet->balance : 0.0;

        // Check if user has enough points/balance
        if ($balance < $requiredPoints) {
            return back()->withInput()->withErrors([
                'balance' => "Insufficient point/wallet balance. Required: {$requiredPoints} points. Available: " . number_format($balance, 0) . " points."
            ]);
        }

        // Generate external order ID
        $externalOrderId = '#TV-' . rand(10000, 99999);

        // Parse keywords array if provided
        $keywordsArray = [];
        if (!empty($validated['keywords'])) {
            // Split by line or comma
            $lines = preg_split('/[\r\n,]+/', $validated['keywords']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $keywordsArray[] = $trimmed;
                }
            }
        }

        // Deduct points from wallet balance
        if ($wallet) {
            $wallet->decrement('balance', $requiredPoints);
        }

        // Create local campaign with 30 days validity per requirement
        $campaign = TrafficCampaign::create([
            'user_id' => $user->id,
            'external_order_id' => $externalOrderId,
            'campaign_type' => $campaignType,
            'url' => $validated['url'],
            'total_limit' => $totalLimit,
            'hourly_limit' => (int) $validated['hourly_limit'],
            'duration' => $duration,
            'sub_page_visits' => $subPageVisits,
            'sub_page_duration' => $subPageDuration,
            'device_type' => strtolower($validated['device_type']),
            'target_country' => $validated['target_country'],
            'search_engine' => $validated['search_engine'] ?? 'google',
            'keywords' => !empty($keywordsArray) ? $keywordsArray : ['website traffic'],
            'max_page' => (int) ($validated['max_page'] ?? 1),
            'captcha_mode' => $captchaMode,
            'points_deducted' => $requiredPoints,
            'hits_delivered' => 0,
            'status' => 'active',
            'expires_at' => now()->addDays(30), // 30 days validity
        ]);

        // Construct API Payload matching Core Automation Engine specification
        $apiPayload = [
            'external_order_id' => $externalOrderId,
            'client_name' => $user->name,
            'url' => $campaign->url,
            'campaign_type' => $campaign->campaign_type,
            'total_limit' => $campaign->total_limit,
            'hourly_limit' => $campaign->hourly_limit,
            'duration' => $campaign->duration,
            'sub_page_visits' => $campaign->sub_page_visits,
            'sub_page_duration' => $campaign->sub_page_duration,
            'device_type' => $campaign->device_type,
            'target_country' => $campaign->target_country,
            'search_engine' => $campaign->search_engine,
            'keywords' => json_encode($campaign->keywords),
            'max_page' => $campaign->max_page,
            'captcha_mode' => $campaign->captcha_mode,
        ];

        // Call Core Engine API
        $apiResponse = $apiService->createCampaign($apiPayload);

        if ($apiResponse['success'] ?? false) {
            $campaign->update([
                'remote_campaign_id' => $apiResponse['data']['campaign_id'] ?? null
            ]);
        } else {
            Log::info("Campaign created locally, remote engine notification pending for {$externalOrderId}");
        }

        return redirect()->route('client.traffic_campaign.monitor', $campaign)
            ->with('success', 'Traffic Campaign launched successfully! Monitoring live delivery below.');
    }

    /**
     * List all traffic campaigns for client
     */
    public function index()
    {
        $campaigns = auth()->user()->trafficCampaigns()->latest()->paginate(15);
        return view('client.traffic_campaign.index', compact('campaigns'));
    }

    /**
     * Google Analytics & Live Campaign Monitoring Page
     */
    public function monitor(TrafficCampaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id() && !auth()->user()->is_admin, 403);

        return view('client.traffic_campaign.monitor', compact('campaign'));
    }

    /**
     * Live JSON status poller for the Monitoring Page progress bar
     */
    public function liveStatus(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        abort_if($campaign->user_id !== auth()->id() && !auth()->user()->is_admin, 403);

        // Fetch live status from surf.abguestpost.net
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
        }

        return response()->json([
            'hits_delivered' => $campaign->hits_delivered,
            'total_limit' => $campaign->total_limit,
            'percentage' => $campaign->delivery_percentage,
            'status' => ucfirst($campaign->status),
            'expires_at' => $campaign->expires_at ? $campaign->expires_at->format('M d, Y') : 'N/A',
        ]);
    }

    /**
     * Pause / Resume Campaign
     */
    public function toggleStatus(TrafficCampaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id() && !auth()->user()->is_admin, 403);

        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->update(['status' => $newStatus]);

        return back()->with('success', "Campaign status updated to " . ucfirst($newStatus));
    }
}

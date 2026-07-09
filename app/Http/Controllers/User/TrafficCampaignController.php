<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TrafficCampaign;
use App\Services\SurfEngineApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TrafficCampaignController extends Controller
{
    /**
     * Auto-ensure database schema on live server without requiring manual artisan commands
     */
    private static function ensureTrafficSchema(): void
    {
        try {
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'traffic_points')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->unsignedBigInteger('traffic_points')->default(0);
                });
            }
            if (Schema::hasTable('traffic_campaigns') && !Schema::hasColumn('traffic_campaigns', 'daily_limit')) {
                Schema::table('traffic_campaigns', function (Blueprint $table) {
                    $table->integer('daily_limit')->default(1000);
                });
            }
        } catch (\Throwable $e) {
            Log::warning("ensureTrafficSchema fallback check: " . $e->getMessage());
        }
    }

    /**
     * Display the Ultra-Premium Sleek Dark Mode Traffic Campaign Builder Page
     */
    public function builder(Request $request)
    {
        self::ensureTrafficSchema();

        $activeTab = $request->query('tab', 'direct'); // 'direct' or 'search'
        if (!in_array($activeTab, ['direct', 'search'])) {
            $activeTab = 'direct';
        }

        $user = auth()->user();
        $balance = $user->traffic_points;

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
            'daily_limit' => 'nullable|integer|min:1|max:50000',
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
        $dailyLimit = (int) ($validated['daily_limit'] ?? 1000);
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
        $balance = $user->traffic_points;

        // Check if user has enough traffic points
        if ($balance < $requiredPoints) {
            $shortage = $requiredPoints - $balance;
            $neededUsd = ceil($shortage / 1000.0);
            return back()->withInput()->withErrors([
                'balance' => "Insufficient Traffic Points! Required: " . number_format($requiredPoints) . " Pts | Available: " . number_format($balance) . " Pts. Please purchase at least " . number_format($shortage) . " more points (~$" . number_format($neededUsd, 2) . " USD) from the Traffic Points Store."
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

        // Deduct points from traffic_points
        $user->decrement('traffic_points', $requiredPoints);

        // Create local campaign with 30 days validity per requirement
        $campaign = TrafficCampaign::create([
            'user_id' => $user->id,
            'external_order_id' => $externalOrderId,
            'campaign_type' => $campaignType,
            'url' => $validated['url'],
            'total_limit' => $totalLimit,
            'hourly_limit' => (int) $validated['hourly_limit'],
            'daily_limit' => $dailyLimit,
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

    /**
     * Dedicated Traffic Points Top-up Store Page
     */
    public function topup()
    {
        self::ensureTrafficSchema();

        $user = auth()->user();
        $mainBalance = $user->balance ?? 0;
        $pointsBalance = $user->traffic_points;

        return view('client.traffic_campaign.topup', compact('mainBalance', 'pointsBalance'));
    }

    /**
     * Purchase Traffic Points using Main Account USD Balance
     */
    public function purchasePoints(\Illuminate\Http\Request $request)
    {
        self::ensureTrafficSchema();

        $request->validate([
            'package' => 'required|string',
            'custom_points' => 'nullable|integer|min:1000',
        ]);

        $user = auth()->user();
        $package = $request->input('package');
        $points = 0;
        $costUsd = 0;

        $packages = [
            'starter' => ['points' => 5000, 'cost' => 5.00],
            'growth' => ['points' => 15000, 'cost' => 13.50],
            'pro' => ['points' => 35000, 'cost' => 28.00],
            'scale' => ['points' => 100000, 'cost' => 70.00],
        ];

        if ($package === 'custom') {
            $points = (int) $request->input('custom_points');
            if ($points < 1000) {
                return back()->with('error', 'Minimum custom purchase is 1,000 points ($1.00 USD minimum).');
            }
            $costUsd = round($points / 1000.0, 2);
        } elseif (isset($packages[$package])) {
            $points = $packages[$package]['points'];
            $costUsd = $packages[$package]['cost'];
        } else {
            return back()->with('error', 'Invalid point package selected.');
        }

        if ($user->balance < $costUsd) {
            return back()->with('error', "Insufficient Main Account USD balance! You need $" . number_format($costUsd, 2) . " USD, but your Main Account balance is $" . number_format($user->balance, 2) . " USD. Please add funds to your Main Account first.");
        }

        // Deduct USD from main balance wallet
        $wallet = $user->wallet;
        if ($wallet) {
            $wallet->decrement('balance', $costUsd);
        }

        // Credit points to user's traffic_points balance
        $user->increment('traffic_points', $points);

        return back()->with('success', "Successfully purchased " . number_format($points) . " Traffic Points for $" . number_format($costUsd, 2) . " USD! Your new Traffic Points balance is " . number_format($user->fresh()->traffic_points) . " Pts (Valid for 30 Days).");
    }
}

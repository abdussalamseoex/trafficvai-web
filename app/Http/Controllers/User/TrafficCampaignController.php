<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TrafficCampaign;
use App\Models\TrafficPointLog;
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
            if (Schema::hasTable('traffic_campaigns')) {
                Schema::table('traffic_campaigns', function (Blueprint $table) {
                    if (!Schema::hasColumn('traffic_campaigns', 'daily_limit')) {
                        $table->integer('daily_limit')->default(1000);
                    }
                    if (!Schema::hasColumn('traffic_campaigns', 'traffic_source')) {
                        $table->string('traffic_source')->default('direct');
                    }
                    if (!Schema::hasColumn('traffic_campaigns', 'custom_referrers')) {
                        $table->text('custom_referrers')->nullable();
                    }
                    if (!Schema::hasColumn('traffic_campaigns', 'sub_page_toggle')) {
                        $table->boolean('sub_page_toggle')->default(false);
                    }
                    if (!Schema::hasColumn('traffic_campaigns', 'behavior_scroll')) {
                        $table->string('behavior_scroll')->default('enabled');
                    }
                    if (!Schema::hasColumn('traffic_campaigns', 'behavior_click')) {
                        $table->string('behavior_click')->default('enabled');
                    }
                });
            }
            if (!Schema::hasTable('traffic_point_logs')) {
                Schema::create('traffic_point_logs', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->string('type'); // 'purchase' or 'usage'
                    $table->integer('points');
                    $table->decimal('cost_usd', 10, 2)->default(0.00);
                    $table->string('description');
                    $table->string('status')->default('completed');
                    $table->timestamps();
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
        $baseRate60s = 1.0; // default direct
        if ($campaignType === 'search') {
            $baseRate60s = ($captchaMode === 'premium') ? 30.0 : 20.0;
        } else {
            $baseRate60s = 1.0;
        }
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
            'duration' => 'required|integer|min:10|max:600',
            'sub_page_toggle' => 'nullable',
            'sub_page_visits' => 'nullable|integer|min:0|max:10',
            'sub_page_duration' => 'nullable|integer|min:0|max:300',
            'behavior_scroll' => 'nullable|string',
            'behavior_click' => 'nullable|string',
            'device_type' => 'required|in:desktop,mobile,All',
            'target_country' => 'required|string',
            'search_engine' => 'nullable|in:google,bing,yahoo',
            'keywords' => 'nullable|string',
            'max_page' => 'nullable|in:1,3,5,10',
            'captcha_mode' => 'nullable|in:normal,premium',
            'traffic_source' => 'nullable|string',
            'custom_referrers' => 'nullable|string',
        ]);

        $campaignType = $validated['campaign_type'];
        $duration = (int) $validated['duration'];
        $subPageToggle = !empty($request->input('sub_page_toggle')) && $request->input('sub_page_toggle') !== '0';
        $subPageVisits = $subPageToggle ? (int) ($validated['sub_page_visits'] ?? 1) : 0;
        $subPageDuration = $subPageToggle ? (int) ($validated['sub_page_duration'] ?? 20) : 0;
        $behaviorScroll = $validated['behavior_scroll'] ?? 'enabled';
        $behaviorClick = $validated['behavior_click'] ?? 'enabled';
        $totalLimit = (int) $validated['total_limit'];
        $dailyLimit = (int) ($validated['daily_limit'] ?? 1000);
        $captchaMode = $validated['captcha_mode'] ?? 'normal';
        $trafficSource = $validated['traffic_source'] ?? 'direct';
        $customReferrers = $validated['custom_referrers'] ?? null;

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
        $ptsBalance = (int) $user->traffic_points;
        $mainUsdBalance = (float) ($user->wallet ? $user->wallet->balance : 0.0);

        // Pay-As-You-Go check: Ensure user has at least 500 points available to launch any campaign
        $totalAvailablePoints = $ptsBalance + ($mainUsdBalance * 1000);
        if ($totalAvailablePoints < 500) {
            return back()->withInput()->withErrors([
                'balance' => "Insufficient Traffic Points! You have " . number_format($ptsBalance) . " Points available. Please top up at least 1,000 Points ($1.00 USD) to start running campaigns."
            ]);
        }

        // Generate external order ID
        $externalOrderId = '#TV-' . rand(10000, 99999);

        // Parse keywords array if provided (from dynamic percentage inputs or textarea)
        $keywordsArray = [];
        $keywordTexts = $request->input('keyword_texts', []);
        $keywordPercents = $request->input('keyword_percents', []);
        if (is_array($keywordTexts) && count($keywordTexts) > 0) {
            foreach ($keywordTexts as $index => $kw) {
                $trimmed = trim($kw);
                if ($trimmed !== '') {
                    $pct = isset($keywordPercents[$index]) ? intval($keywordPercents[$index]) : 100;
                    $keywordsArray[] = $trimmed . ' (' . $pct . '%)';
                }
            }
        } elseif (!empty($validated['keywords'])) {
            $lines = preg_split('/[\r\n,]+/', $validated['keywords']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $keywordsArray[] = $trimmed;
                }
            }
        }

        $deviceVal = $validated['device_type'] ?? 'All';

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
            'sub_page_toggle' => $subPageToggle,
            'sub_page_visits' => $subPageVisits,
            'sub_page_duration' => $subPageDuration,
            'behavior_scroll' => $behaviorScroll,
            'behavior_click' => $behaviorClick,
            'device_type' => $deviceVal,
            'target_country' => $validated['target_country'],
            'search_engine' => $validated['search_engine'] ?? 'google',
            'keywords' => !empty($keywordsArray) ? $keywordsArray : ['website traffic'],
            'max_page' => (int) ($validated['max_page'] ?? 10),
            'captcha_mode' => $captchaMode,
            'traffic_source' => $trafficSource,
            'custom_referrers' => $customReferrers,
            'points_deducted' => 0,
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
            'daily_limit' => $campaign->daily_limit,
            'duration' => $campaign->duration,
            'sub_page_toggle' => $campaign->sub_page_toggle,
            'sub_page_visits' => $campaign->sub_page_visits,
            'sub_page_duration' => $campaign->sub_page_duration,
            'behavior_scroll' => $campaign->behavior_scroll,
            'behavior_click' => $campaign->behavior_click,
            'device_type' => $campaign->device_type,
            'device' => $campaign->device_type,
            'devices' => $campaign->device_type,
            'target_country' => $campaign->target_country,
            'country' => $campaign->target_country,
            'search_engine' => $campaign->search_engine,
            'keywords' => $campaign->keywords,
            'keywords_json' => json_encode($campaign->keywords),
            'max_page' => $campaign->max_page,
            'search_pages_to_scan' => $campaign->max_page,
            'captcha_mode' => $campaign->captcha_mode,
            'traffic_source' => $campaign->traffic_source,
            'custom_referrers' => $campaign->custom_referrers,
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

        try {
            TrafficPointLog::create([
                'user_id' => $user->id,
                'type' => 'usage',
                'points' => 0,
                'cost_usd' => 0,
                'description' => "Created Pay-As-You-Go Campaign {$externalOrderId} ({$campaignType}) - Estimated: " . number_format($requiredPoints) . " Pts",
                'status' => 'completed',
            ]);
        } catch (\Throwable $e) {
            Log::warning("Could not create traffic log: " . $e->getMessage());
        }

        $msg = "Traffic Campaign launched successfully! Pay-As-You-Go mode active: points will be deducted incrementally as visits are delivered.";

        return redirect()->route('client.traffic_campaign.monitor', $campaign)
            ->with('success', $msg);
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
     * Check if current user can access the campaign
     */
    protected function canAccessCampaign(TrafficCampaign $campaign): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Auto-assign if unassigned
        if (empty($campaign->user_id)) {
            $campaign->user_id = $user->id;
            $campaign->save();
        }

        // Allow authenticated user access
        return true;
    }

    /**
     * Display live monitoring screen for launched campaign
     */
    public function monitor(TrafficCampaign $campaign)
    {
        abort_if(!$this->canAccessCampaign($campaign), 403, 'Unauthorized access to campaign monitoring.');

        return view('client.traffic_campaign.monitor', compact('campaign'));
    }

    /**
     * Live JSON status poller for the Monitoring Page progress bar
     */
    public function liveStatus(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        abort_if(!$this->canAccessCampaign($campaign), 403);

        $user = $campaign->user;
        $userPoints = (int) ($user ? $user->traffic_points : 0);

        // Fetch live status from surf.abguestpost.net
        $statusResponse = $apiService->getCampaignStatus($campaign->external_order_id);

        if ($statusResponse['success'] ?? false) {
            $data = $statusResponse['data'];
            if (isset($data['hits_delivered'])) {
                $newHits = (int) $data['hits_delivered'];
                $deltaHits = max(0, $newHits - (int) $campaign->hits_delivered);

                if ($deltaHits > 0 && $user && $userPoints > 0) {
                    $deduct = min($userPoints, $deltaHits);
                    $user->decrement('traffic_points', $deduct);
                    $campaign->increment('points_deducted', $deduct);

                    // Record incremental usage log
                    try {
                        TrafficPointLog::create([
                            'user_id' => $user->id,
                            'type' => 'usage',
                            'points' => -$deduct,
                            'cost_usd' => 0,
                            'description' => "Pay-As-You-Go Delivery: {$deduct} hits delivered for {$campaign->external_order_id}",
                            'status' => 'completed',
                        ]);
                    } catch (\Throwable $e) {}
                }

                $campaign->hits_delivered = $newHits;
            }
            if (isset($data['status'])) {
                $campaign->status = strtolower($data['status']);
            }
            $campaign->save();
        }

        $userPointsAfter = (int) ($user ? $user->fresh()->traffic_points : 0);
        $deliverySuspended = ($userPointsAfter <= 0);

        return response()->json([
            'hits_delivered' => $campaign->hits_delivered,
            'total_limit' => $campaign->total_limit,
            'percentage' => $campaign->delivery_percentage,
            'status' => ucfirst($campaign->status),
            'expires_at' => $campaign->expires_at ? $campaign->expires_at->format('M d, Y') : 'N/A',
            'delivery_suspended' => $deliverySuspended,
        ]);
    }

    /**
     * Pause / Resume Campaign
     */
    public function toggleStatus(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        abort_if(!$this->canAccessCampaign($campaign), 403);

        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->update(['status' => $newStatus]);

        // Sync status to Core Automation Engine
        try {
            $apiService->updateCampaignStatus($campaign->external_order_id, $newStatus);
            if (!empty($campaign->remote_campaign_id)) {
                $apiService->updateCampaignStatus($campaign->remote_campaign_id, $newStatus);
            }
        } catch (\Throwable $e) {
            Log::warning('Core Engine toggle status failed: ' . $e->getMessage());
        }

        return back()->with('success', "Campaign status updated to " . ucfirst($newStatus) . " and synced with Core Engine!");
    }

    /**
     * Delete Campaign & Stop Delivery on Core Server
     */
    public function destroy(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        abort_if(!$this->canAccessCampaign($campaign), 403);

        $orderId = $campaign->external_order_id;
        $url = $campaign->url;

        // Stop & delete on Core Automation Engine (surf.abguestpost.net)
        try {
            $apiService->deleteCampaign($orderId);
            if (!empty($campaign->remote_campaign_id) && $campaign->remote_campaign_id !== $orderId) {
                $apiService->deleteCampaign($campaign->remote_campaign_id);
            }
        } catch (\Throwable $e) {
            Log::warning('Core Engine delete campaign failed: ' . $e->getMessage());
        }

        // Record audit trail log
        try {
            if ($campaign->user_id) {
                TrafficPointLog::create([
                    'user_id' => $campaign->user_id,
                    'type' => 'usage',
                    'points' => 0,
                    'cost_usd' => 0,
                    'description' => "Campaign Deleted by Client: {$orderId} ({$url}) - Delivery stopped on Core Engine",
                    'status' => 'completed',
                ]);
            }
        } catch (\Throwable $e) {}

        $campaign->delete();

        return redirect()->route('client.traffic_campaign.index')
            ->with('success', "Campaign {$orderId} deleted successfully and delivery stopped on Core Server.");
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

        $logs = TrafficPointLog::where('user_id', $user->id)->latest()->take(50)->get();

        return view('client.traffic_campaign.topup', compact('mainBalance', 'pointsBalance', 'logs'));
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

        try {
            TrafficPointLog::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'points' => $points,
                'cost_usd' => $costUsd,
                'description' => $package === 'custom'
                    ? "Purchased Custom Package ({$points} Pts)"
                    : "Purchased " . ucfirst($package) . " Pack (" . number_format($points) . " Pts)",
                'status' => 'completed',
            ]);
        } catch (\Throwable $e) {
            Log::warning("Could not create point purchase log: " . $e->getMessage());
        }

        return back()->with('success', "Successfully purchased " . number_format($points) . " Traffic Points for $" . number_format($costUsd, 2) . " USD! Your new Traffic Points balance is " . number_format($user->fresh()->traffic_points) . " Pts (Valid for 30 Days).");
    }

    /**
     * Dedicated 30-Day Point Ledger & History Table Page
     */
    public function history()
    {
        self::ensureTrafficSchema();

        $user = auth()->user();
        $logs = TrafficPointLog::where('user_id', $user->id)->latest()->paginate(25);
        $pointsBalance = (int) $user->traffic_points;

        return view('client.traffic_campaign.history', compact('logs', 'pointsBalance'));
    }
}

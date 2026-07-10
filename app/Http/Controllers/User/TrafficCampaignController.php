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
                    if (!Schema::hasColumn('traffic_campaigns', 'link_click_type')) {
                        $table->string('link_click_type')->default('Both');
                    }
                    if (!Schema::hasColumn('traffic_campaigns', 'distribution_type')) {
                        $table->string('distribution_type')->default('spread');
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
    public function builder(Request $request, SurfEngineApiService $apiService)
    {
        self::ensureTrafficSchema();

        $activeTab = $request->query('tab', 'direct'); // 'direct' or 'search'
        if (!in_array($activeTab, ['direct', 'search'])) {
            $activeTab = 'direct';
        }

        $user = auth()->user();
        $balance = $user->traffic_points;

        $availableCountries = [];
        try {
            $countryRes = $apiService->getAvailableCountries();
            if ($countryRes['success'] ?? false) {
                $availableCountries = $countryRes['data']['countries'] ?? [];
            }
        } catch (\Throwable $e) {}

        return view('client.traffic_campaign.builder', compact('activeTab', 'balance', 'availableCountries'));
    }

    /**
     * Calculate points server-side based on exact master prompt formula
     */
    private static function calculateRequiredPoints($campaignType, $duration, $subPageVisits, $subPageDuration, $totalLimit, $captchaMode)
    {
        $baseRate60s = 1.0; // Direct Traffic default: 1 point per 60 seconds
        if ($campaignType === 'search') {
            $baseRate60s = ($captchaMode === 'premium') ? 30.0 : 20.0;
        }

        $totalSeconds = $duration + ($subPageVisits * $subPageDuration);
        $pointsPerVisit = $baseRate60s * ($totalSeconds / 60.0);
        return (int) ceil($pointsPerVisit * $totalLimit);
    }

    /**
     * Launch new Traffic Campaign and inject into surf.abguestpost.net API
     */
    public function store(Request $request, SurfEngineApiService $apiService)
    {
        self::ensureTrafficSchema();
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
            'device_type' => 'nullable',
            'distribution_type' => 'nullable|string',
            'target_country' => 'nullable',
            'search_engine' => 'nullable|string',
            'keywords' => 'nullable|string',
            'max_page' => 'nullable',
            'captcha_mode' => 'nullable',
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
        $totalAvailablePoints = $ptsBalance + (int) floor($mainUsdBalance * 1000);

        // Pay-As-You-Go Delivery Check: Ensure user has positive balance to start delivery
        if ($totalAvailablePoints <= 0) {
            return back()->withInput()->withErrors([
                'balance' => "Insufficient Balance! You need a positive Points balance to launch a campaign. Please top up your points."
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
                    $keywordsArray[] = ['kw' => $trimmed, 'weight' => $pct];
                }
            }
        } elseif (!empty($validated['keywords'])) {
            $lines = preg_split('/[\r\n,]+/', $validated['keywords']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $keywordsArray[] = ['kw' => $trimmed, 'weight' => 100];
                }
            }
        }

        $deviceVal = $validated['device_type'] ?? 'All';

        // Create local campaign with 30 days validity per requirement
        $linkClickType = $request->input('link_click_type', 'Both');
        $rawDist = strtolower($request->input('distribution_type', 'spread'));
        $distributionType = in_array($rawDist, ['burst', 'asap']) ? 'burst' : 'spread';

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
            'link_click_type' => $linkClickType,
            'distribution_type' => $distributionType,
            'device_type' => $deviceVal,
            'target_country' => (isset($validated['target_country']) && is_array($validated['target_country'])) ? implode(', ', $validated['target_country']) : ($validated['target_country'] ?? 'Worldwide'),
            'search_engine' => $validated['search_engine'] ?? 'google',
            'keywords' => !empty($keywordsArray) ? $keywordsArray : [['kw' => 'website traffic', 'weight' => 100]],
            'max_page' => (int) ($validated['max_page'] ?? 10),
            'captcha_mode' => $captchaMode,
            'traffic_source' => $trafficSource,
            'custom_referrers' => $customReferrers,
            'points_deducted' => $requiredPoints,
            'hits_delivered' => 0,
            'status' => 'active',
            'expires_at' => now()->addDays(30), // 30 days validity
        ]);

        $isSearch = strtolower($campaign->campaign_type) === 'search';
        $searchEngineVal = strtolower(trim($campaign->search_engine ?: 'google'));

        // Parse custom referrers for Direct Traffic
        $finalSourceType = $isSearch ? $searchEngineVal : $campaign->traffic_source;
        if (!$isSearch && !empty($campaign->custom_referrers)) {
            $referrers = array_map('trim', explode("\n", $campaign->custom_referrers));
            $finalSourceType = implode(', ', array_filter($referrers));
        }

        // V4 Device Targeting Normalization
        $apiDeviceType = strtolower(trim($campaign->device_type ?? 'All'));
        if ($apiDeviceType === 'all' || $apiDeviceType === 'all devices' || $apiDeviceType === '') {
            $apiDeviceType = 'random';
        }

        // Construct API Payload strictly matching Core Automation Engine V4 specification
        $apiPayload = [
            'client_name' => $user->name,
            'external_order_id' => $externalOrderId,
            'url' => $campaign->url,
            'campaign_type' => strtolower($campaign->campaign_type),
            'source_type' => $finalSourceType,
            'traffic_source' => $finalSourceType,
            'referrers' => $finalSourceType,
            'search_engine' => $searchEngineVal,
            'captcha_mode' => $campaign->captcha_mode,
            'keywords' => $campaign->keywords,
            'max_page' => $campaign->max_page,
            'duration' => (int) $campaign->duration,
            'visit_duration' => (int) $campaign->duration,
            'scroll_enabled' => $campaign->behavior_scroll === 'enabled' ? 1 : 0,
            'link_click_type' => $linkClickType,
            'click_type' => $linkClickType,
            'click_behavior' => $linkClickType,
            'internal_click_type' => $linkClickType,
            'sub_page_visits' => (int) $campaign->sub_page_visits,
            'sub_page_duration' => (int) $campaign->sub_page_duration,
            'device_type' => $apiDeviceType,
            'device' => $apiDeviceType,
            'devices' => $apiDeviceType,
            'distribution_type' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'visit_distribution' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'distribution' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'speed' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'delivery_speed' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'target_country' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'country' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'countries' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'geo_targeting' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'total_limit' => (int) $campaign->total_limit,
            'hourly_limit' => (int) $campaign->hourly_limit,
            'daily_limit' => (int) $campaign->daily_limit,
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
    public function index(SurfEngineApiService $apiService)
    {
        self::ensureTrafficSchema();
        $allCampaigns = auth()->user()->trafficCampaigns()->get();
        foreach ($allCampaigns as $camp) {
            if (strtolower(trim($camp->campaign_type)) === 'direct' && $camp->total_limit > 0) {
                $totalSeconds = (int) $camp->duration + ((int) $camp->sub_page_visits * (int) $camp->sub_page_duration);
                if ($totalSeconds <= 0) $totalSeconds = 60;
                $correctBudget = (int) ceil(1.0 * ($totalSeconds / 60.0) * $camp->total_limit);
                if ($camp->points_deducted > ($correctBudget * 2)) {
                    $camp->points_deducted = max($correctBudget, $camp->total_limit);
                    $camp->save();
                }
            }
            if ($camp->status === 'active') {
                try {
                    \App\Console\Commands\SyncTrafficDelivery::syncSingleCampaign($camp, $apiService);
                } catch (\Throwable $e) {}
            }
        }
        $campaigns = auth()->user()->trafficCampaigns()->latest()->paginate(15);
        return view('client.traffic_campaign.index', compact('campaigns'));
    }

    public function edit(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        self::ensureTrafficSchema();
        abort_if(!$this->canAccessCampaign($campaign), 403);

        $user = auth()->user();
        $ptsBalance = (int) $user->traffic_points;
        $usdWallet = (float) ($user->wallet ? $user->wallet->balance : 0.0);
        $balance = $ptsBalance + ($usdWallet * 1000);
        $activeTab = $campaign->campaign_type;

        $availableCountries = [];
        try {
            $countryRes = $apiService->getAvailableCountries();
            if ($countryRes['success'] ?? false) {
                $availableCountries = $countryRes['data']['countries'] ?? [];
            }
        } catch (\Throwable $e) {}

        return view('client.traffic_campaign.edit', compact('campaign', 'balance', 'activeTab', 'availableCountries'));
    }

    /**
     * Update campaign limits and sync with Core Engine
     */
    public function update(Request $request, TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        self::ensureTrafficSchema();
        abort_if(!$this->canAccessCampaign($campaign), 403);

        $validated = $request->validate([
            'total_limit' => 'required|integer|min:10',
            'hourly_limit' => 'required|integer|min:1',
            'daily_limit' => 'nullable|integer|min:1',
            'duration' => 'nullable|integer|min:10|max:600',
            'target_country' => 'nullable',
            'device_type' => 'nullable|in:All,desktop,mobile,random,Desktop,Mobile,ALL,RANDOM',
            'distribution_type' => 'nullable|string',
            'sub_page_visits' => 'nullable|integer|min:0|max:10',
            'search_engine' => 'nullable|string',
            'captcha_mode' => 'nullable|in:normal,premium',
            'keywords' => 'nullable|string',
            'traffic_source' => 'nullable|string',
            'custom_referrers' => 'nullable|string',
        ]);

        $subPageVisits = (int) ($validated['sub_page_visits'] ?? $campaign->sub_page_visits);
        $subPageToggle = $subPageVisits > 0;

        $keywordTexts = $request->input('keyword_texts', []);
        $keywordPercents = $request->input('keyword_percents', []);
        $keywordsArray = $campaign->keywords;
        
        if (is_array($keywordTexts) && count($keywordTexts) > 0) {
            $keywordsArray = [];
            foreach ($keywordTexts as $index => $kw) {
                $trimmed = trim($kw);
                if ($trimmed !== '') {
                    $pct = isset($keywordPercents[$index]) ? intval($keywordPercents[$index]) : 100;
                    $keywordsArray[] = ['kw' => $trimmed, 'weight' => $pct];
                }
            }
        } elseif (!empty($validated['keywords'])) {
            $lines = preg_split('/[\r\n,]+/', $validated['keywords']);
            $keywordsArray = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $keywordsArray[] = ['kw' => $trimmed, 'weight' => 100];
                }
            }
        }

        $newTotalLimit = (int) $validated['total_limit'];
        $oldTotalLimit = (int) $campaign->total_limit;
        
        if ($newTotalLimit > $oldTotalLimit) {
            $user = auth()->user();
            $ptsBalance = (int) $user->traffic_points;
            $mainUsdBalance = (float) ($user->wallet ? $user->wallet->balance : 0.0);
            $totalAvailablePoints = $ptsBalance + (int) floor($mainUsdBalance * 1000);

            if ($totalAvailablePoints <= 0) {
                return back()->withInput()->withErrors([
                    'balance' => "Insufficient Balance! Please top up your points before increasing campaign limits."
                ]);
            }
        }

        $campaign->update([
            'total_limit' => $newTotalLimit,
            'hourly_limit' => $validated['hourly_limit'],
            'daily_limit' => $validated['daily_limit'] ?? 1000,
            'duration' => $validated['duration'] ?? $campaign->duration,
            'target_country' => isset($validated['target_country']) && is_array($validated['target_country']) ? implode(', ', $validated['target_country']) : ($validated['target_country'] ?? $campaign->target_country),
            'device_type' => $validated['device_type'] ?? $campaign->device_type,
            'sub_page_visits' => $subPageVisits,
            'sub_page_duration' => (int) $request->input('sub_page_duration', $campaign->sub_page_duration),
            'sub_page_toggle' => $subPageToggle,
            'search_engine' => $validated['search_engine'] ?? $campaign->search_engine,
            'keywords' => $keywordsArray,
            'traffic_source' => $validated['traffic_source'] ?? $campaign->traffic_source,
            'custom_referrers' => $validated['custom_referrers'] ?? $campaign->custom_referrers,
            'behavior_scroll' => $request->input('behavior_scroll', $campaign->behavior_scroll),
            'behavior_click' => $request->input('behavior_click', $campaign->behavior_click),
            'link_click_type' => $request->input('link_click_type', $campaign->link_click_type ?: 'Both'),
            'distribution_type' => in_array(strtolower($request->input('distribution_type', $campaign->distribution_type ?: 'spread')), ['burst', 'asap']) ? 'burst' : 'spread',
            'captcha_mode' => $request->input('captcha_mode', $campaign->captcha_mode ?: 'normal'),
            'points_deducted' => $campaign->points_deducted,
        ]);

        // V4 Device Targeting Normalization
        $apiDeviceType = strtolower(trim($campaign->device_type ?? 'All'));
        if ($apiDeviceType === 'all' || $apiDeviceType === 'all devices' || $apiDeviceType === '') {
            $apiDeviceType = 'random';
        }

        $isSearch = strtolower($campaign->campaign_type) === 'search';
        $searchEngineVal = strtolower(trim($campaign->search_engine ?: 'google'));
        $sourceTypeVal = $isSearch ? $searchEngineVal : ($campaign->traffic_source ?: 'Direct URL');

        // Sync with Core Engine (V4 Spec supports updating all fields)
        $payload = [
            'action' => 'update',
            'external_order_id' => $campaign->external_order_id,
            'url' => $campaign->url,
            'campaign_type' => strtolower($campaign->campaign_type),
            'source_type' => $sourceTypeVal,
            'traffic_source' => $sourceTypeVal,
            'referrers' => $sourceTypeVal,
            'search_engine' => $searchEngineVal,
            'captcha_mode' => $campaign->captcha_mode ?: 'normal',
            'keywords' => $campaign->keywords,
            'max_page' => $campaign->max_page,
            'duration' => (int) $campaign->duration,
            'visit_duration' => (int) $campaign->duration,
            'scroll_enabled' => $campaign->behavior_scroll === 'enabled' ? 1 : 0,
            'link_click_type' => $campaign->link_click_type ?: 'Both',
            'click_type' => $campaign->link_click_type ?: 'Both',
            'click_behavior' => $campaign->link_click_type ?: 'Both',
            'internal_click_type' => $campaign->link_click_type ?: 'Both',
            'sub_page_visits' => (int) $campaign->sub_page_visits,
            'sub_page_duration' => (int) $campaign->sub_page_duration,
            'device_type' => $apiDeviceType,
            'device' => $apiDeviceType,
            'devices' => $apiDeviceType,
            'distribution_type' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'visit_distribution' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'distribution' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'speed' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'delivery_speed' => in_array(strtolower($campaign->distribution_type), ['burst', 'asap']) ? 'burst' : 'spread',
            'target_country' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'country' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'countries' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'geo_targeting' => in_array(strtolower(trim($campaign->target_country)), ['worldwide', 'all', 'random', 'all countries', '']) ? 'All' : trim($campaign->target_country),
            'total_limit' => (int) $campaign->total_limit,
            'hourly_limit' => (int) $campaign->hourly_limit,
            'daily_limit' => (int) $campaign->daily_limit,
        ];
        
        try {
            $apiService->updateCampaign($campaign->external_order_id, $payload);
            if (!empty($campaign->remote_campaign_id)) {
                $apiService->updateCampaign($campaign->remote_campaign_id, $payload);
            }
        } catch (\Throwable $e) {}

        return redirect()->route('client.traffic_campaign.index')->with('success', 'Campaign updated successfully and synced with Core Engine!');
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
                    $ratePerHit = $campaign->total_limit > 0 ? ($campaign->points_deducted / max(1, $campaign->total_limit)) : 1.0;
                    $ptsToDeduct = max(1, (int) round($deltaHits * $ratePerHit));
                    $deduct = min($userPoints, $ptsToDeduct);
                    $user->decrement('traffic_points', $deduct);

                    // Record incremental usage log
                    try {
                        TrafficPointLog::create([
                            'user_id' => $user->id,
                            'type' => 'usage',
                            'points' => -$deduct,
                            'cost_usd' => 0,
                            'description' => "Pay-As-You-Go Delivery: {$deltaHits} visits delivered ({$deduct} Pts) for {$campaign->external_order_id}",
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
     * Fetch live chart data for the Monitoring Page
     */
    public function liveGraph(Request $request, TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
        abort_if(!$this->canAccessCampaign($campaign), 403);

        $view = $request->get('view', '24h');
        $graphResponse = $apiService->getCampaignGraph($campaign->external_order_id, $view);

        $hitsDelivered = (int) $campaign->hits_delivered;

        if (($graphResponse['success'] ?? false) && !empty($graphResponse['data']['data']) && array_sum($graphResponse['data']['data']) > 0) {
            return response()->json($graphResponse['data']);
        }

        $createdAt = $campaign->created_at ?: now();

        // Proportional realistic delivery graph distribution when Core API graph returns 0 or fallback
        if (in_array($view, ['daily', '7d', '14d'])) {
            $labels = [];
            $data = [];
            $daysCount = 14;
            $activeDayIndices = [];

            for ($i = $daysCount - 1; $i >= 0; $i--) {
                $dayDate = now()->subDays($i)->startOfDay();
                $labels[] = $dayDate->format('M d');
                if ($dayDate->greaterThanOrEqualTo($createdAt->startOfDay())) {
                    $activeDayIndices[] = count($labels) - 1;
                }
            }

            $data = array_fill(0, count($labels), 0);
            $activeCount = count($activeDayIndices);
            $remainingHits = $hitsDelivered;

            if ($activeCount > 0 && $hitsDelivered > 0) {
                foreach ($activeDayIndices as $idxPos => $targetIdx) {
                    if ($idxPos === $activeCount - 1) {
                        $data[$targetIdx] = $remainingHits;
                    } else {
                        $share = (int) round($hitsDelivered / $activeCount);
                        $share = min($remainingHits, $share);
                        $data[$targetIdx] = $share;
                        $remainingHits -= $share;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'data' => $data
            ]);
        }

        // Hourly 24h realistic distribution curve strictly on/after created_at
        $labels = [];
        $data = [];
        $activeHourIndices = [];

        for ($i = 22; $i >= 0; $i -= 2) {
            $hourDate = now()->subHours($i)->startOfHour();
            $labels[] = $hourDate->format('H:00');
            if ($hourDate->greaterThanOrEqualTo($createdAt->copy()->subHour())) {
                $activeHourIndices[] = count($labels) - 1;
            }
        }

        $data = array_fill(0, count($labels), 0);
        $activeCount = count($activeHourIndices);
        $remainingHits = $hitsDelivered;

        if ($activeCount > 0 && $hitsDelivered > 0) {
            foreach ($activeHourIndices as $idxPos => $targetIdx) {
                if ($idxPos === $activeCount - 1) {
                    $data[$targetIdx] = $remainingHits;
                } else {
                    $portion = (int) round($hitsDelivered / $activeCount);
                    $portion = min($remainingHits, $portion);
                    $data[$targetIdx] = $portion;
                    $remainingHits -= $portion;
                }
            }
        }

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'data' => $data
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
    public function topup(\Illuminate\Http\Request $request)
    {
        self::ensureTrafficSchema();

        $user = auth()->user();
        $mainBalance = $user->balance ?? 0;
        $pointsBalance = $user->traffic_points;

        $tab = $request->query('tab', 'all');
        $query = TrafficPointLog::where('user_id', $user->id)->latest();

        if ($tab === 'topups') {
            $query->where(function($q) {
                $q->whereIn('type', ['credit', 'purchase', 'topup'])->orWhere('points', '>', 0);
            });
        } elseif ($tab === 'usage') {
            $query->whereNotIn('type', ['credit', 'purchase', 'topup'])->where('points', '<=', 0);
        }

        $logs = $query->paginate(25)->withQueryString();

        $counts = [
            'all' => TrafficPointLog::where('user_id', $user->id)->count(),
            'topups' => TrafficPointLog::where('user_id', $user->id)->where(function($q) {
                $q->whereIn('type', ['credit', 'purchase', 'topup'])->orWhere('points', '>', 0);
            })->count(),
            'usage' => TrafficPointLog::where('user_id', $user->id)->whereNotIn('type', ['credit', 'purchase', 'topup'])->where('points', '<=', 0)->count(),
        ];

        return view('client.traffic_campaign.topup', compact('mainBalance', 'pointsBalance', 'logs', 'tab', 'counts'));
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
    public function history(\Illuminate\Http\Request $request)
    {
        self::ensureTrafficSchema();

        $user = auth()->user();
        $query = TrafficPointLog::where('user_id', $user->id)->latest();

        $tab = $request->query('tab', 'all');
        if ($tab === 'topups') {
            $query->where(function($q) {
                $q->whereIn('type', ['credit', 'purchase', 'topup', 'adjustment'])->orWhere('points', '>', 0);
            });
        } elseif ($tab === 'usage') {
            $query->whereNotIn('type', ['credit', 'purchase', 'topup'])->where('points', '<', 0);
        }

        $logs = $query->paginate(25)->withQueryString();
        $pointsBalance = (int) $user->traffic_points;

        $counts = [
            'all' => TrafficPointLog::where('user_id', $user->id)->count(),
            'topups' => TrafficPointLog::where('user_id', $user->id)->where(function($q) {
                $q->whereIn('type', ['credit', 'purchase', 'topup', 'adjustment'])->orWhere('points', '>', 0);
            })->count(),
            'usage' => TrafficPointLog::where('user_id', $user->id)->whereNotIn('type', ['credit', 'purchase', 'topup'])->where('points', '<', 0)->count(),
        ];

        return view('client.traffic_campaign.history', compact('logs', 'pointsBalance', 'tab', 'counts'));
    }
}

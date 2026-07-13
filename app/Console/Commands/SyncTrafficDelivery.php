<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrafficCampaign;
use App\Models\TrafficPointLog;
use App\Models\User;
use App\Services\SurfEngineApiService;
use Illuminate\Support\Facades\Log;

class SyncTrafficDelivery extends Command
{
    protected $signature = 'traffic:sync-delivery';
    protected $description = 'Sync live hits delivery from Core Engine for all active traffic campaigns and deduct points proportionally';

    public function handle(SurfEngineApiService $apiService)
    {
        $activeCampaigns = TrafficCampaign::where('status', 'active')->get();

        $this->info("Syncing delivery for " . $activeCampaigns->count() . " active campaigns...");

        // Track which users have run out of points during this sync cycle
        $usersExhausted = [];

        foreach ($activeCampaigns as $campaign) {
            try {
                $exhausted = self::syncSingleCampaign($campaign, $apiService);
                if ($exhausted && $campaign->user_id) {
                    $usersExhausted[$campaign->user_id] = true;
                }
            } catch (\Throwable $e) {
                Log::warning("Traffic sync failed for campaign #{$campaign->id}: " . $e->getMessage());
            }
        }

        // Batch-pause ALL remaining active campaigns for users who ran out of points
        // (covers campaigns that had 0 new hits so didn't individually trigger the check)
        if (!empty($usersExhausted)) {
            foreach (array_keys($usersExhausted) as $userId) {
                $remaining = TrafficCampaign::where('user_id', $userId)
                    ->where('status', 'active')
                    ->get();

                foreach ($remaining as $camp) {
                    try {
                        $pauseResp = $apiService->updateCampaignStatus($camp->external_order_id, 'paused');
                        if ($pauseResp['success'] ?? false) {
                            $camp->status = 'paused';
                            $camp->auto_paused = true;
                            $camp->save();
                            $this->info("Batch auto-paused {$camp->external_order_id} for user #{$userId} (zero balance).");
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Batch auto-pause failed for campaign #{$camp->id}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Traffic delivery sync completed.");
        return 0;
    }

    /**
     * Sync a single campaign.
     * Returns true if user's balance is now exhausted (caller will batch-pause remaining campaigns).
     */
    public static function syncSingleCampaign(TrafficCampaign $campaign, SurfEngineApiService $apiService): bool
    {
        $statusResponse = $apiService->getCampaignStatus($campaign->external_order_id);

        if (!($statusResponse['success'] ?? false)) {
            return false;
        }

        $data = $statusResponse['data'] ?? [];
        if (!isset($data['hits_delivered'])) {
            return false;
        }

        $newHits   = (int) $data['hits_delivered'];
        $deltaHits = max(0, $newHits - (int) $campaign->hits_delivered);

        // Always read FRESH user points from DB — avoids stale cached values across campaigns
        $user       = $campaign->user_id ? User::find($campaign->user_id) : null;
        $userPoints = (int) ($user ? $user->traffic_points : 0);

        $balanceExhausted = false;

        if ($deltaHits > 0 && $user) {
            // Calculate EXACT points per visit from campaign parameters
            // This avoids rounding errors from stored points_deducted budget
            $pointsPerVisit = self::calcExactPointsPerVisit($campaign);
            $ptsToDeduct    = max(1, (int) round($deltaHits * $pointsPerVisit));

            $user->decrement('traffic_points', $ptsToDeduct);
            $userPoints = max(0, $userPoints - $ptsToDeduct);

            try {
                TrafficPointLog::create([
                    'user_id'     => $user->id,
                    'type'        => 'usage',
                    'points'      => -$ptsToDeduct,
                    'hits_count'  => $deltaHits,
                    'cost_usd'    => 0,
                    'description' => "Pay-As-You-Go: {$deltaHits} visits × " . round($pointsPerVisit, 1) . " pts = {$ptsToDeduct} Pts for {$campaign->external_order_id}",
                    'status'      => 'completed',
                ]);
            } catch (\Throwable $e) {}
        }

        $campaign->hits_delivered = $newHits;
        if (isset($data['status'])) {
            $campaign->status = strtolower($data['status']);
        }

        // Auto-pause THIS campaign if balance is now zero or negative
        if ($user && $userPoints <= 0 && strtolower($campaign->status) === 'active') {
            $pauseResp = $apiService->updateCampaignStatus($campaign->external_order_id, 'paused');
            if ($pauseResp['success'] ?? false) {
                $campaign->status      = 'paused';
                $campaign->auto_paused = true;
            }
            $balanceExhausted = true; // Tell the caller to also pause the user's other campaigns
        }

        $campaign->save();

        return $balanceExhausted;
    }

    /**
     * Calculate the exact points-per-visit from actual campaign parameters.
     * Search Normal  = 20 pts/min, Search Premium = 30 pts/min, Direct = 1 pt/min.
     * Total time = main duration + (sub_page_visits × sub_page_duration), in seconds.
     */
    public static function calcExactPointsPerVisit(TrafficCampaign $campaign): float
    {
        $type        = strtolower(trim($campaign->campaign_type ?? 'direct'));
        $captcha     = strtolower(trim($campaign->captcha_mode ?? 'normal'));

        if ($type === 'search') {
            $baseRate60s = ($captcha === 'premium') ? 30.0 : 20.0;
        } else {
            $baseRate60s = 1.0; // Direct traffic
        }

        $mainDuration    = max(1, (int) $campaign->duration);
        $subVisits       = (int) ($campaign->sub_page_visits  ?? 0);
        $subDuration     = (int) ($campaign->sub_page_duration ?? 0);
        $totalSeconds    = $mainDuration + ($subVisits * $subDuration);
        if ($totalSeconds <= 0) $totalSeconds = 60;

        return $baseRate60s * ($totalSeconds / 60.0);
    }
}

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
        // Sanity-check: fix over-inflated points_deducted for direct campaigns
        if (strtolower(trim($campaign->campaign_type)) === 'direct' && $campaign->total_limit > 0) {
            $totalSeconds = (int) $campaign->duration + ((int) $campaign->sub_page_visits * (int) $campaign->sub_page_duration);
            if ($totalSeconds <= 0) $totalSeconds = 60;
            $correctBudget = (int) ceil(1.0 * ($totalSeconds / 60.0) * $campaign->total_limit);
            if ($campaign->points_deducted > ($correctBudget * 2)) {
                $campaign->points_deducted = max($correctBudget, $campaign->total_limit);
                $campaign->save();
            }
        }

        $statusResponse = $apiService->getCampaignStatus($campaign->external_order_id);

        if (!($statusResponse['success'] ?? false)) {
            return false;
        }

        $data = $statusResponse['data'] ?? [];
        if (!isset($data['hits_delivered'])) {
            return false;
        }

        $newHits  = (int) $data['hits_delivered'];
        $deltaHits = max(0, $newHits - (int) $campaign->hits_delivered);

        // Always read FRESH user points from DB — avoids stale cached values across campaigns
        $user = $campaign->user_id ? User::find($campaign->user_id) : null;
        $userPoints = (int) ($user ? $user->traffic_points : 0);

        $balanceExhausted = false;

        if ($deltaHits > 0 && $user) {
            $ratePerHit   = $campaign->total_limit > 0 ? ($campaign->points_deducted / max(1, $campaign->total_limit)) : 1.0;
            $ptsToDeduct  = max(1, (int) round($deltaHits * $ratePerHit));

            $user->decrement('traffic_points', $ptsToDeduct);
            $userPoints = max(0, $userPoints - $ptsToDeduct);

            try {
                TrafficPointLog::create([
                    'user_id'     => $user->id,
                    'type'        => 'usage',
                    'points'      => -$ptsToDeduct,
                    'cost_usd'    => 0,
                    'description' => "Pay-As-You-Go: {$deltaHits} visits delivered ({$ptsToDeduct} Pts) for {$campaign->external_order_id}",
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
                $campaign->status     = 'paused';
                $campaign->auto_paused = true;
            }
            $balanceExhausted = true; // Tell the caller to also pause the user's other campaigns
        }

        $campaign->save();

        return $balanceExhausted;
    }
}

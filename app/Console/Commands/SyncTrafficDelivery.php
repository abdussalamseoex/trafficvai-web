<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrafficCampaign;
use App\Models\TrafficPointLog;
use App\Services\SurfEngineApiService;
use Illuminate\Support\Facades\Log;

class SyncTrafficDelivery extends Command
{
    protected $signature = 'traffic:sync-delivery';
    protected $description = 'Sync live hits delivery from Core Engine for all active traffic campaigns and deduct points proportionally';

    public function handle(SurfEngineApiService $apiService)
    {
        $activeCampaigns = TrafficCampaign::with('user')->where('status', 'active')->get();

        $this->info("Syncing delivery for " . $activeCampaigns->count() . " active campaigns...");

        foreach ($activeCampaigns as $campaign) {
            try {
                self::syncSingleCampaign($campaign, $apiService);
            } catch (\Throwable $e) {
                Log::warning("Traffic sync failed for campaign #{$campaign->id}: " . $e->getMessage());
            }
        }

        $this->info("Traffic delivery sync completed.");
        return 0;
    }

    public static function syncSingleCampaign(TrafficCampaign $campaign, SurfEngineApiService $apiService)
    {
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

        $newHits = (int) $data['hits_delivered'];
        $currentHits = (int) $campaign->hits_delivered;
        $deltaHits = max(0, $newHits - $currentHits);

        $user = $campaign->user;
        $userPoints = (int) ($user ? $user->traffic_points : 0);

        if ($deltaHits > 0 && $user) {
            $ratePerHit = $campaign->total_limit > 0 ? ($campaign->points_deducted / max(1, $campaign->total_limit)) : 1.0;
            $ptsToDeduct = max(1, (int) round($deltaHits * $ratePerHit));
            
            $user->decrement('traffic_points', $ptsToDeduct);
            $userPoints -= $ptsToDeduct;

            try {
                TrafficPointLog::create([
                    'user_id' => $user->id,
                    'type' => 'usage',
                    'points' => -$ptsToDeduct,
                    'cost_usd' => 0,
                    'description' => "Pay-As-You-Go Delivery: {$deltaHits} visits delivered ({$ptsToDeduct} Pts) for {$campaign->external_order_id}",
                    'status' => 'completed',
                ]);
            } catch (\Throwable $e) {}
        }

        $campaign->hits_delivered = $newHits;
        if (isset($data['status'])) {
            $campaign->status = strtolower($data['status']);
        }
        
        // Auto-pause if insufficient funds
        if ($user && $userPoints <= 0 && strtolower($campaign->status) === 'active') {
            $pauseResp = $apiService->updateCampaignStatus($campaign->external_order_id, 'paused');
            if ($pauseResp['success'] ?? false) {
                $campaign->status = 'paused';
                $campaign->auto_paused = true;
            }
        }
        
        $campaign->save();

        return true;
    }
}

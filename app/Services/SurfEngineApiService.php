<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SurfEngineApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.surf_engine.url', env('SURF_ENGINE_BASE_URL', 'https://surf.abguestpost.net')), '/');
        $this->apiKey = config('services.surf_engine.key', env('SURF_ENGINE_API_KEY', 'tv_live_eaae33f4453a00dde2e727e55741d7c6'));
    }

    /**
     * Send Order Injection POST request to Core Automation Engine
     */
    public function createCampaign(array $payload): array
    {
        $url = "{$this->baseUrl}/api/v1/external/campaign/create";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(15)->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status()
                ];
            }

            Log::warning('SurfEngineApiService Create Campaign Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Failed to launch campaign on core engine.',
                'status' => $response->status(),
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('SurfEngineApiService Exception: ' . $e->getMessage(), [
                'url' => $url,
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'error' => 'Connection error communicating with Core Automation Engine: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fetch live campaign status from Core Automation Engine
     */
    public function getCampaignStatus(string $orderId): array
    {
        $url = "{$this->baseUrl}/api/v1/external/campaign/{$orderId}/status";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Stop and Delete campaign on Core Automation Engine
     */
    public function deleteCampaign(string $orderId): array
    {
        $urls = [
            "{$this->baseUrl}/api/v1/external/campaign/{$orderId}/delete",
            "{$this->baseUrl}/api/v1/external/campaign/action",
        ];

        $results = [];
        foreach ($urls as $url) {
            try {
                $response = Http::withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])->timeout(10)->post($url, [
                    'external_order_id' => $orderId,
                    'order_id' => $orderId,
                    'action' => 'delete',
                ]);

                if ($response->successful()) {
                    return ['success' => true, 'data' => $response->json()];
                }
                $results[] = $response->status();
            } catch (\Exception $e) {
                Log::warning('SurfEngineApiService deleteCampaign Exception: ' . $e->getMessage());
            }
        }

        return ['success' => false, 'error' => 'Delete sync attempted: ' . implode(', ', $results)];
    }

    /**
     * Update campaign status (pause/active) on Core Automation Engine
     */
    public function updateCampaignStatus(string $orderId, string $status): array
    {
        $url = "{$this->baseUrl}/api/v1/external/campaign/action";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(10)->post($url, [
                'external_order_id' => $orderId,
                'order_id' => $orderId,
                'action' => $status,
                'status' => $status,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('SurfEngineApiService updateCampaignStatus Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

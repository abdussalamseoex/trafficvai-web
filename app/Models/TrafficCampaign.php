<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrafficCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'external_order_id',
        'remote_campaign_id',
        'campaign_type',
        'url',
        'total_limit',
        'hourly_limit',
        'daily_limit',
        'duration',
        'sub_page_visits',
        'sub_page_duration',
        'device_type',
        'target_country',
        'search_engine',
        'keywords',
        'max_page',
        'captcha_mode',
        'traffic_source',
        'custom_referrers',
        'sub_page_toggle',
        'behavior_scroll',
        'behavior_click',
        'points_deducted',
        'hits_delivered',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'keywords' => 'array',
        'expires_at' => 'datetime',
        'points_deducted' => 'integer',
        'hits_delivered' => 'integer',
        'total_limit' => 'integer',
        'hourly_limit' => 'integer',
        'daily_limit' => 'integer',
        'duration' => 'integer',
        'sub_page_visits' => 'integer',
        'sub_page_duration' => 'integer',
        'max_page' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to calculate delivery percentage
     */
    public function getDeliveryPercentageAttribute(): float
    {
        if ($this->total_limit <= 0) {
            return 0.0;
        }
        return min(100.0, round(($this->hits_delivered / $this->total_limit) * 100, 1));
    }

    /**
     * Check if campaign points have expired (30 days validity)
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestPostSite extends Model
{
    protected $fillable = [
        'url', 'niche', 'da', 'dr', 'traffic', 'price', 'is_active',
        'link_type', 'max_links_allowed', 'is_sponsored', 'language', 'service_type', 'spam_score',
        'price_creation_placement', 'price_link_insertion', 'description', 'sample_post_url',
        'delivery_time_days', 'express_delivery_time_days', 'express_delivery_price', 'word_count'
    ];

    protected $casts = [
        'niche' => 'array',
        'is_active' => 'boolean',
        'is_sponsored' => 'boolean',
    ];

    /**
     * Users who favorited this site.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'guest_post_favorites', 'guest_post_site_id', 'user_id')->withTimestamps();
    }

    /**
     * Check if the site is favorited by the current user.
     */
    public function getIsFavoritedAttribute()
    {
        if (!auth()->check()) {
            return false;
        }
        return $this->favoritedBy()->where('user_id', auth()->id())->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoGlobalSetting extends Model
{
    protected $fillable = [
        'robots_txt', 'sitemap_enabled', 'sitemap_last_generated',
        'ga_code', 'gsc_verification', 'header_scripts', 'footer_scripts',
        'default_og_image', 'site_name', 'twitter_handle'
    ];

    protected $casts = [
        'sitemap_enabled' => 'boolean',
        'sitemap_last_generated' => 'datetime',
    ];
}

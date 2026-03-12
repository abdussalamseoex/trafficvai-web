<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'entity_type', 'entity_id', 'meta_title', 'meta_description',
        'meta_keywords', 'slug', 'canonical_url', 'focus_keyword',
        'og_title', 'og_description', 'og_image', 'featured_image',
        'image_alt_text', 'breadcrumb_title', 'robots_index',
        'robots_directive', 'schema_json', 'schema_type', 'publish_date', 'update_date'
    ];

    protected $casts = [
        'robots_index' => 'boolean',
        'publish_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}

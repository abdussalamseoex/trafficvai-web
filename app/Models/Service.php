<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['service_type', 'category_id', 'name', 'slug', 'description', 'is_active', 'faqs', 'hero_image', 'hero_video_url', 'sample_link'];

    protected $casts = [
        'faqs' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function requirements()
    {
        return $this->hasMany(ServiceRequirement::class);
    }

    public function addons()
    {
        return $this->hasMany(Addon::class);
    }

    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class , 'entity');
    }
}

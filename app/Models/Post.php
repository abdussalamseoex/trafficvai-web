<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'status',
        'category_id',
    ];

    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class , 'entity');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

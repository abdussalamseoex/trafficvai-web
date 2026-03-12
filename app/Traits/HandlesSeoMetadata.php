<?php

namespace App\Traits;

use App\Models\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesSeoMetadata
{
    /**
     * Store or update SEO metadata for an entity.
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function syncSeoMetadata($entity, Request $request)
    {
        $data = $request->only([
            'meta_title', 'meta_description', 'meta_keywords',
            'canonical_url', 'focus_keyword', 'og_title', 'og_description',
            'image_alt_text', 'breadcrumb_title', 'robots_index',
            'robots_directive', 'schema_json', 'schema_type', 'publish_date'
        ]);

        if ($request->has('custom_slug')) {
            $data['slug'] = $request->input('custom_slug');
        }

        // Handle file uploads
        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('seo', 'public');
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('seo', 'public');
        }

        // Default robots_index to 1 if not provided
        if (!$request->has('robots_index')) {
            $data['robots_index'] = 1;
        }

        $entity->seoMeta()->updateOrCreate([], $data);
    }
}

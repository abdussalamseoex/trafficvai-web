<?php

namespace App\Services;

use App\Models\SeoMeta;
use App\Models\SeoGlobalSetting;
use App\Models\SeoRedirect;
use App\Models\Setting;
use Illuminate\Support\Facades\Request;

class SeoService
{
    /**
     * Get metadata for the current page/entity.
     */
    public function getMetadata($entity = null)
    {
        $global = $this->getGlobalSettings();
        $seo = $entity ? $entity->seoMeta : null;

        return [
            'title' => $this->getTitle($entity, $seo, $global),
            'description' => $this->getDescription($entity, $seo),
            'keywords' => $seo->meta_keywords ?? null,
            'canonical' => $seo->canonical_url ?? $this->getEntityUrl($entity),
            'robots' => $seo->robots_directive ?? 'index,follow',
            'og' => [
                'title' => $seo->og_title ?? $this->getTitle($entity, $seo, $global),
                'description' => $seo->og_description ?? $this->getDescription($entity, $seo),
                'image' => $this->getOgImage($seo, $global),
                'site_name' => $global->site_name ?? config('app.name'),
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'site' => $global->twitter_handle ?? null,
            ],
            'schema' => $seo->schema_json ?? $this->generateDefaultSchema($entity),
            'scripts' => [
                'header' => $global->header_scripts ?? '',
                'footer' => $global->footer_scripts ?? '',
                'ga' => $global->ga_code ?? null,
                'gsc' => $global->gsc_verification ?? null,
            ]
        ];
    }

    /**
     * Fallback logic for Title.
     */
    private function getTitle($entity, $seo, $global)
    {
        if ($seo && $seo->meta_title)
            return $seo->meta_title;

        if (!$entity && Request::getPathInfo() === '/') {
            $homeTitle = Setting::get('home_seo_title');
            if ($homeTitle) return $homeTitle;
        }

        $title = $entity ? ($entity->title ?? $entity->name ?? 'Page') : 'Home';
        $siteName = $global->site_name ?? Setting::get('site_name') ?? config('app.name');

        return "{$title} | {$siteName}";
    }

    /**
     * Fallback logic for Description.
     */
    private function getDescription($entity, $seo)
    {
        if ($seo && $seo->meta_description)
            return $seo->meta_description;

        if (!$entity && Request::getPathInfo() === '/') {
            $homeDesc = Setting::get('home_seo_description');
            if ($homeDesc) return $homeDesc;
        }

        $content = $entity ? ($entity->content ?? $entity->description ?? '') : '';
        return substr(strip_tags($content), 0, 160);
    }

    private function getOgImage($seo, $global)
    {
        if ($seo && $seo->og_image)
            return asset('storage/' . $seo->og_image);
        if ($seo && $seo->featured_image)
            return asset('storage/' . $seo->featured_image);
        return $global->default_og_image ? asset('storage/' . $global->default_og_image) : null;
    }

    public function getGlobalSettings()
    {
        return SeoGlobalSetting::first() ?? new SeoGlobalSetting();
    }

    /**
     * Generate default JSON-LD schema based on entity type.
     */
    private function generateDefaultSchema($entity)
    {
        if (!$entity)
            return null;

        $type = get_class($entity);
        $data = [
            '@context' => 'https://schema.org',
            'name' => $entity->title ?? $entity->name,
            'url' => 'https://trafficvai.com' . (Request::getPathInfo() === '/' ? '' : Request::getPathInfo()),
        ];

        if (str_contains($type, 'Post')) {
            $data['@type'] = 'Article';
            $data['headline'] = $entity->title;
            $data['datePublished'] = $entity->created_at->toIso8601String();
        }
        elseif (str_contains($type, 'Service')) {
            $data['@type'] = 'Service';
            $data['description'] = $this->getDescription($entity, null);
        }
        elseif (str_contains($type, 'Category')) {
            $data['@type'] = 'CollectionPage';
        }
        else {
            $data['@type'] = 'WebPage';
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate Sitemap XML content.
     */
    public function generateSitemap()
    {
        $entities = [
            \App\Models\Page::class ,
            \App\Models\Service::class ,
            \App\Models\Post::class ,
            \App\Models\Category::class ,
        ];

        $urls = [];
        $processedUrls = [];

        foreach ($entities as $modelClass) {
            $items = $modelClass::with('seoMeta')->get();
            foreach ($items as $item) {
                // Explicitly exclude noindex pages
                $seo = $item->seoMeta;
                if ($seo && (str_contains($seo->robots_directive ?? '', 'noindex') || ($seo->robots_index ?? 1) == 0)) {
                    continue;
                }

                $loc = $this->getEntityUrl($item);
                
                // Avoid duplicates in sitemap
                if (in_array($loc, $processedUrls)) {
                    continue;
                }

                $processedUrls[] = $loc;
                $urls[] = [
                    'loc' => $loc,
                    'lastmod' => $item->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => 0.8
                ];
            }
        }

        // Add the homepage if not present
        if (!in_array(url('/'), $processedUrls)) {
            array_unshift($urls, [
                'loc' => url('/'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => 1.0
            ]);
        }

        return $urls;
    }

    /**
     * Get the canonical/public URL for an entity.
     */
    public function getEntityUrl($entity)
    {
        if (!$entity) {
            return url('/') . (Request::getPathInfo() === '/' ? '' : Request::getPathInfo());
        }

        $slug = $entity->slug;
        $class = get_class($entity);

        // System Pages (The 13 indices)
        $systemSlugs = ['services', 'guest-posts', 'website-traffic', 'link-building', 'seo-campaigns', 'keyword-research', 'on-page-seo', 'technical-seo', 'local-seo', 'content-seo', 'seo-audit', 'monthly-seo', 'e-commerce-seo'];
        
        if (in_array($slug, $systemSlugs)) {
            if ($slug === 'website-traffic') return url('website-traffic');
            if ($slug === 'guest-posts') return url('guest-posts');
            if ($slug === 'link-building') return url('link-building');
            return url($slug);
        }

        if (str_contains($class, 'Post')) {
            return url('blog/' . $slug);
        }
        
        if (str_contains($class, 'Service')) {
            // Check if it's under a specific campaign type
            $seoTypes = ['seo-campaigns', 'keyword-research', 'on-page-seo', 'technical-seo', 'local-seo', 'content-seo', 'seo-audit', 'monthly-seo', 'e-commerce-seo'];
            if (in_array($entity->type, $seoTypes)) {
                return url($entity->type . '/' . $slug);
            }
            if ($entity->type === 'link-building') {
                return url('link-building/' . $slug);
            }
            if ($entity->type === 'traffic') {
                return url('website-traffic/' . $slug);
            }
            return url('services/' . $slug);
        }

        if (str_contains($class, 'Category')) {
            return url('services/category/' . $slug);
        }

        if (str_contains($class, 'Page')) {
            // Standard static pages
            $rootPages = ['contact', 'about', 'privacy-policy', 'terms', 'refund-policy'];
            if (in_array($slug, $rootPages)) {
                return url($slug);
            }
            return url('page/' . $slug);
        }

        return url($slug);
    }
}

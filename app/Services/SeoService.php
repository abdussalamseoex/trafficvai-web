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
            'canonical' => $seo->canonical_url ?? 'https://trafficvai.com' . (Request::getPathInfo() === '/' ? '' : Request::getPathInfo()),
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

        foreach ($entities as $modelClass) {
            $items = $modelClass::with('seoMeta')->get();
            foreach ($items as $item) {
                // Explicitly exclude noindex pages
                $seo = $item->seoMeta;
                if ($seo && (str_contains($seo->robots_directive, 'noindex') || $seo->robots_index == 0)) {
                    continue;
                }

                $prefix = '';
                if ($modelClass === \App\Models\Post::class)
                    $prefix = 'blog/';
                elseif ($modelClass === \App\Models\Service::class)
                    $prefix = 'services/';
                elseif ($modelClass === \App\Models\Category::class)
                    $prefix = 'services/category/';
                elseif ($modelClass === \App\Models\Page::class)
                    $prefix = 'page/';
                // for raw pages at root you might adjust prefix if needed

                $urls[] = [
                    'loc' => url($prefix . $item->slug),
                    'lastmod' => $item->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => 0.8
                ];
            }
        }

        return $urls;
    }
}

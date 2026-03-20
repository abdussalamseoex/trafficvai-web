<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class ServicePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Guest Posting Services',
                'slug' => 'guest-posts',
                'hero_badge' => 'Premium Quality',
                'hero_description' => 'Browse our inventory of premium guest post sites with real traffic and high authority metrics.',
                'content' => 'Our guest posting service helps you build high-quality backlinks from real websites with organic traffic.',
                'is_active' => true,
            ],
            [
                'title' => 'Targeted Website Traffic',
                'slug' => 'website-traffic',
                'hero_badge' => 'Guaranteed Results',
                'hero_description' => 'Boost your website metrics with high-quality, targeted geographic traffic to improve visibility.',
                'content' => 'Drive real, targeted visitors to your website to improve engagement and search engine rankings.',
                'is_active' => true,
            ],
            [
                'title' => 'Link Building Services',
                'slug' => 'link-building',
                'hero_badge' => 'Expert Outreach',
                'hero_description' => 'Manual outreach services to acquire high-authority, contextual backlinks for your brand.',
                'content' => 'Our expert link building team uses white-hat techniques to secure the most powerful links in your niche.',
                'is_active' => true,
            ],
            [
                'title' => 'SEO Campaigns',
                'slug' => 'seo-campaigns',
                'hero_badge' => 'Full Management',
                'hero_description' => 'Comprehensive, fully-managed SEO campaigns designed to dominate search engine results.',
                'content' => 'From technical audits to strategy execution, we handle everything to grow your organic visibility.',
                'is_active' => true,
            ],
            [
                'title' => 'All Solutions',
                'slug' => 'services',
                'hero_badge' => 'Complete Ecosystem',
                'hero_description' => 'Explore our full range of professional digital marketing and SEO services tailored for growth.',
                'content' => 'We provide everything you need to build, track, and scale your digital authority.',
                'is_active' => true,
            ],
            [
                'title' => 'Keyword Research',
                'slug' => 'keyword-research',
                'hero_badge' => 'Strategic Foundation',
                'hero_description' => 'Uncover high-value, low-competition keywords to drive targeted traffic to your site.',
                'content' => 'Data-driven keyword analysis for maximum ROI.',
                'is_active' => true,
            ],
            [
                'title' => 'On-Page SEO',
                'slug' => 'on-page-seo',
                'hero_badge' => 'Content Optimization',
                'hero_description' => 'Optimize your website content and structure for better visibility and higher rankings.',
                'content' => 'Premium on-page optimization for maximum relevance.',
                'is_active' => true,
            ],
            [
                'title' => 'Technical SEO',
                'slug' => 'technical-seo',
                'hero_badge' => 'Solid Engineering',
                'hero_description' => 'Ensure your site is healthy, fast, and easy for search engines to crawl and index.',
                'content' => 'Deep technical audits and fixes for peak performance.',
                'is_active' => true,
            ],
            [
                'title' => 'Local SEO',
                'slug' => 'local-seo',
                'hero_badge' => 'Neighborhood Authority',
                'hero_description' => 'Dominate your local market and appear in top local search results.',
                'content' => 'Targeted local growth strategies for high-intent customers.',
                'is_active' => true,
            ],
            [
                'title' => 'Content SEO',
                'slug' => 'content-seo',
                'hero_badge' => 'Rank-Driven Articles',
                'hero_description' => 'AI-proof, high-authority content designed specifically to rank and convert.',
                'content' => 'Premium content creation optimized for modern SEO.',
                'is_active' => true,
            ],
            [
                'title' => 'SEO Audit',
                'slug' => 'seo-audit',
                'hero_badge' => 'Full Diagnostics',
                'hero_description' => 'A comprehensive 250+ point checkup of your website health and ranking potential.',
                'content' => 'Expert analysis of your site’s strengths and weaknesses.',
                'is_active' => true,
            ],
            [
                'title' => 'Monthly SEO Packages',
                'slug' => 'monthly-seo',
                'hero_badge' => 'Sustained Growth',
                'hero_description' => 'Ongoing ranking authority and optimization to keep your traffic growing every month.',
                'content' => 'Comprehensive monthly management for long-term dominance.',
                'is_active' => true,
            ],
            [
                'title' => 'E-commerce SEO',
                'slug' => 'e-commerce-seo',
                'hero_badge' => 'Sales Driven',
                'hero_description' => 'Specialized ranking strategies for Shopify, WooCommerce, and custom stores.',
                'content' => 'Boost your product visibility and drive sales with expert shop SEO.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }
}

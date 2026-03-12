<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Agency Admin',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]
        );

        $client = User::factory()->create([
            'name' => 'John Doe (Client)',
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // 1.5 Create Global Settings
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'TrafficVai', 'group' => 'general', 'type' => 'text'],
            ['key' => 'contact_email', 'value' => 'support@trafficvai.com', 'group' => 'general', 'type' => 'text'],
            ['key' => 'contact_phone', 'value' => '+1 (555) 123-4567', 'group' => 'general', 'type' => 'text'],

            // Homepage SEO
            ['key' => 'home_seo_title', 'value' => 'TrafficVai | Premium Link Building & SEO Services', 'group' => 'seo', 'type' => 'text'],
            ['key' => 'home_seo_description', 'value' => 'Accelerate your organic growth with our elite, white-hat link building and fully-managed SEO campaigns. Safe, sustainable, and scalable results.', 'group' => 'seo', 'type' => 'textarea'],

            // Homepage Content
            ['key' => 'home_hero_headline', 'value' => 'Dominant SEO &<br/>Premium Links', 'group' => 'homepage', 'type' => 'text'],
            ['key' => 'home_hero_subheadline', 'value' => 'The premier white-label link building platform for agencies and ambitious brands. Secure high-authority placements that move the needle without the risk.', 'group' => 'homepage', 'type' => 'textarea'],
            ['key' => 'home_trust_stat_1', 'value' => '15,000+', 'group' => 'homepage', 'type' => 'text'],
            ['key' => 'home_trust_stat_2', 'value' => '1,200+', 'group' => 'homepage', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
            ['key' => $setting['key']],
                $setting
            );
        }

        $client = User::updateOrCreate(
        ['email' => 'client@example.com'],
        [
            'name' => 'John Doe (Client)',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]
        );

        // 2. Create Demo Services
        $linkBuilding = \App\Models\Service::create([
            'name' => 'High DA Authority Link Building',
            'slug' => 'high-da-link-building',
            'description' => 'Boost your website rankings with our manual outreach link building service. We secure contextual do-follow backlinks on high Domain Authority websites within your specific niche.',
            'is_active' => true,
        ]);

        $guestPosting = \App\Models\Service::create([
            'name' => 'Premium Guest Posting',
            'slug' => 'premium-guest-posting',
            'description' => 'Publish your content on real websites with genuine traffic. Includes content creation and natural placement of your target URLs.',
            'is_active' => true,
        ]);

        // 3. Create Packages for Link Building
        $bronzePackage = $linkBuilding->packages()->create([
            'name' => 'Starter Boost',
            'description' => 'Perfect for new websites looking for initial traction.',
            'price' => 199.00,
            'features' => ['5 High DA Backlinks (DA 40+)', 'Contextual Placement', 'Do-Follow Links', 'Detailed Excel Report'],
            'turnaround_time_days' => 7,
        ]);

        $goldPackage = $linkBuilding->packages()->create([
            'name' => 'Growth Engine',
            'description' => 'Our most popular package for competitive keywords.',
            'price' => 499.00,
            'features' => ['15 High DA Backlinks (DA 50+)', 'Contextual Placement', 'Drip-fed over 2 weeks', 'Premium Content Included'],
            'turnaround_time_days' => 14,
        ]);

        $guestPosting->packages()->create([
            'name' => 'Authority Author',
            'description' => 'One premium guest post on a DA 70+ website in your niche.',
            'price' => 350.00,
            'features' => ['1 Post on DA 70+ Site', '1000+ Word Human Written Content', 'Do-Follow Link', 'Guaranteed Indexing'],
            'turnaround_time_days' => 10,
        ]);

        // 4. Set Dynamic Requirements for Link Building
        $reqUrl = $linkBuilding->requirements()->create([
            'name' => 'Target Website URL',
            'type' => 'url',
            'is_required' => true,
        ]);

        $reqAnchor = $linkBuilding->requirements()->create([
            'name' => 'Preferred Anchor Texts (Comma separated)',
            'type' => 'textarea',
            'is_required' => true,
        ]);

        $linkBuilding->requirements()->create([
            'name' => 'Additional Notes / Competitors',
            'type' => 'textarea',
            'is_required' => false,
        ]);

        // 5. Create Mock Orders for the Client

        // Order 1: Pending Requirements
        $orderPending = \App\Models\Order::create([
            'user_id' => $client->id,
            'package_id' => $bronzePackage->id,
            'status' => 'pending_requirements',
            'total_amount' => $bronzePackage->price,
        ]);

        // Order 2: Processing (Already submitted requirements)
        $orderProcessing = \App\Models\Order::create([
            'user_id' => $client->id,
            'package_id' => $goldPackage->id,
            'status' => 'processing',
            'total_amount' => $goldPackage->price,
            'created_at' => now()->subDays(2),
        ]);

        // Seek some submitted requirements for the processing order
        $orderProcessing->requirements()->create([
            'service_requirement_id' => $reqUrl->id,
            'value' => 'https://example-client-site.com/seo-services',
        ]);
        $orderProcessing->requirements()->create([
            'service_requirement_id' => $reqAnchor->id,
            'value' => 'best seo software, seo tools for agencies, rank tracker',
        ]);

        // 6. Create Guest Post Sites
        $site1 = \App\Models\GuestPostSite::create([
            'url' => 'https://techcrunchy.example.com',
            'niche' => 'Technology',
            'da' => 82,
            'dr' => 79,
            'traffic' => 500000,
            'price' => 450.00,
            'is_active' => true,
        ]);

        $site2 = \App\Models\GuestPostSite::create([
            'url' => 'https://healthandfitness.example.com',
            'niche' => 'Health',
            'da' => 65,
            'dr' => 60,
            'traffic' => 120000,
            'price' => 200.00,
            'is_active' => true,
        ]);

        $site3 = \App\Models\GuestPostSite::create([
            'url' => 'https://financeweekly.example.com',
            'niche' => 'Finance',
            'da' => 71,
            'dr' => 68,
            'traffic' => 250000,
            'price' => 300.00,
            'is_active' => true,
        ]);

        // 7. Mock Guest Post Order
        $orderGp = \App\Models\Order::create([
            'user_id' => $client->id,
            'guest_post_site_id' => $site1->id,
            'status' => 'processing',
            'total_amount' => $site1->price,
            'guest_post_url' => 'https://example-client-site.com/tech-news',
            'guest_post_anchor' => 'latest tech news software',
            'created_at' => now()->subDay(),
        ]);
    }
}

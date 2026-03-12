<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Support\Str;

class SeoCampaignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = [
            'Keyword Research',
            'On-Page SEO',
            'Technical SEO',
            'Link Building',
            'Local SEO',
            'Content SEO',
            'SEO Audit',
            'Monthly SEO',
            'E-commerce SEO',
        ];

        foreach ($campaigns as $campaign) {
            $slug = Str::slug($campaign);

            $service = Service::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $campaign,
                'service_type' => $slug,
                'description' => 'Professional ' . $campaign . ' services to improve your search engine rankings.',
                'is_active' => true,
            ]
            );
        }
    }
}

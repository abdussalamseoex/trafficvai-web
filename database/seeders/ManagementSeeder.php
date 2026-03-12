<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'NexusSEO', 'group' => 'General', 'type' => 'text'],
            ['key' => 'contact_email', 'value' => 'support@nexusseo.example.com', 'group' => 'General', 'type' => 'text'],
            ['key' => 'copyright_text', 'value' => '© 2026 NexusSEO. All rights reserved.', 'group' => 'Footer', 'type' => 'text'],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'System', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // LEDs / Leads
        \App\Models\Lead::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Question about Bulk Orders',
            'message' => 'Hello, I want to order 50 guest posts. Do you have a discount?',
            'status' => 'pending'
        ]);

        \App\Models\Lead::create([
            'name' => 'Jane Smith',
            'email' => 'jane@agency.com',
            'subject' => 'Service Inquiry',
            'message' => 'I am interested in your organic traffic service for my new blog.',
            'status' => 'contacted'
        ]);

        // FAQs
        \App\Models\SiteFaq::create([
            'question' => 'How long does delivery take?',
            'answer' => 'Most orders are processed within 3-7 business days depending on the selected package.',
            'category' => 'General',
            'sort_order' => 1
        ]);

        \App\Models\SiteFaq::create([
            'question' => 'Do you provide reports?',
            'answer' => 'Yes, all orders include a detailed PDF/live report upon completion.',
            'category' => 'Orders',
            'sort_order' => 2
        ]);
    }
}

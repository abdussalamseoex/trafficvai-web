<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'mail_from_address', 'value' => 'info@trafficvai.com', 'group' => 'Email Settings', 'type' => 'text'],
            ['key' => 'mail_from_name', 'value' => 'TrafficVai SEO', 'group' => 'Email Settings', 'type' => 'text'],
            ['key' => 'mail_mailer', 'value' => 'smtp', 'group' => 'Email Settings', 'type' => 'text'],
            ['key' => 'mail_host', 'value' => 'smtp.mailtrap.io', 'group' => 'Email Settings', 'type' => 'text'],
            ['key' => 'mail_port', 'value' => '2525', 'group' => 'Email Settings', 'type' => 'text'],
            ['key' => 'mail_username', 'value' => '', 'group' => 'Email Settings', 'type' => 'text'],
            ['key' => 'mail_password', 'value' => '', 'group' => 'Email Settings', 'type' => 'password'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'group' => 'Email Settings', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}

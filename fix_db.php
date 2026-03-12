<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Package::query()->update(['turnaround_time_days' => 5, 'express_turnaround_time_days' => 2]);
App\Models\GuestPostSite::query()->update(['delivery_time_days' => 14, 'express_delivery_time_days' => 5]);
App\Models\Order::whereIn('status', ['processing', 'pending_requirements'])->update([
    'expected_delivery_date' => now()->addDays(2),
    'status' => 'processing',
    'is_emergency' => 1
]);

echo "Updated successfully.\n";

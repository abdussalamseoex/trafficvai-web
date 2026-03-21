<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('invoices:send-renewal-reminders')->daily();

// Ensure background jobs run continuously on typical cPanel hosting setup
Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

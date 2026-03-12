<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\App\Models\Setting::updateOrCreate(['key' => 'gateway_crypto_enabled'], ['value' => '1', 'group' => 'Payment Gateways', 'type' => 'text']);
\App\Models\Setting::updateOrCreate(['key' => 'gateway_bd_enabled'], ['value' => '1', 'group' => 'Payment Gateways', 'type' => 'text']);

echo "Enabled crypto and bangladesh categories.\n";

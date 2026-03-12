<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "gateway_crypto_enabled: " . (\App\Models\Setting::get('gateway_crypto_enabled', '0')) . PHP_EOL;
echo "gateway_bd_enabled: " . (\App\Models\Setting::get('gateway_bd_enabled', '0')) . PHP_EOL;
echo "gateway_plisio_enabled: " . (\App\Models\Setting::get('gateway_plisio_enabled', '0')) . PHP_EOL;

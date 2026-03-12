<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$gateways = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
echo json_encode($gateways, JSON_PRETTY_PRINT) . PHP_EOL;

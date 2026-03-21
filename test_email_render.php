<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $mail = new \App\Mail\CustomPromotionalEmail('Test', '<h1>Hello</h1>');
    echo $mail->render();
    echo "\nRendered OK\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

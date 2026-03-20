<?php
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Refreshing System...</h1>";

try {
    echo "Clearing Route Cache... ";
    Artisan::call('route:clear');
    echo "Done.<br>";

    echo "Clearing View Cache... ";
    Artisan::call('view:clear');
    echo "Done.<br>";

    echo "Clearing Config Cache... ";
    Artisan::call('config:clear');
    echo "Done.<br>";

    echo "Clearing Cache... ";
    Artisan::call('cache:clear');
    echo "Done.<br>";
    
    echo "Optimizing... ";
    Artisan::call('optimize:clear');
    echo "Done.<br>";

    echo "<br><h2 style='color: green;'>Success! All caches cleared.</h2>";
    echo "<p><a href='/admin/updates'>Go back to System Updates</a></p>";
} catch (\Exception $e) {
    echo "<br><h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}

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
    echo "--- Code Sync ---<br>";
    echo "Fetching latest code... ";
    $fetch = shell_exec("git fetch 2>&1");
    echo "<pre>$fetch</pre>";

    $currentBranch = trim(shell_exec("git rev-parse --abbrev-ref HEAD 2>&1") ?: 'main');
    echo "Resetting to origin/$currentBranch... ";
    $reset = shell_exec("git reset --hard origin/$currentBranch 2>&1");
    echo "<pre>$reset</pre>";

    echo "<br>--- Cache Refresh ---<br>";
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

    echo "<br><h2 style='color: green;'>Success! System refreshed and updated.</h2>";
    echo "<p><a href='/admin/updates'>Go to System Updates</a> to run Force Sync if needed.</p>";
} catch (\Exception $e) {
    echo "<br><h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}

<?php
define('LARAVEL_START', microtime(true));

// 1. Load Autoloader
require __DIR__.'/../vendor/autoload.php';

// 2. Start Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre style='font-family:monospace; padding:20px; background:#f4f4f4; border-radius:10px;'>";
echo "<b style='color:#4f46e5; font-size:1.2rem;'>TrafficVai - Main Domain Setup</b>\n";
echo "--------------------------------------------------\n\n";

$commands = [
    'migrate --force' => 'Creating Database Tables',
    'db:seed --force' => 'Seeding Admin & Initial Data',
    'config:clear'    => 'Clearing Config Cache',
    'cache:clear'     => 'Clearing Application Cache',
    'storage:link'    => 'Creating Storage Link',
];

foreach ($commands as $cmd => $desc) {
    echo "<b>[RUNNING]</b> $desc...\n";
    try {
        $exitCode = $kernel->call($cmd);
        echo $kernel->output();
        if ($exitCode === 0) {
            echo "<span style='color:green;'>✅ Success</span>\n\n";
        } else {
            echo "<span style='color:red;'>❌ Failed (Exit Code: $exitCode)</span>\n\n";
        }
    } catch (\Exception $e) {
        echo "<span style='color:red;'>❌ ERROR: " . $e->getMessage() . "</span>\n\n";
    }
}

echo "--------------------------------------------------\n";
echo "<b style='color:#4f46e5;'>SETUP COMPLETE!</b>\n\n";
echo "<b>Default Admin Credentials:</b>\n";
echo "URL: <a href='/login'>/login</a>\n";
echo "Email: admin@example.com\n";
echo "Pass: password\n\n";
echo "<span style='color:red; font-weight:bold;'>CRITICAL: Please DELETE this file (public/setup_main.php) immediately for security.</span>\n";
echo "</pre>";

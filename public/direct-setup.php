<?php
/**
 * TRAFFICVAI FINAL MASTER - Solution & Reset
 * This version handles the "Duplicate Column" error and provides a fresh start option.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<body style='font-family:sans-serif; background:#0f172a; color:#f1f5f9; padding:20px; line-height:1.6;'>";
echo "<div style='max-width:900px; margin:auto; background:#1e293b; padding:40px; border-radius:20px; border:1px solid #334155;'>";
echo "<h1 style='color:#4ade80;'>TrafficVai Master Setup</h1>";

$basePath = realpath(__DIR__ . '/..');
if (!file_exists($basePath . '/vendor/autoload.php')) $basePath = realpath(__DIR__);

try {
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $conn = config('database.default');
    $dbName = config("database.connections.$conn.database");
    
    echo "<div style='background:#111827; padding:20px; border-radius:10px; border-left:5px solid #60a5fa; margin-bottom:20px;'>";
    echo "<b>Current Connection:</b> $dbName<br>";
    echo "<b>Tables Found:</b> " . count(Illuminate\Support\Facades\DB::select('SHOW TABLES'));
    echo "</div>";

    if (isset($_GET['reset']) && $_GET['reset'] == 'true') {
        echo "<h3>🚀 Performing Master Reset (migrate:fresh)...</h3>";
        Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        echo "<pre style='background:#000; padding:15px; color:#94a3b8; border-radius:10px; font-size:12px;'>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
        echo "<h2 style='color:#4ade80;'>✅ SYSTEM RESET COMPLETE!</h2>";
        echo "<p><a href='/login' style='display:inline-block; background:#10b981; color:white; padding:12px 30px; text-decoration:none; border-radius:10px;'>GO TO LOGIN</a></p>";
    } else {
        echo "<h3>📋 System Diagnostics</h3>";
        echo "<p>আপনার স্ক্রিনশট বলছে ডাটাবেজে আগে থেকেই কিছু টেবিল তৈরি হয়ে আছে। এ কারণে 'Duplicate Column' এরর আসছে।</p>";
        
        echo "<div style='display:flex; gap:20px; margin-top:20px;'>";
        
        // Option 1: Just Seed
        echo "<div style='flex:1; background:#1e293b; border:1px solid #4ade80; padding:20px; border-radius:15px;'>";
        echo "<h4 style='color:#4ade80; margin-top:0;'>Option A: Just Seed Content</h4>";
        echo "<p>যদি সব টেবিল ঠিক থাকে তবে শুধু নিচের বাটনটি চাপুন।</p>";
        echo "<form><input type='hidden' name='seed' value='true'><button type='submit' style='background:#4ade80; color:#111; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-weight:bold;'>RUN SEEDER ONLY</button></form>";
        echo "</div>";

        // Option 2: Master Reset
        echo "<div style='flex:1; background:#1e293b; border:1px solid #f87171; padding:20px; border-radius:15px;'>";
        echo "<h4 style='color:#f87171; margin-top:0;'>Option B: Master Reset</h4>";
        echo "<p style='color:#fca5a5;'>সব টেবিল মুছে নতুন করে শুরু করতে এটি ব্যবহার করুন। (প্রস্তাবিত)</p>";
        echo "<a href='?reset=true' style='display:inline-block; background:#f87171; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:bold;'>MASTER RESET (FRESH INSTALL)</a>";
        echo "</div>";

        echo "</div>";

        if (isset($_GET['seed']) && $_GET['seed'] == 'true') {
            echo "<h3>🌱 Seeding Data...</h3>";
            Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            echo "<pre style='background:#000; padding:15px; color:#94a3b8;'>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
            echo "<h2 style='color:#4ade80;'>✅ SEEDING COMPLETE!</h2>";
        }
    }

    echo "<hr style='border:none; border-top:1px solid #334155; margin:40px 0;'>";
    echo "<p style='color:#94a3b8; font-size:14px;'><b>Note to User:</b> আপনি যদি phpMyAdmin এ টেবিল দেখতে না পান, তবে নিশ্চিত হোন যে আপনি <b>[$dbName]</b> নামের ডাটাবেজটিই চেক করছেন। সিপ্যানেলে একাধিক ডাটাবেজ থাকতে পারে।</p>";

} catch (Exception $e) {
    echo "<div style='background:#451a1a; padding:20px; border-radius:10px; border:1px solid #991b1b;'>";
    echo "<b style='color:#f87171;'>ERROR:</b> " . $e->getMessage();
    echo "</div>";
}

echo "</div></body>";

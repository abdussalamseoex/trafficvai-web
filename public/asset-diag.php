<?php
/**
 * TRAFFICVAI ASSET DIAGNOSTIC
 * This script checks if CSS/JS URLs are generated correctly.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<body style='font-family:sans-serif; background:#0f172a; color:#f1f5f9; padding:20px;'>";
echo "<div style='max-width:900px; margin:auto; background:#1e293b; padding:30px; border-radius:15px; border:1px solid #334155;'>";
echo "<h1 style='color:#60a5fa;'>TrafficVai Asset Diagnostic</h1>";

$basePath = realpath(__DIR__ . '/..');
if (!file_exists($basePath . '/vendor/autoload.php')) $basePath = realpath(__DIR__);

try {
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "<h3>1. Environment Info</h3>";
    echo "<ul>";
    echo "<li><b>APP_URL:</b> " . config('app.url') . "</li>";
    echo "<li><b>APP_ENV:</b> " . config('app.env') . "</li>";
    echo "<li><b>Public Path:</b> " . public_path() . "</li>";
    echo "</ul>";

    echo "<h3>2. Vite Asset Check</h3>";
    try {
        $vite = app(Illuminate\Foundation\Vite::class);
        $cssUrl = asset('build/' . json_decode(file_get_contents(public_path('build/manifest.json')), true)['resources/css/app.css']['file']);
        $jsUrl = asset('build/' . json_decode(file_get_contents(public_path('build/manifest.json')), true)['resources/js/app.js']['file']);
        
        echo "<ul>";
        echo "<li><b>CSS URL (from manifest):</b> <a href='$cssUrl' style='color:#4ade80' target='_blank'>$cssUrl</a></li>";
        echo "<li><b>JS URL (from manifest):</b> <a href='$jsUrl' style='color:#4ade80' target='_blank'>$jsUrl</a></li>";
        echo "</ul>";
        
        echo "<h4>Testing File Accessibility...</h4>";
        $cssFilePath = public_path('build/' . json_decode(file_get_contents(public_path('build/manifest.json')), true)['resources/css/app.css']['file']);
        if (file_exists($cssFilePath)) {
            echo "<p style='color:#4ade80;'>✅ CSS file exists on disk: " . basename($cssFilePath) . "</p>";
        } else {
            echo "<p style='color:#f87171;'>❌ CSS file MISSING from disk at: $cssFilePath</p>";
        }

    } catch (Exception $viteE) {
        echo "<p style='color:#f87171;'>❌ Vite/Manifest Error: " . $viteE->getMessage() . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:#f87171;'>❌ General Error: " . $e->getMessage() . "</p>";
}

echo "</div></body>";

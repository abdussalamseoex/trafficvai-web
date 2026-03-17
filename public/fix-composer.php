<?php
/**
 * Emergency Composer Install Script
 * Upload to public_html via FTP, visit URL, then DELETE immediately!
 * URL: https://dev.trafficvai.com/fix-composer.php
 */

echo "<pre style='font-family:monospace;background:#111;color:#0f0;padding:20px;'>";
echo "=== Emergency Composer Install ===\n\n";

$laravelRoot = __DIR__ . '/..';

// Common composer paths on cPanel
$composerPaths = [
    'composer',
    'composer.phar',
    '/usr/bin/composer',
    '/usr/local/bin/composer',
    '/opt/cpanel/composer/bin/composer',
    $laravelRoot . '/composer.phar',
];

$composerCmd = null;
foreach ($composerPaths as $path) {
    $test = shell_exec("which {$path} 2>/dev/null") ?? shell_exec("{$path} --version 2>/dev/null");
    if (!empty(trim($test ?? ''))) {
        $composerCmd = $path;
        echo "✓ Found composer at: {$path}\n\n";
        break;
    }
}

if (!$composerCmd) {
    echo "⚠ Composer not found in common paths. Trying to download...\n\n";
    // Download composer.phar
    $composerPhar = $laravelRoot . '/composer.phar';
    file_put_contents($composerPhar, file_get_contents('https://getcomposer.org/composer-stable.phar'));
    if (file_exists($composerPhar)) {
        $composerCmd = 'php ' . $composerPhar;
        echo "✓ Downloaded composer.phar\n\n";
    } else {
        echo "✗ Could not find or download composer. Please install manually via cPanel.\n";
        exit;
    }
}

// Run composer install
echo "--- Running: composer install --no-interaction --no-dev --optimize-autoloader ---\n\n";
$output = shell_exec("cd {$laravelRoot} && {$composerCmd} install --no-interaction --no-dev --optimize-autoloader 2>&1");
echo htmlspecialchars($output);

// Check if dompdf installed
if (is_dir($laravelRoot . '/vendor/barryvdh/laravel-dompdf')) {
    echo "\n\n✓ dompdf installed successfully!\n";
} else {
    echo "\n\n✗ dompdf still not found. Check output above for errors.\n";
}

echo "\n=== Done! ===\n";
echo "<strong style='color:red'>⚠ DELETE THIS FILE IMMEDIATELY via cPanel File Manager!</strong>\n";
echo "</pre>";

<?php

/**
 * Diagnosis Script for Laravel Storage Symlink
 * This script checks if the storage symlink is correctly configured and pointing to the right directory.
 */

header('Content-Type: text/html; charset=utf-8');

function get_status_icon($success) {
    return $success ? '<span style="color: green;">✔</span>' : '<span style="color: red;">✘</span>';
}

$public_path = __DIR__;
$root_path = dirname($public_path);
$storage_link = $public_path . DIRECTORY_SEPARATOR . 'storage';
$target_path = $root_path . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';

echo "<h1>TrafficVai Storage Diagnosis</h1>";

// 1. Check if public/storage exists
echo "<h2>1. Symbolic Link Check</h2>";
if (file_exists($storage_link)) {
    echo get_status_icon(true) . " <code>public/storage</code> exists.<br>";
    
    if (is_link($storage_link)) {
        echo get_status_icon(true) . " <code>public/storage</code> is a symbolic link.<br>";
        $link_target = readlink($storage_link);
        echo "Target: <code>$link_target</code><br>";
        
        if (realpath($link_target) === realpath($target_path)) {
            echo get_status_icon(true) . " Link points to the correct storage directory.<br>";
        } else {
            echo get_status_icon(false) . " Link points to: <code>" . realpath($link_target) . "</code>, but should point to: <code>" . realpath($target_path) . "</code><br>";
        }
    } else {
        echo get_status_icon(false) . " <code>public/storage</code> is a DIRECTORY, not a symlink. This is a common issue on cPanel.<br>";
        echo "<strong>Fix:</strong> Delete the <code>public/storage</code> folder and run the fix button below.<br>";
    }
} else {
    echo get_status_icon(false) . " <code>public/storage</code> does not exist.<br>";
}

// 2. Check if target exists
echo "<h2>2. Target Directory Check</h2>";
if (file_exists($target_path)) {
    echo get_status_icon(true) . " Target directory <code>storage/app/public</code> exists.<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($target_path)), -4) . "<br>";
} else {
    echo get_status_icon(false) . " Target directory <code>storage/app/public</code> does NOT exist.<br>";
}

// 3. Environment Check
echo "<h2>3. Environment Check</h2>";
$env_file = $root_path . DIRECTORY_SEPARATOR . '.env';
if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    preg_match('/APP_URL=(.*)/', $env_content, $matches);
    $app_url = isset($matches[1]) ? trim($matches[1]) : 'Not found';
    echo "APP_URL in .env: <code>$app_url</code><br>";
    echo "Actual request URL: <code>" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]</code><br>";
}

// 4. Action
echo "<h2>4. Actions</h2>";
if (isset($_GET['action']) && $_GET['action'] === 'fix') {
    if (file_exists($storage_link)) {
        if (is_link($storage_link)) {
            unlink($storage_link);
        } else {
            // Need to recurse and delete if it's a directory
            function rrmdir($dir) {
                if (is_dir($dir)) {
                    $objects = scandir($dir);
                    foreach ($objects as $object) {
                        if ($object != "." && $object != "..") {
                            if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir.DIRECTORY_SEPARATOR.$object))
                                rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                            else
                                unlink($dir. DIRECTORY_SEPARATOR .$object);
                        }
                    }
                    rmdir($dir);
                }
            }
            rrmdir($storage_link);
        }
    }
    
    if (symlink($target_path, $storage_link)) {
        echo '<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
        echo "Symlink created successfully! Refresh the page to see details.";
        echo '</div>';
    } else {
        echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
        echo "Failed to create symlink. You might need to ask your hosting provider to enable symlinking or do it via shell.";
        echo '</div>';
    }
}

echo '<a href="?action=fix" style="display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Attempt to Fix Symlink</a>';
echo '<br><br><p style="color: gray; font-size: 0.8em;">Note: Delete this file (<code>public/diagnose.php</code>) after you are done for security reasons.</p>';

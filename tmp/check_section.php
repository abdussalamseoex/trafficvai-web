<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$section = \App\Models\HomeSection::where('key', 'services')->first();
if ($section) {
    echo "Key: " . $section->key . "\n";
    echo "Content type: " . gettype($section->content) . "\n";
    if (is_array($section->content)) {
        foreach ($section->content as $k => $v) {
            echo "Key [$k] type: " . gettype($v) . "\n";
            if (is_string($v)) {
                echo "Value: " . substr($v, 0, 100) . "...\n";
            }
        }
    }
} else {
    echo "Section not found.\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = \App\Models\Service::where('slug', 'website-traffic-growth-package')->first();
if (!$service) {
    echo "Service not found!\n";
    exit;
}

echo "Service Name: " . $service->name . "\n";
echo "Service ID: " . $service->id . "\n";
echo "Service Class: " . get_class($service) . "\n";

$seo = $service->seoMeta;
if ($seo) {
    echo "SeoMeta Found!\n";
    echo "Title: " . $seo->meta_title . "\n";
    echo "Description: " . $seo->meta_description . "\n";
} else {
    echo "SeoMeta NOT FOUND for this service via relationship!\n";
    
    // Check all seo_meta records to see what's there
    $allSeo = \App\Models\SeoMeta::all();
    echo "\nAll SEO Meta Records:\n";
    foreach ($allSeo as $s) {
        echo "Type: " . $s->entity_type . " | ID: " . $s->entity_id . " | Title: " . $s->meta_title . "\n";
    }
}

$seoService = app(\App\Services\SeoService::class);
$metadata = $seoService->getMetadata($service);
echo "\nMetadata from SeoService:\n";
print_r($metadata);

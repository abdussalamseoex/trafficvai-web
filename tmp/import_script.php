<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GuestPostSite;
use Illuminate\Support\Facades\DB;

$csvPath = storage_path('app/guest_posts.csv');

if (!file_exists($csvPath)) {
    die("CSV file not found at: {$csvPath}\n");
}

$handle = fopen($csvPath, 'r');
$header = fgetcsv($handle);

$header = array_map('strtolower', $header);
$header = array_map('trim', $header);
$header = array_map(function($h) {
    return str_replace([' ', '-'], '_', $h);
}, $header);

echo "Headers: " . implode(', ', $header) . "\n";

$successCount = 0;

DB::beginTransaction();
try {
    while (($row = fgetcsv($handle)) !== false) {
        if (count($header) !== count($row)) {
            continue;
        }
        $data = array_combine($header, $row);
        
        // Find correct keys depending on Excel column formatting
        $urlKey = null;
        foreach($data as $k => $v) {
            if (strpos($k, 'url') !== false || strpos($k, 'website') !== false || strpos($k, 'domain') !== false) {
                $urlKey = $k;
                break;
            }
        }
        
        if (!$urlKey || empty($data[$urlKey])) continue;
        
        $url = trim($data[$urlKey]);
        
        // Ensure https:// is prefixed if missing, to have standard valid URLs if needed, but we'll trust their data first.
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }

        // Price mapping
        $priceKey = 'price';
        if (!isset($data['price'])) {
            // Find fuzzy
            foreach($data as $k => $v) {
                if (strpos($k, 'price') !== false || strpos($k, 'cost') !== false) {
                    $priceKey = $k; break;
                }
            }
        }
        
        $rawPrice = isset($data[$priceKey]) ? (float)$data[$priceKey] : 0.00;
        
        // Rule: Price which is 1 dollar will be 5 dollars. All are 1 dollar he said, so all will be 5.
        // We'll enforce 5 for all those. Wait, he said if price is 1 it becomes 5.
        $finalPrice = ($rawPrice == 1 || $rawPrice == 1.0) ? 5.00 : $rawPrice;
        if ($rawPrice == 0 && isset($data[$priceKey])) { // maybe it was text?
            $clean = preg_replace('/[^0-9.]/', '', $data[$priceKey]);
            if ($clean == '1') $finalPrice = 5.00;
            else $finalPrice = (float)$clean;
            
            // if excel had '$1' or something
        }
        
        // If everything is completely empty, default to 5 just in case, but let's stick to finalPrice
        if ($finalPrice <= 0) $finalPrice = 5.00; 

        $da = isset($data['da']) ? (int)$data['da'] : null;
        $dr = isset($data['dr']) ? (int)$data['dr'] : null;
        $trafficKey = 'traffic';
        if(!isset($data['traffic'])) {
             foreach($data as $k => $v) {
                if (strpos($k, 'traffic') !== false) {
                    $trafficKey = $k; break;
                }
            }
        }
        $traffic = isset($data[$trafficKey]) ? (int)preg_replace('/[^0-9]/', '', $data[$trafficKey]) : null;

        $wordCount = isset($data['word_count']) ? (int)$data['word_count'] : null;

        $niche = ['All Categories'];
        
        $insertData = [
            'niche' => $niche,
            'da' => $da,
            'dr' => $dr,
            'traffic' => $traffic,
            'price' => $finalPrice,
            'is_active' => true,
            'link_type' => 'DoFollow',
            'max_links_allowed' => 1,
            'is_sponsored' => false,
            'language' => 'English',
            'service_type' => 'Guest Post',
            'price_link_insertion' => 10.00,
            'word_count' => $wordCount,
            'description' => 'High-quality guest post placement with fast delivery. Supports All Categories.',
        ];

        // Clean out nulls so we don't overwrite with nulls if defaults exist
        $insertData = array_filter($insertData, function($value) {
            return !is_null($value);
        });

        GuestPostSite::updateOrCreate(
            ['url' => $url],
            $insertData
        );
        $successCount++;
    }
    DB::commit();
    echo "Successfully imported/updated {$successCount} sites.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

fclose($handle);

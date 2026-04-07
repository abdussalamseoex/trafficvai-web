const fs = require('fs');

const csvPath = 'C:/Users/User/Downloads/TrafficVai/storage/app/guest_posts.csv';
const lines = fs.readFileSync(csvPath, 'utf8').split('\n');
if(lines.length < 2) process.exit(0);

const headers = lines[0].split(',').map(h => h.trim().toLowerCase().replace(/ /g, '_'));
let insertData = [];

for(let i=1; i<lines.length; i++) {
    if(!lines[i].trim()) continue;
    
    // basic CSV parsing
    const row = lines[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
    if(row.length !== headers.length) continue;
    
    const data = {};
    headers.forEach((h, idx) => {
        data[h] = row[idx];
    });
    
    let urlKey = Object.keys(data).find(k => k.includes('url') || k.includes('website') || k.includes('domain'));
    if(!urlKey || !data[urlKey]) continue;
    
    let url = data[urlKey];
    if(!url.startsWith('http')) url = 'https://' + url;
    
    let basePrice = data.guest_post_price ? parseFloat(data.guest_post_price.replace(/[^0-9.]/g, '')) : 1;
    if(isNaN(basePrice) || basePrice === 0 || basePrice === 1) basePrice = 5;
    else if (basePrice === 1) basePrice = 5;
    
    // Just enforce 5 for all those that are $1 or default
    let linkPrice = 10;
    
    let wordCount = data['no._of_words'] || null;
    
    insertData.push({
        url: url,
        da: data.da ? parseInt(data.da) : null,
        dr: data.dr ? parseInt(data.dr) : null,
        traffic: data.monthly_traffic ? parseInt(data.monthly_traffic.replace(/[^0-9]/g, '')) : null,
        price: basePrice,
        price_link_insertion: linkPrice,
        niche: JSON.stringify(["All Categories"]),
        is_active: 1,
        link_type: 'DoFollow',
        max_links_allowed: 1,
        is_sponsored: 0,
        language: 'English',
        service_type: 'Guest Post',
        word_count: wordCount ? parseInt(wordCount) : null,
        description: 'High-quality guest post placement with fast delivery. Supports All Categories.',
        created_at: '2026-04-07 00:00:00',
        updated_at: '2026-04-07 00:00:00'
    });
}

function chunkArray(myArray, chunk_size){
    let index = 0;
    let arrayLength = myArray.length;
    let tempArray = [];
    for (index = 0; index < arrayLength; index += chunk_size) {
        myChunk = myArray.slice(index, index+chunk_size);
        tempArray.push(myChunk);
    }
    return tempArray;
}

const chunks = chunkArray(insertData, 50);

let migrationCode = `<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safe import checking duplicates manually in case of constraints
`;

chunks.forEach(chunk => {
    let phpArray = JSON.stringify(chunk, null, 4);
    // Convert JSON array syntax to PHP array syntax roughly
    phpArray = phpArray.replace(/\[/g, '[').replace(/\]/g, ']');
    
    migrationCode += `        $data = json_decode('${phpArray.replace(/'/g, "\\'")}', true);
        foreach($data as $row) {
            $exists = DB::table('guest_post_sites')->where('url', $row['url'])->first();
            if (!$exists) {
                DB::table('guest_post_sites')->insert($row);
            } else {
                DB::table('guest_post_sites')->where('url', $row['url'])->update([
                    'price' => $row['price'],
                    'price_link_insertion' => $row['price_link_insertion'],
                    'niche' => $row['niche'],
                    'description' => $row['description']
                ]);
            }
        }
`;
});

migrationCode += `    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
`;

fs.writeFileSync('C:/Users/User/Downloads/TrafficVai/database/migrations/2026_04_07_000000_import_guest_posts_from_excel.php', migrationCode);
console.log('Migration generated successfully.');

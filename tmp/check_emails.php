<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$templates = \App\Models\EmailTemplate::all();
echo "Total Templates: " . $templates->count() . "\n";
foreach ($templates as $t) {
    echo "Slug: " . $t->slug . " | Name: " . $t->name . "\n";
}

$settings = \App\Models\Setting::where('group', 'Email Settings')->get();
echo "\nEmail Settings:\n";
foreach ($settings as $s) {
    echo $s->key . ": " . $s->value . " (" . $s->type . ")\n";
}

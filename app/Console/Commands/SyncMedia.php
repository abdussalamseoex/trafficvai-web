<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing storage files to the media library';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = 'public';
        $files = Storage::disk($disk)->allFiles();
        
        $count = 0;
        foreach ($files as $file) {
            // Only include common image formats
            if (!Str::is(['*.jpg', '*.jpeg', '*.png', '*.gif', '*.svg', '*.webp', '*.ico'], strtolower($file))) {
                continue;
            }

            // Check if already exists
            if (Media::where('path', $file)->exists()) {
                continue;
            }

            $size = Storage::disk($disk)->size($file);
            $mime = Storage::disk($disk)->mimeType($file);
            $filename = basename($file);

            Media::create([
                'filename' => $filename,
                'path' => $file,
                'disk' => $disk,
                'size' => $size,
                'mime_type' => $mime,
                'title' => pathinfo($filename, PATHINFO_FILENAME),
            ]);

            $count++;
        }

        $this->info("Successfully synced {$count} new media files.");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'disk',
        'alt_text',
        'title',
        'description',
        'size',
        'mime_type'
    ];

    /**
     * Get the full URL for the media file.
     * 
     * @return string|null
     */
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get human readable size.
     * 
     * @return string
     */
    public function getHumanSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

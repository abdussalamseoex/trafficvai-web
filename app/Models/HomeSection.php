<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'content',
        'order',
        'status',
    ];

    protected $casts = [
        'content' => 'array',
    ];
}

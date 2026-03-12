<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['slug', 'name', 'subject', 'body', 'type', 'variables_hint'];

    protected $casts = [
        'variables_hint' => 'array',
    ];
}

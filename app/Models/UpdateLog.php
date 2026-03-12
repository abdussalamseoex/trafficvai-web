<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateLog extends Model
{
    protected $fillable = [
        'version',
        'changes',
        'status',
        'output',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];
}

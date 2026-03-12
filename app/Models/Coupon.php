<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'is_global',
        'service_id',
        'max_uses',
        'used_count',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'status' => 'boolean',
        'value' => 'decimal:2',
        'expires_at' => 'datetime',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function isValid()
    {
        if (!$this->status)
            return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses)
            return false;
        if ($this->expires_at !== null && $this->expires_at->isPast())
            return false;

        return true;
    }
}

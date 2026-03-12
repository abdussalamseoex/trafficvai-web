<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['service_id', 'name', 'description', 'price', 'features', 'turnaround_time_days', 'express_turnaround_time_days', 'emergency_fee'];

    protected function casts(): array
    {
        return [
            'features' => 'array',
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

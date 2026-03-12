<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequirement extends Model
{
    protected $fillable = ['service_id', 'name', 'type', 'is_required'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

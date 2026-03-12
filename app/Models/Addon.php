<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $fillable = ['service_id', 'name', 'description', 'price'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRequirement extends Model
{
    protected $fillable = ['order_id', 'service_requirement_id', 'value'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function serviceRequirement()
    {
        return $this->belongsTo(ServiceRequirement::class);
    }
}

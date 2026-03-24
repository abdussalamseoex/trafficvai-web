<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderExtension extends Model
{
    protected $fillable = ['order_id', 'admin_id', 'added_days', 'reason'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

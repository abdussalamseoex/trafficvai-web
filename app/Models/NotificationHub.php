<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationHub extends Model
{
    protected $table = 'notifications_hub';
    protected $fillable = ['user_id', 'title', 'message', 'type', 'is_read', 'link'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

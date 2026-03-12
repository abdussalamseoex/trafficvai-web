<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectMessage extends Model
{
    protected $fillable = ['client_id', 'sender_id', 'message', 'is_read', 'attachment_path', 'attachment_name'];

    public function client()
    {
        return $this->belongsTo(User::class , 'client_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class , 'sender_id');
    }
}

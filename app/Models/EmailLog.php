<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = ['recipient', 'subject', 'template_id', 'status', 'error_message', 'payload'];

    protected $casts = [
        'payload' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class , 'template_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailListContact extends Model
{
    protected $fillable = ['email_list_id', 'email'];

    public function emailList()
    {
        return $this->belongsTo(EmailList::class);
    }
}

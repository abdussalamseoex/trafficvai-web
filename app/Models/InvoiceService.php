<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceService extends Model
{
    protected $fillable = ['name', 'price', 'description'];
}

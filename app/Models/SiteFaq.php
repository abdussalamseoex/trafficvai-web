<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteFaq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'is_active', 'sort_order'];
}

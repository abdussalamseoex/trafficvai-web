<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'type',
        'order_id',
        'user_id',
        'invoice_number',
        'currency',
        'subtotal',
        'discount_type',
        'discount_value',
        'tax_rate',
        'tax_amount',
        'total',
        'status',
        'payment_method',
        'transaction_id',
        'payment_notes',
        'due_date',
        'notes',
        'terms',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}

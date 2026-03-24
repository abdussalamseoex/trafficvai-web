<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'package_id', 'guest_post_site_id',
        'status', 'total_amount', 'is_read_admin', 'report_file_path',
        'guest_post_url', 'guest_post_anchor', 'service_tier', 'article_body',
        'published_url', 'is_emergency', 'expected_delivery_date', 'expiry_date',
        'coupon_id', 'discount_amount', 'subtotal_amount',
        'payment_method', 'payment_status', 'transaction_id', 'payment_url',
        'wallet_amount', 'payment_notes', 'payment_proof', 'sender_number'
    ];

    protected function casts(): array
    {
        return [
            'expected_delivery_date' => 'datetime',
            'expiry_date' => 'datetime',
            'is_emergency' => 'boolean',
            'wallet_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function service()
    {
        return $this->hasOneThrough(Service::class , Package::class , 'id', 'id', 'package_id', 'service_id');
    }

    public function requirements()
    {
        return $this->hasMany(OrderRequirement::class);
    }

    public function guestPostSite()
    {
        return $this->belongsTo(GuestPostSite::class);
    }

    public function messages()
    {
        return $this->hasMany(OrderMessage::class);
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class , 'order_addons')->withPivot('price')->withTimestamps();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function extensions()
    {
        return $this->hasMany(OrderExtension::class)->latest();
    }

    /**
     * Accessor for full price (before wallet deduction)
     */
    public function getSubtotalDisplayAttribute()
    {
        if ($this->subtotal_amount > 0) {
            return (float)$this->subtotal_amount;
        }
        return (float)$this->total_amount + (float)$this->wallet_amount + (float)$this->discount_amount;
    }

    /**
     * Accessor for amount actually paid (Wallet + Gateway)
     */
    public function getTotalPaidDisplayAttribute()
    {
        return (float)$this->total_amount + (float)$this->wallet_amount;
    }
}

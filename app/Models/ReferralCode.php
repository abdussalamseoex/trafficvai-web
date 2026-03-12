<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReferralCode extends Model
{
    protected $fillable = ['user_id', 'code', 'clicks'];

    /**
     * The user who owns this referral code.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All referrals via this code.
     */
    public function referrals()
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    /**
     * Referrals that resulted in a signup.
     */
    public function signups()
    {
        return $this->hasMany(AffiliateReferral::class)->whereNotNull('referred_user_id');
    }

    /**
     * Referrals that resulted in an order.
     */
    public function orders()
    {
        return $this->hasMany(AffiliateReferral::class)->whereNotNull('order_id');
    }

    /**
     * Total commission earned.
     */
    public function getTotalCommissionAttribute(): float
    {
        return $this->referrals()->sum('commission_amount');
    }

    /**
     * Generate or retrieve a code for a given user.
     */
    public static function getOrCreateForUser(User $user): self
    {
        return static::firstOrCreate(
        ['user_id' => $user->id],
        ['code' => self::generateUniqueCode()]
        );
    }

    /**
     * Generate a unique short code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtolower(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}

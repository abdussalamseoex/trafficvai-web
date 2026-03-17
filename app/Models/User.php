<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'referred_by_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function messages()
    {
        return $this->hasMany(OrderMessage::class);
    }

    public function directMessages()
    {
        return $this->hasMany(DirectMessage::class , 'client_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is staff (any internal role)
     */
    public function isStaff()
    {
        return $this->is_admin || in_array($this->role, ['manager', 'seo_expert', 'writer']);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function referralCode()
    {
        return $this->hasOne(ReferralCode::class);
    }

    public function referralsGiven()
    {
        return $this->hasMany(AffiliateReferral::class , 'referred_user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function topupRequests()
    {
        return $this->hasMany(TopupRequest::class);
    }

    public function getBalanceAttribute()
    {
        return $this->wallet ? $this->wallet->balance : 0.00;
    }
}

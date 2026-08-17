<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpVerification extends Model
{
    use HasFactory;

    protected $table = 'otp_verifications';

    protected $fillable = [
        'uuid',
        'user_id',
        'phone',
        'email',
        'otp_code',
        'user_type',
        'purpose',
        'attempts',
        'expires_at',
        'verified_at',
        'ip_address'
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the User account this verification belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($verification) {
            if (empty($verification->uuid)) {
                $verification->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Check if the OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Mutator to automatically normalize phone numbers on set.
     */
    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = User::normalizePhone($value);
    }
}

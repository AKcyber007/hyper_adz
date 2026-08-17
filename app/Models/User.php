<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LocationPartnerProfile;
use App\Models\AdvertiserProfile;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasRoles, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'phone_verified_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
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
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the Location Partner Profile linked to this User.
     */
    public function partnerProfile()
    {
        return $this->hasOne(LocationPartnerProfile::class, 'user_id');
    }

    /**
     * Get the Advertiser Profile linked to this User.
     */
    public function advertiserProfile()
    {
        return $this->hasOne(AdvertiserProfile::class, 'user_id');
    }

    /**
     * Normalize Indian phone numbers to standard 10-digit format.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if (is_null($phone) || $phone === '') {
            return null;
        }

        // Remove all non-numeric characters
        $cleaned = preg_replace('/\D/', '', $phone);
        
        // If it starts with 91 and has 12 digits, strip the 91
        if (strlen($cleaned) === 12 && str_starts_with($cleaned, '91')) {
            return substr($cleaned, 2);
        }
        
        // If it starts with 0 and has 11 digits, strip the 0
        if (strlen($cleaned) === 11 && str_starts_with($cleaned, '0')) {
            return substr($cleaned, 1);
        }
        
        return $cleaned;
    }

    /**
     * Mutator to automatically normalize phone numbers on set.
     */
    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = self::normalizePhone($value);
    }
}

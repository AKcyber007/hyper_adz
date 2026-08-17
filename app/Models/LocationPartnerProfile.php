<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LocationPartnerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'partner_code',
        'user_id',
        'lead_id',
        'company_name',
        'contact_person',
        'designation',
        'phone',
        'email',
        'website',
        'gst_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'postal_code',
        'logo_path',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'last_login_at',
        'login_count',
    ];

    protected $casts = [
        'approved_at'   => 'datetime',
        'last_login_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($profile) {
            if (empty($profile->uuid)) {
                $profile->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get the locations owned by this partner.
     */
    public function locations()
    {
        return $this->hasMany(Location::class, 'location_partner_id');
    }

    /**
     * Get the lead enquiry this profile was converted from.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * Get the user account linked to this partner (nullable).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin user who approved this partner.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Dynamic Attribute: Get total count of locations.
     */
    public function getLocationsCountAttribute(): int
    {
        return $this->locations()->count();
    }

    /**
     * Dynamic Attribute: Get total count of screens under owned locations.
     */
    public function getScreensCountAttribute(): int
    {
        $locationIds = $this->locations()->pluck('id');
        return Screen::whereIn('location_id', $locationIds)->count();
    }

    /**
     * Mutator to automatically normalize phone numbers on set.
     */
    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = User::normalizePhone($value);
    }

    /**
     * Dynamic Attribute: Get total count of daily impressions across all screens.
     */
    public function getTotalImpressionsAttribute(): int
    {
        $locationIds = $this->locations()->pluck('id');
        return (int) Screen::whereIn('location_id', $locationIds)->sum('daily_impressions');
    }
}

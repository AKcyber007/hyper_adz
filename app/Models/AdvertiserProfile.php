<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AdvertiserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'advertiser_code',
        'user_id',
        'lead_id',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'website',
        'gst_number',
        'logo_path',
        'industry_id',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'postal_code',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'last_login_at',
        'login_count',
    ];

    protected $casts = [
        'approved_at'  => 'datetime',
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
     * Get the industry sector this advertiser belongs to.
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    /**
     * Get the lead enquiry this profile was converted from.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * Get the user account linked to this advertiser (nullable).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin user who approved this advertiser.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Mutator to automatically normalize phone numbers on set.
     */
    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = User::normalizePhone($value);
    }
}

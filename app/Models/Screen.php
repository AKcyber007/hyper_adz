<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Screen extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'screen_code',
        'screen_identifier',
        'location_id',
        'name',
        'slug',
        'screen_type_id',
        'description',
        'orientation',
        'screen_width',
        'screen_height',
        'resolution',
        'operating_hours',
        'daily_impressions',
        'status',
        'availability_status',
        'supported_formats',
        'max_video_duration',
        'created_by',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rejected_by' => 'integer',
        'rejected_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($screen) {
            // Auto generate UUID
            if (empty($screen->uuid)) {
                $screen->uuid = (string) Str::uuid();
            }

            // Auto generate Slug
            if (empty($screen->slug)) {
                $screen->slug = Str::slug($screen->name);
            }

            // Generate temporary unique screen code on creation
            if (empty($screen->screen_code)) {
                $screen->screen_code = 'SCR-' . strtoupper(Str::random(6));
            }

            // Assign creator
            if (empty($screen->created_by) && auth()->check()) {
                $screen->created_by = auth()->id();
            }
        });

        static::created(function ($screen) {
            // Refine unique sequential code based on ID
            if (str_starts_with($screen->screen_code, 'SCR-')) {
                $screen->screen_code = 'SCR-' . str_pad($screen->id, 5, '0', STR_PAD_LEFT);
                $screen->saveQuietly();
            }
        });

        static::updating(function ($screen) {
            // Regenerate slug if name changed
            if ($screen->isDirty('name')) {
                $screen->slug = Str::slug($screen->name);
            }
        });
    }

    /**
     * Get the location that this screen belongs to.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the screen type.
     */
    public function type()
    {
        return $this->belongsTo(ScreenType::class, 'screen_type_id');
    }

    /**
     * Get the location partner who owns this screen (inherited from its parent location).
     */
    public function partner()
    {
        return $this->hasOneThrough(
            LocationPartnerProfile::class,
            Location::class,
            'id', // Foreign key on locations table (parent location's primary key)
            'id', // Foreign key on location_partner_profiles table (partner profile's primary key)
            'location_id', // Local key on screens table
            'location_partner_id' // Local key on locations table
        );
    }

    /**
     * Get the admin user who rejected this screen.
     */
    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the images for the screen.
     */
    public function images()
    {
        return $this->hasMany(ScreenImage::class, 'screen_id');
    }

    /**
     * Get the primary image of this screen.
     */
    public function primaryImage()
    {
        return $this->hasOne(ScreenImage::class, 'screen_id')->where('is_primary', true);
    }

    /**
     * Get the primary image URL.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return $primary->image_url;
        }

        $first = $this->images()->orderBy('display_order')->first();
        if ($first) {
            return $first->image_url;
        }

        return asset('assets/images/venue-placeholder.jpg');
    }

    /*
     |--------------------------------------------------------------------------
     | FUTURE COMPATIBILITY / ARCHITECTURAL NOTES
     |--------------------------------------------------------------------------
     |
     | Below are comments outlining relationships for future phases:
     |
     | 1. Campaigns relationship:
     |    A screen can belong to multiple campaigns through a pivot table (e.g. campaign_screens).
     |    Public mapping:
     |    public function campaigns() {
     |        return $this->belongsToMany(Campaign::class, 'campaign_screens');
     |    }
     |
     | 2. Bookings relationship:
     |    A screen can have multiple bookings over time.
     |    Public mapping:
     |    public function bookings() {
     |        return $this->hasMany(Booking::class, 'screen_id');
     |    }
     */
}

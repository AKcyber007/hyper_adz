<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    // Status Constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_MAINTENANCE = 'maintenance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'location_code',
        'uuid',
        'name',
        'business_name',
        'slug',
        'category_id',
        'location_partner_id',
        'address',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'description',
        'daily_footfall',
        'audience_count',
        'repeats_per_day',
        'audience_type',
        'operating_hours',
        'operating_days',
        'opening_time',
        'closing_time',
        'screen_size',
        'screen_orientation',
        'video_supported',
        'audio_supported',
        'nearby_places',
        'status',
        'created_by',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'price_per_day',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'daily_footfall' => 'integer',
        'category_id' => 'integer',
        'location_partner_id' => 'integer',
        'created_by' => 'integer',
        'rejected_by' => 'integer',
        'rejected_at' => 'datetime',
        'price_per_day' => 'decimal:2',
        'audience_type' => 'array',
        'operating_days' => 'array',
        'video_supported' => 'boolean',
        'audio_supported' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($location) {
            // Auto generate UUID
            if (empty($location->uuid)) {
                $location->uuid = (string) Str::uuid();
            }

            // Auto generate Slug
            if (empty($location->slug)) {
                $baseSlug = Str::slug($location->name);
                // Ensure slug is unique
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->withTrashed()->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }
                $location->slug = $slug;
            }

            // Generate temporary unique location code on creation
            if (empty($location->location_code)) {
                $location->location_code = 'LOC-' . strtoupper(Str::random(6));
            }

            // Assign creator
            if (empty($location->created_by) && auth()->check()) {
                $location->created_by = auth()->id();
            }
        });

        static::created(function ($location) {
            // Refine unique sequential code based on ID
            if (str_starts_with($location->location_code, 'LOC-')) {
                $location->location_code = 'LOC-' . str_pad($location->id, 5, '0', STR_PAD_LEFT);
                $location->saveQuietly();
            }
        });

        static::updating(function ($location) {
            // Regenerate slug if name changed
            if ($location->isDirty('name')) {
                $baseSlug = Str::slug($location->name);
                // Ensure slug is unique
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $location->id)->withTrashed()->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }
                $location->slug = $slug;
            }
        });
    }

    /**
     * Get the category that this location belongs to.
     */
    public function category()
    {
        return $this->belongsTo(LocationCategory::class, 'category_id');
    }

    /**
     * Get the location partner who owns this location.
     */
    public function partner()
    {
        return $this->belongsTo(LocationPartnerProfile::class, 'location_partner_id');
    }

    /**
     * Alias for partner() — used in views for readability.
     */
    public function locationPartner()
    {
        return $this->belongsTo(LocationPartnerProfile::class, 'location_partner_id');
    }

    /**
     * Get the admin user who rejected this location.
     */
    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the images uploaded for this location.
     */
    public function images()
    {
        return $this->hasMany(LocationImage::class);
    }

    /**
     * Get the primary image for the location (if exists), or default fallback.
     */
    public function getPrimaryImageAttribute(): ?LocationImage
    {
        return $this->images()->where('is_primary', true)->first() 
            ?? $this->images()->orderBy('display_order')->first();
    }

    /**
     * Get the screens belonging to this location.
     */
    public function screens()
    {
        return $this->hasMany(Screen::class);
    }

    /**
     * Get the reviews for this location.
     */
    public function reviews()
    {
        return $this->hasMany(LocationReview::class);
    }

    /**
     * Get the favorites for this location.
     */
    public function favorites()
    {
        return $this->hasMany(LocationFavorite::class);
    }

    /**
     * Get the average rating of the location based on valid reviews.
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->avg('rating') ?: 0;
    }

    /**
     * Get the total number of reviews for the location.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}

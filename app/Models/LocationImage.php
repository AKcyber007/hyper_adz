<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LocationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'image_path',
        'display_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'display_order' => 'integer',
        'location_id' => 'integer',
    ];

    /**
     * Get the location that owns the image.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the URL to the image path.
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }
}

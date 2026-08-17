<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenImage extends Model
{
    use HasFactory;

    protected $fillable = ['screen_id', 'image_path', 'display_order', 'is_primary'];

    /**
     * Get the screen that owns the image.
     */
    public function screen()
    {
        return $this->belongsTo(Screen::class, 'screen_id');
    }

    /**
     * Get the absolute URL of the image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('assets/images/venue-placeholder.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }
}

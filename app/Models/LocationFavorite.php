<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'user_id',
    ];

    /**
     * Get the location that was favorited.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the user who favorited the location.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

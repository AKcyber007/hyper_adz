<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'user_id',
        'rating',
        'review',
    ];

    /**
     * Get the location that this review belongs to.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

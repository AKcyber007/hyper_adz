<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'status',
    ];

    /**
     * Get the locations belonging to this category.
     */
    public function locations()
    {
        return $this->hasMany(Location::class, 'category_id');
    }
}

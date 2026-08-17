<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Get the advertiser profiles registered under this industry category.
     */
    public function advertisers()
    {
        return $this->hasMany(AdvertiserProfile::class, 'industry_id');
    }
}

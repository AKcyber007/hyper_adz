<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationUpdateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'partner_id',
        'request_type',
        'current_value',
        'requested_value',
        'notes',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the location associated with this update request.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the location partner profile associated with this update request.
     */
    public function partner()
    {
        return $this->belongsTo(LocationPartnerProfile::class, 'partner_id');
    }

    /**
     * Get the admin user who reviewed this request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

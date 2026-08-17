<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'action',
        'performed_by',
        'remarks',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the campaign associated with the log entry.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_code',
        'advertiser_id',
        'campaign_name',
        'description',
        'campaign_type',
        'industry_id',
        'start_date',
        'end_date',
        'budget',
        'creative_path',
        'creative_name',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
        'updated_by',
        'payment_status',
        'payment_amount',
        'payment_due_date',
        'payment_confirmed_at',
        'payment_confirmed_by',
        'report_path',
        'report_name',
        'report_uploaded_at',
        'rejection_type',
        'creative_review_notes',
        'zoho_payment_link_id',
        'zoho_payment_url',
        'zoho_payment_id',
        'payment_paid_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_due_date' => 'date',
        'approved_at' => 'datetime',
        'payment_confirmed_at' => 'datetime',
        'report_uploaded_at' => 'datetime',
        'budget' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Get the advertiser profile that owns this campaign.
     */
    public function advertiser()
    {
        return $this->belongsTo(AdvertiserProfile::class, 'advertiser_id');
    }

    /**
     * Get the industry of the campaign.
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    /**
     * Get the locations assigned to this campaign.
     */
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'campaign_location');
    }

    /**
     * Get the activity logs for the campaign.
     */
    public function activityLogs()
    {
        return $this->hasMany(CampaignActivityLog::class, 'campaign_id');
    }

    /**
     * Get the user who approved this campaign.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

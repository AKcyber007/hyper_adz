<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Campaign;

class CampaignPaidNotification extends Notification
{
    use Queueable;

    protected Campaign $campaign;

    /**
     * Create a new notification instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Only database for now as per requirements
    }

    /**
     * Get the array representation of the notification for the database.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'campaign_id' => $this->campaign->id,
            'campaign_name' => $this->campaign->campaign_name,
            'advertiser_name' => $this->campaign->advertiser->company_name ?? 'Unknown',
            'amount' => $this->campaign->payment_amount,
            'payment_id' => $this->campaign->zoho_payment_id,
            'payment_date' => $this->campaign->payment_paid_at,
            'message' => 'Campaign payment received for ' . $this->campaign->campaign_name,
        ];
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use Carbon\Carbon;

class ProcessCampaignStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:process-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automated campaign status transitions based on dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // 1. Payment Expiry Check
        // Payment Pending and due date < today -> Rejected (Payment Expired)
        $expiredCampaigns = Campaign::where('status', 'Payment Pending')
            ->whereNotNull('payment_due_date')
            ->whereDate('payment_due_date', '<', $today)
            ->get();

        foreach ($expiredCampaigns as $campaign) {
            $campaign->update([
                'status' => 'Rejected (Payment Expired)',
                'payment_status' => 'Expired',
                'rejection_type' => 'payment_expired'
            ]);
            $campaign->activityLogs()->create([
                'action' => 'Payment Expired',
                'performed_by' => 'System Scheduler',
                'remarks' => 'Campaign rejected due to non-payment by due date.'
            ]);
            $this->info("Campaign {$campaign->campaign_code} payment expired.");
        }

        // 2. Scheduled -> Running
        // Scheduled and start date <= today -> Running
        $startingCampaigns = Campaign::where('status', 'Scheduled')
            ->whereDate('start_date', '<=', $today)
            ->get();

        foreach ($startingCampaigns as $campaign) {
            $campaign->update([
                'status' => 'Running'
            ]);
            $campaign->activityLogs()->create([
                'action' => 'Started Running',
                'performed_by' => 'System Scheduler',
                'remarks' => 'Campaign automatically started as scheduled.'
            ]);
            $this->info("Campaign {$campaign->campaign_code} started running.");
        }

        // 3. Running -> Completed
        // Running and end date < today -> Completed
        $completingCampaigns = Campaign::where('status', 'Running')
            ->whereDate('end_date', '<', $today)
            ->get();

        foreach ($completingCampaigns as $campaign) {
            $campaign->update([
                'status' => 'Completed'
            ]);
            $campaign->activityLogs()->create([
                'action' => 'Completed',
                'performed_by' => 'System Scheduler',
                'remarks' => 'Campaign ended automatically upon reaching its end date.'
            ]);
            $this->info("Campaign {$campaign->campaign_code} completed.");
        }

        $this->info('Campaign statuses processed successfully.');
    }
}


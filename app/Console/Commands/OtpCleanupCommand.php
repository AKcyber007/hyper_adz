<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OtpService;

class OtpCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup {--days=7 : Retention days for verified OTPs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTPs and verified OTPs older than X days';

    /**
     * Execute the console command.
     */
    public function handle(OtpService $otpService): int
    {
        $days = (int) $this->option('days');
        $this->info("Starting OTP cleanup (verified retention limit: {$days} days)...");

        $deletedCount = $otpService->cleanupVerifications($days);

        $this->info("OTP cleanup finished. Deleted {$deletedCount} stale verification records.");
        
        return Command::SUCCESS;
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\OtpVerification;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Carbon\Carbon;

class OtpRepository implements OtpRepositoryInterface
{
    /**
     * Store a new OTP verification record.
     */
    public function createOtp(array $data): OtpVerification
    {
        return OtpVerification::create($data);
    }

    /**
     * Get the latest active/valid OTP record for a phone number and user type.
     */
    public function getLatestValidOtp(string $phone, string $userType): ?OtpVerification
    {
        return OtpVerification::where('phone', $phone)
            ->where('user_type', $userType)
            ->whereNull('verified_at')
            ->where('attempts', '<', 5)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Increment validation attempts count for a verification record.
     */
    public function incrementAttempts(OtpVerification $verification): void
    {
        $verification->increment('attempts');
        
        // If it reaches the maximum 5 attempts, expire the OTP immediately
        if ($verification->attempts >= 5) {
            $verification->update([
                'expires_at' => Carbon::now()
            ]);
        }
    }

    /**
     * Mark an OTP verification record as verified.
     */
    public function markAsVerified(OtpVerification $verification): void
    {
        $verification->update([
            'verified_at' => Carbon::now()
        ]);
    }

    /**
     * Delete expired verification records and verified ones older than X days.
     */
    public function cleanupStaleOtps(int $verifiedRetentionDays = 7): int
    {
        // 1. Delete all expired OTPs
        $expiredCount = OtpVerification::where('expires_at', '<', Carbon::now())
            ->delete();

        // 2. Delete verified OTPs older than retention limit
        $retentionDate = Carbon::now()->subDays($verifiedRetentionDays);
        $verifiedCount = OtpVerification::whereNotNull('verified_at')
            ->where('verified_at', '<', $retentionDate)
            ->delete();

        return $expiredCount + $verifiedCount;
    }
}

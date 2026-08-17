<?php

namespace App\Repositories\Contracts;

use App\Models\OtpVerification;

interface OtpRepositoryInterface
{
    /**
     * Store a new OTP verification record.
     */
    public function createOtp(array $data): OtpVerification;

    /**
     * Get the latest active/valid OTP record for a phone number and user type.
     */
    public function getLatestValidOtp(string $phone, string $userType): ?OtpVerification;

    /**
     * Increment validation attempts count for a verification record.
     */
    public function incrementAttempts(OtpVerification $verification): void;

    /**
     * Mark an OTP verification record as verified.
     */
    public function markAsVerified(OtpVerification $verification): void;

    /**
     * Delete expired verification records and verified ones older than X days.
     */
    public function cleanupStaleOtps(int $verifiedRetentionDays = 7): int;
}

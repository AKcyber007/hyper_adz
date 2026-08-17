<?php

namespace App\Services\Otp;

use App\Mail\LoginOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailOtpProvider implements OtpProviderInterface
{
    /**
     * Send OTP to the user's email address.
     */
    public function sendOtp(string $phone, string $email, string $otpCode): bool
    {
        try {
            Mail::to($email)->send(new LoginOtpMail($otpCode));
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify the OTP (relies on service-level database checking).
     */
    public function verifyOtp(string $phone, string $otpCode): bool
    {
        return true;
    }
}

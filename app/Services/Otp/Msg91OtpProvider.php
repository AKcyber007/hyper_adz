<?php

namespace App\Services\Otp;

use Illuminate\Support\Facades\Log;

class Msg91OtpProvider implements OtpProviderInterface
{
    /**
     * Placeholder method returning false with warning log alerts.
     */
    public function sendOtp(string $phone, string $email, string $otpCode): bool
    {
        Log::warning("MSG91 OTP Provider is not fully implemented yet. Attempted to send OTP to {$phone}.");
        return false;
    }

    /**
     * Placeholder method returning false with warning log alerts.
     */
    public function verifyOtp(string $phone, string $otpCode): bool
    {
        Log::warning("MSG91 OTP Provider is not fully implemented yet. Attempted to verify OTP for {$phone}.");
        return false;
    }
}

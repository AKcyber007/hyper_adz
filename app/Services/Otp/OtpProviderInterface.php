<?php

namespace App\Services\Otp;

interface OtpProviderInterface
{
    /**
     * Send OTP to the user.
     *
     * @param string $phone
     * @param string $email
     * @param string $otpCode
     * @return bool
     */
    public function sendOtp(string $phone, string $email, string $otpCode): bool;

    /**
     * Verify the OTP.
     *
     * @param string $phone
     * @param string $otpCode
     * @return bool
     */
    public function verifyOtp(string $phone, string $otpCode): bool;
}

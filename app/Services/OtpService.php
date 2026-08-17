<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\ActivityLog;
use App\Repositories\Contracts\OtpRepositoryInterface;
use App\Services\Otp\OtpProviderInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OtpService
{
    protected OtpRepositoryInterface $otpRepository;
    protected OtpProviderInterface $otpProvider;

    public function __construct(
        OtpRepositoryInterface $otpRepository,
        OtpProviderInterface $otpProvider
    ) {
        $this->otpRepository = $otpRepository;
        $this->otpProvider = $otpProvider;
    }

    /**
     * Generate and dispatch a new OTP verification code with rate-limit protections.
     */
    public function requestOtp(?string $phone, string $email, string $userType, string $ipAddress): OtpVerification
    {
        $phone = $phone ? \App\Models\User::normalizePhone($phone) : null;
        // 1. Match User by email first, phone second
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user && !empty($phone)) {
            $user = \App\Models\User::where('phone', $phone)->first();
        }

        // Self-heal: If User does not exist, but profile exists, create the User
        if (!$user) {
            if ($userType === 'location_partner') {
                $profile = \App\Models\LocationPartnerProfile::where('email', $email)
                    ->orWhere('phone', $phone)
                    ->first();
                if ($profile) {
                    $user = \App\Models\User::create([
                        'name'   => $profile->company_name ?: $profile->contact_person,
                        'email'  => $profile->email,
                        'phone'  => $profile->phone ? \App\Models\User::normalizePhone($profile->phone) : null,
                        'status' => 'active',
                    ]);
                    $profile->update(['user_id' => $user->id]);
                    
                    $roleRecord = \Spatie\Permission\Models\Role::where('name', $userType)->first();
                    if (!$roleRecord) {
                        $roleRecord = \Spatie\Permission\Models\Role::create(['name' => $userType, 'guard_name' => 'web']);
                    }
                    $user->assignRole($roleRecord->name);
                }
            } else if ($userType === 'advertiser') {
                $profile = \App\Models\AdvertiserProfile::where('email', $email)
                    ->orWhere('phone', $phone)
                    ->first();
                if ($profile) {
                    $user = \App\Models\User::create([
                        'name'   => $profile->company_name ?: $profile->contact_person,
                        'email'  => $profile->email,
                        'phone'  => $profile->phone ? \App\Models\User::normalizePhone($profile->phone) : null,
                        'status' => 'active',
                    ]);
                    $profile->update(['user_id' => $user->id]);
                    
                    $roleRecord = \Spatie\Permission\Models\Role::where('name', $userType)->first();
                    if (!$roleRecord) {
                        $roleRecord = \Spatie\Permission\Models\Role::create(['name' => $userType, 'guard_name' => 'web']);
                    }
                    $user->assignRole($roleRecord->name);
                }
            }
        }

        if (!$user) {
            throw new \Exception("Authentication failed. No user registered with these details.");
        }

        // Ensure role is assigned only if user has the profile
        $roleRecord = \Spatie\Permission\Models\Role::where('name', $userType)->first();
        if (!$roleRecord) {
            $roleRecord = \Spatie\Permission\Models\Role::create(['name' => $userType, 'guard_name' => 'web']);
        }
        $actualRoleName = $roleRecord->name;

        if (!$user->hasRole($actualRoleName)) {
            $hasProfile = false;
            if ($userType === 'location_partner' && $user->partnerProfile()->exists()) {
                $hasProfile = true;
            } else if ($userType === 'advertiser' && $user->advertiserProfile()->exists()) {
                $hasProfile = true;
            }

            if ($hasProfile) {
                $user->assignRole($actualRoleName);
            }
        }

        // Verify status
        if ($user->status !== 'active') {
            throw new \Exception("Authentication failed. Your account is " . $user->status . ".");
        }

        // Verify role
        if (!$user->hasRole($actualRoleName)) {
            throw new \Exception("Authentication failed. Unauthorized role access.");
        }

        // Use correct details from User record
        $email = $user->email;
        $phone = $user->phone ? \App\Models\User::normalizePhone($user->phone) : null;

        // 2. Enforce Rate Limiting per Phone or Email
        if ($phone) {
            $limitKey = 'otp-request-phone:' . $phone;
        } else {
            $limitKey = 'otp-request-email:' . $email;
        }
        
        if (RateLimiter::tooManyAttempts($limitKey, 8)) {
            $seconds = RateLimiter::availableIn($limitKey);
            $minutes = ceil($seconds / 60);
            throw new \Exception("Too many OTP requests. Please try again in {$minutes} minutes.");
        }

        // 3. Enforce Rate Limiting per IP (10 OTP requests per hour)
        $ipLimitKey = 'otp-request-ip:' . $ipAddress;
        if (RateLimiter::tooManyAttempts($ipLimitKey, 10)) {
            $seconds = RateLimiter::availableIn($ipLimitKey);
            $minutes = ceil($seconds / 60);
            throw new \Exception("Too many OTP requests from this IP. Please try again in {$minutes} minutes.");
        }

        // Hit limiters
        RateLimiter::hit($limitKey, 300);
        RateLimiter::hit($ipLimitKey, 3600);

        // 4. Generate 6-digit code
        $plainOtp = (string) random_int(100000, 999999);

        // 5. Save hashed record to db with user_id!
        $verification = $this->otpRepository->createOtp([
            'user_id'     => $user->id,
            'phone'       => $phone ?? '',
            'email'       => $email,
            'otp_code'    => Hash::make($plainOtp),
            'user_type'   => $userType,
            'purpose'     => 'login',
            'attempts'    => 0,
            'expires_at'  => Carbon::now()->addMinutes(10),
            'ip_address'  => $ipAddress
        ]);

        // 6. Dispatch via active provider
        $sent = $this->otpProvider->sendOtp($phone ?? '', $email, $plainOtp);
        if (!$sent) {
            throw new \Exception("Failed to deliver verification code. Please try again later.");
        }

        // 7. Log activity
        $identifier = $phone ?? $email;
        $this->logActivity('otp_requested', $verification, "OTP Requested for {$userType}: {$identifier}");

        return $verification;
    }

    /**
     * Validate verification OTP code and expiration thresholds.
     */
    public function verifyOtp(?string $phone, ?string $email, string $userType, string $otpCode, string $ipAddress): bool
    {
        $phone = $phone ? \App\Models\User::normalizePhone($phone) : null;
        $user = null;
        
        if ($phone) {
            $user = \App\Models\User::where('phone', $phone)->first();
        } else if ($email) {
            $user = \App\Models\User::where('email', $email)->first();
        }

        // Query verification record linked to user_id or matching phone/email directly
        $verification = OtpVerification::where(function($query) use ($user, $phone, $email) {
                if ($user) {
                    $query->where('user_id', $user->id);
                    if ($phone) $query->orWhere('phone', $phone);
                    if ($email) $query->orWhere('email', $email);
                } else {
                    if ($phone) $query->where('phone', $phone);
                    else if ($email) $query->where('email', $email);
                }
            })
            ->where('user_type', $userType)
            ->whereNull('verified_at')
            ->where('attempts', '<', 5)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();

        $identifier = $phone ?? $email ?? 'unknown';

        if (!$verification) {
            $this->logStaticActivity('otp_failed', "OTP verification failed (no active OTP) for {$userType}: {$identifier}", $ipAddress);
            throw new \Exception("Invalid or expired OTP code.");
        }

        // Guard against locked verification
        if ($verification->attempts >= 5) {
            $this->logActivity('otp_failed', $verification, "OTP verification failed (attempts exhausted) for {$userType}: {$identifier}");
            throw new \Exception("This verification code has been locked due to too many failed attempts. Please request a new one.");
        }

        // Perform code verify match checks
        if (!Hash::check($otpCode, $verification->otp_code)) {
            // Increment retry count
            $this->otpRepository->incrementAttempts($verification);
            $this->logActivity('otp_failed', $verification, "OTP verification failed (incorrect code) for {$userType}: {$identifier}");
            
            $remaining = 5 - ($verification->attempts + 1);
            if ($remaining <= 0) {
                throw new \Exception("Incorrect code. This OTP has been locked. Please request a new code.");
            }
            throw new \Exception("Incorrect verification code. {$remaining} attempts remaining.");
        }

        // Mark OTP as verified
        $this->otpRepository->markAsVerified($verification);
        $this->logActivity('otp_verified', $verification, "OTP verified successfully for {$userType}: {$identifier}");

        return true;
    }

    /**
     * Clean up stale/expired verifications.
     */
    public function cleanupVerifications(int $retentionDays = 7): int
    {
        return $this->otpRepository->cleanupStaleOtps($retentionDays);
    }

    /**
     * Log actions inside the central activity_logs table for instantiated records.
     */
    protected function logActivity(string $action, OtpVerification $verification, string $description): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id(), // null if guest
            'action'      => $action,
            'entity_type' => OtpVerification::class,
            'entity_id'   => $verification->id,
            'description' => $description,
        ]);
    }

    /**
     * Log actions when no entity model is available.
     */
    protected function logStaticActivity(string $action, string $description, string $ipAddress): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'entity_type' => OtpVerification::class,
            'entity_id'   => null,
            'description' => $description . " (IP: {$ipAddress})",
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AdvertiserProfile;
use App\Models\LocationPartnerProfile;
use App\Models\OtpVerification;
use App\Services\OtpService;
use App\Services\AdvertiserService;
use App\Services\LocationPartnerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected OtpService $otpService;
    protected AdvertiserService $advertiserService;
    protected LocationPartnerService $partnerService;

    protected function setUp(): void
    {
        parent::setUp();

        // Bind services
        $this->otpService = app(OtpService::class);
        $this->advertiserService = app(AdvertiserService::class);
        $this->partnerService = app(LocationPartnerService::class);

        // Seed roles
        Role::firstOrCreate(['name' => 'advertiser']);
        Role::firstOrCreate(['name' => 'location_partner']);
        Role::firstOrCreate(['name' => 'Admin']);
    }

    /**
     * Test email and phone unique constraints at users table level.
     */
    public function test_user_email_and_phone_must_be_unique_for_active_users(): void
    {
        User::create([
            'name' => 'User One',
            'email' => 'unique@example.com',
            'phone' => '1234567890',
            'status' => 'active',
        ]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        User::create([
            'name' => 'User Two',
            'email' => 'unique@example.com',
            'phone' => '1234567890',
            'status' => 'active',
        ]);
    }

    /**
     * Test soft deleted users are ignored by uniqueness checks.
     */
    public function test_soft_deleted_user_uniqueness_ignored(): void
    {
        $user1 = User::create([
            'name' => 'Soft Deleted User',
            'email' => 'softdelete@example.com',
            'phone' => '9876543210',
            'status' => 'active',
        ]);

        // Soft delete user
        $user1->delete();

        // Attempting to create new user with same email and phone should succeed
        $user2 = User::create([
            'name' => 'New Active User',
            'email' => 'softdelete@example.com',
            'phone' => '1231231234',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'email' => 'softdelete@example.com',
        ]);
    }

    /**
     * Test restoring a soft-deleted user fails if email is taken.
     */
    public function test_restoring_soft_deleted_user_fails_if_email_taken(): void
    {
        $user1 = User::create([
            'name' => 'Original User',
            'email' => 'duplicate@example.com',
            'phone' => '1111111111',
            'status' => 'active',
        ]);
        
        $user1->delete();
        
        User::create([
            'name' => 'New Active User',
            'email' => 'duplicate@example.com',
            'phone' => '2222222222',
            'status' => 'active',
        ]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        $user1->restore();
    }

    /**
     * Test single user account can hold both roles and profiles simultaneously.
     */
    public function test_single_user_can_be_both_advertiser_and_location_partner(): void
    {
        // 1. Create a Location Partner
        $partnerProfile = $this->partnerService->createPartner([
            'company_name' => 'Unified Company',
            'contact_person' => 'Akil',
            'email' => 'akil@example.com',
            'phone' => '9994206375',
            'status' => 'active',
        ]);

        $user = $partnerProfile->user;
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('location_partner'));
        $this->assertFalse($user->hasRole('advertiser'));

        // 2. Register same person as Advertiser (should reuse the User)
        $advertiserProfile = $this->advertiserService->createAdvertiser([
            'company_name' => 'Unified Company',
            'contact_person' => 'Akil',
            'email' => 'akil@example.com',
            'phone' => '9994206375',
            'status' => 'active',
            'industry_id' => \App\Models\Industry::create(['name' => 'Retail', 'status' => 'active'])->id,
        ]);

        $user = $user->fresh();
        $this->assertTrue($user->hasRole('location_partner'));
        $this->assertTrue($user->hasRole('advertiser'));
        $this->assertEquals($partnerProfile->user_id, $advertiserProfile->user_id);
    }

    /**
     * Test OTP login request and role verification.
     */
    public function test_otp_login_request_and_validation(): void
    {
        // Create user with location partner role and active status
        $user = User::create([
            'name' => 'Partner Owner',
            'email' => 'partner@example.com',
            'phone' => '1112223333',
            'status' => 'active',
        ]);
        $user->assignRole('location_partner');

        LocationPartnerProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Partner Venue',
            'contact_person' => 'Owner',
            'email' => 'partner@example.com',
            'phone' => '1112223333',
            'status' => 'active',
            'partner_code' => 'LP-00001',
        ]);

        // Request OTP
        $otp = $this->otpService->requestOtp('1112223333', 'partner@example.com', 'location_partner', '127.0.0.1');

        $this->assertDatabaseHas('otp_verifications', [
            'user_id' => $user->id,
            'user_type' => 'location_partner',
        ]);

        // Try requesting OTP as advertiser (should throw role mismatch exception)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Authentication failed. Unauthorized role access.');
        $this->otpService->requestOtp('1112223333', 'partner@example.com', 'advertiser', '127.0.0.1');
    }

    /**
     * Test blocked OTP request for suspended users.
     */
    public function test_suspended_user_otp_request_blocked(): void
    {
        $user = User::create([
            'name' => 'Suspended Owner',
            'email' => 'suspended@example.com',
            'phone' => '5556667777',
            'status' => 'suspended',
        ]);
        $user->assignRole('location_partner');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Authentication failed. Your account is suspended.');

        $this->otpService->requestOtp('5556667777', 'suspended@example.com', 'location_partner', '127.0.0.1');
    }
}

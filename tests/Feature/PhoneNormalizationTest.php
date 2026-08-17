<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AdvertiserProfile;
use App\Models\LocationPartnerProfile;
use App\Models\Lead;
use App\Models\OtpVerification;
use App\Services\OtpService;
use App\Services\AdvertiserService;
use App\Services\LocationPartnerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PhoneNormalizationTest extends TestCase
{
    use RefreshDatabase;

    protected OtpService $otpService;
    protected AdvertiserService $advertiserService;
    protected LocationPartnerService $partnerService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->otpService = app(OtpService::class);
        $this->advertiserService = app(AdvertiserService::class);
        $this->partnerService = app(LocationPartnerService::class);

        Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'location_partner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    }

    /**
     * Test User::normalizePhone returns correct standard formats.
     */
    public function test_phone_normalization_helper(): void
    {
        $this->assertEquals('9994206375', User::normalizePhone('+919994206375'));
        $this->assertEquals('9994206375', User::normalizePhone('919994206375'));
        $this->assertEquals('9994206375', User::normalizePhone('09994206375'));
        $this->assertEquals('9994206375', User::normalizePhone('999-420-6375'));
        $this->assertEquals('9994206375', User::normalizePhone(' 999 420 6375 '));
        $this->assertEquals('9994206375', User::normalizePhone('9994206375'));
        $this->assertNull(User::normalizePhone(null));
        $this->assertNull(User::normalizePhone(''));
    }

    /**
     * Test automatic model normalization on save.
     */
    public function test_automatic_model_normalization_on_save(): void
    {
        // 1. User Model
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'phone' => '+919994206375',
            'status' => 'active',
        ]);
        $this->assertEquals('9994206375', $user->phone);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'phone' => '9994206375']);

        // 2. Lead Model
        $lead = Lead::create([
            'lead_type' => 'advertiser',
            'name' => 'Enquiry Person',
            'phone' => '09994206375',
            'email' => 'lead@example.com',
        ]);
        $this->assertEquals('9994206375', $lead->phone);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'phone' => '9994206375']);

        // 3. AdvertiserProfile Model
        $industry = \App\Models\Industry::create(['name' => 'Tech', 'status' => 'active']);
        $advProfile = AdvertiserProfile::create([
            'company_name' => 'Company A',
            'contact_person' => 'Person A',
            'phone' => '919994206375',
            'email' => 'adv@example.com',
            'industry_id' => $industry->id,
            'status' => 'active',
            'user_id' => $user->id,
            'advertiser_code' => 'ADV-00001',
        ]);
        $this->assertEquals('9994206375', $advProfile->phone);
        $this->assertDatabaseHas('advertiser_profiles', ['id' => $advProfile->id, 'phone' => '9994206375']);

        // 4. LocationPartnerProfile Model
        $partnerProfile = LocationPartnerProfile::create([
            'company_name' => 'Company B',
            'contact_person' => 'Person B',
            'phone' => '999-420-6375',
            'email' => 'partner@example.com',
            'status' => 'active',
            'user_id' => $user->id,
            'partner_code' => 'LP-00001',
        ]);
        $this->assertEquals('9994206375', $partnerProfile->phone);
        $this->assertDatabaseHas('location_partner_profiles', ['id' => $partnerProfile->id, 'phone' => '9994206375']);

        // 5. OtpVerification Model
        $otp = OtpVerification::create([
            'user_id' => $user->id,
            'phone' => '+91 999 420 6375',
            'email' => 'user@example.com',
            'otp_code' => '123456',
            'user_type' => 'advertiser',
            'expires_at' => now()->addMinutes(5),
        ]);
        $this->assertEquals('9994206375', $otp->phone);
        $this->assertDatabaseHas('otp_verifications', ['id' => $otp->id, 'phone' => '9994206375']);
    }

    /**
     * Test that the same phone number can be registered for both roles (Multi-Role Support).
     */
    public function test_multi_role_phone_number_allowance(): void
    {
        // Register Location Partner first
        $partnerProfile = $this->partnerService->createPartner([
            'company_name' => 'Unified Partner',
            'contact_person' => 'Partner Owner',
            'email' => 'partner@unified.com',
            'phone' => '+919994206375',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('location_partner_profiles', [
            'id' => $partnerProfile->id,
            'phone' => '9994206375',
        ]);

        // Register Advertiser using same phone number
        $industry = \App\Models\Industry::create(['name' => 'General', 'status' => 'active']);
        $advertiserProfile = $this->advertiserService->createAdvertiser([
            'company_name' => 'Unified Advertiser',
            'contact_person' => 'Advertiser Owner',
            'email' => 'advertiser@unified.com',
            'phone' => '9994206375',
            'industry_id' => $industry->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('advertiser_profiles', [
            'id' => $advertiserProfile->id,
            'phone' => '9994206375',
        ]);

        // Confirm both profiles reuse the same user record
        $this->assertEquals($partnerProfile->user_id, $advertiserProfile->user_id);
    }

    /**
     * Test that duplicate active records within the same role are prohibited.
     */
    public function test_uniqueness_enforced_within_role_profile_table(): void
    {
        $industry = \App\Models\Industry::create(['name' => 'Retail', 'status' => 'active']);

        // Create Advertiser A
        $this->advertiserService->createAdvertiser([
            'company_name' => 'Advertiser A',
            'contact_person' => 'A Owner',
            'email' => 'a@advertiser.com',
            'phone' => '+919994206375',
            'industry_id' => $industry->id,
            'status' => 'active',
        ]);

        // Create admin user with the required permission for the route middleware
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage-advertisers', 'guard_name' => 'web']);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '8888888888',
        ]);
        $adminUser->assignRole('Admin');
        $adminUser->givePermissionTo('manage-advertisers');

        // Test store endpoint for duplicate Advertiser
        $response = $this->actingAs($adminUser)->post(route('admin.advertisers.store'), [
            'company_name' => 'Advertiser B',
            'contact_person' => 'B Owner',
            'email' => 'b@advertiser.com',
            'phone' => '9994206375', // duplicate
            'industry_id' => $industry->id,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    /**
     * Test OTP verification works correctly with normalized value.
     */
    public function test_otp_login_resolves_appropriate_role_account(): void
    {
        Mail::fake();
        $industry = \App\Models\Industry::create(['name' => 'Retail', 'status' => 'active']);

        // Create a user who is both Location Partner and Advertiser
        $user = User::create([
            'name' => 'Dual User',
            'email' => 'dual@example.com',
            'phone' => '9994206375',
            'status' => 'active',
        ]);
        $user->assignRole('location_partner');
        $user->assignRole('advertiser');

        $partnerProfile = LocationPartnerProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Dual Partner',
            'contact_person' => 'Dual Owner',
            'email' => 'partner@dual.com',
            'phone' => '9994206375',
            'status' => 'active',
            'partner_code' => 'LP-00001',
        ]);

        $advertiserProfile = AdvertiserProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Dual Advertiser',
            'contact_person' => 'Dual Owner',
            'email' => 'adv@dual.com',
            'phone' => '9994206375',
            'industry_id' => $industry->id,
            'status' => 'active',
            'advertiser_code' => 'ADV-00001',
        ]);

        // --- Advertiser OTP ---
        // OtpService hashes the code before storing — capture plain text via Mail::fake()
        $plainOtpAdv = null;
        $otpAdv = $this->otpService->requestOtp('+919994206375', 'adv@dual.com', 'advertiser', '127.0.0.1');
        $this->assertEquals('9994206375', $otpAdv->phone);
        $this->assertEquals('advertiser', $otpAdv->user_type);

        Mail::assertSent(\App\Mail\LoginOtpMail::class, function ($mail) use (&$plainOtpAdv) {
            $plainOtpAdv = $mail->otpCode;
            return true;
        });
        $this->assertNotNull($plainOtpAdv, 'OTP mail was not sent for advertiser');

        // Verify using captured plain-text code (not the stored bcrypt hash)
        $verifiedAdv = $this->otpService->verifyOtp('+919994206375', null, 'advertiser', $plainOtpAdv, '127.0.0.1');
        $this->assertTrue($verifiedAdv);

        // --- Location Partner OTP ---
        Mail::fake(); // Reset fake so assertSent below only sees the new mail
        $plainOtpPartner = null;
        $otpPartner = $this->otpService->requestOtp('9994206375', 'partner@dual.com', 'location_partner', '127.0.0.2');
        $this->assertEquals('9994206375', $otpPartner->phone);
        $this->assertEquals('location_partner', $otpPartner->user_type);

        Mail::assertSent(\App\Mail\LoginOtpMail::class, function ($mail) use (&$plainOtpPartner) {
            $plainOtpPartner = $mail->otpCode;
            return true;
        });
        $this->assertNotNull($plainOtpPartner, 'OTP mail was not sent for location_partner');

        // Verify using captured plain-text code
        $verifiedPartner = $this->otpService->verifyOtp('9994206375', null, 'location_partner', $plainOtpPartner, '127.0.0.2');
        $this->assertTrue($verifiedPartner);
    }
}

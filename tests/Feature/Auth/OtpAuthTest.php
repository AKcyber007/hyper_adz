<?php

namespace Tests\Feature\Auth;

use App\Mail\LoginOtpMail;
use App\Models\ActivityLog;
use App\Models\AdvertiserProfile;
use App\Models\LocationPartnerProfile;
use App\Models\OtpVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OtpAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    /**
     * Create an active advertiser profile (no linked User yet — auth creates it).
     */
    private function createActiveAdvertiser(array $overrides = []): AdvertiserProfile
    {
        $industry = \App\Models\Industry::first() ?? \App\Models\Industry::create(['name' => 'Automobile', 'status' => 'active']);

        return AdvertiserProfile::create(array_merge([
            'advertiser_code' => 'ADV-TEST-001',
            'company_name'    => 'Test Advertiser Co',
            'contact_person'  => 'Jane Smith',
            'phone'           => '+919900000001',
            'email'           => 'advertiser@test.com',
            'status'          => 'active',
            'login_count'     => 0,
            'industry_id'     => $industry->id,
        ], $overrides));
    }

    /**
     * Create an active location partner profile (no linked User yet).
     */
    private function createActivePartner(array $overrides = []): LocationPartnerProfile
    {
        return LocationPartnerProfile::create(array_merge([
            'partner_code'   => 'PART-TEST-001',
            'company_name'   => 'Test Partner Co',
            'contact_person' => 'John Doe',
            'phone'          => '+919900000002',
            'email'          => 'partner@test.com',
            'status'         => 'active',
            'login_count'    => 0,
        ], $overrides));
    }

    /**
     * Manually create an OTP record for a phone.
     */
    private function seedOtp(string $phone, string $email, string $userType, string $plainOtp, array $overrides = []): OtpVerification
    {
        $phone = preg_replace('/^\+?91/', '', trim($phone));
        return OtpVerification::create(array_merge([
            'phone'      => $phone,
            'email'      => $email,
            'otp_code'   => Hash::make($plainOtp),
            'user_type'  => $userType,
            'purpose'    => 'login',
            'attempts'   => 0,
            'expires_at' => Carbon::now()->addMinutes(10),
            'ip_address' => '127.0.0.1',
        ], $overrides));
    }

    // ─────────────────────────────────────────────
    // Advertiser Login Page
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_login_page_renders_successfully(): void
    {
        $response = $this->get(route('advertiser.login'));
        $response->assertStatus(200);
        $response->assertSee('Advertiser Portal');
    }

    /** @test */
    public function authenticated_advertiser_is_redirected_away_from_login(): void
    {
        $profile = $this->createActiveAdvertiser();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'advertiser')
                         ->get(route('advertiser.login'));

        $response->assertRedirect(route('advertiser.dashboard'));
    }

    // ─────────────────────────────────────────────
    // Advertiser OTP Request
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_can_request_otp_with_valid_phone(): void
    {
        Mail::fake();

        $profile = $this->createActiveAdvertiser();

        $response = $this->post(route('advertiser.login.post'), [
            'phone' => $profile->phone,
        ]);

        $response->assertRedirect(route('advertiser.login.verify'));
        Mail::assertSent(LoginOtpMail::class);
        $this->assertDatabaseHas('otp_verifications', [
            'phone'     => preg_replace('/^\+?91/', '', $profile->phone),
            'user_type' => 'advertiser',
            'purpose'   => 'login',
        ]);
    }

    /** @test */
    public function advertiser_otp_request_fails_for_unknown_phone(): void
    {
        Mail::fake();

        $response = $this->post(route('advertiser.login.post'), [
            'phone' => '+910000000000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        Mail::assertNothingSent();
    }

    /** @test */
    public function advertiser_otp_request_fails_for_inactive_account(): void
    {
        Mail::fake();

        $profile = $this->createActiveAdvertiser(['status' => 'inactive']);

        $response = $this->post(route('advertiser.login.post'), [
            'phone' => $profile->phone,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        Mail::assertNothingSent();
    }

    // ─────────────────────────────────────────────
    // Advertiser OTP Verify — Valid
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_can_login_with_valid_otp(): void
    {
        $profile  = $this->createActiveAdvertiser();
        $plainOtp = '123456';
        $this->seedOtp($profile->phone, $profile->email, 'advertiser', $plainOtp);

        $response = $this->withSession(['auth_login_phone' => $profile->phone])
                         ->post(route('advertiser.login.verify.post'), [
                             'phone'    => $profile->phone,
                             'otp_code' => $plainOtp,
                         ]);

        $response->assertRedirect(route('advertiser.dashboard'));

        // User created and analytics updated
        $this->assertDatabaseHas('users', ['email' => $profile->email]);
        $profile->refresh();
        $this->assertNotNull($profile->user_id);
        $this->assertEquals(1, $profile->login_count);
        $this->assertNotNull($profile->last_login_at);
    }

    /** @test */
    public function advertiser_otp_is_marked_verified_after_successful_login(): void
    {
        $profile  = $this->createActiveAdvertiser();
        $plainOtp = '654321';
        $otp      = $this->seedOtp($profile->phone, $profile->email, 'advertiser', $plainOtp);

        $this->withSession(['auth_login_phone' => $profile->phone])
             ->post(route('advertiser.login.verify.post'), [
                 'phone'    => $profile->phone,
                 'otp_code' => $plainOtp,
             ]);

        $otp->refresh();
        $this->assertNotNull($otp->verified_at);
    }

    // ─────────────────────────────────────────────
    // Advertiser OTP Verify — Invalid / Expired
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_login_fails_with_incorrect_otp(): void
    {
        $profile = $this->createActiveAdvertiser();
        $this->seedOtp($profile->phone, $profile->email, 'advertiser', '111111');

        $response = $this->withSession(['auth_login_phone' => $profile->phone])
                         ->post(route('advertiser.login.verify.post'), [
                             'phone'    => $profile->phone,
                             'otp_code' => '999999',
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function advertiser_login_fails_with_expired_otp(): void
    {
        $profile = $this->createActiveAdvertiser();
        $this->seedOtp($profile->phone, $profile->email, 'advertiser', '222222', [
            'expires_at' => Carbon::now()->subMinutes(15),
        ]);

        $response = $this->withSession(['auth_login_phone' => $profile->phone])
                         ->post(route('advertiser.login.verify.post'), [
                             'phone'    => $profile->phone,
                             'otp_code' => '222222',
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function advertiser_otp_locks_after_five_failed_attempts(): void
    {
        $profile  = $this->createActiveAdvertiser();
        $plainOtp = '333333';
        $otp      = $this->seedOtp($profile->phone, $profile->email, 'advertiser', $plainOtp);

        for ($i = 0; $i < 5; $i++) {
            $this->withSession(['auth_login_phone' => $profile->phone])
                 ->post(route('advertiser.login.verify.post'), [
                     'phone'    => $profile->phone,
                     'otp_code' => '000000',
                 ]);
        }

        $otp->refresh();
        $this->assertGreaterThanOrEqual(5, $otp->attempts);

        // OTP should be expired / locked — correct code also fails now
        $response = $this->withSession(['auth_login_phone' => $profile->phone])
                         ->post(route('advertiser.login.verify.post'), [
                             'phone'    => $profile->phone,
                             'otp_code' => $plainOtp,
                         ]);

        $response->assertSessionHas('error');
    }

    // ─────────────────────────────────────────────
    // Advertiser Rate Limiting
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_otp_request_is_rate_limited_per_phone(): void
    {
        Mail::fake();

        $profile = $this->createActiveAdvertiser();

        // Exhaust the per-phone rate limit (8 per 5 mins, hit 10 times to be sure)
        $cleanPhone = preg_replace('/^\+?91/', '', $profile->phone);
        $key = 'otp-request-phone:' . $cleanPhone;
        for ($i = 0; $i < 10; $i++) {
            RateLimiter::hit($key, 300);
        }

        $response = $this->post(route('advertiser.login.post'), [
            'phone' => $profile->phone,
        ]);

        $response->assertSessionHas('error');
    }

    // ─────────────────────────────────────────────
    // Advertiser Session / Guard Protection
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_dashboard_requires_authentication(): void
    {
        $response = $this->get(route('advertiser.dashboard'));
        $response->assertRedirect(route('advertiser.login'));
    }

    /** @test */
    public function authenticated_advertiser_can_access_dashboard(): void
    {
        $profile = $this->createActiveAdvertiser();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'advertiser')
                         ->get(route('advertiser.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($profile->company_name);
    }

    /** @test */
    public function advertiser_logout_terminates_session(): void
    {
        $profile = $this->createActiveAdvertiser();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'advertiser')
                         ->post(route('advertiser.logout'));

        $response->assertRedirect(route('advertiser.login'));
        $this->assertGuest('advertiser');
    }

    // ─────────────────────────────────────────────
    // Advertiser Activity Logging
    // ─────────────────────────────────────────────

    /** @test */
    public function activity_log_is_written_on_advertiser_login_success(): void
    {
        $profile  = $this->createActiveAdvertiser();
        $plainOtp = '555555';
        $this->seedOtp($profile->phone, $profile->email, 'advertiser', $plainOtp);

        $this->withSession(['auth_login_phone' => $profile->phone])
             ->post(route('advertiser.login.verify.post'), [
                 'phone'    => $profile->phone,
                 'otp_code' => $plainOtp,
             ]);

        $this->assertDatabaseHas('activity_logs', [
            'action'      => 'login_success',
            'entity_type' => AdvertiserProfile::class,
        ]);
    }

    /** @test */
    public function activity_log_is_written_on_advertiser_logout(): void
    {
        $profile = $this->createActiveAdvertiser();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $this->actingAs($user, 'advertiser')
             ->post(route('advertiser.logout'));

        $this->assertDatabaseHas('activity_logs', [
            'action'  => 'logout',
            'user_id' => $user->id,
        ]);
    }

    // ─────────────────────────────────────────────
    // Location Partner — Login Page
    // ─────────────────────────────────────────────

    /** @test */
    public function partner_login_page_renders_successfully(): void
    {
        $response = $this->get(route('partner.login'));
        $response->assertStatus(200);
        $response->assertSee('Partner');
    }

    // ─────────────────────────────────────────────
    // Location Partner — OTP Request
    // ─────────────────────────────────────────────

    /** @test */
    public function partner_can_request_otp_with_valid_phone(): void
    {
        Mail::fake();

        $profile = $this->createActivePartner();

        $response = $this->post(route('partner.login.post'), [
            'phone' => $profile->phone,
        ]);

        $response->assertRedirect(route('partner.login.verify'));
        Mail::assertSent(LoginOtpMail::class);
        $this->assertDatabaseHas('otp_verifications', [
            'phone'     => preg_replace('/^\+?91/', '', $profile->phone),
            'user_type' => 'location_partner',
        ]);
    }

    /** @test */
    public function partner_otp_request_fails_for_unknown_phone(): void
    {
        $response = $this->post(route('partner.login.post'), [
            'phone' => '+910000000099',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function partner_otp_request_fails_for_inactive_account(): void
    {
        Mail::fake();

        $profile = $this->createActivePartner(['status' => 'suspended']);

        $response = $this->post(route('partner.login.post'), [
            'phone' => $profile->phone,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        Mail::assertNothingSent();
    }

    // ─────────────────────────────────────────────
    // Location Partner — OTP Verify
    // ─────────────────────────────────────────────

    /** @test */
    public function partner_can_login_with_valid_otp(): void
    {
        $profile  = $this->createActivePartner();
        $plainOtp = '789012';
        $this->seedOtp($profile->phone, $profile->email, 'location_partner', $plainOtp);

        $response = $this->withSession(['auth_login_phone' => $profile->phone])
                         ->post(route('partner.login.verify.post'), [
                             'phone'    => $profile->phone,
                             'otp_code' => $plainOtp,
                         ]);

        $response->assertRedirect(route('partner.dashboard'));
        $this->assertDatabaseHas('users', ['email' => $profile->email]);
        $profile->refresh();
        $this->assertEquals(1, $profile->login_count);
        $this->assertNotNull($profile->last_login_at);
    }

    /** @test */
    public function partner_login_fails_with_incorrect_otp(): void
    {
        $profile = $this->createActivePartner();
        $this->seedOtp($profile->phone, $profile->email, 'location_partner', '456789');

        $response = $this->withSession(['auth_login_phone' => $profile->phone])
                         ->post(route('partner.login.verify.post'), [
                             'phone'    => $profile->phone,
                             'otp_code' => '000000',
                         ]);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function partner_login_fails_with_expired_otp(): void
    {
        $profile = $this->createActivePartner();
        $this->seedOtp($profile->phone, $profile->email, 'location_partner', '111222', [
            'expires_at' => Carbon::now()->subMinutes(20),
        ]);

        $response = $this->withSession(['auth_login_phone' => $profile->phone])
                         ->post(route('partner.login.verify.post'), [
                             'phone'    => $profile->phone,
                             'otp_code' => '111222',
                         ]);

        $response->assertSessionHas('error');
    }

    // ─────────────────────────────────────────────
    // Location Partner — Guard & Dashboard
    // ─────────────────────────────────────────────

    /** @test */
    public function partner_dashboard_requires_authentication(): void
    {
        $response = $this->get(route('partner.dashboard'));
        $response->assertRedirect(route('partner.login'));
    }

    /** @test */
    public function authenticated_partner_can_access_dashboard(): void
    {
        $profile = $this->createActivePartner();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'location_partner')
                         ->get(route('partner.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($profile->company_name);
    }

    /** @test */
    public function partner_logout_terminates_session(): void
    {
        $profile = $this->createActivePartner();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'location_partner')
                         ->post(route('partner.logout'));

        $response->assertRedirect(route('partner.login'));
        $this->assertGuest('location_partner');
    }

    /** @test */
    public function activity_log_is_written_on_partner_login_success(): void
    {
        $profile  = $this->createActivePartner();
        $plainOtp = '246810';
        $this->seedOtp($profile->phone, $profile->email, 'location_partner', $plainOtp);

        $this->withSession(['auth_login_phone' => $profile->phone])
             ->post(route('partner.login.verify.post'), [
                 'phone'    => $profile->phone,
                 'otp_code' => $plainOtp,
             ]);

        $this->assertDatabaseHas('activity_logs', [
            'action'      => 'login_success',
            'entity_type' => LocationPartnerProfile::class,
        ]);
    }

    // ─────────────────────────────────────────────
    // Cross-Guard Isolation
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_guard_cannot_access_partner_dashboard(): void
    {
        $profile = $this->createActiveAdvertiser();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'advertiser')
                         ->get(route('partner.dashboard'));

        $response->assertRedirect(route('partner.login'));
    }

    /** @test */
    public function partner_guard_cannot_access_advertiser_dashboard(): void
    {
        $profile = $this->createActivePartner();
        $user    = User::factory()->create(['email' => $profile->email]);
        $profile->update(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'location_partner')
                         ->get(route('advertiser.dashboard'));

        $response->assertRedirect(route('advertiser.login'));
    }

    // ─────────────────────────────────────────────
    // OTP Verify Page — Session Guard
    // ─────────────────────────────────────────────

    /** @test */
    public function advertiser_verify_page_requires_session_phone(): void
    {
        $response = $this->get(route('advertiser.login.verify'));
        $response->assertRedirect(route('advertiser.login'));
    }

    /** @test */
    public function partner_verify_page_requires_session_phone(): void
    {
        $response = $this->get(route('partner.login.verify'));
        $response->assertRedirect(route('partner.login'));
    }
}

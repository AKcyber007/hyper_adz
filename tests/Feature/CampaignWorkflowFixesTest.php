<?php

namespace Tests\Feature;

use App\Models\AdvertiserProfile;
use App\Models\Campaign;
use App\Models\Industry;
use App\Models\Location;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\AdvertiserService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampaignWorkflowFixesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Helper to create advertiser user and profile.
     */
    private function createAdvertiser(): array
    {
        $user = User::factory()->create([
            'email' => 'advertiser@example.com',
            'phone' => '9998887776',
        ]);
        
        $role = Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
        $user->assignRole($role);

        $industry = Industry::first() ?? Industry::create(['name' => 'Automobile', 'status' => 'active']);

        $profile = AdvertiserProfile::create([
            'advertiser_code' => 'ADV-00001',
            'company_name'    => 'Test Adv Co',
            'contact_person'  => 'Jane Smith',
            'phone'           => '9998887776',
            'email'           => 'advertiser@example.com',
            'status'          => 'active',
            'user_id'         => $user->id,
            'industry_id'     => $industry->id,
        ]);

        return compact('user', 'profile');
    }

    /**
     * Helper to create admin user.
     */
    private function createAdmin(): User
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $user->assignRole($role);
        
        // Grant permissions for dashboard/campaign management
        $manageUsersPerm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage-users', 'guard_name' => 'web']);
        $manageCampaignsPerm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage-campaigns', 'guard_name' => 'web']);
        $user->givePermissionTo($manageUsersPerm);
        $user->givePermissionTo($manageCampaignsPerm);

        return $user;
    }

    /**
     * Helper to create a campaign request.
     */
    private function createCampaign(int $advertiserId, array $overrides = []): Campaign
    {
        $industry = Industry::first() ?? Industry::create(['name' => 'Automobile', 'status' => 'active']);

        return Campaign::create(array_merge([
            'campaign_code'   => 'CAMP-' . rand(10000, 99999),
            'campaign_name'   => 'Test Campaign',
            'campaign_type'   => 'Custom',
            'industry_id'     => $industry->id,
            'start_date'      => Carbon::tomorrow(),
            'end_date'        => Carbon::tomorrow()->addDays(5),
            'budget'          => 5000.00,
            'status'          => 'Submitted',
            'approval_status' => 'Pending Review',
            'advertiser_id'   => $advertiserId,
        ], $overrides));
    }

    /** @test */
    public function campaign_date_validation_rejects_today_and_past_dates(): void
    {
        $data = $this->createAdvertiser();
        $user = $data['user'];
        
        $location = Location::factory()->create(['status' => 'active', 'price_per_day' => 100]);

        // 1. Try to submit with start_date set to TODAY (should fail validation)
        $response = $this->actingAs($user, 'advertiser')
            ->post(route('advertiser.my-requests.store'), [
                'campaign_name' => 'Test Today Campaign',
                'start_date'    => Carbon::today()->format('Y-m-d'),
                'end_date'      => Carbon::tomorrow()->format('Y-m-d'),
                'locations'     => [$location->id],
                'action'        => 'submit',
            ]);

        $response->assertSessionHasErrors('start_date');

        // 2. Try to submit with start_date set to YESTERDAY (should fail validation)
        $response = $this->actingAs($user, 'advertiser')
            ->post(route('advertiser.my-requests.store'), [
                'campaign_name' => 'Test Past Campaign',
                'start_date'    => Carbon::yesterday()->format('Y-m-d'),
                'end_date'      => Carbon::tomorrow()->format('Y-m-d'),
                'locations'     => [$location->id],
                'action'        => 'submit',
            ]);

        $response->assertSessionHasErrors('start_date');

        // 3. Submit with start_date set to TOMORROW (should pass validation)
        $response = $this->actingAs($user, 'advertiser')
            ->post(route('advertiser.my-requests.store'), [
                'campaign_name' => 'Test Valid Campaign',
                'start_date'    => Carbon::tomorrow()->format('Y-m-d'),
                'end_date'      => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
                'locations'     => [$location->id],
                'action'        => 'submit',
            ]);

        $response->assertRedirect(route('advertiser.my-requests'));
        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('campaigns', [
            'campaign_name' => 'Test Valid Campaign',
            'status'        => 'Submitted',
        ]);
    }

    /** @test */
    public function admin_dashboard_shows_real_campaign_counts(): void
    {
        $admin = $this->createAdmin();
        $data = $this->createAdvertiser();
        $profile = $data['profile'];

        // Seed some campaigns with different statuses
        $this->createCampaign($profile->id, ['status' => 'Submitted']);
        $this->createCampaign($profile->id, ['status' => 'Creative Review']);
        $this->createCampaign($profile->id, ['status' => 'Running']);
        $this->createCampaign($profile->id, ['status' => 'Completed']);

        // Get admin dashboard
        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $kpis = $response->viewData('kpis');

        // Real count must be 2 (Submitted + Creative Review)
        $this->assertEquals(2, $kpis['pending_campaigns']);
        $this->assertEquals(1, $kpis['total_advertisers']);
    }

    /** @test */
    public function advertiser_deletion_cleans_up_non_historical_campaigns(): void
    {
        $data = $this->createAdvertiser();
        $profile = $data['profile'];

        // Create campaigns with different statuses
        $draft = $this->createCampaign($profile->id, ['status' => 'Draft']);
        $submitted = $this->createCampaign($profile->id, ['status' => 'Submitted']);
        $rejected = $this->createCampaign($profile->id, ['status' => 'Rejected (Admin)']);
        $running = $this->createCampaign($profile->id, ['status' => 'Running']);
        $completed = $this->createCampaign($profile->id, ['status' => 'Completed']);

        // Call Advertiser deletion
        $service = app(AdvertiserService::class);
        $service->deleteAdvertiser($profile->id);

        // Verify Advertiser is soft-deleted
        $this->assertSoftDeleted('advertiser_profiles', [
            'id' => $profile->id,
        ]);

        // Verify non-historical campaign requests are deleted (soft-deleted)
        $this->assertSoftDeleted('campaigns', ['id' => $draft->id]);
        $this->assertSoftDeleted('campaigns', ['id' => $submitted->id]);
        $this->assertSoftDeleted('campaigns', ['id' => $rejected->id]);

        // Verify historical campaigns are NOT deleted
        $this->assertDatabaseHas('campaigns', [
            'id'         => $running->id,
            'deleted_at' => null,
        ]);
        
        $this->assertDatabaseHas('campaigns', [
            'id'         => $completed->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function admin_can_reverse_approval_before_payment(): void
    {
        $admin = $this->createAdmin();
        $data = $this->createAdvertiser();
        $profile = $data['profile'];

        $campaign = $this->createCampaign($profile->id, [
            'status'         => 'Payment Pending',
            'payment_amount' => 5000.00,
        ]);

        // 1. Reverse to Creative Review
        $response = $this->actingAs($admin)
            ->post(route('admin.advertising.requests.reverse-approval', $campaign->id), [
                'target_status' => 'creative_review',
                'reason'        => 'Please upload a higher resolution video',
            ]);

        $response->assertRedirect(route('admin.advertising.requests.show', $campaign->id));
        $response->assertSessionHas('success');

        $campaign->refresh();
        $this->assertEquals('Creative Review', $campaign->status);
        $this->assertNull($campaign->payment_amount);
        $this->assertNull($campaign->payment_due_date);
        $this->assertEquals('Please upload a higher resolution video', $campaign->creative_review_notes);

        // Verify advertiser activity log (notification) was created
        $this->assertDatabaseHas('activity_logs', [
            'entity_type' => AdvertiserProfile::class,
            'entity_id'   => $profile->id,
            'action'      => 'Approval Reversed',
        ]);

        // 2. Setup next run for Rejection
        $campaign->update([
            'status'         => 'Payment Pending',
            'payment_amount' => 5000.00,
        ]);

        // Reverse to Rejected
        $response = $this->actingAs($admin)
            ->post(route('admin.advertising.requests.reverse-approval', $campaign->id), [
                'target_status' => 'rejected',
                'reason'        => 'Content violates screen guidelines',
            ]);

        $response->assertRedirect(route('admin.advertising.requests.show', $campaign->id));

        $campaign->refresh();
        $this->assertEquals('Rejected (Admin)', $campaign->status);
        $this->assertNull($campaign->payment_amount);
        $this->assertEquals('Content violates screen guidelines', $campaign->rejection_reason);
    }
}

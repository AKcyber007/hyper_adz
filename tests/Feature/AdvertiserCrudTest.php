<?php

namespace Tests\Feature;

use App\Models\AdvertiserProfile;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdvertiserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Industry $industry;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Spatie role and permission
        $manageAdvertisers = Permission::create(['name' => 'manage-advertisers']);
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo($manageAdvertisers);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');

        $this->regularUser = User::factory()->create();

        // Seed default industry
        $this->industry = Industry::create(['name' => 'Automobile', 'status' => 'active']);
    }

    /** @test */
    public function guests_cannot_access_advertisers_management()
    {
        $this->get(route('admin.advertisers.index'))->assertRedirect(route('login'));
        $this->get(route('admin.advertisers.dashboard'))->assertRedirect(route('login'));
    }

    /** @test */
    public function users_without_permission_cannot_access_advertisers()
    {
        $this->actingAs($this->regularUser);

        $this->get(route('admin.advertisers.index'))->assertStatus(403);
        $this->get(route('admin.advertisers.dashboard'))->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_advertiser_with_logo_and_autogenerates_sequential_code()
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser);

        $logoFile = UploadedFile::fake()->create('brand_logo.png', 10, 'image/png');

        $data = [
            'company_name'   => 'Audi India',
            'contact_person' => 'Vikram Oberoi',
            'phone'          => '9988776655',
            'email'          => 'vikram@audi.in',
            'website'        => 'www.audi.in',
            'gst_number'     => '33AAAAA0000A1Z1',
            'industry_id'    => $this->industry->id,
            'logo'           => $logoFile,
            'status'         => 'active',
            'notes'          => 'Premium luxury car segment',
            'address_line_1' => 'Avinashi Road',
            'city'           => 'Coimbatore',
            'state'          => 'Tamil Nadu',
            'country'        => 'India',
            'postal_code'    => '641018'
        ];

        $response = $this->post(route('admin.advertisers.store'), $data);
        $response->assertRedirect(route('admin.advertisers.index'));

        // Verify Advertiser Profile exists
        $profile = AdvertiserProfile::first();
        $this->assertNotNull($profile);
        $expectedCode = 'ADV-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedCode, $profile->advertiser_code);
        $this->assertNotNull($profile->logo_path);
        Storage::disk('public')->assertExists($profile->logo_path);

        // Verify Activity Log is written
        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $this->adminUser->id,
            'entity_type' => AdvertiserProfile::class,
            'entity_id'   => $profile->id,
            'action'      => 'created',
            'description' => 'Created Advertiser ' . $expectedCode
        ]);
    }

    /** @test */
    public function admin_can_convert_approved_lead_to_advertiser()
    {
        $this->actingAs($this->adminUser);

        // Create an approved advertiser lead
        $lead = Lead::create([
            'lead_type' => 'advertiser',
            'name'      => 'BMW Dealer',
            'phone'     => '9876543210',
            'email'     => 'dealer@bmw.in',
            'message'   => 'Looking for promotional screen ads.',
            'status'    => 'approved',
            'source'    => 'contact_form'
        ]);

        $data = [
            'industry_id' => $this->industry->id,
            'gst_number'  => '33BMWAA0000A1Z1',
            'website'     => 'dealer.bmw.in'
        ];

        $response = $this->post(route('admin.advertisers.convert', $lead->id), $data);
        $response->assertRedirect(route('admin.leads.show', $lead->id));

        $lead->refresh();
        $this->assertNotNull($lead->converted_at);

        // Verify profile details mapping
        $profile = AdvertiserProfile::where('lead_id', $lead->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('BMW Dealer', $profile->contact_person);
        $this->assertEquals('dealer@bmw.in', $profile->email);
        $expectedCode = 'ADV-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedCode, $profile->advertiser_code);
        $this->assertEquals($this->industry->id, $profile->industry_id);
    }

    /** @test */
    public function admin_can_update_advertiser_profile_details()
    {
        $this->actingAs($this->adminUser);

        $profile = AdvertiserProfile::create([
            'advertiser_code' => 'ADV-00001',
            'company_name'   => 'Nexon Motors',
            'contact_person' => 'Sam',
            'phone'          => '9000000000',
            'email'          => 'sam@nexon.com',
            'industry_id'    => $this->industry->id,
            'status'         => 'pending'
        ]);

        $updateData = [
            'company_name'   => 'Nexon Motors Pvt Ltd',
            'contact_person' => 'Samuel',
            'phone'          => '9000000000',
            'email'          => 'sam@nexon.com',
            'industry_id'    => $this->industry->id,
            'status'         => 'pending',
            'notes'          => 'Updated info'
        ];

        $response = $this->put(route('admin.advertisers.update', $profile->id), $updateData);
        $response->assertRedirect(route('admin.advertisers.index'));

        $profile->refresh();
        $this->assertEquals('Nexon Motors Pvt Ltd', $profile->company_name);
        $this->assertEquals('Samuel', $profile->contact_person);
        $this->assertEquals('Updated info', $profile->notes);
    }

    /** @test */
    public function admin_can_activate_and_suspend_advertiser_status()
    {
        $this->actingAs($this->adminUser);

        $profile = AdvertiserProfile::create([
            'advertiser_code' => 'ADV-00001',
            'company_name'   => 'Porsche Dealer',
            'contact_person' => 'Rajesh',
            'phone'          => '9000000000',
            'email'          => 'raj@porsche.in',
            'industry_id'    => $this->industry->id,
            'status'         => 'pending'
        ]);

        // Activate
        $this->put(route('admin.advertisers.updateStatus', $profile->id), ['status' => 'active'])->assertRedirect();
        $profile->refresh();
        $this->assertEquals('active', $profile->status);
        $this->assertEquals($this->adminUser->id, $profile->approved_by);
        $this->assertNotNull($profile->approved_at);

        // Suspend
        $this->put(route('admin.advertisers.updateStatus', $profile->id), ['status' => 'suspended'])->assertRedirect();
        $profile->refresh();
        $this->assertEquals('suspended', $profile->status);
    }

    /** @test */
    public function admin_can_search_and_filter_advertisers()
    {
        $this->actingAs($this->adminUser);

        $indOther = Industry::create(['name' => 'Retail', 'status' => 'active']);

        $adv1 = AdvertiserProfile::create([
            'advertiser_code' => 'ADV-00001',
            'company_name'   => 'Tata Retail',
            'contact_person' => 'Rahul',
            'phone'          => '9000000000',
            'email'          => 'rahul@tata.com',
            'industry_id'    => $indOther->id,
            'status'         => 'active'
        ]);

        $adv2 = AdvertiserProfile::create([
            'advertiser_code' => 'ADV-00002',
            'company_name'   => 'Ford Motors',
            'contact_person' => 'Gaurav',
            'phone'          => '9111111111',
            'email'          => 'gaurav@ford.in',
            'industry_id'    => $this->industry->id,
            'status'         => 'suspended'
        ]);

        // Search company name
        $response = $this->get(route('admin.advertisers.index', ['search' => 'Tata']));
        $response->assertSee('Tata Retail');
        $response->assertDontSee('Ford Motors');

        // Filter Industry
        $response = $this->get(route('admin.advertisers.index', ['industry_id' => $this->industry->id]));
        $response->assertSee('Ford Motors');
        $response->assertDontSee('Tata Retail');

        // Filter Status
        $response = $this->get(route('admin.advertisers.index', ['status' => 'active']));
        $response->assertSee('Tata Retail');
        $response->assertDontSee('Ford Motors');
    }

    /** @test */
    public function admin_can_soft_delete_advertiser()
    {
        $this->actingAs($this->adminUser);

        $profile = AdvertiserProfile::create([
            'advertiser_code' => 'ADV-00001',
            'company_name'   => 'Audi Coimbatore',
            'contact_person' => 'Vikram',
            'phone'          => '9000000000',
            'email'          => 'vik@audi.in',
            'industry_id'    => $this->industry->id,
            'status'         => 'active'
        ]);

        $response = $this->delete(route('admin.advertisers.destroy', $profile->id));
        $response->assertRedirect(route('admin.advertisers.index'));

        $this->assertSoftDeleted('advertiser_profiles', [
            'id' => $profile->id
        ]);
    }
}

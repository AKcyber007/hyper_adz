<?php

namespace Tests\Feature;

use App\Models\LocationPartnerProfile;
use App\Models\Location;
use App\Models\Screen;
use App\Models\Lead;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\LocationCategory;
use App\Models\ScreenType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LocationPartnerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected LocationCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Spatie role and permission
        $managePartners = Permission::create(['name' => 'manage-location-partners']);
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo($managePartners);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');

        $this->regularUser = User::factory()->create();

        // Seed default category
        $this->category = LocationCategory::create(['name' => 'Mall', 'status' => 'active']);
    }

    /** @test */
    public function guests_cannot_access_location_partners_management()
    {
        $this->get(route('admin.location-partners.index'))->assertRedirect(route('login'));
        $this->get(route('admin.location-partners.dashboard'))->assertRedirect(route('login'));
    }

    /** @test */
    public function users_without_permission_cannot_access_partners()
    {
        $this->actingAs($this->regularUser);

        $this->get(route('admin.location-partners.index'))->assertStatus(403);
        $this->get(route('admin.location-partners.dashboard'))->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_partner_with_logo_and_autogenerates_sequential_code()
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser);

        $logoFile = UploadedFile::fake()->create('partner_logo.png', 10, 'image/png');

        $data = [
            'company_name'   => 'Brookefields Mall',
            'contact_person' => 'Ramesh Kumar',
            'designation'    => 'CEO',
            'phone'          => '9988776655',
            'email'          => 'ramesh@brookefields.in',
            'website'        => 'www.brookefields.in',
            'gst_number'     => '33AAAAA0000A1Z1',
            'logo'           => $logoFile,
            'status'         => 'active',
            'notes'          => 'Premium Coimbatore Mall partner',
            'address_line_1' => 'Dr. Krishnasamy Road',
            'city'           => 'Coimbatore',
            'state'          => 'Tamil Nadu',
            'country'        => 'India',
            'postal_code'    => '641001'
        ];

        $response = $this->post(route('admin.location-partners.store'), $data);
        $response->assertRedirect(route('admin.location-partners.index'));

        // Verify Partner Profile exists
        $profile = LocationPartnerProfile::first();
        $this->assertNotNull($profile);
        $expectedCode = 'LP-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedCode, $profile->partner_code);
        $this->assertNotNull($profile->logo_path);
        Storage::disk('public')->assertExists($profile->logo_path);

        // Verify Activity Log is written
        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $this->adminUser->id,
            'entity_type' => LocationPartnerProfile::class,
            'entity_id'   => $profile->id,
            'action'      => 'created',
            'description' => 'Created Partner ' . $expectedCode
        ]);
    }

    /** @test */
    public function admin_can_convert_approved_partner_lead()
    {
        $this->actingAs($this->adminUser);

        // Create an approved partner lead
        $lead = Lead::create([
            'lead_type' => 'location_partner',
            'name'      => 'Fun Republic Mall',
            'phone'     => '9876543210',
            'email'     => 'partner@funrep.in',
            'message'   => 'Interested in screen placements.',
            'status'    => 'approved',
            'source'    => 'partner_page'
        ]);

        $data = [
            'designation' => 'Director',
            'gst_number'  => '33FUNAA0000A1Z1',
            'website'     => 'funrep.in'
        ];

        $response = $this->post(route('admin.location-partners.convert', $lead->id), $data);
        $response->assertRedirect(route('admin.leads.show', $lead->id));

        $lead->refresh();
        $this->assertNotNull($lead->converted_at);

        // Verify profile details mapping
        $profile = LocationPartnerProfile::where('lead_id', $lead->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('Fun Republic Mall', $profile->contact_person);
        $this->assertEquals('partner@funrep.in', $profile->email);
        $expectedCode = 'LP-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedCode, $profile->partner_code);
        $this->assertEquals('Director', $profile->designation);
    }

    /** @test */
    public function admin_can_update_partner_profile_details()
    {
        $this->actingAs($this->adminUser);

        $profile = LocationPartnerProfile::create([
            'partner_code'   => 'LP-00001',
            'company_name'   => 'Prozone Mall',
            'contact_person' => 'Vivek',
            'phone'          => '9000000000',
            'email'          => 'vivek@prozone.com',
            'status'         => 'pending'
        ]);

        $updateData = [
            'company_name'   => 'Prozone Mall Coimbatore',
            'contact_person' => 'Vivekanand',
            'phone'          => '9000000000',
            'email'          => 'vivek@prozone.com',
            'status'         => 'pending',
            'notes'          => 'Updated remarks'
        ];

        $response = $this->put(route('admin.location-partners.update', $profile->id), $updateData);
        $response->assertRedirect(route('admin.location-partners.index'));

        $profile->refresh();
        $this->assertEquals('Prozone Mall Coimbatore', $profile->company_name);
        $this->assertEquals('Vivekanand', $profile->contact_person);
        $this->assertEquals('Updated remarks', $profile->notes);
    }

    /** @test */
    public function admin_can_activate_and_suspend_partner_status()
    {
        $this->actingAs($this->adminUser);

        $profile = LocationPartnerProfile::create([
            'partner_code'   => 'LP-00001',
            'company_name'   => 'Fun Mall',
            'contact_person' => 'David',
            'phone'          => '9000000000',
            'email'          => 'david@fun.in',
            'status'         => 'pending'
        ]);

        // Activate
        $this->put(route('admin.location-partners.updateStatus', $profile->id), ['status' => 'active'])->assertRedirect();
        $profile->refresh();
        $this->assertEquals('active', $profile->status);
        $this->assertEquals($this->adminUser->id, $profile->approved_by);
        $this->assertNotNull($profile->approved_at);

        // Suspend
        $this->put(route('admin.location-partners.updateStatus', $profile->id), ['status' => 'suspended'])->assertRedirect();
        $profile->refresh();
        $this->assertEquals('suspended', $profile->status);
    }

    /** @test */
    public function admin_can_assign_and_remove_locations_inventory()
    {
        $this->actingAs($this->adminUser);

        // Create partner
        $profile = LocationPartnerProfile::create([
            'partner_code'   => 'LP-00001',
            'company_name'   => 'Mall Partner',
            'contact_person' => 'Kumar',
            'phone'          => '9000000000',
            'email'          => 'kumar@mall.com',
            'status'         => 'active'
        ]);

        // Create unassigned locations and screens
        $location = Location::create([
            'name'                => 'Lobby Block',
            'slug'                => 'lobby-block',
            'category_id'         => $this->category->id,
            'address'             => 'Race Course Road',
            'city'                => 'Coimbatore',
            'state'               => 'Tamil Nadu',
            'postal_code'         => '641018',
            'latitude'            => 11.0022,
            'longitude'           => 76.9654,
            'location_partner_id' => null,
        ]);

        // Create a screen type
        $screenType = ScreenType::create([
            'name'   => 'LED Standee',
            'status' => 'active'
        ]);

        $screen = Screen::create([
            'name'                => 'Nexon Display 01',
            'slug'                => 'nexon-display-01',
            'location_id'         => $location->id,
            'screen_type_id'      => $screenType->id,
            'orientation'         => 'Landscape',
            'daily_impressions'   => 15000,
            'status'              => 'active',
            'availability_status' => 'available'
        ]);

        // 1. Assign Location
        $this->post(route('admin.location-partners.locations.assign', $profile->id), [
            'location_ids' => [$location->id]
        ])->assertRedirect();

        $location->refresh();
        $this->assertEquals($profile->id, $location->location_partner_id);

        // Check computed profile statistics (screens count & impressions)
        $profile->refresh();
        $this->assertEquals(1, $profile->locations_count);
        $this->assertEquals(1, $profile->screens_count);
        $this->assertEquals(15000, $profile->total_impressions);

        // Check Screen partner relation inherits properly
        $screen->refresh();
        $this->assertNotNull($screen->partner);
        $this->assertEquals($profile->id, $screen->partner->id);

        // 2. Remove Location Assignment
        $this->delete(route('admin.location-partners.locations.remove', $location->id))->assertRedirect();

        $location->refresh();
        $this->assertNull($location->location_partner_id);

        $profile->refresh();
        $this->assertEquals(0, $profile->locations_count);
        $this->assertEquals(0, $profile->screens_count);
        $this->assertEquals(0, $profile->total_impressions);
    }

    /** @test */
    public function admin_can_search_and_filter_partners()
    {
        $this->actingAs($this->adminUser);

        $p1 = LocationPartnerProfile::create([
            'partner_code'   => 'LP-00001',
            'company_name'   => 'Tata Venues',
            'contact_person' => 'Karan',
            'phone'          => '9000000000',
            'email'          => 'karan@tata.com',
            'city'           => 'Coimbatore',
            'status'         => 'active'
        ]);

        $p2 = LocationPartnerProfile::create([
            'partner_code'   => 'LP-00002',
            'company_name'   => 'Apex Properties',
            'contact_person' => 'Sam',
            'phone'          => '9111111111',
            'email'          => 'sam@apex.in',
            'city'           => 'Chennai',
            'status'         => 'suspended'
        ]);

        // Search company name
        $response = $this->get(route('admin.location-partners.index', ['search' => 'Tata']));
        $response->assertSee('Tata Venues');
        $response->assertDontSee('Apex Properties');

        // Filter status
        $response = $this->get(route('admin.location-partners.index', ['status' => 'suspended']));
        $response->assertSee('Apex Properties');
        $response->assertDontSee('Tata Venues');

        // Filter city
        $response = $this->get(route('admin.location-partners.index', ['city' => 'Coimbatore']));
        $response->assertSee('Tata Venues');
        $response->assertDontSee('Apex Properties');
    }

    /** @test */
    public function admin_can_soft_delete_partner_which_clears_location_assignments()
    {
        $this->actingAs($this->adminUser);

        $profile = LocationPartnerProfile::create([
            'partner_code'   => 'LP-00001',
            'company_name'   => 'V-Mall Group',
            'contact_person' => 'Sundar',
            'phone'          => '9000000000',
            'email'          => 'sundar@vmall.com',
            'status'         => 'active'
        ]);

        $location = Location::create([
            'name'                => 'Arcade lobby',
            'slug'                => 'arcade-lobby',
            'category_id'         => $this->category->id,
            'address'             => 'Race Course Road',
            'city'                => 'Coimbatore',
            'state'               => 'Tamil Nadu',
            'postal_code'         => '641018',
            'latitude'            => 11.0022,
            'longitude'           => 76.9654,
            'location_partner_id' => $profile->id,
        ]);

        $response = $this->delete(route('admin.location-partners.destroy', $profile->id));
        $response->assertRedirect(route('admin.location-partners.index'));

        $this->assertSoftDeleted('location_partner_profiles', [
            'id' => $profile->id
        ]);

        // Verify location assignment is cleared
        $location->refresh();
        $this->assertNull($location->location_partner_id);
    }
}

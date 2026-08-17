<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\LocationCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\LocationUpdateRequest;
use App\Models\LocationPartnerProfile;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LocationCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected LocationCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Spatie role and permission
        $manageLocations = Permission::create(['name' => 'manage-locations']);
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo($manageLocations);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');

        $this->regularUser = User::factory()->create();

        $this->category = LocationCategory::create([
            'name' => 'Mall',
            'icon' => 'bi-building',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function guests_cannot_access_locations_crud()
    {
        $this->get(route('admin.locations.index'))->assertRedirect(route('login'));
        $this->post(route('admin.locations.store'), [])->assertRedirect(route('login'));
    }

    /** @test */
    public function users_without_permission_cannot_access_locations_crud()
    {
        $this->actingAs($this->regularUser);

        $this->get(route('admin.locations.index'))->assertStatus(403);
        $this->post(route('admin.locations.store'), [])->assertStatus(403);
    }

    /** @test */
    public function admins_can_view_locations_index()
    {
        $this->actingAs($this->adminUser);

        Location::factory()->create([
            'category_id' => $this->category->id,
            'status' => 'active',
        ]);

        $response = $this->get(route('admin.locations.index'));
        $response->assertStatus(200);
        $response->assertSee('Locations Inventory');
    }

    /** @test */
    public function admin_can_create_location_with_images()
    {
        $this->actingAs($this->adminUser);
        Storage::fake('public');

        $partner = \App\Models\LocationPartnerProfile::create([
            'company_name' => 'Test Partner',
            'contact_person' => 'John Partner',
            'email' => 'partner@test.com',
            'phone' => '1234567890',
            'partner_code' => 'LP-00001',
        ]);

        $data = [
            'name' => 'Brookefields Mall',
            'category_id' => $this->category->id,
            'location_partner_id' => $partner->id,
            'address' => 'Krishnaswamy Road',
            'city' => 'Coimbatore',
            'state' => 'Tamil Nadu',
            'postal_code' => '641001',
            'latitude' => 11.0168,
            'longitude' => 76.9558,
            'daily_footfall' => 15000,
            'operating_hours' => '10:00 AM - 10:00 PM',
            'description' => 'Top shopping mall',
            'status' => 'active',
            'price_per_day' => 1500.00,
            'images' => [
                UploadedFile::fake()->create('venue1.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('venue2.jpg', 100, 'image/jpeg'),
            ]
        ];

        $response = $this->post(route('admin.locations.store'), $data);

        $response->assertRedirect(route('admin.locations.index'));
        $this->assertDatabaseHas('locations', [
            'name' => 'Brookefields Mall',
            'city' => 'Coimbatore',
        ]);

        $location = Location::first();
        $this->assertNotNull($location->location_code);
        $this->assertNotNull($location->uuid);
        $this->assertNotNull($location->slug);

        $this->assertCount(2, $location->images);
        Storage::disk('public')->assertExists($location->images->first()->image_path);
    }

    /** @test */
    public function location_coordinates_validation_rules()
    {
        $this->actingAs($this->adminUser);

        $data = [
            'name' => 'Brookefields Mall',
            'category_id' => $this->category->id,
            'address' => 'Krishnaswamy Road',
            'city' => 'Coimbatore',
            'state' => 'Tamil Nadu',
            'postal_code' => '641001',
            'latitude' => 'invalid-latitude',
            'longitude' => 200,
            'daily_footfall' => 15000,
            'status' => 'active',
        ];

        $response = $this->post(route('admin.locations.store'), $data);
        $response->assertSessionHasErrors(['latitude', 'longitude']);
    }

    /** @test */
    public function admin_can_update_location_and_manage_images()
    {
        $this->actingAs($this->adminUser);
        Storage::fake('public');

        $location = Location::factory()->create([
            'category_id' => $this->category->id,
            'status' => 'active',
        ]);

        // Upload initial image
        $image = UploadedFile::fake()->create('initial.jpg', 100, 'image/jpeg');
        $location->images()->create([
            'image_path' => $image->store('locations', 'public'),
            'display_order' => 0,
            'is_primary' => true,
        ]);

        $imageId = $location->images->first()->id;

        $partner = \App\Models\LocationPartnerProfile::create([
            'company_name' => 'Test Partner',
            'contact_person' => 'John Partner',
            'email' => 'partnerupdate@test.com',
            'phone' => '1234567891',
            'partner_code' => 'LP-00002',
        ]);

        $updateData = [
            'name' => 'Updated Mall Name',
            'category_id' => $this->category->id,
            'location_partner_id' => $partner->id,
            'address' => $location->address,
            'city' => 'Chennai',
            'state' => $location->state,
            'postal_code' => $location->postal_code,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'daily_footfall' => 25000,
            'status' => 'inactive',
            'price_per_day' => 2000.00,
            'delete_images' => [$imageId],
            'images' => [
                UploadedFile::fake()->create('new.jpg', 100, 'image/jpeg'),
            ]
        ];

        $response = $this->put(route('admin.locations.update', $location->id), $updateData);

        $response->assertRedirect(route('admin.locations.index'));
        
        $location->refresh();
        $this->assertEquals('Updated Mall Name', $location->name);
        $this->assertEquals('Chennai', $location->city);
        $this->assertEquals('inactive', $location->status);

        $this->assertDatabaseMissing('location_images', ['id' => $imageId]);

        $this->assertCount(1, $location->images);
        $this->assertTrue($location->images->first()->is_primary);
    }

    /** @test */
    public function admin_can_soft_delete_location()
    {
        $this->actingAs($this->adminUser);

        $location = Location::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->delete(route('admin.locations.destroy', $location->id));
        $response->assertRedirect(route('admin.locations.index'));

        $this->assertSoftDeleted('locations', [
            'id' => $location->id,
        ]);
    }

    /** @test */
    public function admin_can_view_location_update_requests()
    {
        $this->actingAs($this->adminUser);

        $partner = LocationPartnerProfile::create([
            'company_name' => 'Test Partner',
            'contact_person' => 'John Partner',
            'email' => 'partner@test.com',
            'phone' => '1234567890',
            'partner_code' => 'LP-00001',
        ]);

        $location = Location::factory()->create([
            'category_id' => $this->category->id,
        ]);

        LocationUpdateRequest::create([
            'location_id' => $location->id,
            'partner_id' => $partner->id,
            'request_type' => 'details_edit',
            'requested_value' => json_encode(['name' => 'Requested New Name']),
            'status' => 'pending',
        ]);

        $response = $this->get(route('admin.locations.update-requests'));
        $response->assertStatus(200);
        $response->assertSee('Test Partner');
    }

    /** @test */
    public function admin_can_approve_location_update_request()
    {
        $this->actingAs($this->adminUser);

        $partner = LocationPartnerProfile::create([
            'company_name' => 'Test Partner',
            'contact_person' => 'John Partner',
            'email' => 'partner@test.com',
            'phone' => '1234567890',
            'partner_code' => 'LP-00001',
        ]);

        $location = Location::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Old Name',
        ]);

        $req = LocationUpdateRequest::create([
            'location_id' => $location->id,
            'partner_id' => $partner->id,
            'request_type' => 'details_edit',
            'requested_value' => json_encode(['name' => 'Requested New Name']),
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.locations.update-requests.approve', $req->id));
        $response->assertRedirect(route('admin.locations.update-requests'));

        $this->assertEquals('approved', $req->refresh()->status);
        $this->assertEquals('Requested New Name', $location->refresh()->name);
    }

    /** @test */
    public function admin_can_reject_location_update_request()
    {
        $this->actingAs($this->adminUser);

        $partner = LocationPartnerProfile::create([
            'company_name' => 'Test Partner',
            'contact_person' => 'John Partner',
            'email' => 'partner@test.com',
            'phone' => '1234567890',
            'partner_code' => 'LP-00001',
        ]);

        $location = Location::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Old Name',
        ]);

        $req = LocationUpdateRequest::create([
            'location_id' => $location->id,
            'partner_id' => $partner->id,
            'request_type' => 'details_edit',
            'requested_value' => json_encode(['name' => 'Requested New Name']),
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.locations.update-requests.reject', $req->id), [
            'rejection_reason' => 'Reason for rejection',
        ]);
        
        $response->assertRedirect(route('admin.locations.update-requests'));

        $this->assertEquals('rejected', $req->refresh()->status);
        $this->assertEquals('Reason for rejection', $req->rejection_reason);
        // For details_edit: location record is NOT changed
        $this->assertEquals('Old Name', $location->refresh()->name);
        $this->assertNotEquals('rejected', $location->refresh()->status);
    }

    /** @test */
    public function admin_reject_of_new_location_request_marks_location_as_rejected()
    {
        $this->actingAs($this->adminUser);

        $partner = LocationPartnerProfile::create([
            'company_name' => 'Test Partner',
            'contact_person' => 'John Partner',
            'email' => 'partnernewloc@test.com',
            'phone' => '1234567892',
            'partner_code' => 'LP-00003',
        ]);

        $location = Location::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Pending New Location',
            'status' => 'pending',
            'location_partner_id' => $partner->id,
        ]);

        $req = LocationUpdateRequest::create([
            'location_id' => $location->id,
            'partner_id' => $partner->id,
            'request_type' => 'new_location',
            'requested_value' => json_encode(['name' => 'Pending New Location', 'city' => 'Coimbatore']),
            'status' => 'pending',
        ]);

        $response = $this->post(route('admin.locations.update-requests.reject', $req->id), [
            'rejection_reason' => 'Location does not meet criteria.',
        ]);

        $response->assertRedirect(route('admin.locations.update-requests'));

        // The request is marked rejected
        $this->assertEquals('rejected', $req->refresh()->status);

        // The location itself is also marked rejected with the reason
        $location->refresh();
        $this->assertEquals('rejected', $location->status);
        $this->assertEquals('Location does not meet criteria.', $location->rejection_reason);
    }
}

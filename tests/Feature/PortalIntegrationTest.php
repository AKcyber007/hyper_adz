<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Location;
use App\Models\Screen;
use App\Models\ScreenType;
use App\Models\LocationCategory;
use App\Models\LocationPartnerProfile;
use App\Models\AdvertiserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $partnerAUser;
    protected LocationPartnerProfile $partnerAProfile;
    protected User $partnerBUser;
    protected LocationPartnerProfile $partnerBProfile;
    protected User $advertiserUser;
    protected AdvertiserProfile $advertiserProfile;
    protected LocationCategory $category;
    protected ScreenType $screenType;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles & Permissions
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        // 2. Setup Category, ScreenType & Industry
        $this->category = LocationCategory::create(['name' => 'Mall', 'status' => 'active']);
        $this->screenType = ScreenType::create(['name' => 'LED Video Wall', 'status' => 'active']);
        $industry = \App\Models\Industry::create(['name' => 'Retail', 'status' => 'active']);

        // 3. Create Partner A
        $this->partnerAUser = User::factory()->create();
        $this->partnerAUser->assignRole('location_partner');
        $this->partnerAProfile = LocationPartnerProfile::create([
            'partner_code' => 'LP-00001',
            'user_id' => $this->partnerAUser->id,
            'company_name' => 'Partner A Corp',
            'contact_person' => 'John Partner A',
            'phone' => '9999999991',
            'email' => 'partnera@example.com',
            'status' => 'active',
        ]);

        // 4. Create Partner B
        $this->partnerBUser = User::factory()->create();
        $this->partnerBUser->assignRole('location_partner');
        $this->partnerBProfile = LocationPartnerProfile::create([
            'partner_code' => 'LP-00002',
            'user_id' => $this->partnerBUser->id,
            'company_name' => 'Partner B Corp',
            'contact_person' => 'John Partner B',
            'phone' => '9999999992',
            'email' => 'partnerb@example.com',
            'status' => 'active',
        ]);

        // 5. Create Advertiser
        $this->advertiserUser = User::factory()->create();
        $this->advertiserUser->assignRole('advertiser');
        $this->advertiserProfile = AdvertiserProfile::create([
            'advertiser_code' => 'ADV-00001',
            'user_id' => $this->advertiserUser->id,
            'company_name' => 'Advertiser Corp',
            'contact_person' => 'John Advertiser',
            'phone' => '8888888881',
            'email' => 'advertiser@example.com',
            'status' => 'active',
            'industry_id' => $industry->id,
        ]);
    }

    /**
     * Test Location Partner Dashboard view and Stats.
     */
    public function test_partner_can_access_dashboard_and_views_accurate_stats(): void
    {
        $location = Location::create([
            'location_code' => 'LOC-00001',
            'uuid' => 'uuid-1',
            'name' => 'Location A',
            'slug' => 'location-a',
            'category_id' => $this->category->id,
            'location_partner_id' => $this->partnerAProfile->id,
            'address' => '123 Main St',
            'city' => 'Coimbatore',
            'state' => 'Tamil Nadu',
            'postal_code' => '641001',
            'latitude' => 11.0183,
            'longitude' => 76.9725,
            'status' => 'active',
        ]);

        Screen::create([
            'uuid' => 'uuid-screen-1',
            'screen_code' => 'SCR-00001',
            'location_id' => $location->id,
            'name' => 'Screen A',
            'slug' => 'screen-a',
            'screen_type_id' => $this->screenType->id,
            'orientation' => 'Landscape',
            'status' => 'active',
            'daily_impressions' => 5000,
        ]);

        $response = $this->actingAs($this->partnerAUser, 'location_partner')
            ->get(route('partner.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('John Partner A');
        $response->assertSee('Partner A Corp');
    }

    /**
     * Test Location Partner can create Location which defaults to pending.
     */
    public function test_partner_can_submit_new_location_which_defaults_to_pending(): void
    {
        $response = $this->actingAs($this->partnerAUser, 'location_partner')
            ->post(route('partner.locations.store'), [
                'name' => 'New Mall Venue',
                'category_id' => $this->category->id,
                'address' => '456 Cross Cut Rd',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'postal_code' => '641012',
                'latitude' => 11.0250,
                'longitude' => 76.9800,
                'daily_footfall' => 20000,
                'operating_hours' => '10:00 AM - 10:00 PM',
                'description' => 'Premium shopping spot.',
                'price_per_day' => 1200.00,
            ]);

        $response->assertRedirect(route('partner.locations.index'));
        $this->assertDatabaseHas('locations', [
            'name' => 'New Mall Venue',
            'location_partner_id' => $this->partnerAProfile->id,
            'status' => 'pending'
        ]);
    }

    /**
     * Test Location Partner ownership isolation on Locations.
     */
    public function test_partner_ownership_isolation_on_locations(): void
    {
        // Create location belonging to Partner B
        $locationB = Location::create([
            'location_code' => 'LOC-00002',
            'uuid' => 'uuid-2',
            'name' => 'Location B',
            'slug' => 'location-b',
            'category_id' => $this->category->id,
            'location_partner_id' => $this->partnerBProfile->id,
            'address' => '789 Sathy Rd',
            'city' => 'Coimbatore',
            'state' => 'Tamil Nadu',
            'postal_code' => '641035',
            'latitude' => 11.0552,
            'longitude' => 76.9943,
            'status' => 'active',
        ]);

        // Partner A tries to access Partner B's location page
        $response = $this->actingAs($this->partnerAUser, 'location_partner')
            ->get(route('partner.locations.show', $locationB->id));

        $response->assertStatus(404);

        // Partner A tries to edit Partner B's location
        $response = $this->actingAs($this->partnerAUser, 'location_partner')
            ->put(route('partner.locations.update', $locationB->id), [
                'name' => 'Malicious Edit',
                'category_id' => $this->category->id,
                'address' => 'Modified address',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'postal_code' => '641035',
                'latitude' => 11.0552,
                'longitude' => 76.9943,
                'daily_footfall' => 5000,
            ]);

        $response->assertStatus(404);
    }



    /**
     * Test rejected location update by partner creates a pending review request.
     * The actual location record is NOT changed until admin approves.
     */
    public function test_rejected_location_resubmitted_upon_update(): void
    {
        $location = Location::create([
            'location_code' => 'LOC-00001',
            'uuid' => 'uuid-1',
            'name' => 'Location A',
            'slug' => 'location-a',
            'category_id' => $this->category->id,
            'location_partner_id' => $this->partnerAProfile->id,
            'address' => '123 Main St',
            'city' => 'Coimbatore',
            'state' => 'Tamil Nadu',
            'postal_code' => '641001',
            'latitude' => 11.0183,
            'longitude' => 76.9725,
            'status' => 'rejected',
            'rejection_reason' => 'Invalid latitude',
        ]);

        $response = $this->actingAs($this->partnerAUser, 'location_partner')
            ->put(route('partner.locations.update', $location->id), [
                'name' => 'Location A (Corrected)',
                'category_id' => $this->category->id,
                'address' => '123 Main St',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'postal_code' => '641001',
                'latitude' => 11.0190, // Corrected
                'longitude' => 76.9725,
                'daily_footfall' => 5000,
                'price_per_day' => 1000.00,
                'status' => 'active',
            ]);

        $response->assertRedirect(route('partner.locations.index'));

        // The location record itself must NOT be updated directly — it awaits admin approval.
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Location A',       // unchanged until admin approves
            'status' => 'rejected',        // still rejected
        ]);

        // A pending update request must have been created for admin review.
        $this->assertDatabaseHas('location_update_requests', [
            'location_id' => $location->id,
            'partner_id' => $this->partnerAProfile->id,
            'request_type' => 'details_edit',
            'status' => 'pending',
        ]);
    }

    /**
     * Test Advertiser Dashboard.
     */
    public function test_advertiser_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->advertiserUser, 'advertiser')
            ->get(route('advertiser.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('John Advertiser');
        $response->assertSee('Advertiser Corp');
        $response->assertSee('Go to your Campaign Requests');
    }

    /**
     * Test JSON APIs.
     */
    public function test_partner_and_advertiser_dashboard_json_apis(): void
    {
        // Partner Dashboard API
        $response = $this->actingAs($this->partnerAUser, 'location_partner')
            ->get('/api/partner/dashboard');
            
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'partner' => ['code', 'company_name'],
                'stats' => ['total_locations', 'total_screens']
            ]);

        // Advertiser Dashboard API
        $response = $this->actingAs($this->advertiserUser, 'advertiser')
            ->get('/api/advertiser/dashboard');
            
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'advertiser' => ['code', 'company_name'],
                'stats' => ['total_campaigns', 'active_campaigns']
            ]);
    }
}

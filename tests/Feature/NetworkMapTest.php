<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NetworkMapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database roles, permissions and locations
        $this->seed();
    }

    /**
     * Test that the public /network page is accessible.
     */
    public function test_public_network_page_is_accessible(): void
    {
        $response = $this->get('/network');
        $response->assertStatus(200);
        $response->assertSee('networkMap');
    }

    /**
     * Test that the network locations API returns active locations.
     */
    public function test_api_returns_active_locations(): void
    {
        // Sample location already seeded by LocationSeeder (Brookefields Mall & Prozone Mall)
        $response = $this->getJson('/api/network/locations');

        $response->assertStatus(200);
        
        // Assert JSON structure
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'latitude',
                'longitude',
                'status',
            ]
        ]);

        // Brookefields Mall and Prozone Mall are active, they should be present
        $response->assertJsonFragment([
            'name' => 'Brookefields Mall',
            'latitude' => '11.01830000',
            'longitude' => '76.97250000',
            'status' => 'active',
        ]);

        $response->assertJsonFragment([
            'name' => 'Prozone Mall',
            'latitude' => '11.05520000',
            'longitude' => '76.99430000',
            'status' => 'active',
        ]);

        // Fun Republic Mall is maintenance (not active), so the active locations list should not return it
        $response->assertJsonMissing([
            'name' => 'Fun Republic Mall',
        ]);
    }

    /**
     * Test that Admin users can access the map settings page.
     */
    public function test_admin_can_access_map_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get('/admin/map-settings');
        $response->assertStatus(200);
        $response->assertSee('Map Settings');
        $response->assertSee('11.0168'); // Default Coimbatore coordinate
    }

    /**
     * Test that guest users are redirected to login for map settings.
     */
    public function test_guest_is_redirected_from_map_settings(): void
    {
        $response = $this->get('/admin/map-settings');
        $response->assertRedirect('/login');
    }

    /**
     * Test that location partner cannot access map settings.
     */
    public function test_partner_cannot_access_map_settings(): void
    {
        $partner = User::factory()->create();
        $partner->assignRole('location_partner');

        $response = $this->actingAs($partner)->get('/admin/map-settings');
        $response->assertStatus(403);
    }
}

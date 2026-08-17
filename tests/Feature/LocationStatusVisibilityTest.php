<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use App\Models\AdvertiserProfile;
use App\Models\Campaign;
use App\Models\Industry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 4 — Location Status Visibility Testing
 * Phase 6.3 — Location Visibility Business Rules in Campaign Form
 * Phase 6.4 — No Location Selected Validation
 * Phase 6.5 — Auto-Calculated Cost Accuracy
 */
class LocationStatusVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected Location $activeLocation;
    protected Location $inactiveLocation;
    protected Location $maintenanceLocation;
    protected int $industryId;

    /** All NOT NULL columns per migration */
    private function locationBase(string $name, string $status, int $price): array
    {
        return [
            'name'          => $name,
            'address'       => '123 Test Street, Coimbatore',
            'city'          => 'Coimbatore',
            'state'         => 'Tamil Nadu',
            'postal_code'   => '641001',
            'latitude'      => 11.01,
            'longitude'     => 76.97,
            'status'        => $status,
            'price_per_day' => $price,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Grab a seeded industry (seeder runs before this)
        $industry = Industry::first();
        if (! $industry) {
            $industry = Industry::create(['name' => 'Test Industry', 'slug' => 'test-industry']);
        }
        $this->industryId = $industry->id;

        $this->activeLocation      = Location::create($this->locationBase('Active Test Location',      'active',      500));
        $this->inactiveLocation    = Location::create($this->locationBase('Inactive Test Location',    'inactive',    300));
        $this->maintenanceLocation = Location::create($this->locationBase('Maintenance Test Location', 'maintenance', 400));
    }

    protected function makeAdvertiser(string $suffix): User
    {
        $user = User::factory()->create();
        AdvertiserProfile::create([
            'user_id'         => $user->id,
            'company_name'    => 'Test Co ' . $suffix,
            'advertiser_code' => 'ADV-TST-' . $suffix,
            'industry_id'     => $this->industryId,
            'status'          => 'active',
            'contact_person'  => 'Tester',
            'email'           => $user->email,
            'phone'           => '9000' . $suffix,
        ]);
        return $user;
    }

    // ── Phase 4: Network API returns only active locations ───────────────────

    public function test_active_location_appears_in_network_api(): void
    {
        $ids = collect($this->getJson('/api/network/locations')->assertStatus(200)->json())->pluck('id')->toArray();
        $this->assertContains($this->activeLocation->id, $ids,
            'Active location must appear in /api/network/locations');
    }

    public function test_inactive_location_hidden_from_network_api(): void
    {
        $ids = collect($this->getJson('/api/network/locations')->json())->pluck('id')->toArray();
        $this->assertNotContains($this->inactiveLocation->id, $ids,
            'Inactive location must NOT appear in /api/network/locations');
    }

    public function test_maintenance_location_hidden_from_network_api(): void
    {
        $ids = collect($this->getJson('/api/network/locations')->json())->pluck('id')->toArray();
        $this->assertNotContains($this->maintenanceLocation->id, $ids,
            'Maintenance location must NOT appear in /api/network/locations');
    }

    // ── Phase 4: Admin sees all statuses ────────────────────────────────────

    public function test_admin_sees_all_location_statuses_in_admin_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get('/admin/locations');
        $response->assertStatus(200);
        $response->assertSee($this->activeLocation->name);
        $response->assertSee($this->inactiveLocation->name);
        $response->assertSee($this->maintenanceLocation->name);
    }

    // ── Phase 6.3: Campaign create form only shows active locations ──────────

    public function test_campaign_create_form_shows_only_active_locations(): void
    {
        $user     = $this->makeAdvertiser('001');
        $response = $this->actingAs($user, 'advertiser')->get('/advertiser/requests/create');

        $response->assertStatus(200);
        $response->assertSee($this->activeLocation->name);
        $response->assertDontSee($this->inactiveLocation->name);
        $response->assertDontSee($this->maintenanceLocation->name);
    }

    // ── Phase 6.4: No location selected — server-side validation ────────────

    public function test_campaign_fails_validation_when_no_location_selected(): void
    {
        $user = $this->makeAdvertiser('002');

        $response = $this->actingAs($user, 'advertiser')
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
            ->post('/advertiser/requests', [
                'campaign_name' => 'Zero Location Campaign',
                'start_date'    => now()->addDay()->format('Y-m-d'),
                'end_date'      => now()->addDays(7)->format('Y-m-d'),
                'action'        => 'submit',
            ]);

        $response->assertSessionHasErrors('locations');
        $this->assertDatabaseMissing('campaigns', [
            'campaign_name' => 'Zero Location Campaign',
        ]);
    }

    // ── Phase 6.5: Auto cost = price_per_day × duration ─────────────────────

    public function test_campaign_total_cost_is_calculated_correctly(): void
    {
        $user = $this->makeAdvertiser('003');

        // price_per_day = 500, 7 days (2026-09-01 to 2026-09-07) → expected = 3500
        $this->actingAs($user, 'advertiser')
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
            ->post('/advertiser/requests', [
                'campaign_name' => 'Cost Calc Test',
                'start_date'    => '2026-09-01',
                'end_date'      => '2026-09-07',
                'locations'     => [$this->activeLocation->id],
                'action'        => 'draft',
            ]);

        $campaign = Campaign::where('campaign_name', 'Cost Calc Test')->first();
        $this->assertNotNull($campaign, 'Campaign draft must be saved in DB');
        $this->assertEquals(3500.00, (float) $campaign->budget,
            'Total cost must be price_per_day(500) x days(7) = 3500');
    }
}

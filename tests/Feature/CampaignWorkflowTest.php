<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\AdvertiserProfile;
use App\Models\Location;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CampaignWorkflowTest extends TestCase
{
    use RefreshDatabase; 

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_advertiser_campaign_workflow()
    {
        $advertiserUser = User::factory()->create();
        $industry = \App\Models\Industry::first() ?? \App\Models\Industry::create(['name' => 'Test', 'slug' => 'test']);
        $advertiserProfile = AdvertiserProfile::create([
            'user_id' => $advertiserUser->id,
            'company_name' => 'Test Company',
            'advertiser_code' => 'ADV-001',
            'industry_id' => $industry->id,
            'status' => 'active',
            'contact_person' => 'John Doe',
            'email' => 'john@test.com',
            'phone' => '1234567890'
        ]);

        // 2. Find an active location
        $location = Location::where('status', 'active')->first();
        if (!$location) {
            $location = Location::create([
                'name' => 'Test Location',
                'address' => '123 Test Street, Coimbatore',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'postal_code' => '641001',
                'latitude' => 11.01,
                'longitude' => 76.97,
                'status' => 'active',
                'price_per_day' => 100
            ]);
        }
        $this->assertNotNull($location, 'No active location found.');

        // 3. Act as Advertiser (guard: advertiser)
        $this->actingAs($advertiserUser, 'advertiser');

        // 4. Test Create Campaign Page
        $response = $this->get(route('advertiser.my-requests.create'));
        $response->assertStatus(200);

        // 5. Test Submit Draft Campaign
        $payload = [
            'campaign_name' => 'Test Campaign Draft',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'locations' => [$location->id],
            'action' => 'draft',
            '_token' => csrf_token(),
        ];

        // Need to bypass CSRF for API-like POST request in testing
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->post(route('advertiser.my-requests.store'), $payload);
        
        $campaign = Campaign::where('campaign_name', 'Test Campaign Draft')->orderBy('id', 'desc')->first();
        $this->assertNotNull($campaign, 'Campaign draft was not created. Error: ' . $response->getContent());
        $this->assertEquals('Draft', $campaign->status);

        // 6. Test Update Draft -> Submit
        $updatePayload = [
            'campaign_name' => 'Test Campaign Submitted',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'locations' => [$location->id],
            'action' => 'submit',
        ];
        
        $response = $this->post(route('advertiser.my-requests.update', $campaign->id), $updatePayload);
        $campaign->refresh();
        $this->assertEquals('Submitted', $campaign->status);

        // 7. Logout advertiser, then create Admin
        Auth::logout();
        $admin = User::factory()->create();
        
        // 8. Act as Admin (forces guard to web, so role resolves correctly)
        $this->actingAs($admin, 'web');
        $admin->assignRole('Admin');

        // 9. Admin view campaigns list
        $response = $this->get('/admin/advertising/requests');
        $response->assertStatus(200);

        // 10. Admin view specific campaign
        $response = $this->get('/admin/advertising/requests/' . $campaign->id);
        $response->assertStatus(200);

        // 11. Admin approve campaign
        $response = $this->post('/admin/advertising/requests/' . $campaign->id . '/approve');
        $campaign->refresh();
        $this->assertEquals('Payment Pending', $campaign->status);
        
        // 12. Cleanup
        $campaign->activityLogs()->delete();
        $campaign->locations()->detach();
        $campaign->forceDelete();
        
        $this->assertTrue(true);
    }
}

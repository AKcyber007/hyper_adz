<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeadCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Spatie role and permission
        $manageLeads = Permission::create(['name' => 'manage-leads']);
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo($manageLeads);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function guests_cannot_access_admin_leads_endpoints()
    {
        $this->get(route('admin.leads.index'))->assertRedirect(route('login'));
        $this->get(route('admin.leads.dashboard'))->assertRedirect(route('login'));
    }

    /** @test */
    public function users_without_permission_cannot_access_leads_endpoints()
    {
        $this->actingAs($this->regularUser);

        $this->get(route('admin.leads.index'))->assertStatus(403);
        $this->get(route('admin.leads.dashboard'))->assertStatus(403);
    }

    /** @test */
    public function public_user_can_submit_contact_enquiry_via_api()
    {
        $data = [
            'name'      => 'John Doe',
            'phone'     => '+919988776655',
            'email'     => 'john.doe@example.com',
            'lead_type' => 'contact',
            'message'   => 'I want to ask about pricing.',
            'source'    => 'contact_form'
        ];

        $response = $this->postJson(route('leads.store'), $data);

        $response->assertStatus(201);
        $lead = Lead::latest('id')->first();

        $response->assertJson([
            'success'   => true,
            'lead_code' => $lead->lead_code
        ]);

        $this->assertDatabaseHas('leads', [
            'name'      => 'John Doe',
            'lead_code' => $lead->lead_code,
            'lead_type' => 'contact',
            'status'    => 'new'
        ]);

        // Verify activity log is written
        $lead = Lead::first();
        $this->assertDatabaseHas('activity_logs', [
            'entity_type' => Lead::class,
            'entity_id'   => $lead->id,
            'action'      => 'created'
        ]);
    }

    /** @test */
    public function public_user_can_submit_partner_enquiry_via_api()
    {
        $data = [
            'name'         => 'Jane Partner',
            'phone'        => '+919900990099',
            'email'        => 'jane.partner@example.com',
            'lead_type'    => 'location_partner',
            'company_name' => 'Partner Cafe',
            'message'      => 'Venue Type: Restaurant / Cafe / Bakery\nDaily Footfall: 150 – 500\nAccommodate: 2 screens',
            'source'       => 'partner_page'
        ];

        $response = $this->postJson(route('leads.store'), $data);

        $response->assertStatus(201);
        $lead = Lead::latest('id')->first();

        $response->assertJson([
            'success'   => true,
            'lead_code' => $lead->lead_code
        ]);

        $this->assertDatabaseHas('leads', [
            'name'         => 'Jane Partner',
            'company_name' => 'Partner Cafe',
            'lead_type'    => 'location_partner',
            'status'       => 'new'
        ]);
    }

    /** @test */
    public function admin_can_view_leads_index_and_details()
    {
        $this->actingAs($this->adminUser);

        $lead = Lead::create([
            'lead_type' => 'advertiser',
            'name'      => 'Ad Lead',
            'phone'     => '9876543210',
            'email'     => 'ad.lead@example.com',
            'source'    => 'advertise_page'
        ]);

        $this->get(route('admin.leads.index'))->assertStatus(200)->assertSee('Ad Lead');
        $this->get(route('admin.leads.show', $lead->id))->assertStatus(200)->assertSee($lead->lead_code);
    }

    /** @test */
    public function admin_can_assign_lead_to_self()
    {
        $this->actingAs($this->adminUser);

        $lead = Lead::create([
            'lead_type' => 'contact',
            'name'      => 'John Assignee',
            'phone'     => '9876543210',
            'email'     => 'john.assign@example.com',
        ]);

        $response = $this->post(route('admin.leads.assign', $lead->id));
        $response->assertRedirect();

        $lead->refresh();
        $this->assertEquals($this->adminUser->id, $lead->assigned_admin_id);

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $this->adminUser->id,
            'entity_id'   => $lead->id,
            'action'      => 'updated',
        ]);
    }

    /** @test */
    public function admin_can_update_lead_remarks_and_status()
    {
        $this->actingAs($this->adminUser);

        $lead = Lead::create([
            'lead_type' => 'contact',
            'name'      => 'John Flow',
            'phone'     => '9876543210',
            'email'     => 'john.flow@example.com',
        ]);

        // Update status
        $this->put(route('admin.leads.updateStatus', $lead->id), ['status' => 'contacted'])->assertRedirect();
        
        // Add remarks
        $this->post(route('admin.leads.remarks', $lead->id), ['remarks' => 'Admin remarked something.'])->assertRedirect();

        $lead->refresh();
        $this->assertEquals('contacted', $lead->status);
        $this->assertEquals('Admin remarked something.', $lead->remarks);
    }

    /** @test */
    public function admin_can_approve_lead()
    {
        $this->actingAs($this->adminUser);

        $lead = Lead::create([
            'lead_type' => 'advertiser',
            'name'      => 'Approved Lead',
            'phone'     => '9876543210',
            'email'     => 'approved.lead@example.com',
        ]);

        $response = $this->post(route('admin.leads.approve', $lead->id));
        $response->assertRedirect();

        $lead->refresh();
        $this->assertEquals('approved', $lead->status);
        $this->assertNotNull($lead->approved_at);
        $this->assertNull($lead->rejected_at);

        $this->assertDatabaseHas('activity_logs', [
            'entity_id' => $lead->id,
            'action'    => 'approved',
        ]);
    }

    /** @test */
    public function admin_can_reject_lead()
    {
        $this->actingAs($this->adminUser);

        $lead = Lead::create([
            'lead_type' => 'advertiser',
            'name'      => 'Rejected Lead',
            'phone'     => '9876543210',
            'email'     => 'rejected.lead@example.com',
        ]);

        $response = $this->post(route('admin.leads.reject', $lead->id));
        $response->assertRedirect();

        $lead->refresh();
        $this->assertEquals('rejected', $lead->status);
        $this->assertNotNull($lead->rejected_at);
        $this->assertNull($lead->approved_at);

        $this->assertDatabaseHas('activity_logs', [
            'entity_id' => $lead->id,
            'action'    => 'rejected',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin']);
    }

    public function test_guest_cannot_access_admin_profile(): void
    {
        $response = $this->get('/admin/profile');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/profile');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get('/admin/profile');

        $response->assertOk();
    }
}

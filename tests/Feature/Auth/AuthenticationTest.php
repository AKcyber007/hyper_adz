<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('admin.login.verify'));
        $this->assertEquals($user->email, session('admin_login_email'));
    }

    public function test_users_without_admin_role_cannot_authenticate(): void
    {
        $user = User::factory()->create(); // Has no Admin role by default

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'any-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_suspended_users_cannot_authenticate(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $user = User::factory()->create(['status' => 'suspended']);
        $user->assignRole('Admin');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'any-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}

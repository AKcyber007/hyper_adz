<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RolePermissionService;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database to set up roles and permissions
        $this->seed();
    }

    /**
     * Test that the default roles exist in the database.
     */
    public function test_default_roles_exist(): void
    {
        $this->assertTrue(Role::where('name', 'Admin')->exists());
        $this->assertTrue(Role::where('name', 'location_partner')->exists());
        $this->assertTrue(Role::where('name', 'Advertiser')->exists());
    }

    /**
     * Test that the default permissions exist in the database.
     */
    public function test_default_permissions_exist(): void
    {
        $permissions = [
            'manage-users',
            'manage-location-partners',
            'manage-advertisers',
            'manage-locations',
            'manage-screens',
            'manage-campaigns',
            'manage-reports',
            'manage-cms',
        ];

        foreach ($permissions as $permission) {
            $this->assertTrue(Permission::where('name', $permission)->exists(), "Permission {$permission} does not exist.");
        }
    }

    /**
     * Test that the seeded Admin user has the Admin role and all permissions.
     */
    public function test_admin_user_has_admin_role_and_permissions(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@hyperadz.in');
        $adminUser = User::where('email', $adminEmail)->first();

        $this->assertNotNull($adminUser);
        $this->assertTrue($adminUser->hasRole('Admin'));
        $this->assertTrue($adminUser->can('manage-users'));
        $this->assertTrue($adminUser->can('manage-location-partners'));
    }

    /**
     * Test that Admin users can access protected admin routes.
     */
    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertOk();
    }

    /**
     * Test that Partner users cannot access protected admin routes.
     */
    public function test_partner_cannot_access_admin_routes(): void
    {
        $partner = User::factory()->create();
        $partner->assignRole('location_partner');

        $response = $this->actingAs($partner)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test that Advertiser users cannot access protected admin routes.
     */
    public function test_advertiser_cannot_access_admin_routes(): void
    {
        $advertiser = User::factory()->create();
        $advertiser->assignRole('Advertiser');

        $response = $this->actingAs($advertiser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test that the RoleRepository performs assignment operations correctly.
     */
    public function test_role_repository_operations(): void
    {
        $roleRepo = app(RoleRepositoryInterface::class);
        $user = User::factory()->create();

        $roleRepo->assignRole($user, 'location_partner');
        $this->assertTrue($user->refresh()->hasRole('location_partner'));

        $roleRepo->removeRole($user, 'location_partner');
        $this->assertFalse($user->refresh()->hasRole('location_partner'));
    }

    /**
     * Test that the PermissionRepository operates correctly.
     */
    public function test_permission_repository_operations(): void
    {
        $permissionRepo = app(PermissionRepositoryInterface::class);
        $roleRepo = app(RoleRepositoryInterface::class);

        $role = $roleRepo->findByName('location_partner');
        $permissionRepo->givePermissionTo($role, 'manage-locations');

        $user = User::factory()->create();
        $user->assignRole('location_partner');

        // Clear Spatie's cache since we just assigned a permission to a role
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assertTrue($user->can('manage-locations'));
    }

    /**
     * Test that the RolePermissionService handles operations correctly.
     */
    public function test_role_permission_service_operations(): void
    {
        $service = app(RolePermissionService::class);
        $user = User::factory()->create();

        // Assign role
        $service->assignRoleToUser($user, 'Advertiser');
        $this->assertTrue($service->userHasRole($user, 'Advertiser'));

        // Assign permission to role and verify
        $service->syncPermissionsToRole('Advertiser', ['manage-reports']);
        
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assertTrue($service->userHasPermission($user, 'manage-reports'));
    }
}

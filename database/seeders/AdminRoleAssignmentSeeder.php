<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminRoleAssignmentSeeder extends Seeder
{
    protected RolePermissionService $service;

    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Get all permission names
        $permissionNames = \Spatie\Permission\Models\Permission::pluck('name')->toArray();

        // 2. Sync all permissions to the Admin role
        $this->service->syncPermissionsToRole('Admin', $permissionNames);

        // 3. Find or create the Super Admin user
        $adminEmail = env('ADMIN_EMAIL', 'admin@hyperadz.in');
        $adminName = env('ADMIN_NAME', 'Super Admin');

        $adminUser = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
            ]
        );

        // 4. Assign the Admin role to the Super Admin user
        $this->service->assignRoleToUser($adminUser, 'Admin');
    }
}

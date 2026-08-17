<?php

namespace Database\Seeders;

use App\Services\RolePermissionService;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
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
        $permissions = [
            'manage-users',
            'manage-location-partners',
            'manage-advertisers',
            'manage-locations',
            'manage-screens',
            'manage-leads',
            'manage-campaigns',
            'manage-reports',
            'manage-cms',
        ];

        foreach ($permissions as $permission) {
            // Only create if it doesn't already exist to avoid errors in subsequent seeding
            if (!\Spatie\Permission\Models\Permission::where('name', $permission)->exists()) {
                $this->service->createPermission($permission);
            }
        }
    }
}

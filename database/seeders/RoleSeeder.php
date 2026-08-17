<?php

namespace Database\Seeders;

use App\Services\RolePermissionService;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
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
        $roles = ['Admin', 'location_partner', 'Advertiser', 'advertiser'];

        foreach ($roles as $role) {
            // Only create if it doesn't already exist to avoid errors in subsequent seeding
            if (!\Spatie\Permission\Models\Role::where('name', $role)->exists()) {
                $this->service->createRole($role);
            }
        }
    }
}

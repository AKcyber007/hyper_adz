<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
      * Seed the application's database.
      */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminRoleAssignmentSeeder::class,
            IndustrySeeder::class,
            LocationCategorySeeder::class,
            LocationSeeder::class,
            ScreenTypeSeeder::class,
            ScreenSeeder::class,
            FaqSeeder::class,
            BlogSeeder::class,
        ]);
    }
}

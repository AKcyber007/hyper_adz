<?php

namespace Database\Seeders;

use App\Models\LocationCategory;
use Illuminate\Database\Seeder;

class LocationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Mall', 'icon' => 'bi-building'],
            ['name' => 'Apartment', 'icon' => 'bi-houses'],
            ['name' => 'Theatre', 'icon' => 'bi-film'],
            ['name' => 'Retail Outlet', 'icon' => 'bi-cart'],
            ['name' => 'Restaurant', 'icon' => 'bi-egg-fried'],
            ['name' => 'Cafe', 'icon' => 'bi-cup-hot'],
            ['name' => 'Gym', 'icon' => 'bi-activity'],
            ['name' => 'Salon', 'icon' => 'bi-scissors'],
            ['name' => 'Hospital', 'icon' => 'bi-heart-pulse'],
            ['name' => 'Corporate Office', 'icon' => 'bi-briefcase'],
            ['name' => 'Other', 'icon' => 'bi-geo-alt'],
        ];

        foreach ($categories as $cat) {
            LocationCategory::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }
    }
}

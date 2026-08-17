<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\LocationCategory;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mallCat = LocationCategory::where('name', 'Mall')->first();

        $locations = [
            [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'location_code' => 'LOC-00001',
                'name' => 'Brookefields Mall',
                'latitude' => 11.0183,
                'longitude' => 76.9725,
                'category_id' => $mallCat ? $mallCat->id : null,
                'address' => '67-71, Krishnaswamy Road, Coimbatore Central',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'postal_code' => '641001',
                'daily_footfall' => 15000,
                'operating_hours' => '10:00 AM - 10:00 PM',
                'description' => 'A premier shopping and entertainment mall in the heart of Coimbatore.',
                'status' => Location::STATUS_ACTIVE,
            ],
            [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'location_code' => 'LOC-00002',
                'name' => 'Prozone Mall',
                'latitude' => 11.0552,
                'longitude' => 76.9943,
                'category_id' => $mallCat ? $mallCat->id : null,
                'address' => 'Sathy Road, Saravanampatti',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'postal_code' => '641035',
                'daily_footfall' => 12000,
                'operating_hours' => '11:00 AM - 10:00 PM',
                'description' => 'One of the largest retail shopping malls in Coimbatore offering multiple screen advertising nodes.',
                'status' => Location::STATUS_ACTIVE,
            ],
            [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'location_code' => 'LOC-00003',
                'name' => 'Fun Republic Mall',
                'latitude' => 11.0245,
                'longitude' => 77.0106,
                'category_id' => $mallCat ? $mallCat->id : null,
                'address' => 'Avinashi Road, Peelamedu',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'postal_code' => '641004',
                'daily_footfall' => 8000,
                'operating_hours' => '10:00 AM - 09:30 PM',
                'description' => 'Popular hangout destination near major universities and corporate parks.',
                'status' => Location::STATUS_MAINTENANCE,
            ],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}

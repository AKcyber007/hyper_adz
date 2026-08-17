<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\LocationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Center',
            'category_id' => LocationCategory::factory(),
            'address' => $this->faker->streetAddress(),
            'city' => 'Coimbatore',
            'state' => 'Tamil Nadu',
            'postal_code' => $this->faker->postcode(),
            'latitude' => $this->faker->latitude(10.95, 11.08),
            'longitude' => $this->faker->longitude(76.90, 77.05),
            'daily_footfall' => $this->faker->numberBetween(1000, 30000),
            'operating_hours' => '10:00 AM - 10:00 PM',
            'description' => $this->faker->paragraph(),
            'status' => 'active',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\LocationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LocationCategory>
 */
class LocationCategoryFactory extends Factory
{
    protected $model = LocationCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' Venue',
            'icon' => 'bi-building',
            'status' => 'active',
        ];
    }
}

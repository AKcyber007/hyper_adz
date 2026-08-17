<?php

namespace Database\Factories;

use App\Models\ScreenType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScreenType>
 */
class ScreenTypeFactory extends Factory
{
    protected $model = ScreenType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' Screen Type',
            'description' => $this->faker->sentence(),
            'status' => 'active',
        ];
    }
}

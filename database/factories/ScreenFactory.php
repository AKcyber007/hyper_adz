<?php

namespace Database\Factories;

use App\Models\Screen;
use App\Models\ScreenType;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Screen>
 */
class ScreenFactory extends Factory
{
    protected $model = Screen::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Display',
            'screen_identifier' => 'SCR-ID-' . $this->faker->unique()->randomNumber(4),
            'location_id' => Location::factory(),
            'screen_type_id' => ScreenType::factory(),
            'description' => $this->faker->sentence(),
            'orientation' => 'Landscape',
            'screen_width' => 1920,
            'screen_height' => 1080,
            'resolution' => '1920x1080',
            'operating_hours' => '10:00 AM - 10:00 PM',
            'daily_impressions' => $this->faker->numberBetween(1000, 10000),
            'status' => 'active',
            'availability_status' => 'available',
            'supported_formats' => 'MP4,JPG,PNG',
            'max_video_duration' => 15,
        ];
    }
}

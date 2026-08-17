<?php

namespace Database\Seeders;

use App\Models\ScreenType;
use Illuminate\Database\Seeder;

class ScreenTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Indoor LED', 'description' => 'High-resolution indoor digital display.', 'status' => 'active'],
            ['name' => 'Video Wall', 'description' => 'Multi-screen video wall layout.', 'status' => 'active'],
            ['name' => 'Digital Standee', 'description' => 'Vertical lobby standee display.', 'status' => 'active'],
            ['name' => 'LED Kiosk', 'description' => 'Interactive screen kiosk.', 'status' => 'active'],
            ['name' => 'Lift Display', 'description' => 'Elevator advertising display.', 'status' => 'active'],
            ['name' => 'Lobby Screen', 'description' => 'Screen placed in entrance lobby.', 'status' => 'active'],
            ['name' => 'Food Court Screen', 'description' => 'Dining area display panel.', 'status' => 'active'],
            ['name' => 'Retail Screen', 'description' => 'In-store merchandising display.', 'status' => 'active'],
            ['name' => 'Custom', 'description' => 'Custom digital signage installation.', 'status' => 'active'],
        ];

        foreach ($types as $type) {
            ScreenType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}

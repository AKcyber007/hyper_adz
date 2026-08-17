<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Screen;
use App\Models\ScreenType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brookefields = Location::where('name', 'Brookefields Mall')->first();
        $prozone = Location::where('name', 'Prozone Mall')->first();
        $funRepublic = Location::where('name', 'Fun Republic Mall')->first();

        $indoorLed = ScreenType::where('name', 'Indoor LED')->first();
        $videoWall = ScreenType::where('name', 'Video Wall')->first();
        $standee = ScreenType::where('name', 'Digital Standee')->first();

        $adminUser = User::whereHas('roles', function($q) {
            $q->where('name', 'Admin');
        })->first();

        $adminId = $adminUser ? $adminUser->id : null;

        $screens = [];

        if ($brookefields) {
            $screens[] = [
                'location_id' => $brookefields->id,
                'screen_type_id' => $indoorLed->id,
                'name' => 'Main Lobby LED Screen',
                'screen_identifier' => 'BFM-LED-01',
                'description' => 'Main lobby high visibility LED screen at Brookefields Mall.',
                'orientation' => 'Landscape',
                'screen_width' => 1920,
                'screen_height' => 1080,
                'resolution' => '1920x1080',
                'operating_hours' => '10:00 AM - 10:00 PM',
                'daily_impressions' => 8000,
                'status' => 'active',
                'availability_status' => 'available',
                'supported_formats' => 'MP4,JPG,PNG',
                'max_video_duration' => 15,
                'created_by' => $adminId,
            ];

            $screens[] = [
                'location_id' => $brookefields->id,
                'screen_type_id' => $standee->id,
                'name' => 'South Wing Digital Standee',
                'screen_identifier' => 'BFM-DS-02',
                'description' => 'Portrait digital standee located near south wing escalator.',
                'orientation' => 'Portrait',
                'screen_width' => 1080,
                'screen_height' => 1920,
                'resolution' => '1080x1920',
                'operating_hours' => '10:00 AM - 10:00 PM',
                'daily_impressions' => 4500,
                'status' => 'active',
                'availability_status' => 'available',
                'supported_formats' => 'JPG,PNG',
                'max_video_duration' => null,
                'created_by' => $adminId,
            ];
        }

        if ($prozone) {
            $screens[] = [
                'location_id' => $prozone->id,
                'screen_type_id' => $videoWall->id,
                'name' => 'Central Atrium Video Wall',
                'screen_identifier' => 'PRM-VW-01',
                'description' => 'Stunning video wall display in the central atrium.',
                'orientation' => 'Landscape',
                'screen_width' => 3840,
                'screen_height' => 2160,
                'resolution' => '3840x2160',
                'operating_hours' => '11:00 AM - 11:00 PM',
                'daily_impressions' => 12000,
                'status' => 'active',
                'availability_status' => 'available',
                'supported_formats' => 'MP4,JPG',
                'max_video_duration' => 30,
                'created_by' => $adminId,
            ];
        }

        if ($funRepublic) {
            $screens[] = [
                'location_id' => $funRepublic->id,
                'screen_type_id' => $indoorLed->id,
                'name' => 'Food Court LED Panel',
                'screen_identifier' => 'FRM-LED-01',
                'description' => 'Bright LED panel mounted in the food court seating area.',
                'orientation' => 'Landscape',
                'screen_width' => 1920,
                'screen_height' => 1080,
                'resolution' => '1920x1080',
                'operating_hours' => '09:00 AM - 11:00 PM',
                'daily_impressions' => 6000,
                'status' => 'active',
                'availability_status' => 'available',
                'supported_formats' => 'MP4,JPG,PNG',
                'max_video_duration' => 15,
                'created_by' => $adminId,
            ];

            $screens[] = [
                'location_id' => $funRepublic->id,
                'screen_type_id' => $standee->id,
                'name' => 'Elevator lobby Standee',
                'screen_identifier' => 'FRM-DS-02',
                'description' => 'Digital standee near the ground floor elevator lobby.',
                'orientation' => 'Portrait',
                'screen_width' => 1080,
                'screen_height' => 1920,
                'resolution' => '1080x1920',
                'operating_hours' => '09:00 AM - 11:00 PM',
                'daily_impressions' => 3000,
                'status' => 'maintenance',
                'availability_status' => 'occupied',
                'supported_formats' => 'JPG,PNG',
                'max_video_duration' => null,
                'created_by' => $adminId,
            ];
        }

        foreach ($screens as $scr) {
            $screen = Screen::updateOrCreate(
                ['screen_identifier' => $scr['screen_identifier']],
                $scr
            );

            // Seed mock images
            if ($screen->images()->count() === 0) {
                $screen->images()->create([
                    'image_path' => 'placeholders/screen_placeholder.jpg',
                    'display_order' => 0,
                    'is_primary' => true,
                ]);
            }
        }
    }
}

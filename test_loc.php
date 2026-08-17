<?php

use Illuminate\Support\Facades\Validator;

$data = [
    'name' => 'Test Location',
    'category_id' => 1,
    'address' => '123 Test St',
    'city' => 'Test City',
    'state' => 'Test State',
    'postal_code' => '12345',
    'latitude' => 11.0168,
    'longitude' => 76.9558,
    'status' => 'active',
    'price_per_day' => 100,
];

$rules = [
    'name' => 'required|string|max:255',
    'business_name' => 'nullable|string|max:255',
    'category_id' => 'required|exists:location_categories,id',
    'location_partner_id' => 'nullable|exists:location_partner_profiles,id',
    'address' => 'required|string',
    'city' => 'required|string|max:255',
    'state' => 'required|string|max:255',
    'postal_code' => 'required|string|max:20',
    'latitude' => 'required|numeric|between:-90,90',
    'longitude' => 'required|numeric|between:-180,180',
    'description' => 'nullable|string',
    'nearby_places' => 'nullable|string',
    'operating_hours' => 'nullable|string|max:255',
    'status' => 'required|in:active,inactive,maintenance',
    'price_per_day' => 'required|numeric|min:0',
    'audience_count' => 'nullable|integer|min:0',
    'repeats_per_day' => 'nullable|integer|min:0',
    'audience_type' => 'nullable|array',
    'operating_days' => 'nullable|array',
    'opening_time' => 'nullable|date_format:H:i',
    'closing_time' => 'nullable|date_format:H:i',
    'screen_size' => 'nullable|string|max:255',
    'screen_orientation' => 'nullable|string|max:255',
    'video_supported' => 'boolean',
    'audio_supported' => 'boolean',
    'images.*' => 'nullable|image|max:5120',
];

$validator = Validator::make($data, $rules);

if ($validator->fails()) {
    echo "Validation failed:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "Validation passed!\n";
    
    // Now try to save using LocationService
    try {
        $service = app(\App\Services\LocationService::class);
        $location = $service->createLocation($validator->validated(), []);
        echo "Location created successfully with ID: " . $location->id . "\n";
    } catch (\Exception $e) {
        echo "Exception during createLocation:\n";
        echo $e->getMessage() . "\n";
        echo $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

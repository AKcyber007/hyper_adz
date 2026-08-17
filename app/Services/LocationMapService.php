<?php

namespace App\Services;

use App\Repositories\Contracts\LocationRepositoryInterface;

class LocationMapService
{
    /**
     * The location repository instance.
     */
    protected LocationRepositoryInterface $locationRepository;

    /**
     * Create a new service instance.
     */
    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Get and format active location data for map rendering, supports filtering.
     * Only return fields necessary for map rendering to optimize performance.
     *
     * @param array $filters
     * @return array
     */
    public function getFormattedLocationsForMap(array $filters = []): array
    {
        if (!empty($filters)) {
            $locations = $this->locationRepository->paginate(1000, $filters)->items();
        } else {
            $locations = $this->locationRepository->getActiveLocations();
        }

        $showAll = isset($filters['status']) && $filters['status'] === 'all';

        $formatted = [];
        foreach ($locations as $location) {
            // Skip inactive locations on public network map unless status is 'all'
            if (!$showAll && $location->status !== 'active') {
                continue;
            }

            $primaryImage = $location->primary_image;
            $imageUrl = $primaryImage ? $primaryImage->url : asset('images/location-placeholder.png');

            $isFavorited = false;
            if (auth()->check()) {
                $isFavorited = $location->favorites()->where('user_id', auth()->id())->exists();
            }

            $formatted[] = [
                'id' => $location->id,
                'uuid' => $location->uuid,
                'name' => $location->name,
                'slug' => $location->slug,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'status' => $location->status,
                'address' => $location->address,
                'city' => $location->city,
                'state' => $location->state,
                'postal_code' => $location->postal_code,
                'operating_hours' => $location->operating_hours,
                'description' => $location->description,
                'price_per_day' => (float) $location->price_per_day,
                'daily_footfall' => number_format($location->daily_footfall),
                'location_partner_id' => $location->location_partner_id,
                'category_id' => $location->category_id,
                'audience_count' => number_format($location->audience_count ?: 0),
                'repeats_per_day' => $location->repeats_per_day,
                'screen_size' => $location->screen_size,
                'screen_orientation' => $location->screen_orientation,
                'video_supported' => $location->video_supported,
                'audio_supported' => $location->audio_supported,
                'average_rating' => $location->average_rating,
                'reviews_count' => $location->reviews_count,
                'is_favorited' => $isFavorited,
                'images' => $location->images->map(fn($img) => ['id' => $img->id, 'image_path' => $img->image_path, 'is_primary' => $img->is_primary])->toArray(),
                'category' => $location->category ? [
                    'id' => $location->category->id,
                    'name' => $location->category->name,
                    'icon' => $location->category->icon ?? 'bi-geo-alt-fill',
                ] : [
                    'id' => null,
                    'name' => 'Uncategorized',
                    'icon' => 'bi-geo-alt-fill',
                ],
                'primary_image' => $imageUrl,
            ];
        }

        // Sort by favorited first
        usort($formatted, function ($a, $b) {
            if ($a['is_favorited'] && !$b['is_favorited']) {
                return -1;
            } elseif (!$a['is_favorited'] && $b['is_favorited']) {
                return 1;
            }
            return 0; // preserve original order for remaining items
        });

        return $formatted;
    }
}

<?php

namespace App\Services;

use App\Models\Location;
use App\Models\LocationImage;
use App\Repositories\Contracts\LocationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LocationService
{
    protected LocationRepositoryInterface $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Create a new location with optional images.
     *
     * @param array $data
     * @param array $images
     * @return Location
     */
    public function createLocation(array $data, array $images = []): Location
    {
        return DB::transaction(function () use ($data, $images) {
            $location = Location::create($data);

            if (!empty($images)) {
                $this->uploadImages($location, $images);
            }

            return $location;
        });
    }

    /**
     * Update an existing location, handle new images, and delete old images.
     *
     * @param Location $location
     * @param array $data
     * @param array $images
     * @param array $deleteImageIds
     * @param int|null $primaryImageId
     * @return Location
     */
    public function updateLocation(
        Location $location,
        array $data,
        array $images = [],
        array $deleteImageIds = [],
        ?int $primaryImageId = null
    ): Location {
        return DB::transaction(function () use ($location, $data, $images, $deleteImageIds, $primaryImageId) {
            $location->update($data);

            // Handle deletions
            if (!empty($deleteImageIds)) {
                $this->deleteImages($deleteImageIds);
            }

            // Handle uploads
            if (!empty($images)) {
                $this->uploadImages($location, $images);
            }

            // Set primary image
            if ($primaryImageId !== null) {
                // Set all other images is_primary = false
                $location->images()->update(['is_primary' => false]);
                // Set specific image is_primary = true
                LocationImage::where('id', $primaryImageId)->update(['is_primary' => true]);
            }

            return $location;
        });
    }

    /**
     * Delete (soft delete) a location.
     *
     * @param Location $location
     * @return bool|null
     */
    public function deleteLocation(Location $location): ?bool
    {
        return $location->delete();
    }

    /**
     * Upload and store files.
     *
     * @param Location $location
     * @param array $images
     * @return void
     */
    public function uploadImages(Location $location, array $images): void
    {
        // Get current max display order
        $maxOrder = $location->images()->max('display_order') ?? -1;

        foreach ($images as $index => $image) {
            $path = $image->store('locations', 'public');
            
            // Check if this should be primary (if no primary exists yet)
            $hasPrimary = $location->images()->where('is_primary', true)->exists();

            LocationImage::create([
                'location_id' => $location->id,
                'image_path' => $path,
                'display_order' => $maxOrder + $index + 1,
                'is_primary' => !$hasPrimary && $index === 0,
            ]);
        }
    }

    /**
     * Delete files and database entries.
     *
     * @param array $imageIds
     * @return void
     */
    public function deleteImages(array $imageIds): void
    {
        $images = LocationImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            // Delete file from disk
            Storage::disk('public')->delete($image->image_path);
            // Delete database row
            $image->delete();
        }
    }

    /**
     * Reorder images based on an array of display orders.
     *
     * @param array $orders [imageId => displayOrder]
     * @return void
     */
    public function reorderImages(array $orders): void
    {
        foreach ($orders as $id => $order) {
            LocationImage::where('id', $id)->update(['display_order' => (int)$order]);
        }
    }
}

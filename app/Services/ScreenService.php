<?php

namespace App\Services;

use App\Models\Screen;
use App\Models\ScreenImage;
use App\Models\ActivityLog;
use App\Repositories\Contracts\ScreenRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ScreenService
{
    protected ScreenRepositoryInterface $screenRepository;

    public function __construct(ScreenRepositoryInterface $screenRepository)
    {
        $this->screenRepository = $screenRepository;
    }

    /**
     * Create a new screen with optional images.
     *
     * @param array $data
     * @param array $images
     * @return Screen
     */
    public function createScreen(array $data, array $images = []): Screen
    {
        return DB::transaction(function () use ($data, $images) {
            $screen = Screen::create($data);

            if (!empty($images)) {
                $this->uploadImages($screen, $images);
            }

            // Log activity
            $this->logActivity('created', $screen, "Created Screen {$screen->screen_code} ('{$screen->name}')");

            return $screen;
        });
    }

    /**
     * Update an existing screen, handle new images, and delete old images.
     *
     * @param Screen $screen
     * @param array $data
     * @param array $images
     * @param array $deleteImageIds
     * @param int|null $primaryImageId
     * @return Screen
     */
    public function updateScreen(
        Screen $screen,
        array $data,
        array $images = [],
        array $deleteImageIds = [],
        ?int $primaryImageId = null
    ): Screen {
        return DB::transaction(function () use ($screen, $data, $images, $deleteImageIds, $primaryImageId) {
            $oldLocation = $screen->location_id;
            
            $screen->update($data);

            // Handle deletions
            if (!empty($deleteImageIds)) {
                $this->deleteImages($deleteImageIds);
            }

            // Handle uploads
            if (!empty($images)) {
                $this->uploadImages($screen, $images);
            }

            // Set primary image
            if ($primaryImageId !== null) {
                $screen->images()->update(['is_primary' => false]);
                ScreenImage::where('id', $primaryImageId)->update(['is_primary' => true]);
            }

            // Log activity
            $description = "Updated Screen {$screen->screen_code} ('{$screen->name}')";
            if ($oldLocation != $screen->location_id) {
                $description .= ". Reassigned Screen to Location ID {$screen->location_id}";
            }
            $this->logActivity('updated', $screen, $description);

            return $screen;
        });
    }

    /**
     * Delete (soft delete) a screen.
     *
     * @param Screen $screen
     * @return bool|null
     */
    public function deleteScreen(Screen $screen): ?bool
    {
        return DB::transaction(function () use ($screen) {
            $res = $screen->delete();
            $this->logActivity('deleted', $screen, "Deleted Screen {$screen->screen_code} ('{$screen->name}')");
            return $res;
        });
    }

    /**
     * Upload and store files.
     *
     * @param Screen $screen
     * @param array $images
     * @return void
     */
    public function uploadImages(Screen $screen, array $images): void
    {
        $maxOrder = $screen->images()->max('display_order') ?? -1;

        foreach ($images as $index => $image) {
            $path = $image->store('screens', 'public');
            
            $hasPrimary = $screen->images()->where('is_primary', true)->exists();

            ScreenImage::create([
                'screen_id' => $screen->id,
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
        $images = ScreenImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }

    /**
     * Reorder images based on display orders.
     *
     * @param array $orders [imageId => displayOrder]
     * @return void
     */
    public function reorderImages(array $orders): void
    {
        foreach ($orders as $id => $order) {
            ScreenImage::where('id', $id)->update(['display_order' => (int)$order]);
        }
    }

    /**
     * Log actions to activity_logs table.
     *
     * @param string $action
     * @param Screen $screen
     * @param string $description
     * @return void
     */
    protected function logActivity(string $action, Screen $screen, string $description): void
    {
        ActivityLog::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'action' => $action,
            'entity_type' => Screen::class,
            'entity_id' => $screen->id,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}

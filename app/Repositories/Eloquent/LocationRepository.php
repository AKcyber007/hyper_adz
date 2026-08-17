<?php

namespace App\Repositories\Eloquent;

use App\Models\Location;
use App\Repositories\Contracts\LocationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * Get all active locations.
     *
     * @return Collection
     */
    public function getActiveLocations(): Collection
    {
        return Location::with(['category', 'images'])
            ->where('status', Location::STATUS_ACTIVE)
            ->get();
    }

    /**
     * Get all locations.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Location::with(['category', 'images'])->get();
    }

    /**
     * Paginate locations with filtering and search terms.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = Location::with(['category', 'images']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('location_code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find a location by ID with its images.
     *
     * @param int $id
     * @return Location|null
     */
    public function findWithImages(int $id): ?Location
    {
        return Location::with(['category', 'images'])->find($id);
    }

    /**
     * Find a location by its slug with images.
     *
     * @param string $slug
     * @return Location|null
     */
    public function findBySlug(string $slug): ?Location
    {
        return Location::with(['category', 'images'])->where('slug', $slug)->first();
    }

    /**
     * Find a location by its UUID.
     *
     * @param string $uuid
     * @return Location|null
     */
    public function findByUuid(string $uuid): ?Location
    {
        return Location::with(['category', 'images'])->where('uuid', $uuid)->first();
    }

    /**
     * Get all unique cities where active locations exist.
     *
     * @return array
     */
    public function getCities(): array
    {
        return Location::where('status', Location::STATUS_ACTIVE)
            ->distinct()
            ->pluck('city')
            ->filter()
            ->toArray();
    }
}

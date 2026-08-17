<?php

namespace App\Repositories\Contracts;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LocationRepositoryInterface
{
    /**
     * Get all active locations.
     *
     * @return Collection
     */
    public function getActiveLocations(): Collection;

    /**
     * Get all locations.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Paginate locations with filtering and search terms.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * Find a location by ID with its images.
     *
     * @param int $id
     * @return Location|null
     */
    public function findWithImages(int $id): ?Location;

    /**
     * Find a location by its slug with images.
     *
     * @param string $slug
     * @return Location|null
     */
    public function findBySlug(string $slug): ?Location;

    /**
     * Find a location by its UUID.
     *
     * @param string $uuid
     * @return Location|null
     */
    public function findByUuid(string $uuid): ?Location;

    /**
     * Get all unique cities where active locations exist.
     *
     * @return array
     */
    public function getCities(): array;
}

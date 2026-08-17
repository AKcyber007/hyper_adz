<?php

namespace App\Repositories\Contracts;

use App\Models\Screen;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ScreenRepositoryInterface
{
    /**
     * Get all screens.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Get active screens.
     *
     * @return Collection
     */
    public function getActiveScreens(): Collection;

    /**
     * Paginate screens with filtering and search terms.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * Find a screen by ID.
     *
     * @param int $id
     * @return Screen|null
     */
    public function find(int $id): ?Screen;

    /**
     * Find a screen by slug.
     *
     * @param string $slug
     * @return Screen|null
     */
    public function findBySlug(string $slug): ?Screen;

    /**
     * Find a screen by UUID.
     *
     * @param string $uuid
     * @return Screen|null
     */
    public function findByUuid(string $uuid): ?Screen;

    /**
     * Get dashboard metrics.
     *
     * @return array
     */
    public function getDashboardMetrics(): array;
}

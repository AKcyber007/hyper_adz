<?php

namespace App\Repositories\Contracts;

use App\Models\AdvertiserProfile;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdvertiserRepositoryInterface
{
    /**
     * Get paginated advertisers with filters and search query applied.
     */
    public function getPaginatedAdvertisers(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find advertiser by ID.
     */
    public function findById(int $id): ?AdvertiserProfile;

    /**
     * Find advertiser by UUID.
     */
    public function findByUuid(string $uuid): ?AdvertiserProfile;

    /**
     * Retrieve summary dashboard metrics for advertisers.
     */
    public function getDashboardMetrics(): array;
}

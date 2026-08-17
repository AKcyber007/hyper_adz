<?php

namespace App\Repositories\Contracts;

use App\Models\LocationPartnerProfile;
use Illuminate\Pagination\LengthAwarePaginator;

interface LocationPartnerRepositoryInterface
{
    /**
     * Get paginated location partners with filters and search query applied.
     */
    public function getPaginatedPartners(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find partner by ID.
     */
    public function findById(int $id): ?LocationPartnerProfile;

    /**
     * Find partner by UUID.
     */
    public function findByUuid(string $uuid): ?LocationPartnerProfile;

    /**
     * Retrieve summary dashboard metrics for location partners.
     */
    public function getDashboardMetrics(): array;
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeadRepositoryInterface
{
    /**
     * Get paginated leads with filters and search query applied.
     */
    public function getPaginatedLeads(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find lead by ID.
     */
    public function findById(int $id): ?Lead;

    /**
     * Find lead by UUID.
     */
    public function findByUuid(string $uuid): ?Lead;

    /**
     * Retrieve summary dashboard metrics for leads.
     */
    public function getDashboardMetrics(): array;
}

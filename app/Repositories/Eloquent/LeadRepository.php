<?php

namespace App\Repositories\Eloquent;

use App\Models\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadRepository implements LeadRepositoryInterface
{
    /**
     * Get paginated leads with filters and search query applied.
     */
    public function getPaginatedLeads(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Lead::query()->with('assignedAdmin');

        // Apply Search Term
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('lead_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Apply Lead Type filter
        if (!empty($filters['lead_type'])) {
            $query->where('lead_type', $filters['lead_type']);
        }

        // Apply Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply Source filter
        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        // Apply Date Range filter
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find lead by ID.
     */
    public function findById(int $id): ?Lead
    {
        return Lead::with('assignedAdmin')->find($id);
    }

    /**
     * Find lead by UUID.
     */
    public function findByUuid(string $uuid): ?Lead
    {
        return Lead::with('assignedAdmin')->where('uuid', $uuid)->first();
    }

    /**
     * Retrieve summary dashboard metrics for leads.
     */
    public function getDashboardMetrics(): array
    {
        return [
            'total_leads'     => Lead::count(),
            'new_leads'       => Lead::where('status', 'new')->count(),
            'contacted_leads' => Lead::where('status', 'contacted')->count(),
            'qualified_leads' => Lead::where('status', 'qualified')->count(),
            'approved_leads'  => Lead::where('status', 'approved')->count(),
            'rejected_leads'  => Lead::where('status', 'rejected')->count(),
        ];
    }
}

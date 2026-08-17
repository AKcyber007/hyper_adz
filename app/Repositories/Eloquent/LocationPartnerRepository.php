<?php

namespace App\Repositories\Eloquent;

use App\Models\LocationPartnerProfile;
use App\Models\Location;
use App\Models\Screen;
use App\Repositories\Contracts\LocationPartnerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class LocationPartnerRepository implements LocationPartnerRepositoryInterface
{
    /**
     * Get paginated location partners with filters and search query applied.
     */
    public function getPaginatedPartners(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = LocationPartnerProfile::query()->with(['locations', 'lead', 'user']);

        // Apply Search Term
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('partner_code', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        // Apply Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply City filter
        if (!empty($filters['city'])) {
            $query->where('city', 'like', "%{$filters['city']}%");
        }

        // Apply State filter
        if (!empty($filters['state'])) {
            $query->where('state', 'like', "%{$filters['state']}%");
        }

        // Apply Location Assignment state filter
        if (!empty($filters['assignment'])) {
            if ($filters['assignment'] === 'assigned') {
                $query->has('locations');
            } elseif ($filters['assignment'] === 'unassigned') {
                $query->doesntHave('locations');
            }
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
     * Find partner by ID.
     */
    public function findById(int $id): ?LocationPartnerProfile
    {
        return LocationPartnerProfile::with(['locations.screens', 'lead', 'user', 'approvedByUser'])->find($id);
    }

    /**
     * Find partner by UUID.
     */
    public function findByUuid(string $uuid): ?LocationPartnerProfile
    {
        return LocationPartnerProfile::with(['locations.screens', 'lead', 'user', 'approvedByUser'])->where('uuid', $uuid)->first();
    }

    /**
     * Retrieve summary dashboard metrics for location partners.
     */
    public function getDashboardMetrics(): array
    {
        $assignedLocationIds = Location::whereNotNull('location_partner_id')->pluck('id');
        
        return [
            'total_partners'            => LocationPartnerProfile::count(),
            'active_partners'           => LocationPartnerProfile::where('status', 'active')->count(),
            'suspended_partners'        => LocationPartnerProfile::where('status', 'suspended')->count(),
            'total_assigned_locations'  => count($assignedLocationIds),
            'total_assigned_screens'    => Screen::whereIn('location_id', $assignedLocationIds)->count(),
        ];
    }
}

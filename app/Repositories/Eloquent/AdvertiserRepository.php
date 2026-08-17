<?php

namespace App\Repositories\Eloquent;

use App\Models\AdvertiserProfile;
use App\Repositories\Contracts\AdvertiserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvertiserRepository implements AdvertiserRepositoryInterface
{
    /**
     * Get paginated advertisers with filters and search query applied.
     */
    public function getPaginatedAdvertisers(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = AdvertiserProfile::query()->with(['industry', 'lead', 'user']);

        // Apply Search Term
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('advertiser_code', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        // Apply Industry filter
        if (!empty($filters['industry_id'])) {
            $query->where('industry_id', $filters['industry_id']);
        }

        // Apply Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply Lead Source filter
        if (!empty($filters['source'])) {
            $source = $filters['source'];
            $query->whereHas('lead', function ($q) use ($source) {
                $q->where('source', $source);
            });
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
     * Find advertiser by ID.
     */
    public function findById(int $id): ?AdvertiserProfile
    {
        return AdvertiserProfile::with(['industry', 'lead', 'user', 'approvedByUser'])->find($id);
    }

    /**
     * Find advertiser by UUID.
     */
    public function findByUuid(string $uuid): ?AdvertiserProfile
    {
        return AdvertiserProfile::with(['industry', 'lead', 'user', 'approvedByUser'])->where('uuid', $uuid)->first();
    }

    /**
     * Retrieve summary dashboard metrics for advertisers.
     */
    public function getDashboardMetrics(): array
    {
        $now = now();
        return [
            'total_advertisers'           => AdvertiserProfile::count(),
            'active_advertisers'          => AdvertiserProfile::where('status', 'active')->count(),
            'suspended_advertisers'       => AdvertiserProfile::where('status', 'suspended')->count(),
            'new_advertisers_this_month'  => AdvertiserProfile::whereMonth('created_at', $now->month)
                                                ->whereYear('created_at', $now->year)
                                                ->count(),
        ];
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Screen;
use App\Repositories\Contracts\ScreenRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ScreenRepository implements ScreenRepositoryInterface
{
    /**
     * Get all screens.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Screen::with(['location', 'type', 'images'])->get();
    }

    /**
     * Get active screens.
     *
     * @return Collection
     */
    public function getActiveScreens(): Collection
    {
        return Screen::with(['location', 'type', 'images'])
            ->where('status', 'active')
            ->get();
    }

    /**
     * Paginate screens with filtering and search terms.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = Screen::with(['location', 'type', 'images']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('screen_code', 'like', "%{$search}%")
                  ->orWhere('screen_identifier', 'like', "%{$search}%")
                  ->orWhereHas('location', function ($locationQuery) use ($search) {
                      $locationQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (!empty($filters['screen_type_id'])) {
            $query->where('screen_type_id', $filters['screen_type_id']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['orientation'])) {
            $query->where('orientation', $filters['orientation']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find a screen by ID.
     *
     * @param int $id
     * @return Screen|null
     */
    public function find(int $id): ?Screen
    {
        return Screen::with(['location', 'type', 'images'])->find($id);
    }

    /**
     * Find a screen by slug.
     *
     * @param string $slug
     * @return Screen|null
     */
    public function findBySlug(string $slug): ?Screen
    {
        return Screen::with(['location', 'type', 'images'])->where('slug', $slug)->first();
    }

    /**
     * Find a screen by UUID.
     *
     * @param string $uuid
     * @return Screen|null
     */
    public function findByUuid(string $uuid): ?Screen
    {
        return Screen::with(['location', 'type', 'images'])->where('uuid', $uuid)->first();
    }

    /**
     * Get dashboard metrics.
     *
     * @return array
     */
    public function getDashboardMetrics(): array
    {
        return [
            'total_screens' => Screen::count(),
            'active_screens' => Screen::where('status', 'active')->count(),
            'maintenance_screens' => Screen::where('status', 'maintenance')->count(),
            'inactive_screens' => Screen::where('status', 'inactive')->count(),
            'total_daily_impressions' => Screen::where('status', 'active')->sum('daily_impressions'),
            
            // Stats groupings
            'screens_by_type' => Screen::join('screen_types', 'screens.screen_type_id', '=', 'screen_types.id')
                ->selectRaw('screen_types.name, count(screens.id) as count')
                ->groupBy('screen_types.name')
                ->pluck('count', 'name')
                ->toArray(),
                
            'screens_by_location' => Screen::join('locations', 'screens.location_id', '=', 'locations.id')
                ->selectRaw('locations.name, count(screens.id) as count')
                ->groupBy('locations.name')
                ->pluck('count', 'name')
                ->toArray(),
        ];
    }
}

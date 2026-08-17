<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Screen;
use App\Models\LocationPartnerProfile;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    /**
     * Get the active partner profile.
     */
    protected function getPartnerProfile(): LocationPartnerProfile
    {
        return LocationPartnerProfile::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Dashboard statistics overview.
     */
    public function index(): JsonResponse
    {
        $profile = $this->getPartnerProfile();
        
        $locations = $profile->locations;
        $locationIds = $locations->pluck('id')->toArray();
        $screens = Screen::whereIn('location_id', $locationIds)->get();
        
        $stats = [
            'total_locations'    => $locations->count(),
            'approved_locations' => $locations->where('status', 'approved')->count() + $locations->where('status', 'active')->count(),
            'pending_locations'  => $locations->where('status', 'pending')->count(),
            'rejected_locations' => $locations->where('status', 'rejected')->count(),
            
            'total_screens'      => $screens->count(),
            'active_screens'     => $screens->where('status', 'active')->count(),
            'pending_screens'    => $screens->where('status', 'pending')->count(),
            'offline_screens'    => $screens->where('status', 'offline')->count(),
            
            'pending_approvals'  => $locations->where('status', 'pending')->count() + $screens->where('status', 'pending')->count(),
            'total_impressions'  => $screens->sum('daily_impressions')
        ];

        return response()->json([
            'success' => true,
            'partner' => [
                'code' => $profile->partner_code,
                'company_name' => $profile->company_name,
            ],
            'stats'   => $stats
        ]);
    }

    /**
     * List locations assigned to partner.
     */
    public function locations(): JsonResponse
    {
        $profile = $this->getPartnerProfile();
        
        $locations = $profile->locations()
            ->with(['category', 'screens'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success'   => true,
            'locations' => $locations
        ]);
    }

    /**
     * List screens belonging to partner locations.
     */
    public function screens(): JsonResponse
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();
        
        $screens = Screen::whereIn('location_id', $locationIds)
            ->with(['location', 'type'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'screens' => $screens
        ]);
    }
}

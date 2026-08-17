<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(): View
    {
        // Controller Authorization Example:
        // Ensure user has 'manage-users' permission to view the dashboard KPI data.
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'Unauthorized action.');
        }

        $totalLocations = \App\Models\Location::count();
        $activeLocations = \App\Models\Location::where('status', \App\Models\Location::STATUS_ACTIVE)->count();
        $inactiveLocations = \App\Models\Location::where('status', \App\Models\Location::STATUS_INACTIVE)->count();
        $maintenanceLocations = \App\Models\Location::where('status', \App\Models\Location::STATUS_MAINTENANCE)->count();
        
        $categoriesList = \App\Models\LocationCategory::withCount('locations')->get();

        $kpis = [
            'total_advertisers' => \App\Models\AdvertiserProfile::count(),
            'total_location_partners' => \App\Models\LocationPartnerProfile::count(),
            'total_locations'   => $totalLocations,
            'active_locations'   => $activeLocations,
            'inactive_locations' => $inactiveLocations,
            'maintenance_locations' => $maintenanceLocations,
            'total_screens'     => \App\Models\Screen::count(),
            'pending_campaigns' => \App\Models\Campaign::whereIn('status', ['Submitted', 'Creative Review'])->count(),
        ];
        $paidCampaigns = \App\Models\Campaign::with('advertiser.user')
            ->whereNotNull('payment_paid_at')
            ->orderBy('payment_paid_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact('kpis', 'categoriesList', 'paidCampaigns'));
    }
}

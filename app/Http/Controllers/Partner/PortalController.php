<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Campaign;
use App\Models\Screen;
use App\Models\LocationPartnerProfile;
use App\Models\ActivityLog;
use App\Models\LocationUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalController extends Controller
{
    /**
     * Get the active partner profile.
     */
    protected function getPartnerProfile(): LocationPartnerProfile
    {
        return LocationPartnerProfile::where('user_id', Auth::guard('location_partner')->id())->firstOrFail();
    }

    /**
     * Dashboard overview.
     */
    public function dashboard(): View
    {
        $profile = $this->getPartnerProfile();
        
        $locations = $profile->locations;
        $locationIds = $locations->pluck('id')->toArray();
        $screens = Screen::whereIn('location_id', $locationIds)->get();
        
        // Count statuses
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

        // Fetch active campaigns
        $running_campaigns = \App\Models\Campaign::whereHas('locations', function($q) use ($locationIds) {
            $q->whereIn('location_id', $locationIds);
        })->whereIn('status', ['Scheduled', 'Running'])->orderBy('start_date', 'asc')->get();

        $stats['running_campaigns'] = $running_campaigns->count();

        // Activity Logs
        $activities = ActivityLog::where(function ($query) use ($profile, $locationIds, $screens) {
            $query->where(function ($q) use ($locationIds) {
                $q->where('entity_type', Location::class)
                  ->whereIn('entity_id', $locationIds);
            })
            ->orWhere(function ($q) use ($screens) {
                $q->where('entity_type', Screen::class)
                  ->whereIn('entity_id', $screens->pluck('id')->toArray());
            })
            ->orWhere(function ($q) use ($profile) {
                $q->where('entity_type', LocationPartnerProfile::class)
                  ->where('entity_id', $profile->id);
            });
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        return view('partner.dashboard', compact('profile', 'stats', 'activities', 'running_campaigns', 'locations'));
    }

    /**
     * Network Map page.
     */
    public function map(): View
    {
        $profile = $this->getPartnerProfile();
        $locations = $profile->locations()->with('screens')->get();
        $categories = \App\Models\LocationCategory::where('status', 'active')->get();

        return view('partner.map', compact('profile', 'locations', 'categories'));
    }

    /**
     * Location Requests page.
     */
    public function locationRequests(): View
    {
        $profile = $this->getPartnerProfile();
        
        $locations = $profile->locations()->orderBy('updated_at', 'desc')->get();
        
        $updateRequests = LocationUpdateRequest::with('location')
            ->where('partner_id', $profile->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('partner.location-requests', compact('profile', 'locations', 'updateRequests'));
    }

    /**
     * Cancel/Delete a Location Update Request.
     */
    public function cancelLocationRequest($id)
    {
        $profile = $this->getPartnerProfile();
        $request = LocationUpdateRequest::where('partner_id', $profile->id)->findOrFail($id);
        
        $request->delete();
        
        return redirect()->back()->with('success', 'Location update request cancelled successfully.');
    }

    /**
     * Campaign Activity page — running and upcoming campaigns at partner's locations.
     */
    public function campaignActivity(): View
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();

        $running = Campaign::with(['advertiser', 'locations'])
            ->whereHas('locations', fn($q) => $q->whereIn('location_id', $locationIds))
            ->where('status', 'Running')
            ->orderBy('end_date', 'asc')
            ->get();

        $upcoming = Campaign::with(['advertiser', 'locations'])
            ->whereHas('locations', fn($q) => $q->whereIn('location_id', $locationIds))
            ->whereIn('status', ['Scheduled', 'Payment Pending'])
            ->orderBy('start_date', 'asc')
            ->get();

        $completed = Campaign::with(['advertiser', 'locations'])
            ->whereHas('locations', fn($q) => $q->whereIn('location_id', $locationIds))
            ->whereIn('status', ['Completed', 'Report Uploaded'])
            ->orderBy('end_date', 'desc')
            ->limit(20)
            ->get();

        return view('partner.campaign-activity', compact('profile', 'running', 'upcoming', 'completed', 'locationIds'));
    }

    /**
     * Notification logs.
     */
    public function notifications(): View
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();
        $screenIds = Screen::whereIn('location_id', $locationIds)->pluck('id')->toArray();

        $notifications = ActivityLog::where(function ($query) use ($profile, $locationIds, $screenIds) {
            $query->where(function ($q) use ($locationIds) {
                $q->where('entity_type', Location::class)
                  ->whereIn('entity_id', $locationIds);
            })
            ->orWhere(function ($q) use ($screenIds) {
                $q->where('entity_type', Screen::class)
                  ->whereIn('entity_id', $screenIds);
            })
            ->orWhere(function ($q) use ($profile) {
                $q->where('entity_type', LocationPartnerProfile::class)
                  ->where('entity_id', $profile->id);
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('partner.notifications', compact('profile', 'notifications'));
    }

    /**
     * Business profile detail.
     */
    public function profile(): View
    {
        $profile = $this->getPartnerProfile();
        return view('partner.profile', compact('profile'));
    }

    /**
     * Show form to edit Business Profile.
     */
    public function editProfile(): View
    {
        $profile = $this->getPartnerProfile();
        return view('partner.profile-edit', compact('profile'));
    }

    /**
     * Update Business Profile.
     */
    public function updateProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $profile = $this->getPartnerProfile();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:location_partner_profiles,email,' . $profile->id,
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'gst_number' => 'nullable|string|max:50',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($profile->logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('partner_logos', 'public');
        }

        $profile->update($validated);

        return redirect()->route('partner.profile')->with('success', 'Business profile updated successfully.');
    }
}

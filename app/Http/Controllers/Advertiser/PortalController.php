<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\AdvertiserProfile;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Industry;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PortalController extends Controller
{
    /**
     * Get the active advertiser profile.
     */
    protected function getAdvertiserProfile(): AdvertiserProfile
    {
        return AdvertiserProfile::where('user_id', Auth::guard('advertiser')->id())->firstOrFail();
    }

    /**
     * Dashboard overview.
     */
    public function dashboard(): View
    {
        $profile = $this->getAdvertiserProfile();

        // Count real stats
        $stats = [
            'total_campaigns'     => \App\Models\Campaign::where('advertiser_id', $profile->id)->count(),
            'active_campaigns'    => \App\Models\Campaign::where('advertiser_id', $profile->id)->where('status', 'Running')->count(),
            'pending_campaigns'   => \App\Models\Campaign::where('advertiser_id', $profile->id)->whereIn('status', ['Submitted', 'Creative Review', 'Approved', 'Payment Pending'])->count(),
            'scheduled_campaigns' => \App\Models\Campaign::where('advertiser_id', $profile->id)->where('status', 'Scheduled')->count(),
            'completed_campaigns' => \App\Models\Campaign::where('advertiser_id', $profile->id)->whereIn('status', ['Completed', 'Report Uploaded'])->count(),
        ];

        // Recent Notifications
        $activities = ActivityLog::where(function ($query) use ($profile) {
            $query->where(function ($q) use ($profile) {
                $q->where('entity_type', AdvertiserProfile::class)
                  ->where('entity_id', $profile->id);
            })
            ->orWhere(function ($q) use ($profile) {
                $q->where('entity_type', User::class)
                  ->where('entity_id', $profile->user_id);
            });
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        return view('advertiser.dashboard', compact('profile', 'stats', 'activities'));
    }

    /**
     * Map based location booking.
     */
    public function map(): View
    {
        $profile = $this->getAdvertiserProfile();
        $categories = \App\Models\LocationCategory::where('status', 'active')->get();
        $cities = \App\Models\Location::where('status', 'active')->distinct()->pluck('city')->filter()->toArray();
        return view('advertiser.map', compact('profile', 'categories', 'cities'));
    }

    /**
     * My Requests view list (Unified Campaign Tracker).
     */
    public function myRequests(Request $request): View
    {
        $profile = $this->getAdvertiserProfile();
        
        $query = Campaign::with(['industry', 'locations'])
            ->where('advertiser_id', $profile->id)
            ->orderBy('created_at', 'desc');

        $tab = $request->input('tab', 'all');
        
        if ($tab === 'pending') {
            $query->whereIn('status', ['Draft', 'Submitted', 'Creative Review']);
        } elseif ($tab === 'action_required') {
            $query->whereIn('status', ['Approved', 'Payment Pending', 'Rejected (Payment Expired)', 'Rejected (Admin)']);
        } elseif ($tab === 'active') {
            $query->whereIn('status', ['Payment Completed', 'Scheduled', 'Running']);
        } elseif ($tab === 'completed') {
            $query->whereIn('status', ['Completed', 'Report Uploaded']);
        }
            
        $campaigns = $query->get();
            
        return view('advertiser.my-requests', compact('profile', 'campaigns', 'tab'));
    }

    /**
     * Show form to create a new advertising request.
     */
    public function createRequest(): View
    {
        $profile = $this->getAdvertiserProfile();
        $industries = Industry::all();
        $locations = Location::where('status', 'active')->get();
        
        return view('advertiser.my-requests.create', compact('profile', 'industries', 'locations'));
    }

    /**
     * Store a newly created advertising request (Draft or Submit).
     */
    public function storeRequest(Request $request): RedirectResponse
    {
        $isDraft = $request->input('action') === 'draft';
        
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'nullable|string|max:255',
            'industry_id' => 'nullable|exists:industries,id',
            'start_date' => $isDraft ? 'nullable|date' : 'required|date|after:today',
            'end_date' => $isDraft ? 'nullable|date|after_or_equal:start_date' : 'required|date|after_or_equal:start_date',
            'locations' => $isDraft ? 'nullable|array' : 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
            'creative' => 'nullable|file|extensions:mp4,mov,avi,jpg,png,jpeg|max:20480', // Max 20MB
            'action' => 'required|in:draft,submit',
        ]);

        $profile = $this->getAdvertiserProfile();

        $campaign = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $profile) {
            $campaignData = $request->only([
                'campaign_name',
                'start_date',
                'end_date',
            ]);
            
            $campaignData['campaign_type'] = $request->input('campaign_type', 'Custom');
            $campaignData['industry_id'] = $request->input('industry_id', $profile->industry_id);
            
            // Calculate budget based on price_per_day
            $totalRate = 0;
            $days = 0;
            if ($request->filled('start_date') && $request->filled('end_date') && $request->filled('locations')) {
                $days = \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1;
                $selectedLocations = Location::whereIn('id', $request->locations)->get();
                foreach ($selectedLocations as $loc) {
                    $totalRate += $loc->price_per_day;
                }
            }
            $campaignData['budget'] = $days * $totalRate;
            
            $campaignData['advertiser_id'] = $profile->id;
            
            $status = $request->input('action') === 'draft' ? 'Draft' : 'Submitted';
            $campaignData['status'] = $status;
            // approval_status is legacy, but keep it in sync for now
            $campaignData['approval_status'] = $status === 'Submitted' ? 'Pending Review' : 'Draft'; 
            $campaignData['created_by'] = $profile->user_id;
            
            $campaignData['campaign_code'] = 'CAMP-TEMP-' . strtoupper(\Illuminate\Support\Str::random(6));

            if ($request->hasFile('creative')) {
                $file = $request->file('creative');
                $path = $file->store('creatives', 'public');
                $campaignData['creative_path'] = $path;
                $campaignData['creative_name'] = $file->getClientOriginalName();
            }

            $campaign = Campaign::create($campaignData);
            
            $campaign->update([
                'campaign_code' => 'CAMP-' . str_pad($campaign->id, 5, '0', STR_PAD_LEFT)
            ]);

            if ($request->has('locations')) {
                $campaign->locations()->sync($request->locations);
            }

            $campaign->activityLogs()->create([
                'action' => $status === 'Draft' ? 'Saved as Draft' : 'Submitted',
                'performed_by' => $profile->company_name,
                'remarks' => $status === 'Draft' ? 'Campaign saved as a draft.' : 'Advertising campaign submitted for admin approval.',
            ]);

            return $campaign;
        });

        $msg = $request->input('action') === 'draft' 
            ? 'Campaign saved as draft.' 
            : 'Advertising request submitted successfully: ' . $campaign->campaign_code;

        return redirect()->route('advertiser.my-requests')
            ->with('success', $msg);
    }

    /**
     * Show form to edit a Draft campaign.
     */
    public function editRequest(int $id): View
    {
        $profile = $this->getAdvertiserProfile();
        $campaign = Campaign::with('locations')->where('advertiser_id', $profile->id)->findOrFail($id);

        if (!in_array($campaign->status, ['Draft', 'Creative Review', 'Rejected (Admin)'])) {
            abort(403, 'Only Draft, Creative Review, or Rejected campaigns can be edited.');
        }

        $industries = Industry::all();
        $locations = Location::where('status', 'active')->get();
        
        return view('advertiser.my-requests.edit', compact('profile', 'campaign', 'industries', 'locations'));
    }

    /**
     * Update a Draft campaign.
     */
    public function updateRequest(Request $request, int $id): RedirectResponse
    {
        $profile = $this->getAdvertiserProfile();
        $campaign = Campaign::where('advertiser_id', $profile->id)->findOrFail($id);

        if (!in_array($campaign->status, ['Draft', 'Creative Review', 'Rejected (Admin)'])) {
            abort(403, 'Only Draft, Creative Review, or Rejected campaigns can be updated.');
        }

        $isDraft = $request->input('action') === 'draft';
        
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'nullable|string|max:255',
            'industry_id' => 'nullable|exists:industries,id',
            'start_date' => $isDraft ? 'nullable|date' : 'required|date|after:today',
            'end_date' => $isDraft ? 'nullable|date|after_or_equal:start_date' : 'required|date|after_or_equal:start_date',
            'locations' => $isDraft ? 'nullable|array' : 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
            'creative' => 'nullable|file|extensions:mp4,mov,avi,jpg,png,jpeg|max:20480',
            'action' => 'required|in:draft,submit',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $campaign, $profile) {
            $campaignData = $request->only([
                'campaign_name',
                'start_date',
                'end_date',
            ]);
            
            $campaignData['campaign_type'] = $request->input('campaign_type', 'Custom');
            $campaignData['industry_id'] = $request->input('industry_id', $campaign->industry_id);
            
            $totalRate = 0;
            $days = 0;
            if ($request->filled('start_date') && $request->filled('end_date') && $request->filled('locations')) {
                $days = \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1;
                $selectedLocations = Location::whereIn('id', $request->locations)->get();
                foreach ($selectedLocations as $loc) {
                    $totalRate += $loc->price_per_day;
                }
            }
            $campaignData['budget'] = $days * $totalRate;
            
            $status = $request->input('action') === 'draft' ? 'Draft' : 'Submitted';
            $campaignData['status'] = $status;
            $campaignData['approval_status'] = $status === 'Submitted' ? 'Pending Review' : 'Draft'; 
            $campaignData['updated_by'] = $profile->user_id;

            if ($request->hasFile('creative')) {
                $file = $request->file('creative');
                $path = $file->store('creatives', 'public');
                $campaignData['creative_path'] = $path;
                $campaignData['creative_name'] = $file->getClientOriginalName();
            }

            $campaign->update($campaignData);
            if ($request->has('locations')) {
                $campaign->locations()->sync($request->locations);
            }

            $campaign->activityLogs()->create([
                'action' => $status === 'Draft' ? 'Updated Draft' : 'Submitted',
                'performed_by' => $profile->company_name,
                'remarks' => $status === 'Draft' ? 'Draft campaign updated.' : 'Advertising campaign submitted for admin approval.',
            ]);
        });

        $msg = $request->input('action') === 'draft' 
            ? 'Draft updated.' 
            : 'Advertising request submitted successfully.';

        return redirect()->route('advertiser.my-requests')->with('success', $msg);
    }

    /**
     * Resubmit a Rejected campaign.
     */
    public function resubmitRequest(Request $request, int $id): RedirectResponse
    {
        $profile = $this->getAdvertiserProfile();
        $campaign = Campaign::where('advertiser_id', $profile->id)->findOrFail($id);

        if ($campaign->status !== 'Rejected (Admin)') {
            abort(403, 'Only Admin Rejected campaigns can be resubmitted.');
        }

        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'nullable|string|max:255',
            'industry_id' => 'nullable|exists:industries,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
            'creative' => 'nullable|file|extensions:mp4,mov,avi,jpg,png,jpeg|max:20480',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $campaign, $profile) {
            $campaignData = $request->only([
                'campaign_name',
                'start_date',
                'end_date',
            ]);
            
            $campaignData['campaign_type'] = $request->input('campaign_type', 'Custom');
            $campaignData['industry_id'] = $request->input('industry_id', $campaign->industry_id);
            
            $days = \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1;
            $totalRate = 0;
            $selectedLocations = Location::whereIn('id', $request->locations)->get();
            foreach ($selectedLocations as $loc) {
                $totalRate += $loc->price_per_day;
            }
            $campaignData['budget'] = $days * $totalRate;
            
            $campaignData['status'] = 'Submitted';
            $campaignData['approval_status'] = 'Pending Review';
            $campaignData['rejection_reason'] = null;
            $campaignData['rejection_type'] = null;
            $campaignData['updated_by'] = $profile->user_id;

            if ($request->hasFile('creative')) {
                $file = $request->file('creative');
                $path = $file->store('creatives', 'public');
                $campaignData['creative_path'] = $path;
                $campaignData['creative_name'] = $file->getClientOriginalName();
            }

            $campaign->update($campaignData);
            $campaign->locations()->sync($request->locations);

            $campaign->activityLogs()->create([
                'action' => 'Resubmitted',
                'performed_by' => $profile->company_name,
                'remarks' => 'Advertising campaign revised and resubmitted for admin approval.',
            ]);
        });

        return redirect()->route('advertiser.my-requests.show', $campaign->id)
            ->with('success', 'Campaign resubmitted successfully.');
    }

    /**
     * Download the analytics report.
     */
    public function downloadReport(int $id)
    {
        $profile = $this->getAdvertiserProfile();
        $campaign = Campaign::where('advertiser_id', $profile->id)->findOrFail($id);

        if (!$campaign->report_path) {
            abort(404, 'Report not found.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($campaign->report_path, $campaign->report_name);
    }

    /**
     * Show detail of an advertising request.
     */
    public function showRequest(int $id): View
    {
        $profile = $this->getAdvertiserProfile();
        $campaign = Campaign::with(['locations', 'activityLogs', 'industry'])
            ->where('advertiser_id', $profile->id)
            ->findOrFail($id);

        return view('advertiser.my-requests.show', compact('profile', 'campaign'));
    }

    /**
     * Reports view placeholder.
     */
    public function reports(): View
    {
        $profile = $this->getAdvertiserProfile();
        
        $campaigns = Campaign::with(['locations'])
            ->where('advertiser_id', $profile->id)
            ->whereNotNull('report_path')
            ->orderBy('report_uploaded_at', 'desc')
            ->get();
            
        return view('advertiser.reports', compact('profile', 'campaigns'));
    }

    /**
     * Notification center list.
     */
    public function notifications(): View
    {
        $profile = $this->getAdvertiserProfile();

        $notifications = ActivityLog::where(function ($query) use ($profile) {
            $query->where(function ($q) use ($profile) {
                $q->where('entity_type', AdvertiserProfile::class)
                  ->where('entity_id', $profile->id);
            })
            ->orWhere(function ($q) use ($profile) {
                $q->where('entity_type', User::class)
                  ->where('entity_id', $profile->user_id);
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('advertiser.notifications', compact('profile', 'notifications'));
    }

    /**
     * Company Profile page.
     */
    public function profile(): View
    {
        $profile = $this->getAdvertiserProfile()->load('industry');
        return view('advertiser.profile', compact('profile'));
    }

    /**
     * Show form to edit Company Profile.
     */
    public function editProfile(): View
    {
        $profile = $this->getAdvertiserProfile();
        $industries = Industry::all();
        return view('advertiser.profile-edit', compact('profile', 'industries'));
    }

    /**
     * Update Company Profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $profile = $this->getAdvertiserProfile();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:advertiser_profiles,email,' . $profile->id,
            'phone' => 'required|string|max:20',
            'industry_id' => 'nullable|exists:industries,id',
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
            $validated['logo_path'] = $request->file('logo')->store('advertiser_logos', 'public');
        }

        $profile->update($validated);

        return redirect()->route('advertiser.profile')->with('success', 'Business profile updated successfully.');
    }
}

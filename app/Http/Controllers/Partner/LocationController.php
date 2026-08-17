<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\LocationCategory;
use App\Models\LocationPartnerProfile;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Get the active partner profile.
     */
    protected function getPartnerProfile(): LocationPartnerProfile
    {
        return LocationPartnerProfile::where('user_id', Auth::guard('location_partner')->id())->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $profile = $this->getPartnerProfile();
        
        $query = $profile->locations()->with(['category', 'screens']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('location_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $locations = $query->orderBy('name')->paginate(10);

        return view('partner.locations.index', compact('profile', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $profile = $this->getPartnerProfile();
        $categories = LocationCategory::where('status', 'active')->orderBy('name')->get();

        return view('partner.locations.create', compact('profile', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = $this->getPartnerProfile();

        $rules = [
            'name'            => 'required|string|max:255',
            'business_name'   => 'nullable|string|max:255',
            'category_id'     => 'required|exists:location_categories,id',
            'address'         => 'required|string',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'postal_code'     => 'required|string|max:20',
            'latitude'        => 'required|numeric|between:-90,90',
            'longitude'       => 'required|numeric|between:-180,180',
            'operating_hours' => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'nearby_places'   => 'nullable|string',
            'price_per_day'   => 'required|numeric|min:0',
            'audience_count'  => 'nullable|integer|min:0',
            'repeats_per_day' => 'nullable|integer|min:0',
            'audience_type'   => 'nullable|array',
            'operating_days'  => 'nullable|array',
            'opening_time'    => 'nullable|date_format:H:i',
            'closing_time'    => 'nullable|date_format:H:i',
            'screen_size'     => 'nullable|string|max:255',
            'screen_orientation' => 'nullable|string|max:255',
            'video_supported' => 'boolean',
            'audio_supported' => 'boolean',
            'images'          => 'nullable|array',
            'images.*'        => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];

        $data = $request->validate($rules);
        
        // Force status to pending and bind to location partner
        $data['status'] = 'pending';
        $data['location_partner_id'] = $profile->id;
        $data['created_by'] = Auth::guard('location_partner')->id();

        $images = $request->file('images') ?? [];

        try {
            $location = $this->locationService->createLocation($data, $images);

            // Create LocationUpdateRequest for new location request
            \App\Models\LocationUpdateRequest::create([
                'location_id' => $location->id,
                'partner_id' => $profile->id,
                'request_type' => 'new_location',
                'requested_value' => json_encode($location->only([
                    'name', 'business_name', 'category_id', 'address', 'city', 'state', 'postal_code', 
                    'latitude', 'longitude', 'price_per_day', 'description', 'nearby_places',
                    'audience_count', 'repeats_per_day', 'audience_type', 'operating_days',
                    'opening_time', 'closing_time', 'screen_size', 'screen_orientation',
                    'video_supported', 'audio_supported'
                ])),
                'status' => 'pending',
                'notes' => 'New location registration request.',
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Location request submitted successfully.']);
            }

            return redirect()->route('partner.locations.index')
                ->with('success', 'Location request submitted successfully and is pending review.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating location: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Error creating location: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $profile = $this->getPartnerProfile();
        
        // Enforce ownership
        $location = Location::where('location_partner_id', $profile->id)
            ->with(['category', 'images', 'screens.type'])
            ->findOrFail($id);

        $campaigns = \App\Models\Campaign::whereHas('locations', function($q) use ($id) {
            $q->where('location_id', $id);
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('partner.locations.show', compact('profile', 'location', 'campaigns'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $profile = $this->getPartnerProfile();
        
        // Enforce ownership
        $location = Location::where('location_partner_id', $profile->id)
            ->with('images')
            ->findOrFail($id);
            
        $categories = LocationCategory::where('status', 'active')->orderBy('name')->get();

        return view('partner.locations.edit', compact('profile', 'location', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $profile = $this->getPartnerProfile();
        
        // Enforce ownership
        $location = Location::where('location_partner_id', $profile->id)->findOrFail($id);

        $rules = [
            'name'            => 'required|string|max:255',
            'business_name'   => 'nullable|string|max:255',
            'price_per_day'   => 'required|numeric|min:0',
            'operating_hours' => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'nearby_places'   => 'nullable|string',
            'audience_count'  => 'nullable|integer|min:0',
            'repeats_per_day' => 'nullable|integer|min:0',
            'audience_type'   => 'nullable|array',
            'operating_days'  => 'nullable|array',
            'opening_time'    => 'nullable|date_format:H:i',
            'closing_time'    => 'nullable|date_format:H:i',
            'screen_size'     => 'nullable|string|max:255',
            'screen_orientation' => 'nullable|string|max:255',
            'video_supported' => 'boolean',
            'audio_supported' => 'boolean',
            'status'          => 'required|in:active,inactive,maintenance',
            'images'          => 'nullable|array',
            'images.*'        => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images'   => 'nullable|array',
            'delete_images.*' => 'integer|exists:location_images,id',
            'primary_image'   => 'nullable|integer',
        ];

        $data = $request->validate($rules);
        
        // Handle image files by saving to temp folder
        $tempImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('locations/temp', 'public');
                $tempImages[] = $path;
            }
        }

        $deleteImageIds = $request->input('delete_images') ?? [];
        $requestedValue = $data;
        unset($requestedValue['images']);
        unset($requestedValue['delete_images']);
        unset($requestedValue['primary_image']);

        if (!empty($tempImages)) {
            $requestedValue['temp_images'] = $tempImages;
        }
        if (!empty($deleteImageIds)) {
            $requestedValue['delete_images'] = $deleteImageIds;
        }

        try {
            // Create LocationUpdateRequest instead of updating directly
            \App\Models\LocationUpdateRequest::create([
                'location_id' => $location->id,
                'partner_id' => $profile->id,
                'request_type' => 'details_edit',
                'requested_value' => json_encode($requestedValue),
                'status' => 'pending',
                'notes' => 'Update request for location specs / photos.',
            ]);

            return redirect()->route('partner.locations.index')
                ->with('success', 'Location update request submitted successfully for admin review.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error updating location: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $profile = $this->getPartnerProfile();
        
        // Enforce ownership
        $location = Location::where('location_partner_id', $profile->id)->findOrFail($id);

        try {
            $this->locationService->deleteLocation($location);
            return redirect()->route('partner.locations.index')
                ->with('success', 'Location deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('partner.locations.index')
                ->with('error', 'Error deleting location: ' . $e->getMessage());
        }
    }
}

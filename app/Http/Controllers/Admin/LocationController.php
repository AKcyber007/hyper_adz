<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\LocationCategory;
use App\Services\LocationService;
use App\Repositories\Contracts\LocationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\LocationUpdateRequest;

class LocationController extends Controller
{
    protected LocationRepositoryInterface $locationRepository;
    protected LocationService $locationService;

    public function __construct(
        LocationRepositoryInterface $locationRepository,
        LocationService $locationService
    ) {
        $this->locationRepository = $locationRepository;
        $this->locationService = $locationService;
    }

    /**
     * Display a listing of the locations.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'status', 'city']);
        $locations = $this->locationRepository->paginate(10, $filters);
        
        // Eager-load partner profiles to avoid N+1 and enable view access
        $locations->load('locationPartner');

        $categories = LocationCategory::where('status', 'active')->get();
        $cities = Location::distinct()->pluck('city')->filter()->toArray();

        return view('admin.locations.index', compact('locations', 'categories', 'cities'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.locations.map', ['action' => 'create']);
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'category_id' => 'required|exists:location_categories,id',
            'location_partner_id' => 'nullable|exists:location_partner_profiles,id',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
            'nearby_places' => 'nullable|string',
            'daily_footfall' => 'required|integer|min:0',
            'operating_hours' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'price_per_day' => 'required|numeric|min:0',
            'audience_count' => 'nullable|integer|min:0',
            'repeats_per_day' => 'nullable|integer|min:0',
            'audience_type' => 'nullable|array',
            'operating_days' => 'nullable|array',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'screen_size' => 'nullable|string|max:255',
            'screen_orientation' => 'nullable|string|max:255',
            'video_supported' => 'boolean',
            'audio_supported' => 'boolean',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $this->locationService->createLocation($validated, $request->file('images') ?? []);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Show the specified location and its campaigns.
     */
    public function show(int $id): View
    {
        $location = $this->locationRepository->findWithImages($id);
        if (!$location) {
            abort(404, 'Location not found.');
        }

        $campaigns = \App\Models\Campaign::whereHas('locations', function($q) use ($id) {
            $q->where('location_id', $id);
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.locations.show', compact('location', 'campaigns'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(int $id): View
    {
        $location = $this->locationRepository->findWithImages($id);
        if (!$location) {
            abort(404, 'Location not found.');
        }

        $categories = LocationCategory::where('status', 'active')->get();
        return view('admin.locations.edit', compact('location', 'categories'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'category_id' => 'required|exists:location_categories,id',
            'location_partner_id' => 'nullable|exists:location_partner_profiles,id',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
            'nearby_places' => 'nullable|string',
            'daily_footfall' => 'required|integer|min:0',
            'operating_hours' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'price_per_day' => 'required|numeric|min:0',
            'audience_count' => 'nullable|integer|min:0',
            'repeats_per_day' => 'nullable|integer|min:0',
            'audience_type' => 'nullable|array',
            'operating_days' => 'nullable|array',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'screen_size' => 'nullable|string|max:255',
            'screen_orientation' => 'nullable|string|max:255',
            'video_supported' => 'boolean',
            'audio_supported' => 'boolean',
            'images.*' => 'nullable|image|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:location_images,id',
            'primary_image_id' => 'nullable|exists:location_images,id',
        ]);

        $this->locationService->updateLocation(
            $location,
            $validated,
            $request->file('images') ?? [],
            $request->input('delete_images') ?? [],
            $request->input('primary_image_id')
        );

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified location from storage (soft delete).
     */
    public function destroy(int $id): RedirectResponse
    {
        $location = Location::findOrFail($id);
        $this->locationService->deleteLocation($location);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    /**
     * Display all locations on an admin map interface.
     */
    public function map(): View
    {
        $categories = LocationCategory::where('status', 'active')->get();
        $partners = \App\Models\LocationPartnerProfile::orderBy('company_name')->get();
        return view('admin.locations.map', compact('categories', 'partners'));
    }

    /**
     * Display a breakdown of locations by categories.
     */
    public function categories(): View
    {
        $categories = LocationCategory::withCount('locations')->get();
        return view('admin.locations.categories', compact('categories'));
    }

    /**
     * Display a listing of location update requests.
     */
    public function updateRequests(Request $request): View
    {
        $status = $request->input('status', 'pending');
        
        $requests = LocationUpdateRequest::with(['location', 'partner'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.locations.update-requests', compact('requests', 'status'));
    }

    /**
     * Approve a location update request.
     */
    public function approveRequest(int $id): RedirectResponse
    {
        $updateRequest = LocationUpdateRequest::findOrFail($id);
        
        if ($updateRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($updateRequest) {
            $location = $updateRequest->location;

            if ($updateRequest->request_type === 'new_location') {
                $location->update(['status' => 'active']);
            } elseif ($updateRequest->request_type === 'details_edit' || $updateRequest->request_type === 'price_change') {
                $newData = json_decode($updateRequest->requested_value, true);
                if (is_array($newData)) {
                    // Handle temp image updates on approval
                    if (isset($newData['temp_images'])) {
                        foreach ($newData['temp_images'] as $tempPath) {
                            $filename = basename($tempPath);
                            $newPath = 'locations/' . $filename;
                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($tempPath)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->move($tempPath, $newPath);
                                \App\Models\LocationImage::create([
                                    'location_id' => $location->id,
                                    'image_path' => $newPath,
                                    'is_primary' => !$location->images()->where('is_primary', true)->exists(),
                                    'display_order' => $location->images()->max('display_order') + 1,
                                ]);
                            }
                        }
                        unset($newData['temp_images']);
                    }
                    if (isset($newData['delete_images'])) {
                        foreach ($newData['delete_images'] as $delId) {
                            $img = \App\Models\LocationImage::find($delId);
                            if ($img) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
                                $img->delete();
                            }
                        }
                        unset($newData['delete_images']);
                    }
                    $location->update($newData);
                }
            } elseif ($updateRequest->request_type === 'maintenance') {
                $location->update(['status' => Location::STATUS_MAINTENANCE]);
            } elseif ($updateRequest->request_type === 'active') {
                $location->update(['status' => Location::STATUS_ACTIVE]);
            }

            $updateRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.locations.update-requests')
            ->with('success', 'Location update request approved successfully.');
    }

    /**
     * Reject a location update request.
     */
    public function rejectRequest(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $updateRequest = LocationUpdateRequest::findOrFail($id);

        if ($updateRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($updateRequest, $request) {
            $updateRequest->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
            ]);

            // For a new location request: mark the location itself as rejected
            // so partners can see clear feedback and resubmit via the map.
            if ($updateRequest->request_type === 'new_location') {
                $updateRequest->location?->update([
                    'status'           => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                ]);
            }
            // For details_edit / price_change / status changes: the location record
            // remains unchanged — only the pending request is dismissed.
        });

        return redirect()->route('admin.locations.update-requests')
            ->with('success', 'Location update request rejected.');
    }

    /**
     * Store a newly created location from the map.
     */
    public function storeFromMap(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'category_id' => 'required|exists:location_categories,id',
            'location_partner_id' => 'nullable|exists:location_partner_profiles,id',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
            'nearby_places' => 'nullable|string',
            'operating_hours' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'price_per_day' => 'required|numeric|min:0',
            'audience_count' => 'nullable|integer|min:0',
            'repeats_per_day' => 'nullable|integer|min:0',
            'audience_type' => 'nullable|array',
            'operating_days' => 'nullable|array',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'screen_size' => 'nullable|string|max:255',
            'screen_orientation' => 'nullable|string|max:255',
            'video_supported' => 'boolean',
            'audio_supported' => 'boolean',
            'images.*' => 'nullable|image|max:5120',
        ]);

        try {
            $location = $this->locationService->createLocation($validated, $request->file('images') ?? []);
            return response()->json([
                'success' => true,
                'message' => 'Location created successfully.',
                'location' => $location
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing location from the map.
     */
    public function updateFromMap(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'category_id' => 'required|exists:location_categories,id',
            'location_partner_id' => 'nullable|exists:location_partner_profiles,id',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
            'nearby_places' => 'nullable|string',
            'operating_hours' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'price_per_day' => 'required|numeric|min:0',
            'audience_count' => 'nullable|integer|min:0',
            'repeats_per_day' => 'nullable|integer|min:0',
            'audience_type' => 'nullable|array',
            'operating_days' => 'nullable|array',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'screen_size' => 'nullable|string|max:255',
            'screen_orientation' => 'nullable|string|max:255',
            'video_supported' => 'boolean',
            'audio_supported' => 'boolean',
            'images.*' => 'nullable|image|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:location_images,id',
        ]);

        try {
            $this->locationService->updateLocation(
                $location,
                $validated,
                $request->file('images') ?? [],
                $request->input('delete_images') ?? [],
                $request->input('primary_image_id')
            );
            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully.',
                'location' => $location->refresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating location: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use App\Models\ScreenType;
use App\Models\Location;
use App\Models\LocationPartnerProfile;
use App\Services\ScreenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ScreenController extends Controller
{
    protected ScreenService $screenService;

    public function __construct(ScreenService $screenService)
    {
        $this->screenService = $screenService;
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
        $locationIds = $profile->locations->pluck('id')->toArray();
        
        $query = Screen::whereIn('location_id', $locationIds)->with(['location', 'type']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('screen_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $screens = $query->orderBy('name')->paginate(10);

        return view('partner.screens.index', compact('profile', 'screens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $profile = $this->getPartnerProfile();
        $locations = $profile->locations()->whereIn('status', ['active', 'approved'])->orderBy('name')->get();
        $screenTypes = ScreenType::where('status', 'active')->orderBy('name')->get();

        return view('partner.screens.create', compact('profile', 'locations', 'screenTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();

        $rules = [
            'location_id'        => ['required', Rule::in($locationIds)],
            'name'               => 'required|string|max:255',
            'screen_identifier'  => 'nullable|string|max:100|unique:screens,screen_identifier',
            'screen_type_id'     => 'required|exists:screen_types,id',
            'orientation'        => 'required|string|in:Landscape,Portrait',
            'screen_width'       => 'nullable|integer|min:0',
            'screen_height'      => 'nullable|integer|min:0',
            'resolution'         => 'nullable|string|max:50',
            'operating_hours'    => 'nullable|string|max:100',
            'daily_impressions'  => 'required|integer|min:0',
            'supported_formats'  => 'required|string|max:100',
            'max_video_duration' => 'required|integer|min:0',
            'description'        => 'nullable|string',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];

        $data = $request->validate($rules);
        
        // Force status to pending on creation
        $data['status'] = 'pending';
        $data['created_by'] = Auth::guard('location_partner')->id();

        $images = $request->file('images') ?? [];

        try {
            $this->screenService->createScreen($data, $images);
            return redirect()->route('partner.screens.index')
                ->with('success', 'Screen request submitted successfully and is pending review.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error creating screen: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();
        
        // Enforce ownership of parent location
        $screen = Screen::whereIn('location_id', $locationIds)
            ->with(['location', 'type', 'images'])
            ->findOrFail($id);

        return view('partner.screens.show', compact('profile', 'screen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();
        
        // Enforce ownership
        $screen = Screen::whereIn('location_id', $locationIds)
            ->with('images')
            ->findOrFail($id);

        $locations = $profile->locations()->whereIn('status', ['active', 'approved'])->orderBy('name')->get();
        $screenTypes = ScreenType::where('status', 'active')->orderBy('name')->get();

        return view('partner.screens.edit', compact('profile', 'screen', 'locations', 'screenTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();
        
        // Enforce ownership
        $screen = Screen::whereIn('location_id', $locationIds)->findOrFail($id);

        $rules = [
            'location_id'        => ['required', Rule::in($locationIds)],
            'name'               => 'required|string|max:255',
            'screen_identifier'  => ['nullable', 'string', 'max:100', Rule::unique('screens', 'screen_identifier')->ignore($id)],
            'screen_type_id'     => 'required|exists:screen_types,id',
            'orientation'        => 'required|string|in:Landscape,Portrait',
            'screen_width'       => 'nullable|integer|min:0',
            'screen_height'      => 'nullable|integer|min:0',
            'resolution'         => 'nullable|string|max:50',
            'operating_hours'    => 'nullable|string|max:100',
            'daily_impressions'  => 'required|integer|min:0',
            'supported_formats'  => 'required|string|max:100',
            'max_video_duration' => 'required|integer|min:0',
            'description'        => 'nullable|string',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images'      => 'nullable|array',
            'delete_images.*'    => 'integer|exists:screen_images,id',
            'primary_image'      => 'nullable|integer',
            'status'             => 'nullable|string',
        ];

        $data = $request->validate($rules);
        
        // Validate status changes: partners can toggle status, but only between active/offline/maintenance
        // and only if the screen was already approved/active/offline/maintenance (i.e. not pending or rejected).
        if ($request->has('status')) {
            $newStatus = $request->input('status');
            $currentStatus = $screen->status;

            if (in_array($currentStatus, ['pending', 'rejected'])) {
                // If it is rejected, updating it allows resubmitting for review (sets status back to pending)
                if ($currentStatus === 'rejected') {
                    $data['status'] = 'pending';
                    $data['rejection_reason'] = null;
                    $data['rejected_by'] = null;
                    $data['rejected_at'] = null;
                } else {
                    // Do not allow changing a pending screen's status manually
                    unset($data['status']);
                }
            } else {
                // Screen is approved. Validate that new status is one of the health states: active, offline, maintenance.
                if (in_array($newStatus, ['active', 'offline', 'maintenance'])) {
                    $data['status'] = $newStatus;
                } else {
                    unset($data['status']);
                }
            }
        }

        $images = $request->file('images') ?? [];
        $deleteImageIds = $request->input('delete_images') ?? [];
        $primaryImageId = $request->input('primary_image');

        try {
            $this->screenService->updateScreen($screen, $data, $images, $deleteImageIds, $primaryImageId);
            return redirect()->route('partner.screens.index')
                ->with('success', 'Screen updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error updating screen: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $profile = $this->getPartnerProfile();
        $locationIds = $profile->locations->pluck('id')->toArray();
        
        // Enforce ownership
        $screen = Screen::whereIn('location_id', $locationIds)->findOrFail($id);

        try {
            $this->screenService->deleteScreen($screen);
            return redirect()->route('partner.screens.index')
                ->with('success', 'Screen deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('partner.screens.index')
                ->with('error', 'Error deleting screen: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use App\Models\ScreenType;
use App\Models\Location;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\ScreenService;
use App\Repositories\Contracts\ScreenRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ScreenController extends Controller
{
    protected ScreenRepositoryInterface $screenRepository;
    protected ScreenService $screenService;

    public function __construct(
        ScreenRepositoryInterface $screenRepository,
        ScreenService $screenService
    ) {
        $this->screenRepository = $screenRepository;
        $this->screenService = $screenService;
    }

    /**
     * Display a listing of the screens.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'location_id', 'screen_type_id', 'status', 'orientation']);
        $screens = $this->screenRepository->paginate(10, $filters);

        $locations = Location::orderBy('name')->get();
        $screenTypes = ScreenType::where('status', 'active')->orderBy('name')->get();

        return view('admin.screens.index', compact('screens', 'locations', 'screenTypes'));
    }

    /**
     * Show the form for creating a new screen.
     */
    public function create(): View
    {
        $locations = Location::orderBy('name')->get();
        $screenTypes = ScreenType::where('status', 'active')->orderBy('name')->get();
        
        // Fetch users with location_partner role
        $partners = User::whereHas('roles', function($q) {
            $q->where('name', 'location_partner');
        })->orderBy('name')->get();

        return view('admin.screens.create', compact('locations', 'screenTypes', 'partners'));
    }

    /**
     * Store a newly created screen in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'screen_identifier' => 'nullable|string|max:100|unique:screens,screen_identifier',
            'location_id' => 'required|exists:locations,id',
            'screen_type_id' => 'required|exists:screen_types,id',
            'description' => 'nullable|string',
            'orientation' => 'required|in:Landscape,Portrait,Square',
            'screen_width' => 'nullable|integer|min:0',
            'screen_height' => 'nullable|integer|min:0',
            'resolution' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|string|max:255',
            'daily_impressions' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'availability_status' => 'required|in:available,occupied',
            'supported_formats' => 'required|string|max:255',
            'max_video_duration' => 'nullable|integer|min:0',
            'images.*' => 'nullable|file|max:5120',
        ]);

        $images = $request->file('images') ?? [];

        $this->screenService->createScreen($validated, $images);

        return redirect()->route('admin.screens.index')
            ->with('success', 'Screen created successfully.');
    }

    /**
     * Show the form for editing the specified screen.
     */
    public function edit(int $id): View
    {
        $screen = $this->screenRepository->find($id);
        if (!$screen) {
            abort(404);
        }

        $locations = Location::orderBy('name')->get();
        $screenTypes = ScreenType::where('status', 'active')->orderBy('name')->get();
        
        $partners = User::whereHas('roles', function($q) {
            $q->where('name', 'location_partner');
        })->orderBy('name')->get();

        return view('admin.screens.edit', compact('screen', 'locations', 'screenTypes', 'partners'));
    }

    /**
     * Update the specified screen in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $screen = $this->screenRepository->find($id);
        if (!$screen) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'screen_identifier' => 'nullable|string|max:100|unique:screens,screen_identifier,' . $id,
            'location_id' => 'required|exists:locations,id',
            'screen_type_id' => 'required|exists:screen_types,id',
            'description' => 'nullable|string',
            'orientation' => 'required|in:Landscape,Portrait,Square',
            'screen_width' => 'nullable|integer|min:0',
            'screen_height' => 'nullable|integer|min:0',
            'resolution' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|string|max:255',
            'daily_impressions' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'availability_status' => 'required|in:available,occupied',
            'supported_formats' => 'required|string|max:255',
            'max_video_duration' => 'nullable|integer|min:0',
            'images.*' => 'nullable|file|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:screen_images,id',
            'primary_image_id' => 'nullable|integer|exists:screen_images,id',
        ]);

        $images = $request->file('images') ?? [];
        $deleteImageIds = $request->input('delete_images') ?? [];
        $primaryImageId = $request->input('primary_image_id');

        $this->screenService->updateScreen($screen, $validated, $images, $deleteImageIds, $primaryImageId);

        return redirect()->route('admin.screens.index')
            ->with('success', 'Screen updated successfully.');
    }

    /**
     * Remove the specified screen from storage (soft delete).
     */
    public function destroy(int $id): RedirectResponse
    {
        $screen = $this->screenRepository->find($id);
        if (!$screen) {
            abort(404);
        }

        $this->screenService->deleteScreen($screen);

        return redirect()->route('admin.screens.index')
            ->with('success', 'Screen deleted successfully.');
    }

    /**
     * Display screen inventory metrics.
     */
    public function dashboard(): View
    {
        $metrics = $this->screenRepository->getDashboardMetrics();
        return view('admin.screens.dashboard', compact('metrics'));
    }

    /**
     * Display a listing of system activities.
     */
    public function activityLogs(): View
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.screens.activity', compact('logs'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LocationPartnerRepositoryInterface;
use App\Services\LocationPartnerService;
use App\Models\Location;
use App\Models\ActivityLog;
use App\Models\LocationPartnerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class LocationPartnerController extends Controller
{
    protected LocationPartnerRepositoryInterface $partnerRepository;
    protected LocationPartnerService $partnerService;

    public function __construct(
        LocationPartnerRepositoryInterface $partnerRepository,
        LocationPartnerService $partnerService
    ) {
        $this->partnerRepository = $partnerRepository;
        $this->partnerService = $partnerService;
    }

    /**
     * Display a listing of location partners.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'city', 'state', 'assignment', 'start_date', 'end_date', 'search']);
        $partners = $this->partnerRepository->getPaginatedPartners($filters, 15);

        return view('admin.location-partners.index', compact('partners', 'filters'));
    }

    /**
     * Display the location partner analytics dashboard.
     */
    public function dashboard(): View
    {
        $metrics = $this->partnerRepository->getDashboardMetrics();
        return view('admin.location-partners.dashboard', compact('metrics'));
    }

    /**
     * Show detail view of a location partner profile.
     */
    public function show(int $id): View
    {
        $partner = $this->partnerRepository->findById($id);
        if (!$partner) {
            abort(404, 'Location partner profile not found.');
        }

        // Available locations that can be assigned (unassigned locations)
        $unassignedLocations = Location::whereNull('location_partner_id')->orderBy('name')->get();

        // Retrieve activity logs history
        $logs = ActivityLog::with('user')
            ->where('entity_type', LocationPartnerProfile::class)
            ->where('entity_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.location-partners.show', compact('partner', 'unassignedLocations', 'logs'));
    }

    /**
     * Show creation form.
     */
    public function create(): View
    {
        return view('admin.location-partners.create');
    }

    /**
     * Store new profile.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->has('phone')) {
            $request->merge([
                'phone' => User::normalizePhone($request->phone)
            ]);
        }

        $email = trim($request->email);
        $phone = trim($request->phone);

        // Find existing user by email first, phone second
        $existingUser = User::where('email', $email)->first();
        if (!$existingUser && !empty($phone)) {
            $existingUser = User::where('phone', $phone)->first();
        }

        if ($existingUser) {
            // Check if they already have a Location Partner profile
            if ($existingUser->partnerProfile()->exists()) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'A Location Partner profile is already registered for this user.',
                    'phone' => 'A Location Partner profile is already registered for this user.',
                ]);
            }
        }

        $rules = [
            'company_name'   => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'designation'    => 'nullable|string|max:100',
            'website'        => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:50',
            'logo'           => 'nullable|image|max:5120',
            'status'         => 'required|in:pending,active,inactive,suspended',
            'notes'          => 'nullable|string',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
        ];

        $rules['email'] = [
            'required', 'email', 'max:255',
            Rule::unique('location_partner_profiles', 'email')->whereNull('deleted_at')
        ];
        $rules['phone'] = [
            'required', 'string', 'max:50',
            Rule::unique('location_partner_profiles', 'phone')->whereNull('deleted_at')
        ];

        $request->validate($rules);

        $this->partnerService->createPartner($request->all());

        return redirect()->route('admin.location-partners.index')->with('success', 'Location Partner profile successfully created.');
    }

    /**
     * Show editing form.
     */
    public function edit(int $id): View
    {
        $partner = $this->partnerRepository->findById($id);
        if (!$partner) {
            abort(404);
        }
        return view('admin.location-partners.edit', compact('partner'));
    }

    /**
     * Update location partner profile.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $profile = LocationPartnerProfile::findOrFail($id);

        if ($request->has('phone')) {
            $request->merge([
                'phone' => User::normalizePhone($request->phone)
            ]);
        }

        $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'designation'    => 'nullable|string|max:100',
            'phone'          => [
                'required', 'string', 'max:50',
                Rule::unique('location_partner_profiles', 'phone')->ignore($id)->whereNull('deleted_at')
            ],
            'email'          => [
                'required', 'email', 'max:255',
                Rule::unique('location_partner_profiles', 'email')->ignore($id)->whereNull('deleted_at')
            ],
            'website'        => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:50',
            'logo'           => 'nullable|image|max:5120',
            'delete_logo'    => 'nullable|boolean',
            'status'         => 'required|in:pending,active,inactive,suspended',
            'notes'          => 'nullable|string',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
        ]);

        $this->partnerService->updatePartner($id, $request->all());

        return redirect()->route('admin.location-partners.index')->with('success', 'Location Partner profile successfully updated.');
    }

    /**
     * Convert an approved partner lead.
     */
    public function convertLead(Request $request, int $leadId): RedirectResponse
    {
        $request->validate([
            'website'        => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:50',
            'designation'    => 'nullable|string|max:100',
            'address_line_1' => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
        ]);

        $profile = $this->partnerService->convertLeadToPartner($leadId, $request->all());

        return redirect()->route('admin.leads.show', $leadId)->with('success', "Lead successfully converted to Location Partner Profile {$profile->partner_code}.");
    }

    /**
     * Update active status manually.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,active,inactive,suspended',
        ]);

        $this->partnerService->updateStatus($id, $request->status);

        return redirect()->back()->with('success', "Location Partner status successfully set to '{$request->status}'.");
    }

    /**
     * Assign locations to this partner profile.
     */
    public function assignLocations(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'location_ids'   => 'required|array',
            'location_ids.*' => 'required|exists:locations,id',
        ]);

        $this->partnerService->assignLocations($id, $request->location_ids);

        return redirect()->back()->with('success', 'Locations successfully assigned to partner profile.');
    }

    /**
     * Remove location assignment.
     */
    public function removeLocation(int $locationId): RedirectResponse
    {
        $this->partnerService->removeLocationAssignment($locationId);

        return redirect()->back()->with('success', 'Location assignment successfully removed.');
    }

    /**
     * Soft delete partner profile.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->partnerService->deletePartner($id);
        return redirect()->route('admin.location-partners.index')->with('success', 'Location Partner profile successfully deleted.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AdvertiserRepositoryInterface;
use App\Services\AdvertiserService;
use App\Models\Industry;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\AdvertiserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class AdvertiserController extends Controller
{
    protected AdvertiserRepositoryInterface $advertiserRepository;
    protected AdvertiserService $advertiserService;

    public function __construct(
        AdvertiserRepositoryInterface $advertiserRepository,
        AdvertiserService $advertiserService
    ) {
        $this->advertiserRepository = $advertiserRepository;
        $this->advertiserService = $advertiserService;
    }

    /**
     * Display a listing of advertiser profiles.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['industry_id', 'status', 'source', 'start_date', 'end_date', 'search']);
        $advertisers = $this->advertiserRepository->getPaginatedAdvertisers($filters, 15);
        $industries = Industry::where('status', 'active')->get();

        return view('admin.advertisers.index', compact('advertisers', 'filters', 'industries'));
    }

    /**
     * Display the advertiser metrics analytics dashboard.
     */
    public function dashboard(): View
    {
        $metrics = $this->advertiserRepository->getDashboardMetrics();
        return view('admin.advertisers.dashboard', compact('metrics'));
    }

    /**
     * Show detail view of an advertiser profile.
     */
    public function show(int $id): View
    {
        $advertiser = $this->advertiserRepository->findById($id);
        if (!$advertiser) {
            abort(404, 'Advertiser profile not found.');
        }

        // Retrieve activity history logs
        $logs = ActivityLog::with('user')
            ->where('entity_type', AdvertiserProfile::class)
            ->where('entity_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.advertisers.show', compact('advertiser', 'logs'));
    }

    /**
     * Show campaigns for an advertiser.
     */
    public function campaigns(int $id): View
    {
        $advertiser = $this->advertiserRepository->findById($id);
        if (!$advertiser) {
            abort(404, 'Advertiser profile not found.');
        }

        $campaigns = \App\Models\Campaign::with(['industry'])
            ->where('advertiser_id', $advertiser->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.advertisers.campaigns', compact('advertiser', 'campaigns'));
    }

    /**
     * Show creation form.
     */
    public function create(): View
    {
        $industries = Industry::where('status', 'active')->get();
        return view('admin.advertisers.create', compact('industries'));
    }

    /**
     * Store advertiser.
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
            // Check if they already have an Advertiser profile
            if ($existingUser->advertiserProfile()->exists()) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'An Advertiser profile is already registered for this user.',
                    'phone' => 'An Advertiser profile is already registered for this user.',
                ]);
            }
        }

        $rules = [
            'company_name'   => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'website'        => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:50',
            'industry_id'    => 'required|exists:industries,id',
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
            Rule::unique('advertiser_profiles', 'email')->whereNull('deleted_at')
        ];
        $rules['phone'] = [
            'required', 'string', 'max:50',
            Rule::unique('advertiser_profiles', 'phone')->whereNull('deleted_at')
        ];

        $request->validate($rules);

        $this->advertiserService->createAdvertiser($request->all());

        return redirect()->route('admin.advertisers.index')->with('success', 'Advertiser profile successfully created.');
    }

    /**
     * Show edit form.
     */
    public function edit(int $id): View
    {
        $advertiser = $this->advertiserRepository->findById($id);
        if (!$advertiser) {
            abort(404);
        }
        $industries = Industry::where('status', 'active')->get();

        return view('admin.advertisers.edit', compact('advertiser', 'industries'));
    }

    /**
     * Update advertiser profile.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $profile = AdvertiserProfile::findOrFail($id);

        if ($request->has('phone')) {
            $request->merge([
                'phone' => User::normalizePhone($request->phone)
            ]);
        }

        $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone'          => [
                'required', 'string', 'max:50',
                Rule::unique('advertiser_profiles', 'phone')->ignore($id)->whereNull('deleted_at')
            ],
            'email'          => [
                'required', 'email', 'max:255',
                Rule::unique('advertiser_profiles', 'email')->ignore($id)->whereNull('deleted_at')
            ],
            'website'        => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:50',
            'industry_id'    => 'required|exists:industries,id',
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

        $this->advertiserService->updateAdvertiser($id, $request->all());

        return redirect()->route('admin.advertisers.index')->with('success', 'Advertiser profile successfully updated.');
    }

    /**
     * Convert an approved lead to an advertiser profile.
     */
    public function convertLead(Request $request, int $leadId): RedirectResponse
    {
        $request->validate([
            'industry_id'    => 'required|exists:industries,id',
            'website'        => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:50',
            'address_line_1' => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
        ]);

        $profile = $this->advertiserService->convertLeadToAdvertiser($leadId, $request->industry_id, $request->all());

        return redirect()->route('admin.leads.show', $leadId)->with('success', "Lead successfully converted to Advertiser Profile {$profile->advertiser_code}.");
    }

    /**
     * Update active status manually.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,active,inactive,suspended',
        ]);

        $this->advertiserService->updateStatus($id, $request->status);

        return redirect()->back()->with('success', "Advertiser status successfully set to '{$request->status}'.");
    }

    /**
     * Soft delete advertiser profile.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->advertiserService->deleteAdvertiser($id);
        return redirect()->route('admin.advertisers.index')->with('success', 'Advertiser profile successfully deleted.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Services\LeadService;
use App\Models\ActivityLog;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    protected LeadRepositoryInterface $leadRepository;
    protected LeadService $leadService;

    public function __construct(
        LeadRepositoryInterface $leadRepository,
        LeadService $leadService
    ) {
        $this->leadRepository = $leadRepository;
        $this->leadService = $leadService;
    }

    /**
     * Display a listing of the leads.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['lead_type', 'status', 'source', 'start_date', 'end_date', 'search']);
        $leads = $this->leadRepository->getPaginatedLeads($filters, 15);

        $pageTitle = 'Leads Management';
        if (!empty($filters['lead_type'])) {
            switch ($filters['lead_type']) {
                case 'advertiser':
                    $pageTitle = 'Advertiser Leads';
                    break;
                case 'location_partner':
                    $pageTitle = 'Location Partner Leads';
                    break;
                case 'digital_signage':
                    $pageTitle = 'Digital Signage Leads';
                    break;
                case 'sales_partner':
                    $pageTitle = 'Sales Partner Leads';
                    break;
            }
        }

        return view('admin.leads.index', compact('leads', 'filters', 'pageTitle'));
    }

    /**
     * Display the leads analytics/KPI metrics dashboard.
     */
    public function dashboard(): View
    {
        $metrics = $this->leadRepository->getDashboardMetrics();
        return view('admin.leads.dashboard', compact('metrics'));
    }

    /**
     * Display the specified lead detail.
     */
    public function show(int $id): View
    {
        $lead = $this->leadRepository->findById($id);
        if (!$lead) {
            abort(404, 'Lead not found.');
        }

        // Retrieve status changes / activities for this lead
        $logs = ActivityLog::with('user')
            ->where('entity_type', Lead::class)
            ->where('entity_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.leads.show', compact('lead', 'logs'));
    }

    /**
     * Assign the lead to the currently authenticated admin.
     */
    public function assignSelf(int $id): RedirectResponse
    {
        $this->leadService->assignLeadToAdmin($id, auth()->id());
        return redirect()->back()->with('success', 'Lead successfully assigned to yourself.');
    }

    /**
     * Update the lead's operational status manually.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:new,contacted,qualified,approved,rejected',
        ]);

        $this->leadService->updateLeadStatus($id, $request->status);
        return redirect()->back()->with('success', "Lead status successfully updated to '{$request->status}'.");
    }

    /**
     * Add or update internal administrative remarks notes.
     */
    public function addRemarks(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $this->leadService->updateLead($id, ['remarks' => $request->remarks]);
        return redirect()->back()->with('success', 'Internal remarks successfully updated.');
    }

    /**
     * Approve the lead.
     */
    public function approve(int $id): RedirectResponse
    {
        $this->leadService->approveLead($id);
        return redirect()->back()->with('success', 'Lead successfully Approved. Ready for user account generation.');
    }

    /**
     * Reject the lead.
     */
    public function reject(int $id): RedirectResponse
    {
        $this->leadService->rejectLead($id);
        return redirect()->back()->with('success', 'Lead status set to Rejected.');
    }

    /**
     * Remove the specified lead from database (Soft delete).
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->leadService->deleteLead($id);
        return redirect()->route('admin.leads.index')->with('success', 'Lead successfully deleted.');
    }
}

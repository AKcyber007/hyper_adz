<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    /**
     * Display a listing of advertising campaign requests.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'Review');

        $query = Campaign::with(['advertiser', 'industry'])
            ->has('advertiser')
            ->orderBy('created_at', 'desc');
            
        if ($status === 'Rejected') {
            $query->where('status', 'like', 'Rejected%');
        } elseif ($status === 'Review') {
            $query->whereIn('status', ['Submitted', 'Creative Review']);
        } else {
            $query->where('status', $status);
        }

        $campaigns = $query->paginate(15);

        return view('admin.advertising.requests.index', compact('campaigns', 'status'));
    }

    /**
     * Display the specified campaign request details.
     */
    public function show(int $id): View
    {
        $campaign = Campaign::with(['advertiser', 'industry', 'locations', 'activityLogs'])
            ->findOrFail($id);

        return view('admin.advertising.requests.show', compact('campaign'));
    }

    /**
     * Approve the campaign request.
     */
    public function approve(Request $request, int $id, \App\Services\ZohoPaymentsService $zohoService): RedirectResponse
    {
        $campaign = Campaign::with(['locations', 'advertiser'])->findOrFail($id);

        if (!in_array($campaign->status, ['Submitted', 'Creative Review'])) {
            return redirect()->back()->with('error', 'Campaign must be in Review before approval.');
        }

        try {
            DB::transaction(function () use ($campaign, $request, $zohoService) {
                // Calculate final payment amount based on duration and price_per_day
                $days = \Carbon\Carbon::parse($campaign->start_date)->diffInDays(\Carbon\Carbon::parse($campaign->end_date)) + 1;
                $totalRate = 0;
                foreach ($campaign->locations as $loc) {
                    $totalRate += $loc->price_per_day;
                }
                $paymentAmount = $days * $totalRate;
                
                $campaign->payment_amount = $paymentAmount;

                // Create Zoho Payment Link
                $zohoResult = $zohoService->createPaymentLink($campaign);
                
                if (!$zohoResult['success']) {
                    throw new \Exception($zohoResult['error'] ?? 'Unknown Zoho API Error');
                }

                $campaign->update([
                    'status' => 'Payment Pending',
                    'payment_status' => 'Unpaid',
                    'payment_amount' => $paymentAmount,
                    'payment_due_date' => \Carbon\Carbon::parse($campaign->start_date)->subDay(),
                    'approval_status' => 'Approved', // Legacy sync
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'creative_review_notes' => $request->creative_review_notes,
                    'zoho_payment_link_id' => $zohoResult['payment_link_id'],
                    'zoho_payment_url' => $zohoResult['payment_url'],
                ]);

                $campaign->activityLogs()->create([
                    'action' => 'Approved',
                    'performed_by' => auth()->user()->name,
                    'remarks' => 'Advertising campaign approved. Payment pending. Amount: ₹' . number_format($paymentAmount, 2),
                ]);
            });

            return redirect()->route('admin.advertising.requests.show', $campaign->id)
                ->with('success', 'Campaign request approved successfully. Payment link generated.');
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Campaign Approval Error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to approve campaign due to payment gateway error: ' . $e->getMessage());
        }
    }

    /**
     * Confirm Payment Received.
     */
    public function confirmPayment(int $id): RedirectResponse
    {
        $campaign = Campaign::findOrFail($id);

        if ($campaign->status !== 'Payment Pending') {
            return redirect()->back()->with('error', 'Campaign must be in Payment Pending status.');
        }

        DB::transaction(function () use ($campaign) {
            $campaign->update([
                'status' => 'Scheduled',
                'payment_status' => 'Paid',
                'payment_confirmed_at' => now(),
                'payment_confirmed_by' => auth()->id(),
            ]);

            $campaign->activityLogs()->create([
                'action' => 'Payment Confirmed',
                'performed_by' => auth()->user()->name,
                'remarks' => 'Payment confirmed by admin. Campaign is now scheduled.',
            ]);
        });

        return redirect()->route('admin.advertising.requests.show', $campaign->id)
            ->with('success', 'Payment confirmed. Campaign is scheduled.');
    }

    /**
     * Reject the campaign request.
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $campaign = Campaign::findOrFail($id);

        if ($campaign->status === 'Draft' || $campaign->status === 'Scheduled' || $campaign->status === 'Running' || $campaign->status === 'Completed' || $campaign->status === 'Report Uploaded' || str_starts_with($campaign->status, 'Rejected')) {
            return redirect()->back()->with('error', 'Campaign cannot be rejected at this stage.');
        }

        DB::transaction(function () use ($campaign, $request) {
            $campaign->update([
                'status' => 'Rejected (Admin)',
                'approval_status' => 'Rejected', // Legacy sync
                'rejection_reason' => $request->rejection_reason,
                'rejection_type' => 'admin_rejected',
            ]);

            $campaign->activityLogs()->create([
                'action' => 'Rejected',
                'performed_by' => auth()->user()->name,
                'remarks' => 'Reason: ' . $request->rejection_reason,
            ]);
        });

        return redirect()->route('admin.advertising.requests.show', $campaign->id)
            ->with('success', 'Campaign request rejected.');
    }

    /**
     * Upload analytics report.
     */
    public function uploadReport(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'report' => 'required|file|mimes:pdf,zip,jpg,jpeg,png,doc,docx,xls,xlsx,csv|max:10240', // Max 10MB
        ]);

        $campaign = Campaign::findOrFail($id);

        if (!in_array($campaign->status, ['Scheduled', 'Running', 'Completed', 'Report Uploaded'])) {
            return redirect()->back()->with('error', 'Reports can only be uploaded for Scheduled, Running, or Completed campaigns.');
        }

        DB::transaction(function () use ($campaign, $request) {
            if ($campaign->report_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($campaign->report_path);
            }

            $file = $request->file('report');
            $path = $file->store('reports', 'public');
            
            $campaign->update([
                'report_path' => $path,
                'report_name' => $file->getClientOriginalName(),
                'report_uploaded_at' => now(),
            ]);

            $campaign->activityLogs()->create([
                'action' => 'Report Uploaded',
                'performed_by' => auth()->user()->name,
                'remarks' => 'Admin uploaded campaign report: ' . $file->getClientOriginalName(),
            ]);
        });

        return redirect()->back()
            ->with('success', 'Report uploaded successfully.');
    }

    /**
     * View all campaigns for an advertiser.
     */
    public function byAdvertiser(int $id): View
    {
        $advertiser = \App\Models\AdvertiserProfile::with('user')->findOrFail($id);
        $campaigns = Campaign::with(['industry'])
            ->where('advertiser_id', $advertiser->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.advertisers.campaigns', compact('advertiser', 'campaigns'));
    }

    /**
     * Reverse an already approved campaign back to Creative Review or Rejected status.
     */
    public function reverseApproval(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'target_status' => 'required|in:creative_review,rejected',
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $campaign = Campaign::findOrFail($id);

        if ($campaign->status !== 'Payment Pending') {
            return redirect()->back()->with('error', 'Only campaigns in Payment Pending status can be reversed.');
        }

        DB::transaction(function () use ($campaign, $request) {
            $target = $request->target_status;
            $reason = $request->reason;

            if ($target === 'creative_review') {
                $campaign->update([
                    'status' => 'Creative Review',
                    'approval_status' => 'Pending Review', // Legacy sync
                    'creative_review_notes' => $reason,
                    'payment_amount' => null,
                    'payment_due_date' => null,
                ]);
            } else {
                $campaign->update([
                    'status' => 'Rejected (Admin)',
                    'approval_status' => 'Rejected', // Legacy sync
                    'rejection_reason' => $reason,
                    'rejection_type' => 'admin_rejected',
                    'payment_amount' => null,
                    'payment_due_date' => null,
                ]);
            }

            // Create campaign activity log
            $campaign->activityLogs()->create([
                'action' => $target === 'creative_review' ? 'Moved to Creative Review' : 'Rejected',
                'performed_by' => auth()->user()->name,
                'remarks' => 'Approval reversed by Admin. Reason: ' . $reason,
            ]);

            // Create notification for the advertiser (stored in ActivityLog)
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Approval Reversed',
                'entity_type' => \App\Models\AdvertiserProfile::class,
                'entity_id' => $campaign->advertiser_id,
                'description' => "Approval reversed for campaign '{$campaign->campaign_name}' ({$campaign->campaign_code}) to state: " . ($target === 'creative_review' ? 'Creative Review' : 'Rejected') . ". Reason: " . $reason,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('admin.advertising.requests.show', $campaign->id)
            ->with('success', 'Campaign approval has been successfully reversed.');
    }

    /**
     * View all campaigns at a location.
     */
    public function byLocation(int $id): View
    {
        $location = \App\Models\Location::findOrFail($id);
        $campaigns = $location->campaigns()->with('advertiser')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.locations.campaigns', compact('location', 'campaigns'));
    }
}

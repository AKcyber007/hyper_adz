<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class LeadService
{
    /**
     * Create a new Lead capture record.
     */
    public function createLead(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            $lead = Lead::create([
                'lead_type'         => $data['lead_type'],
                'name'              => $data['name'],
                'company_name'      => $data['company_name'] ?? null,
                'phone'             => $data['phone'],
                'email'             => $data['email'],
                'message'           => $data['message'] ?? null,
                'source'            => $data['source'] ?? 'website',
                'status'            => 'new',
                'assigned_admin_id' => $data['assigned_admin_id'] ?? null,
                'remarks'           => $data['remarks'] ?? null,
            ]);

            // Log activity
            $this->logActivity('created', $lead, "Created Lead {$lead->lead_code}");

            return $lead;
        });
    }

    /**
     * Update an existing Lead configuration details.
     */
    public function updateLead(int $id, array $data): Lead
    {
        return DB::transaction(function () use ($id, $data) {
            $lead = Lead::findOrFail($id);
            $lead->update($data);

            $this->logActivity('updated', $lead, "Updated Lead {$lead->lead_code}");

            return $lead;
        });
    }

    /**
     * Assign a Lead to a specific administrator.
     */
    public function assignLeadToAdmin(int $id, int $adminId): Lead
    {
        return DB::transaction(function () use ($id, $adminId) {
            $lead = Lead::findOrFail($id);
            $lead->update(['assigned_admin_id' => $adminId]);

            $adminName = \App\Models\User::find($adminId)->name ?? 'Admin';
            $this->logActivity('updated', $lead, "Assigned Lead {$lead->lead_code} to Admin {$adminName}");

            return $lead;
        });
    }

    /**
     * Update the operational workflow status of a Lead.
     */
    public function updateLeadStatus(int $id, string $status): Lead
    {
        return DB::transaction(function () use ($id, $status) {
            $lead = Lead::findOrFail($id);
            $oldStatus = $lead->status;
            
            $lead->update(['status' => $status]);

            $this->logActivity('updated', $lead, "Updated Lead {$lead->lead_code} Status from {$oldStatus} to {$status}");

            return $lead;
        });
    }

    /**
     * Approve a Lead.
     */
    public function approveLead(int $id): Lead
    {
        return DB::transaction(function () use ($id) {
            $lead = Lead::findOrFail($id);
            
            $lead->update([
                'status'      => 'approved',
                'approved_at' => now(),
                'rejected_at' => null,
            ]);

            $this->logActivity('approved', $lead, "Approved Lead {$lead->lead_code}");

            return $lead;
        });
    }

    /**
     * Reject a Lead.
     */
    public function rejectLead(int $id): Lead
    {
        return DB::transaction(function () use ($id) {
            $lead = Lead::findOrFail($id);
            
            $lead->update([
                'status'      => 'rejected',
                'rejected_at' => now(),
                'approved_at' => null,
            ]);

            $this->logActivity('rejected', $lead, "Rejected Lead {$lead->lead_code}");

            return $lead;
        });
    }

    /**
     * Delete / Soft delete a Lead.
     */
    public function deleteLead(int $id): void
    {
        DB::transaction(function () use ($id) {
            $lead = Lead::findOrFail($id);
            $leadCode = $lead->lead_code;
            
            $lead->delete();

            $this->logActivity('deleted', $lead, "Deleted Lead {$leadCode}");
        });
    }

    /**
     * Log actions inside the central activity_logs table.
     */
    protected function logActivity(string $action, Lead $lead, string $description): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id(), // null for anonymous public form creators
            'action'      => $action,
            'entity_type' => Lead::class,
            'entity_id'   => $lead->id,
            'description' => $description,
        ]);
    }
}

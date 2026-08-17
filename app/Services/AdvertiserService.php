<?php

namespace App\Services;

use App\Models\AdvertiserProfile;
use App\Models\Lead;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvertiserService
{
    /**
     * Create a new Advertiser Profile manually.
     */
    public function createAdvertiser(array $data): AdvertiserProfile
    {
        return DB::transaction(function () use ($data) {
            // Find or create User record (email primary, phone secondary)
            $user = User::where('email', $data['email'])->first();
            if (!$user && !empty($data['phone'])) {
                $user = User::where('phone', $data['phone'])->first();
            }

            if (!$user) {
                $user = User::create([
                    'name' => $data['company_name'] ?? $data['contact_person'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'status' => 'active',
                ]);
            } else {
                if (empty($user->phone) && !empty($data['phone'])) {
                    $user->update(['phone' => $data['phone']]);
                }
            }

            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
            if (!$user->hasRole('advertiser')) {
                $user->assignRole('advertiser');
            }

            // Handle logo upload
            if (isset($data['logo']) && $data['logo']->isValid()) {
                $logoPath = $data['logo']->store('advertisers/logos', 'public');
                $data['logo_path'] = $logoPath;
            }

            // Create profile with temp code
            $profile = AdvertiserProfile::create(array_merge($data, [
                'advertiser_code' => 'ADV-TEMP-' . strtoupper(Str::random(6)),
                'status'          => $data['status'] ?? 'pending',
                'user_id'         => $user->id,
            ]));

            // Assign proper sequential code based on auto-increment ID
            $profile->update([
                'advertiser_code' => 'ADV-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT)
            ]);

            $this->logActivity('created', $profile, "Created Advertiser {$profile->advertiser_code}");

            return $profile;
        });
    }

    /**
     * Update an advertiser profile configuration.
     */
    public function updateAdvertiser(int $id, array $data): AdvertiserProfile
    {
        return DB::transaction(function () use ($id, $data) {
            $profile = AdvertiserProfile::findOrFail($id);

            // Handle logo upload
            if (isset($data['logo']) && $data['logo']->isValid()) {
                // Delete old logo
                if ($profile->logo_path) {
                    Storage::disk('public')->delete($profile->logo_path);
                }
                $logoPath = $data['logo']->store('advertisers/logos', 'public');
                $data['logo_path'] = $logoPath;
            }

            // Handle logo deletion checkbox
            if (!empty($data['delete_logo']) && $profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
                $data['logo_path'] = null;
            }

            $profile->update($data);

            $this->logActivity('updated', $profile, "Updated Advertiser Profile {$profile->advertiser_code}");

            return $profile;
        });
    }

    /**
     * Convert an approved Lead to Advertiser profile.
     */
    public function convertLeadToAdvertiser(int $leadId, int $industryId, array $additionalData = []): AdvertiserProfile
    {
        return DB::transaction(function () use ($leadId, $industryId, $additionalData) {
            $lead = Lead::findOrFail($leadId);

            // Copy lead details
            $profileData = array_merge([
                'lead_id'        => $lead->id,
                'company_name'   => $lead->company_name ?? $lead->name,
                'contact_person' => $lead->name,
                'phone'          => $lead->phone,
                'email'          => $lead->email,
                'industry_id'    => $industryId,
                'notes'          => $lead->message,
                'status'         => 'active',
            ], $additionalData);

            // Find or create User record
            $user = User::where('email', $profileData['email'])->first();
            if (!$user && !empty($profileData['phone'])) {
                $user = User::where('phone', $profileData['phone'])->first();
            }

            if (!$user) {
                $user = User::create([
                    'name' => $profileData['company_name'] ?? $profileData['contact_person'],
                    'email' => $profileData['email'],
                    'phone' => $profileData['phone'],
                    'status' => 'active',
                ]);
            } else {
                if (empty($user->phone) && !empty($profileData['phone'])) {
                    $user->update(['phone' => $profileData['phone']]);
                }
            }

            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
            if (!$user->hasRole('advertiser')) {
                $user->assignRole('advertiser');
            }

            // Create profile with temporary code
            $profile = AdvertiserProfile::create(array_merge($profileData, [
                'advertiser_code' => 'ADV-TEMP-' . strtoupper(Str::random(6)),
                'user_id'         => $user->id,
            ]));

            // Update to sequential tracking code
            $profile->update([
                'advertiser_code' => 'ADV-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT)
            ]);

            // Mark Lead as converted
            $lead->update([
                'converted_at' => now(),
            ]);

            $this->logActivity('created', $profile, "Converted Lead {$lead->lead_code} to Advertiser {$profile->advertiser_code}");

            return $profile;
        });
    }

    /**
     * Update advertiser operational status (e.g. activate/suspend).
     */
    public function updateStatus(int $id, string $status): AdvertiserProfile
    {
        return DB::transaction(function () use ($id, $status) {
            $profile = AdvertiserProfile::findOrFail($id);
            $oldStatus = $profile->status;

            $updateData = ['status' => $status];

            // If being activated, assign approved admin
            if ($status === 'active' && $oldStatus !== 'active') {
                $updateData['approved_by'] = auth()->id();
                $updateData['approved_at'] = now();
            }

            $profile->update($updateData);

            $action = ($status === 'active') ? 'activated' : (($status === 'suspended') ? 'suspended' : 'updated');
            $this->logActivity($action, $profile, "Changed status of Advertiser {$profile->advertiser_code} to {$status}");

            return $profile;
        });
    }

    /**
     * Remove / soft-delete advertiser profile.
     */
    public function deleteAdvertiser(int $id): void
    {
        DB::transaction(function () use ($id) {
            $profile = AdvertiserProfile::findOrFail($id);
            $code = $profile->advertiser_code;
            
            // Delete logo path if exists
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }

            // Clean up non-historical/live campaign requests belonging to this advertiser
            \App\Models\Campaign::where('advertiser_id', $profile->id)
                ->whereNotIn('status', ['Completed', 'Report Uploaded', 'Running'])
                ->delete();

            $profile->delete();

            $this->logActivity('deleted', $profile, "Deleted Advertiser {$code}");
        });
    }

    /**
     * Log actions inside the central activity_logs table.
     */
    protected function logActivity(string $action, AdvertiserProfile $profile, string $description): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'entity_type' => AdvertiserProfile::class,
            'entity_id'   => $profile->id,
            'description' => $description,
        ]);
    }
}

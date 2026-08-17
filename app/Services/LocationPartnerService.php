<?php

namespace App\Services;

use App\Models\LocationPartnerProfile;
use App\Models\Location;
use App\Models\Lead;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationPartnerService
{
    /**
     * Create a new Location Partner Profile manually.
     */
    public function createPartner(array $data): LocationPartnerProfile
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

            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'location_partner', 'guard_name' => 'web']);
            if (!$user->hasRole('location_partner')) {
                $user->assignRole('location_partner');
            }

            // Handle logo upload
            if (isset($data['logo']) && $data['logo']->isValid()) {
                $logoPath = $data['logo']->store('partners/logos', 'public');
                $data['logo_path'] = $logoPath;
            }

            // Create profile with temp code
            $profile = LocationPartnerProfile::create(array_merge($data, [
                'partner_code' => 'LP-TEMP-' . strtoupper(Str::random(6)),
                'status'       => $data['status'] ?? 'pending',
                'user_id'      => $user->id,
            ]));

            // Assign proper sequential code
            $profile->update([
                'partner_code' => 'LP-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT)
            ]);

            $this->logActivity('created', $profile, "Created Partner {$profile->partner_code}");

            return $profile;
        });
    }

    /**
     * Update a location partner profile configuration.
     */
    public function updatePartner(int $id, array $data): LocationPartnerProfile
    {
        return DB::transaction(function () use ($id, $data) {
            $profile = LocationPartnerProfile::findOrFail($id);

            // Handle logo upload
            if (isset($data['logo']) && $data['logo']->isValid()) {
                if ($profile->logo_path) {
                    Storage::disk('public')->delete($profile->logo_path);
                }
                $logoPath = $data['logo']->store('partners/logos', 'public');
                $data['logo_path'] = $logoPath;
            }

            // Handle logo deletion checkbox
            if (!empty($data['delete_logo']) && $profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
                $data['logo_path'] = null;
            }

            $profile->update($data);

            $this->logActivity('updated', $profile, "Updated Partner Profile {$profile->partner_code}");

            return $profile;
        });
    }

    /**
     * Convert an approved Lead to Location Partner profile.
     */
    public function convertLeadToPartner(int $leadId, array $additionalData = []): LocationPartnerProfile
    {
        return DB::transaction(function () use ($leadId, $additionalData) {
            $lead = Lead::findOrFail($leadId);

            // Copy lead details
            $profileData = array_merge([
                'lead_id'        => $lead->id,
                'company_name'   => $lead->company_name ?? $lead->name,
                'contact_person' => $lead->name,
                'phone'          => $lead->phone,
                'email'          => $lead->email,
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

            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'location_partner', 'guard_name' => 'web']);
            if (!$user->hasRole('location_partner')) {
                $user->assignRole('location_partner');
            }

            // Create profile with temporary code
            $profile = LocationPartnerProfile::create(array_merge($profileData, [
                'partner_code' => 'LP-TEMP-' . strtoupper(Str::random(6)),
                'user_id'      => $user->id,
            ]));

            // Update to sequential code
            $profile->update([
                'partner_code' => 'LP-' . str_pad($profile->id, 5, '0', STR_PAD_LEFT)
            ]);

            // Mark Lead as converted
            $lead->update([
                'converted_at' => now(),
            ]);

            $this->logActivity('created', $profile, "Converted Lead {$lead->lead_code} to Partner {$profile->partner_code}");

            return $profile;
        });
    }

    /**
     * Assign locations to location partner.
     */
    public function assignLocations(int $partnerId, array $locationIds): void
    {
        DB::transaction(function () use ($partnerId, $locationIds) {
            $profile = LocationPartnerProfile::findOrFail($partnerId);

            foreach ($locationIds as $locId) {
                $location = Location::findOrFail($locId);
                $location->update([
                    'location_partner_id' => $profile->id,
                ]);

                $this->logActivity('updated', $profile, "Assigned Location {$location->name} to Partner {$profile->partner_code}");
            }
        });
    }

    /**
     * Remove location assignment.
     */
    public function removeLocationAssignment(int $locationId): void
    {
        DB::transaction(function () use ($locationId) {
            $location = Location::findOrFail($locationId);
            $partnerId = $location->location_partner_id;

            $location->update([
                'location_partner_id' => null,
            ]);

            if ($partnerId) {
                $profile = LocationPartnerProfile::find($partnerId);
                if ($profile) {
                    $this->logActivity('updated', $profile, "Removed Location Assignment {$location->name}");
                }
            }
        });
    }

    /**
     * Update active status.
     */
    public function updateStatus(int $id, string $status): LocationPartnerProfile
    {
        return DB::transaction(function () use ($id, $status) {
            $profile = LocationPartnerProfile::findOrFail($id);
            $oldStatus = $profile->status;

            $updateData = ['status' => $status];

            if ($status === 'active' && $oldStatus !== 'active') {
                $updateData['approved_by'] = auth()->id();
                $updateData['approved_at'] = now();
            }

            $profile->update($updateData);

            $action = ($status === 'active') ? 'activated' : (($status === 'suspended') ? 'suspended' : 'updated');
            $this->logActivity($action, $profile, "Changed status of Partner {$profile->partner_code} to {$status}");

            return $profile;
        });
    }

    /**
     * Soft delete partner profile.
     */
    public function deletePartner(int $id): void
    {
        DB::transaction(function () use ($id) {
            $profile = LocationPartnerProfile::findOrFail($id);
            $code = $profile->partner_code;

            // Clear location assignments
            Location::where('location_partner_id', $profile->id)->update(['location_partner_id' => null]);

            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }

            $profile->delete();

            $this->logActivity('deleted', $profile, "Deleted Partner {$code}");
        });
    }

    /**
     * Log actions inside the central activity_logs table.
     */
    protected function logActivity(string $action, LocationPartnerProfile $profile, string $description): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'entity_type' => LocationPartnerProfile::class,
            'entity_id'   => $profile->id,
            'description' => $description,
        ]);
    }
}

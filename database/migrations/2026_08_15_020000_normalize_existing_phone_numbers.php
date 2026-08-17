<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $normalize = function (?string $phone): ?string {
            if (is_null($phone) || $phone === '') {
                return null;
            }
            $cleaned = preg_replace('/\D/', '', $phone);
            if (strlen($cleaned) === 12 && str_starts_with($cleaned, '91')) {
                return substr($cleaned, 2);
            }
            if (strlen($cleaned) === 11 && str_starts_with($cleaned, '0')) {
                return substr($cleaned, 1);
            }
            return $cleaned;
        };

        // Normalize users table
        DB::table('users')->orderBy('id')->chunk(100, function ($users) use ($normalize) {
            foreach ($users as $user) {
                if ($user->phone) {
                    $normalized = $normalize($user->phone);
                    if ($normalized !== $user->phone) {
                        DB::table('users')->where('id', $user->id)->update(['phone' => $normalized]);
                    }
                }
            }
        });

        // Normalize advertiser_profiles table
        DB::table('advertiser_profiles')->orderBy('id')->chunk(100, function ($profiles) use ($normalize) {
            foreach ($profiles as $profile) {
                if ($profile->phone) {
                    $normalized = $normalize($profile->phone);
                    if ($normalized !== $profile->phone) {
                        DB::table('advertiser_profiles')->where('id', $profile->id)->update(['phone' => $normalized]);
                    }
                }
            }
        });

        // Normalize location_partner_profiles table
        DB::table('location_partner_profiles')->orderBy('id')->chunk(100, function ($profiles) use ($normalize) {
            foreach ($profiles as $profile) {
                if ($profile->phone) {
                    $normalized = $normalize($profile->phone);
                    if ($normalized !== $profile->phone) {
                        DB::table('location_partner_profiles')->where('id', $profile->id)->update(['phone' => $normalized]);
                    }
                }
            }
        });

        // Normalize leads table
        DB::table('leads')->orderBy('id')->chunk(100, function ($leads) use ($normalize) {
            foreach ($leads as $lead) {
                if ($lead->phone) {
                    $normalized = $normalize($lead->phone);
                    if ($normalized !== $lead->phone) {
                        DB::table('leads')->where('id', $lead->id)->update(['phone' => $normalized]);
                    }
                }
            }
        });

        // Normalize otp_verifications table
        DB::table('otp_verifications')->orderBy('id')->chunk(100, function ($otps) use ($normalize) {
            foreach ($otps as $otp) {
                if ($otp->phone) {
                    $normalized = $normalize($otp->phone);
                    if ($normalized !== $otp->phone) {
                        DB::table('otp_verifications')->where('id', $otp->id)->update(['phone' => $normalized]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration logic needed for normalization
    }
};

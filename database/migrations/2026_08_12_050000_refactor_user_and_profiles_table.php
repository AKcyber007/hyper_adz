<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop existing unique index on email in users table safely
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        } catch (\Exception $e) {
            // Ignore if index already dropped
        }

        // 2. Add phone, deleted_at, status, phone_verified_at, last_login_at to users table, and drop password safely
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'pending', 'suspended', 'rejected'])->default('active');
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (Schema::hasColumn('users', 'password')) {
                $table->dropColumn('password');
            }
        });

        // 3. Create unique indexes safely — compatible with both SQLite and MySQL
        try {
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_email_unique ON users(email) WHERE deleted_at IS NULL');
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_phone_unique ON users(phone) WHERE deleted_at IS NULL');
            } else {
                // MySQL does not support partial (WHERE) indexes, so use a standard unique index
                // Wrap in try/catch in case the index already exists
                try { Schema::table('users', function (Blueprint $table) { $table->unique('email', 'users_email_unique'); }); } catch (\Exception $e) {}
                try { Schema::table('users', function (Blueprint $table) { $table->unique('phone', 'users_phone_unique'); }); } catch (\Exception $e) {}
            }
        } catch (\Exception $e) {
            // Already exists or not supported — skip silently
        }

        // 4. Update otp_verifications table to add user_id column safely
        Schema::table('otp_verifications', function (Blueprint $table) {
            if (!Schema::hasColumn('otp_verifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('uuid');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });

        // 5. Data Migration: Match profiles to users by email (primary) & phone (secondary)
        $this->migrateExistingProfiles();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verifications', function (Blueprint $table) {
            if (Schema::hasColumn('otp_verifications', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS users_email_unique');
            DB::statement('DROP INDEX IF EXISTS users_phone_unique');
        } else {
            try {
                DB::statement('DROP INDEX users_email_unique ON users');
                DB::statement('DROP INDEX users_phone_unique ON users');
            } catch (\Exception $e) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique(['email', 'deleted_at']);
                    $table->dropUnique(['phone', 'deleted_at']);
                });
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->default('');
            }
            $table->dropColumn(['phone', 'deleted_at', 'status', 'phone_verified_at', 'last_login_at']);
            $table->unique('email');
        });
    }

    /**
     * Data migration helper.
     */
    protected function migrateExistingProfiles(): void
    {
        $roles = DB::table('roles')->whereIn('name', ['advertiser', 'location_partner'])->get()->keyBy('name');

        // Get all Advertiser profiles (even soft deleted ones)
        $advertiserProfiles = DB::table('advertiser_profiles')->get();
        foreach ($advertiserProfiles as $profile) {
            $user = DB::table('users')->where('email', $profile->email)->first();
            
            if (!$user && !empty($profile->phone)) {
                $user = DB::table('users')->where('phone', $profile->phone)->first();
            }

            if (!$user) {
                // Create user
                $userId = DB::table('users')->insertGetId([
                    'name' => $profile->company_name ?: $profile->contact_person,
                    'email' => $profile->email,
                    'phone' => $profile->phone,
                    'status' => 'active',
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $userId = $user->id;
                if (empty($user->phone) && !empty($profile->phone)) {
                    DB::table('users')->where('id', $userId)->update(['phone' => $profile->phone]);
                }
            }

            // Link profile to user
            DB::table('advertiser_profiles')->where('id', $profile->id)->update([
                'user_id' => $userId
            ]);

            // Assign role advertiser
            if (isset($roles['advertiser'])) {
                DB::table('model_has_roles')->insertOrIgnore([
                    'role_id' => $roles['advertiser']->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId
                ]);
            }
        }

        // Get all Location Partner profiles (even soft deleted ones)
        $partnerProfiles = DB::table('location_partner_profiles')->get();
        foreach ($partnerProfiles as $profile) {
            $user = DB::table('users')->where('email', $profile->email)->first();
            
            if (!$user && !empty($profile->phone)) {
                $user = DB::table('users')->where('phone', $profile->phone)->first();
            }

            if (!$user) {
                // Create user
                $userId = DB::table('users')->insertGetId([
                    'name' => $profile->company_name ?: $profile->contact_person,
                    'email' => $profile->email,
                    'phone' => $profile->phone,
                    'status' => 'active',
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $userId = $user->id;
                if (empty($user->phone) && !empty($profile->phone)) {
                    DB::table('users')->where('id', $userId)->update(['phone' => $profile->phone]);
                }
            }

            // Link profile to user
            DB::table('location_partner_profiles')->where('id', $profile->id)->update([
                'user_id' => $userId
            ]);

            // Assign role location_partner
            if (isset($roles['location_partner'])) {
                DB::table('model_has_roles')->insertOrIgnore([
                    'role_id' => $roles['location_partner']->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId
                ]);
            }
        }
    }
};

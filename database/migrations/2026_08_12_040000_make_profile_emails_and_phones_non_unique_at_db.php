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
        Schema::table('location_partner_profiles', function (Blueprint $table) {
            try {
                $table->dropUnique(['email']);
            } catch (\Exception $e) {}
            try {
                $table->dropUnique(['phone']);
            } catch (\Exception $e) {}
        });

        Schema::table('advertiser_profiles', function (Blueprint $table) {
            try {
                $table->dropUnique(['email']);
            } catch (\Exception $e) {}
            try {
                $table->dropUnique(['phone']);
            } catch (\Exception $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_partner_profiles', function (Blueprint $table) {
            $table->unique('email');
            $table->unique('phone');
        });

        Schema::table('advertiser_profiles', function (Blueprint $table) {
            $table->unique('email');
            $table->unique('phone');
        });
    }
};

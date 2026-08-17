<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('advertiser_profiles', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->integer('login_count')->default(0)->after('last_login_at');
        });

        Schema::table('location_partner_profiles', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->integer('login_count')->default(0)->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertiser_profiles', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'login_count']);
        });

        Schema::table('location_partner_profiles', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'login_count']);
        });
    }
};

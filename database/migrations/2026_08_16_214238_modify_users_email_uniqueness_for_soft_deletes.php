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
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->string('active_email')->nullable()->virtualAs('CASE WHEN deleted_at IS NULL THEN email ELSE NULL END')->unique('users_active_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_active_email_unique');
            $table->dropColumn('active_email');
            $table->unique('email', 'users_email_unique');
        });
    }
};

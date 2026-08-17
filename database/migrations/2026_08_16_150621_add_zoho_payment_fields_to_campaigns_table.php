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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('zoho_payment_link_id')->nullable()->after('payment_confirmed_by')->unique();
            $table->string('zoho_payment_url', 1000)->nullable()->after('zoho_payment_link_id');
            $table->string('zoho_payment_id')->nullable()->after('zoho_payment_url')->unique();
            $table->timestamp('payment_paid_at')->nullable()->after('zoho_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'zoho_payment_link_id',
                'zoho_payment_url',
                'zoho_payment_id',
                'payment_paid_at'
            ]);
        });
    }
};

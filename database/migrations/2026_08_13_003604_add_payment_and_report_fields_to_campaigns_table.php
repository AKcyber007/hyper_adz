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
            $table->string('payment_status')->default('Unpaid')->after('status');
            $table->decimal('payment_amount', 15, 2)->nullable()->after('payment_status');
            $table->date('payment_due_date')->nullable()->after('payment_amount');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_due_date');
            $table->foreignId('payment_confirmed_by')->nullable()->constrained('users')->nullOnDelete()->after('payment_confirmed_at');
            $table->string('report_path')->nullable()->after('rejection_reason');
            $table->string('report_name')->nullable()->after('report_path');
            $table->timestamp('report_uploaded_at')->nullable()->after('report_name');
            $table->string('rejection_type')->nullable()->after('rejection_reason');
            $table->text('creative_review_notes')->nullable()->after('rejection_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['payment_confirmed_by']);
            $table->dropColumn([
                'payment_status',
                'payment_amount',
                'payment_due_date',
                'payment_confirmed_at',
                'payment_confirmed_by',
                'report_path',
                'report_name',
                'report_uploaded_at',
                'rejection_type',
                'creative_review_notes'
            ]);
        });
    }
};

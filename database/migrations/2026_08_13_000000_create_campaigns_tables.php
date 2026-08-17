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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_code')->unique();
            $table->foreignId('advertiser_id')->constrained('advertiser_profiles')->onDelete('cascade');
            $table->string('campaign_name');
            $table->text('description')->nullable();
            $table->string('campaign_type'); // Brand Awareness, Product Launch, etc.
            $table->foreignId('industry_id')->nullable()->constrained('industries')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('budget', 15, 2);
            
            // Creative upload
            $table->string('creative_path')->nullable();
            $table->string('creative_name')->nullable();

            // Statuses
            $table->string('status')->default('Draft'); // Draft, Submitted, Pending Review, Approved, Scheduled, Running, Completed, Rejected
            $table->string('approval_status')->default('Pending Review'); // Pending Review, Approved, Rejected

            // Review Details
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campaign_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('campaign_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('action'); // Created, Submitted, Approved, Rejected, etc.
            $table->string('performed_by'); // User name or role
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_activity_logs');
        Schema::dropIfExists('campaign_location');
        Schema::dropIfExists('campaigns');
    }
};

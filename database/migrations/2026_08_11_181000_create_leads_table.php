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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('lead_code')->unique();
            $table->enum('lead_type', ['contact', 'advertiser', 'location_partner']);
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->text('message')->nullable();
            $table->string('source')->default('website');
            $table->enum('status', ['new', 'contacted', 'qualified', 'approved', 'rejected'])->default('new');
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assigned_admin_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

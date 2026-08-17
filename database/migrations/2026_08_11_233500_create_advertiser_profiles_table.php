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
        Schema::create('advertiser_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('advertiser_code')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('website')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('logo_path')->nullable();
            $table->unsignedBigInteger('industry_id');
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('lead_id')
                  ->references('id')
                  ->on('leads')
                  ->onDelete('set null');

            $table->foreign('industry_id')
                  ->references('id')
                  ->on('industries')
                  ->onDelete('restrict');

            $table->foreign('approved_by')
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
        Schema::dropIfExists('advertiser_profiles');
    }
};

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
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('screen_code')->unique();
            $table->string('screen_identifier')->unique()->nullable();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('screen_type_id')->constrained('screen_types');
            $table->text('description')->nullable();
            $table->string('orientation');
            $table->integer('screen_width')->nullable();
            $table->integer('screen_height')->nullable();
            $table->string('resolution')->nullable();
            $table->string('operating_hours')->nullable();
            $table->integer('daily_impressions')->default(0);
            $table->string('status')->default('active');
            $table->string('availability_status')->default('available');
            $table->string('supported_formats')->default('MP4,JPG,PNG');
            $table->integer('max_video_duration')->default(15)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};

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
        Schema::table('locations', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->integer('audience_count')->default(0)->after('daily_footfall');
            $table->integer('repeats_per_day')->default(0)->after('audience_count');
            $table->json('audience_type')->nullable()->after('repeats_per_day');
            $table->json('operating_days')->nullable()->after('operating_hours');
            $table->time('opening_time')->nullable()->after('operating_days');
            $table->time('closing_time')->nullable()->after('opening_time');
            $table->string('screen_size')->nullable()->after('closing_time');
            $table->string('screen_orientation')->nullable()->after('screen_size');
            $table->boolean('video_supported')->default(false)->after('screen_orientation');
            $table->boolean('audio_supported')->default(false)->after('video_supported');
            $table->text('nearby_places')->nullable()->after('audio_supported');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'audience_count',
                'repeats_per_day',
                'audience_type',
                'operating_days',
                'opening_time',
                'closing_time',
                'screen_size',
                'screen_orientation',
                'video_supported',
                'audio_supported',
                'nearby_places',
            ]);
        });
    }
};

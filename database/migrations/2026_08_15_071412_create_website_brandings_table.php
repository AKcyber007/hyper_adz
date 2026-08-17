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
        Schema::create('website_brandings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('footer_logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('dark_logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_brandings');
    }
};

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
        Schema::table('services', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('description');
            $table->string('hero_video_url')->nullable()->after('hero_image');
            $table->string('sample_link')->nullable()->after('hero_video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['hero_image', 'hero_video_url', 'sample_link']);
        });
    }
};

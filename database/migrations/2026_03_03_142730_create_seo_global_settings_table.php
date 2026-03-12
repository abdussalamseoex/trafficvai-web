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
        Schema::create('seo_global_settings', function (Blueprint $table) {
            $table->id();
            $table->longText('robots_txt')->nullable();
            $table->boolean('sitemap_enabled')->default(true);
            $table->timestamp('sitemap_last_generated')->nullable();
            $table->string('ga_code')->nullable(); // Google Analytics ID
            $table->text('gsc_verification')->nullable(); // Search Console meta tag
            $table->longText('header_scripts')->nullable();
            $table->longText('footer_scripts')->nullable();
            $table->string('default_og_image')->nullable();
            $table->string('site_name')->nullable();
            $table->string('twitter_handle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_global_settings');
    }
};

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
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->index(['entity_type', 'entity_id']);

            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('slug')->nullable(); // Unique constrained in code/observer if needed per type
            $table->string('canonical_url')->nullable();
            $table->string('focus_keyword')->nullable();

            // Social/OG
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('image_alt_text')->nullable();

            // Advanced
            $table->string('breadcrumb_title')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->string('robots_directive')->default('index,follow');
            $table->longText('schema_json')->nullable();

            $table->timestamp('publish_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
    }
};

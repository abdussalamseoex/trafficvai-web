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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('service_tier')->nullable()->after('guest_post_site_id');
            $table->longText('article_body')->nullable()->after('guest_post_anchor');
            $table->string('published_url')->nullable()->after('article_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_tier', 'article_body', 'published_url']);
        });
    }
};

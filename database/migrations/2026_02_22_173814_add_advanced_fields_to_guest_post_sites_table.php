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
        Schema::table('guest_post_sites', function (Blueprint $table) {
            $table->string('link_type')->default('DoFollow')->after('niche');
            $table->integer('max_links_allowed')->default(1)->after('link_type');
            $table->boolean('is_sponsored')->default(false)->after('max_links_allowed');
            $table->string('language')->default('English')->after('is_sponsored');
            $table->string('service_type')->default('Guest Post')->after('language');
            $table->integer('spam_score')->nullable()->after('service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_post_sites', function (Blueprint $table) {
        //
        });
    }
};

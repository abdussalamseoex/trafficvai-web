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
            $table->integer('delivery_time_days')->nullable()->after('price');
            $table->integer('word_count')->nullable()->after('price_creation_placement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_post_sites', function (Blueprint $table) {
            $table->dropColumn(['delivery_time_days', 'word_count']);
        });
    }
};

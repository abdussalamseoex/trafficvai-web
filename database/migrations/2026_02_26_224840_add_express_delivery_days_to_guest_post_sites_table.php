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
            $table->integer('express_delivery_time_days')->nullable()->after('delivery_time_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_post_sites', function (Blueprint $table) {
            $table->dropColumn('express_delivery_time_days');
        });
    }
};

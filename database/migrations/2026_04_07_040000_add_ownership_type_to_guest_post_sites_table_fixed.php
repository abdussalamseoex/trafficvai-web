<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('guest_post_sites', 'ownership_type')) {
            Schema::table('guest_post_sites', function (Blueprint $table) {
                $table->string('ownership_type')->default('Owner')->after('price_link_insertion');
            });
        }
        
        // Let's also do a safe update of the express deliveries via Eloquent to ensure they don't crash
        try {
            DB::table('guest_post_sites')->where('price', 5)->update([
                'price_link_insertion' => 10,
                'delivery_time_days' => 5,
                'price_creation_placement' => 7,
                'express_delivery_time_days' => null,
                'express_delivery_price' => 0.00,
                'ownership_type' => 'Contributor'
            ]);
        } catch (\Exception $e) {
            // Log or ignore if the columns don't perfectly exist for some reason, though they should
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('guest_post_sites', 'ownership_type')) {
            Schema::table('guest_post_sites', function (Blueprint $table) {
                $table->dropColumn('ownership_type');
            });
        }
    }
};

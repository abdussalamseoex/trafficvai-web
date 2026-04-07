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
        // Let's modify the column to be genuinely nullable via raw SQL 
        // to avoid doctrine/dbal dependency issues.
        try {
            DB::statement('ALTER TABLE guest_post_sites MODIFY express_delivery_price DECIMAL(10,2) NULL DEFAULT NULL;');
            
            // Set everything to null 
            DB::table('guest_post_sites')->update([
                'express_delivery_price' => null
            ]);
        } catch (\Exception $e) { 
            // Fallback if alter table fails: just set it to 0.00 so it doesn't crash
            DB::table('guest_post_sites')->update([
                'express_delivery_price' => 0.00
            ]);
        }

        // Fix Ownership strictly using timestamps.
        // The import batch was exactly "2026-04-07 00:00:00"
        DB::table('guest_post_sites')->where('created_at', '2026-04-07 00:00:00')->update([
            'ownership_type' => 'Contributor'
        ]);

        // Anything else is an Owner site.
        DB::table('guest_post_sites')->where('created_at', '!=', '2026-04-07 00:00:00')->update([
            'ownership_type' => 'Owner'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Force all express delivery prices to strictly 0.00
        DB::table('guest_post_sites')->update([
            'express_delivery_price' => 0.00
        ]);

        // Just to ensure absolute consistency:
        // Any site that was NOT part of the recent $5 import should be 'Owner'.
        // Any site that WAS part of the recent $5 import should be 'Contributor'.
        DB::table('guest_post_sites')->where('price', '!=', 5)->update([
            'ownership_type' => 'Owner'
        ]);

        DB::table('guest_post_sites')->where('price', 5)->update([
            'ownership_type' => 'Contributor'
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

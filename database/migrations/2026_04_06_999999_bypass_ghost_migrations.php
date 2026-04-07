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
        $batch = DB::table('migrations')->max('batch') ?? 1;
        
        // We manually insert the broken ghost migrations into the migrations table
        // so that Laravel entirely skips executing them!
        $ghosts = [
            '2026_04_07_010000_update_imported_guest_posts',
            '2026_04_07_020000_add_ownership_type_to_guest_post_sites_table'
        ];

        foreach ($ghosts as $ghost) {
            $exists = DB::table('migrations')->where('migration', $ghost)->exists();
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $ghost,
                    'batch' => $batch
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

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
        Schema::table('guest_post_sites', function (Blueprint $table) {
            $table->text('niche')->change();
        });

        // Convert existing single strings or comma-separated strings to JSON arrays
        $sites = DB::table('guest_post_sites')->get();
        foreach ($sites as $site) {
            if (!empty($site->niche) && !str_starts_with($site->niche, '[')) {
                $categories = array_map('trim', explode(',', $site->niche));
                DB::table('guest_post_sites')
                    ->where('id', $site->id)
                    ->update(['niche' => json_encode($categories)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_post_sites', function (Blueprint $table) {
            $table->string('niche', 255)->change();
        });
    }
};

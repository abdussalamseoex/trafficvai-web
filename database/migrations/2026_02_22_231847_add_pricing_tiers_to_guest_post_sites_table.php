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
            $table->decimal('price_creation_placement', 10, 2)->nullable()->after('price');
            $table->decimal('price_link_insertion', 10, 2)->nullable()->after('price_creation_placement');
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

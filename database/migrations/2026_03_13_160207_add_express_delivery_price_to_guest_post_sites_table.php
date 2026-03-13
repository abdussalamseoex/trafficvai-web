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
            $table->decimal('express_delivery_price', 10, 2)->default(50.00)->after('express_delivery_time_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_post_sites', function (Blueprint $table) {
            $table->dropColumn('express_delivery_price');
        });
    }
};

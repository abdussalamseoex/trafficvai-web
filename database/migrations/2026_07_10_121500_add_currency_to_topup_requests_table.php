<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('topup_requests') && !Schema::hasColumn('topup_requests', 'currency')) {
            Schema::table('topup_requests', function (Blueprint $table) {
                $table->string('currency', 10)->default('USD')->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('topup_requests') && Schema::hasColumn('topup_requests', 'currency')) {
            Schema::table('topup_requests', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
};

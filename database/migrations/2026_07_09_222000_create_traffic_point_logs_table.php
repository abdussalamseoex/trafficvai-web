<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traffic_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'purchase' or 'usage'
            $table->integer('points'); // e.g. 10000 or -30000
            $table->decimal('cost_usd', 10, 2)->default(0.00); // e.g. 10.00
            $table->string('description'); // e.g. "Purchased Starter Pack" or "Launched Campaign #TV-12345"
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_point_logs');
    }
};

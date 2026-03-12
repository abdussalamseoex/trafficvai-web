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
        Schema::create('transactions', function (Blueprint $col) {
            $col->id();
            $col->foreignId('user_id')->constrained()->onDelete('cascade');
            $col->enum('type', ['credit', 'debit']);
            $col->enum('source', ['order', 'topup', 'adjustment', 'refund']);
            $col->decimal('amount', 15, 2);
            $col->string('description');
            $col->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $col->json('meta')->nullable();
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

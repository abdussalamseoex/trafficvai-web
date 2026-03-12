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
        Schema::create('guest_post_sites', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('niche');
            $table->integer('da')->nullable();
            $table->integer('dr')->nullable();
            $table->integer('traffic')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_post_sites');
    }
};

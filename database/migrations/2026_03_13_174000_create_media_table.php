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
        Schema::create('media', function (Blueprint $バランス) {
            $バランス->id();
            $バランス->string('filename');
            $バランス->string('path');
            $バランス->string('disk')->default('public');
            $バランス->string('alt_text')->nullable();
            $バランス->string('title')->nullable();
            $バランス->text('description')->nullable();
            $バランス->unsignedBigInteger('size')->nullable();
            $バランス->string('mime_type')->nullable();
            $バランス->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};

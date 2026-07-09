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
        Schema::create('traffic_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('external_order_id')->unique(); // e.g. #TV-1045
            $table->string('remote_campaign_id')->nullable(); // ID returned from surf.abguestpost.net
            $table->string('campaign_type')->default('direct'); // 'direct' or 'search'
            $table->string('url');
            $table->integer('total_limit')->default(1000);
            $table->integer('hourly_limit')->default(100);
            $table->integer('daily_limit')->default(1000);
            $table->integer('duration')->default(60); // 60, 90, 120
            $table->integer('sub_page_visits')->default(0); // 0, 1, 2, 3
            $table->integer('sub_page_duration')->default(30);
            $table->string('device_type')->default('All'); // Desktop, Mobile, All
            $table->string('target_country')->default('All');
            $table->string('search_engine')->nullable(); // google, bing, yahoo
            $table->text('keywords')->nullable(); // JSON array or string
            $table->integer('max_page')->nullable(); // 1, 3, 5, 10
            $table->string('captcha_mode')->default('normal'); // 'normal' or 'premium'
            
            // Point system & Delivery tracking
            $table->integer('points_deducted')->default(0);
            $table->integer('hits_delivered')->default(0);
            $table->string('status')->default('active'); // active, paused, completed
            $table->timestamp('expires_at')->nullable(); // 30 days validity per requirement
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_campaigns');
    }
};

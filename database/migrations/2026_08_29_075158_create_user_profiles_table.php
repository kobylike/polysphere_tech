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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Core profile fields
            $table->text('about_me')->nullable();
            $table->json('skills')->nullable();
            $table->json('education')->nullable();
            $table->json('social_links')->nullable();
            // Team display fields (admin managed)
            $table->string('position')->nullable();
            $table->boolean('is_featured_team')->default(false);
            $table->integer('display_order')->default(0);

            $table->timestamps();

            // Index for faster queries
            $table->index('user_id');
            $table->index('is_featured_team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};

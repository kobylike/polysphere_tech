<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            // ── Identity ────────────────────────────────────────────
            $table->string('code', 20)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('icon', 60)->nullable();

            // ── Hierarchy ───────────────────────────────────────────
            $table->foreignId('parent_id')->nullable()
                ->constrained('departments')->nullOnDelete();
            $table->foreignId('head_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->unsignedInteger('display_order')->default(0);

            // ── Contact ─────────────────────────────────────────────
            $table->string('email', 120)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('location', 120)->nullable();

            // ── Operations ──────────────────────────────────────────
            $table->decimal('budget', 15, 2)->nullable();
            $table->unsignedInteger('headcount_target')->nullable();
            $table->date('founded_at')->nullable();
            $table->boolean('is_customer_facing')->default(false);
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

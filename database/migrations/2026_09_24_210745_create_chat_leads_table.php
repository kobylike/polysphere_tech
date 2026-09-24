<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_leads', function (Blueprint $table) {
            $table->id();

            // ─── Contact ─────────────────────────────────────────
            $table->string('email')->index();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();

            // ─── Session / request metadata ──────────────────────
            $table->string('session_id', 100)->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('page_url', 500)->nullable();

            // ─── Source & pipeline ───────────────────────────────
            $table->string('source', 50)->default('chat-widget');
            $table->string('status', 30)->default('new')->index();

            // ─── Lead management ─────────────────────────────────
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedTinyInteger('score')->default(0);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_spam')->default(false);

            // ─── Message content ─────────────────────────────────
            $table->string('intent', 500)->nullable();
            $table->text('message')->nullable();
            $table->json('conversation')->nullable();

            // ─── Lifecycle timestamps ────────────────────────────
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->foreignId('contacted_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // ─── Composite indexes for the admin UI ──────────────
            $table->unique(['email', 'session_id']);
            $table->index(['status', 'created_at']);
            $table->index(['is_spam', 'created_at']);
            $table->index(['is_starred', 'created_at']);
            $table->index('score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_leads');
    }
};

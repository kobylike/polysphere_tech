<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_token', 64)->unique();

            // ── Basics ──────────────────────────────────────
            $table->string('name');
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();

            // ── Links ───────────────────────────────────────
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('personal_site_url')->nullable();
            $table->string('behance_url')->nullable();
            $table->string('dribbble_url')->nullable();
            $table->string('writing_samples_url')->nullable();

            // ── Experience ──────────────────────────────────
            $table->string('current_role')->nullable();
            $table->string('current_company')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->string('availability')->nullable();
            $table->string('salary_expectation')->nullable();

            // ── Remote-specific ─────────────────────────────
            $table->string('timezone')->nullable();
            $table->string('work_authorization')->nullable();

            // ── Documents ───────────────────────────────────
            $table->string('cv_path');
            $table->string('cv_original_name');
            $table->text('cover_letter')->nullable();

            // ── Branch-specific Q&A ─────────────────────────
            $table->json('branch_answers')->nullable();

            // ── Meta ────────────────────────────────────────
            $table->string('source')->nullable();
            $table->string('referrer_name')->nullable();
            $table->boolean('gdpr_consent')->default(false);
            $table->ipAddress('ip_address')->nullable();

            // ── Pipeline (admin side) ───────────────────────
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['vacancy_id', 'status']);
            $table->index('email');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

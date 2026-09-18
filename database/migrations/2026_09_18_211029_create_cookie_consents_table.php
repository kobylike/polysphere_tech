<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();

            // Safe external reference (don't leak sequential IDs if you ever
            // expose a "view my consent history" endpoint).
            $table->uuid('uuid')->unique();

            // Nullable on purpose: we log a consent row for guests too, so we
            // can prove *any* visitor consented, not only logged-in accounts.
            // On login, the guest's cookie-based consent is migrated onto
            // their user_id (see CookieConsent::restoreFromCookie()).
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // For guests, this stores the Laravel session ID so a consent row
            // can still be tied back to "a specific visitor" for audit purposes.
            $table->string('session_id', 100)->nullable();

            $table->unsignedInteger('version');
            $table->json('categories');

            // accept_all | reject_all | custom | restored
            $table->string('method', 20)->default('custom');

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('consented_at');

            // Set when a user later withdraws consent (GDPR "right to withdraw").
            // We never delete history — we mark it revoked instead.
            $table->timestamp('revoked_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'version', 'consented_at'], 'cookie_consents_user_version_date_idx');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'status',
        'verification_token',
        'subscribed_at',
        'unsubscribed_at',
        'subscribed_ip',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    // ─── Status checks ──────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnsubscribed(): bool
    {
        return $this->status === 'unsubscribed';
    }

    // ─── Actions ────────────────────────────────────────────────────────────

    public function markAsActive(): void
    {
        $this->update([
            'status' => 'active',
            'subscribed_at' => now(),
            // ─── Keep verification_token for unsubscribe ───────────────────
            // Do NOT set it to null – it's needed for the unsubscribe link.
        ]);
    }

    public function markAsUnsubscribed(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function generateVerificationToken(): void
    {
        $this->update([
            'verification_token' => Str::random(60),
        ]);
    }

    // ─── Regenerate token and resend verification (helper) ───────────────

    public function refreshVerificationToken(): string
    {
        $token = Str::random(60);
        $this->update(['verification_token' => $token]);
        return $token;
    }
}

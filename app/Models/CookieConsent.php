<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CookieConsent extends Model
{
    use HasFactory;

    public const METHOD_ACCEPT_ALL = 'accept_all';
    public const METHOD_REJECT_ALL = 'reject_all';
    public const METHOD_CUSTOM     = 'custom';
    public const METHOD_RESTORED   = 'restored';

    protected $fillable = [
        'uuid',
        'user_id',
        'session_id',
        'version',
        'categories',
        'method',
        'ip_address',
        'user_agent',
        'consented_at',
        'revoked_at',
    ];

    protected $casts = [
        'categories'   => 'array',
        'consented_at' => 'datetime',
        'revoked_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $consent) {
            $consent->uuid = $consent->uuid ?: (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Only rows that haven't been withdrawn. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }

    /**
     * The most recent active consent for a given user (or guest, if $userId
     * is null and you filter by session_id yourself) on a given policy version.
     */
    public function scopeLatestFor(Builder $query, ?int $userId, int $version): Builder
    {
        return $query->active()
            ->where('user_id', $userId)
            ->where('version', $version)
            ->latest('consented_at');
    }

    public function hasCategory(string $key): bool
    {
        return (bool) ($this->categories[$key] ?? false);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Comment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'post_id',
        'user_id',
        'body',
        'parent_id',
        'guest_name',
        'guest_email',
        'verification_token',
        'verified_at',
        'ip_address',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // ─── Activity Log ───────────────────────────────────────────────
    public function getActivitylogOptions(): LogOptions
    {
        $author = $this->user?->name ?? $this->guest_name ?? 'Anonymous';
        $post   = $this->post?->title ?? 'unknown post';

        return LogOptions::defaults()
            ->logOnly(['body', 'verified_at', 'parent_id', 'post_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Comment by {$author} on '{$post}' was created",
                'updated' => "Comment by {$author} on '{$post}' was updated",
                'deleted' => "Comment by {$author} on '{$post}' was deleted",
                default   => "Comment by {$author} on '{$post}' was {$eventName}",
            })
            ->useLogName('comment');
    }

    // ─── Relationships ──────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('user')
            ->orderBy('created_at', 'asc');
    }

    public function repliesRecursive(): HasMany
    {
        return $this->replies()->with('repliesRecursive');
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('verified_at');
    }

    public function scopeVisible($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeFromGuests($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeFromUsers($query)
    {
        return $query->whereNotNull('user_id');
    }

    // ─── Accessors ──────────────────────────────────────────────────
    public function getAuthorNameAttribute(): string
    {
        return $this->user_id
            ? ($this->user?->name ?? 'User')
            : ($this->guest_name ?? 'Guest');
    }

    public function getAuthorEmailAttribute(): ?string
    {
        return $this->user_id
            ? $this->user?->email
            : $this->guest_email;
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->user_id !== null || $this->verified_at !== null;
    }

    public function getIsReplyAttribute(): bool
    {
        return ! is_null($this->parent_id);
    }

    /** True when submitted by an anonymous visitor (no user_id). */
    public function getIsGuestAttribute(): bool
    {
        return is_null($this->user_id);
    }

    /** True when submitted by a registered/logged-in user. */
    public function getIsUserAttribute(): bool
    {
        return ! is_null($this->user_id);
    }

    public function getGravatarAttribute(): string
    {
        $email = $this->author_email ?? '';
        $hash  = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=64";
    }

    /** Human-readable type label. */
    public function getAuthorTypeLabelAttribute(): string
    {
        return $this->is_guest ? 'Guest' : 'Registered';
    }

    /** Bootstrap badge class for the author type. */
    public function getAuthorTypeBadgeAttribute(): string
    {
        return $this->is_guest ? 'bg-secondary' : 'bg-primary';
    }
}

<?php
// app/Models/Comment.php

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
        'ip_address'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $author = $this->user?->name ?? $this->guest_name ?? 'Anonymous';
        $post = $this->post?->title ?? 'unknown post';
        return LogOptions::defaults()
            ->logAll()
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
        return $this->hasMany(Comment::class, 'parent_id')->with('user')->orderBy('created_at', 'asc');
    }

    public function repliesRecursive(): HasMany
    {
        return $this->replies()->with('repliesRecursive');
    }

    // ─── Scopes ──────────────────────────────────────────────────────

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
        return $query->where(function ($q) {
            $q->whereNotNull('user_id')      // logged-in users are always visible
                ->orWhereNotNull('verified_at');
        });
    }

    // ─── Accessors ──────────────────────────────────────────────────

    public function getAuthorNameAttribute(): string
    {
        return $this->user_id ? $this->user->name : ($this->guest_name ?? 'Guest');
    }

    public function getAuthorEmailAttribute(): ?string
    {
        return $this->user_id ? $this->user->email : $this->guest_email;
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->user_id !== null || $this->verified_at !== null;
    }
}

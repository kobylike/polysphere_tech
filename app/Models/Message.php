<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * ─────────────────────────────────────────────────────────────
     *  FILLABLE
     *  All original columns preserved, plus the new advanced
     *  messaging columns added by the migration.
     * ─────────────────────────────────────────────────────────────
     */
    protected $fillable = [
        // Original
        'sender_id',
        'receiver_id',
        'body',
        'read',
        'attachment_path',
        'attachment_type',
        'attachment_name',
        'attachment_size',

        // Advanced
        'reply_to_id',
        'edited_at',
        'delivered_at',
        'deleted_at',              // soft deletes
        'deleted_for_everyone',
        'is_forwarded',
        'is_pinned',
        'metadata',
    ];

    /**
     * ─────────────────────────────────────────────────────────────
     *  CASTS
     * ─────────────────────────────────────────────────────────────
     */
    protected $casts = [
        'read'                 => 'boolean',
        'deleted_for_everyone' => 'boolean',
        'is_forwarded'         => 'boolean',
        'is_pinned'            => 'boolean',
        'delivered_at'         => 'datetime',
        'edited_at'            => 'datetime',
        'metadata'             => 'array',
    ];

    /**
     * ─────────────────────────────────────────────────────────────
     *  APPENDS
     *  Auto-attach virtual attributes when serialized to array/JSON.
     * ─────────────────────────────────────────────────────────────
     */
    protected $appends = [
        'preview',
        'is_voice',
        'rendered_body',
    ];

    /**
     * ─────────────────────────────────────────────────────────────
     *  RELATIONS
     * ─────────────────────────────────────────────────────────────
     */

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /** The message this one is replying to (nullable). */
    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    /** All direct replies to this message. */
    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    /** Emoji reactions on this message. */
    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }

    /** Per-user state (starred / hidden-for-me). */
    public function userStates()
    {
        return $this->hasMany(MessageUserState::class);
    }

    /**
     * ─────────────────────────────────────────────────────────────
     *  ACCESSORS
     * ─────────────────────────────────────────────────────────────
     */

    /** Whether the audio attachment is a voice note (recorded) vs uploaded. */
    public function getIsVoiceAttribute(): bool
    {
        return $this->attachment_type === 'audio'
            && (bool) ($this->metadata['voice'] ?? false);
    }

    /** Short human-readable preview string (used in list, replies, notifications). */
    public function getPreviewAttribute(): string
    {
        if ($this->deleted_for_everyone) {
            return 'This message was deleted';
        }

        return match ($this->attachment_type) {
            'image'    => '📷 Photo',
            'video'    => '🎬 Video',
            'audio'    => $this->is_voice ? '🎤 Voice message' : '🎵 Audio',
            'document' => '📄 ' . ($this->attachment_name ?? 'Document'),
            'sticker'  => $this->attachment_path ?: '😀',
            default    => Str::limit((string) $this->body, 60),
        };
    }

    /**
     * Lightweight markdown → HTML renderer for the message body.
     * Supports: **bold**, *italic*, `code`, ~~strike~~ and clickable URLs.
     * Output is pre-escaped so it's safe to use with {!! !!}.
     */
    public function getRenderedBodyAttribute(): string
    {
        if ($this->deleted_for_everyone || blank($this->body)) {
            return '';
        }

        $safe = e($this->body);

        $safe = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $safe);
        $safe = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $safe);
        $safe = preg_replace('/`(.+?)`/s', '<code>$1</code>', $safe);
        $safe = preg_replace('/~~(.+?)~~/s', '<del>$1</del>', $safe);

        // Clickable URLs (safe because body was escaped above)
        $safe = preg_replace(
            '~(https?://[^\s<]+)~i',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="chat-link">$1</a>',
            $safe
        );

        return nl2br($safe);
    }

    /**
     * ─────────────────────────────────────────────────────────────
     *  HELPERS
     * ─────────────────────────────────────────────────────────────
     */

    /**
     * Aggregate reactions for display (grouped by emoji).
     *
     * @return array<int, array{emoji:string,count:int,mine:bool}>
     */
    public function reactionSummary(?int $viewerId = null): array
    {
        // Use loaded relation when available; fall back to a query.
        $reactions = $this->relationLoaded('reactions')
            ? $this->reactions
            : $this->reactions()->get();

        return $reactions
            ->groupBy('emoji')
            ->map(fn($group) => [
                'emoji' => $group->first()->emoji,
                'count' => $group->count(),
                'mine'  => $viewerId ? $group->contains('user_id', $viewerId) : false,
            ])
            ->values()
            ->toArray();
    }

    /** Has the given user starred this message? */
    public function isStarredBy(int $userId): bool
    {
        $states = $this->relationLoaded('userStates')
            ? $this->userStates
            : $this->userStates()->get();

        return $states
            ->where('user_id', $userId)
            ->where('is_starred', true)
            ->isNotEmpty();
    }

    /** Has the given user deleted this message "for me"? */
    public function isDeletedBy(int $userId): bool
    {
        $states = $this->relationLoaded('userStates')
            ? $this->userStates
            : $this->userStates()->get();

        return $states
            ->where('user_id', $userId)
            ->where('is_deleted', true)
            ->isNotEmpty();
    }

    /** Delivery status string for UI (sent | delivered | read). */
    public function getDeliveryStatusAttribute(): string
    {
        if ($this->read)         return 'read';
        if ($this->delivered_at) return 'delivered';
        return 'sent';
    }

    /**
     * ─────────────────────────────────────────────────────────────
     *  SCOPES
     * ─────────────────────────────────────────────────────────────
     */

    /** Conversation between two users (in either direction). */
    public function scopeBetween($query, int $userIdA, int $userIdB)
    {
        return $query->where(function ($q) use ($userIdA, $userIdB) {
            $q->where('sender_id', $userIdA)->where('receiver_id', $userIdB);
        })->orWhere(function ($q) use ($userIdA, $userIdB) {
            $q->where('sender_id', $userIdB)->where('receiver_id', $userIdA);
        });
    }

    /** Unread messages for a given receiver. */
    public function scopeUnreadFor($query, int $userId)
    {
        return $query->where('receiver_id', $userId)->where('read', false);
    }

    /** Only pinned messages. */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /** Only starred messages for the given user. */
    public function scopeStarredBy($query, int $userId)
    {
        return $query->whereHas('userStates', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('is_starred', true);
        });
    }

    /** Full-text-ish search of message body. */
    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }
        return $query->where('body', 'like', '%' . $term . '%');
    }

    /**
     * ─────────────────────────────────────────────────────────────
     *  ROUTE MODEL BINDING
     * ─────────────────────────────────────────────────────────────
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}

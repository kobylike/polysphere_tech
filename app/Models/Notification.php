<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Notification extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'type',
        'title',
        'body',
        'link',
        'icon',
        'sender_name',
        'notifiable_type',
        'notifiable_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        $title = $this->title ?? 'Untitled';
        $user = $this->notifiable?->name ?? 'unknown user';
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Notification '{$title}' was sent to {$user}",
                'updated' => "Notification '{$title}' for {$user} was updated",
                'deleted' => "Notification '{$title}' for {$user} was deleted",
                default   => "Notification '{$title}' for {$user} was {$eventName}",
            })
            ->useLogName('notification');
    }
    // ─── Relationships ──────────────────────────────────────────────

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}

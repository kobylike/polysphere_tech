<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatLead extends Model
{
    use HasFactory;

    public const STATUSES = [
        'new'       => ['label' => 'New',       'color' => '#6366f1', 'icon' => 'fa-sparkles'],
        'contacted' => ['label' => 'Contacted', 'color' => '#0ea5e9', 'icon' => 'fa-paper-plane'],
        'qualified' => ['label' => 'Qualified', 'color' => '#10b981', 'icon' => 'fa-circle-check'],
        'converted' => ['label' => 'Converted', 'color' => '#16a34a', 'icon' => 'fa-trophy'],
        'lost'      => ['label' => 'Lost',      'color' => '#94a3b8', 'icon' => 'fa-circle-xmark'],
        'spam'      => ['label' => 'Spam',      'color' => '#ef4444', 'icon' => 'fa-ban'],
    ];

    public const SOURCES = [
        'chat-widget'  => 'Chat Widget',
        'contact-form' => 'Contact Form',
        'import'       => 'Import',
    ];

    protected $fillable = [
        'email',
        'name',
        'phone',
        'company',
        'session_id',
        'ip_address',
        'user_agent',
        'source',
        'status',
        'notes',
        'tags',
        'score',
        'is_starred',
        'is_spam',
        'intent',
        'message',
        'conversation',
        'page_url',
        'notified_at',
        'contacted_at',
        'contacted_by',
    ];

    protected $casts = [
        'conversation' => 'array',
        'tags'         => 'array',
        'notified_at'  => 'datetime',
        'contacted_at' => 'datetime',
        'is_starred'   => 'boolean',
        'is_spam'      => 'boolean',
        'score'        => 'integer',
    ];

    /* ──────────────────────────────────────────────────────────── */
    /*  Lifecycle                                                  */
    /* ──────────────────────────────────────────────────────────── */

    protected static function booted(): void
    {
        static::creating(function (ChatLead $lead) {
            if (empty($lead->score)) {
                $lead->score = static::computeScore($lead);
            }
            if (empty($lead->status)) {
                $lead->status = 'new';
            }
        });
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Relationships                                              */
    /* ──────────────────────────────────────────────────────────── */

    public function contactedBy()
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Accessors                                                  */
    /* ──────────────────────────────────────────────────────────── */

    public function getStatusMetaAttribute(): array
    {
        return self::STATUSES[$this->status] ?? self::STATUSES['new'];
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_meta['label'];
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status_meta['color'];
    }

    public function getInitialsAttribute(): string
    {
        $name = trim((string) $this->name);
        if ($name) {
            $parts = preg_split('/\s+/', $name);
            $first = mb_substr($parts[0] ?? '', 0, 1);
            $last  = mb_substr(end($parts) ?: '', 0, 1);
            return strtoupper($first . $last) ?: '?';
        }
        return strtoupper(mb_substr((string) $this->email, 0, 2));
    }

    public function getScoreTierAttribute(): string
    {
        return match (true) {
            $this->score >= 70 => 'hot',
            $this->score >= 40 => 'warm',
            default            => 'cold',
        };
    }

    public function getMessageCountAttribute(): int
    {
        return count($this->conversation ?? []);
    }

    public function getFirstPageAttribute(): ?string
    {
        return $this->page_url;
    }

    public function getShortPageAttribute(): ?string
    {
        if (! $this->page_url) {
            return null;
        }
        $path = parse_url($this->page_url, PHP_URL_PATH) ?: '/';
        return $path;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Scoring                                                    */
    /* ──────────────────────────────────────────────────────────── */

    public static function computeScore(ChatLead $lead): int
    {
        $score = 10;

        if (! empty($lead->name))    $score += 15;
        if (! empty($lead->phone))   $score += 20;
        if (! empty($lead->company)) $score += 15;

        $messageLength = mb_strlen((string) $lead->message);
        if ($messageLength > 60)  $score += 5;
        if ($messageLength > 150) $score += 10;

        $haystack = strtolower(($lead->message ?? '') . ' ' . ($lead->intent ?? ''));
        $highIntent = ['budget', 'timeline', 'urgent', 'ready to start', 'looking for', 'need a', 'build a', 'we want', 'quote', 'proposal', 'meeting', 'call', 'demo'];
        foreach ($highIntent as $kw) {
            if (str_contains($haystack, $kw)) {
                $score += 15;
                break;
            }
        }

        if (! empty($lead->page_url)) {
            if (str_contains($lead->page_url, '/services')) $score += 10;
            if (str_contains($lead->page_url, '/contact'))  $score += 10;
            if (str_contains($lead->page_url, '/careers'))  $score -= 5;
        }

        // Multiple leads from the same email → engaged
        if (! empty($lead->email)) {
            $otherSessions = static::where('email', $lead->email)
                ->when($lead->exists, fn($q) => $q->where('id', '!=', $lead->id))
                ->distinct('session_id')
                ->count('session_id');
            if ($otherSessions > 0) $score += 10;
        }

        return max(0, min(100, $score));
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Scopes                                                     */
    /* ──────────────────────────────────────────────────────────── */

    public function scopeNotSpam($q)
    {
        return $q->where('is_spam', false);
    }

    public function scopeOnlySpam($q)
    {
        return $q->where('is_spam', true);
    }

    public function scopeStarred($q)
    {
        return $q->where('is_starred', true);
    }

    public function scopeHot($q)
    {
        return $q->where('score', '>=', 70);
    }
}

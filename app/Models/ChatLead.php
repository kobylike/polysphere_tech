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

    public const URGENCIES = [
        'high'   => ['label' => 'High',   'color' => '#ef4444', 'icon' => 'fa-bolt'],
        'medium' => ['label' => 'Medium', 'color' => '#f59e0b', 'icon' => 'fa-clock'],
        'low'    => ['label' => 'Low',    'color' => '#94a3b8', 'icon' => 'fa-hourglass'],
    ];

    protected $fillable = [
        'email',
        'name',
        'phone',
        'company',
        'services_interested',
        'industry',
        'budget_range',
        'timeline',
        'urgency',
        'preferred_contact',
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
        'conversation'        => 'array',
        'tags'                => 'array',
        'services_interested' => 'array',
        'notified_at'         => 'datetime',
        'contacted_at'        => 'datetime',
        'is_starred'          => 'boolean',
        'is_spam'             => 'boolean',
        'score'               => 'integer',
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

    public function getUrgencyMetaAttribute(): ?array
    {
        if (empty($this->urgency)) {
            return null;
        }

        return self::URGENCIES[$this->urgency] ?? null;
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

    public function getShortPageAttribute(): ?string
    {
        if (! $this->page_url) {
            return null;
        }
        $path = parse_url($this->page_url, PHP_URL_PATH) ?: '/';
        return $path;
    }

    public function getPrimaryServiceAttribute(): ?string
    {
        $services = $this->services_interested ?? [];
        return $services[0] ?? null;
    }

    public function getServicesCountAttribute(): int
    {
        return count($this->services_interested ?? []);
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Scoring                                                    */
    /* ──────────────────────────────────────────────────────────── */

    public static function computeScore(ChatLead $lead): int
    {
        $score = 10;

        // Identity
        if (! empty($lead->name))    $score += 15;
        if (! empty($lead->phone))   $score += 20;
        if (! empty($lead->company)) $score += 15;

        // Intent / qualification
        if (! empty($lead->services_interested)) $score += 15;
        if (! empty($lead->industry))            $score += 5;

        // Commercial signals
        if (! empty($lead->budget_range))        $score += 15;
        if (! empty($lead->timeline))            $score += 10;

        // Urgency
        if ($lead->urgency === 'high')   $score += 10;
        if ($lead->urgency === 'medium') $score += 5;

        // Message depth
        $messageLength = mb_strlen((string) $lead->message);
        if ($messageLength > 60)  $score += 5;
        if ($messageLength > 150) $score += 10;

        // High-intent keywords
        $haystack = strtolower(($lead->message ?? '') . ' ' . ($lead->intent ?? ''));
        $highIntent = ['budget', 'timeline', 'urgent', 'ready to start', 'looking for', 'need a', 'build a', 'we want', 'quote', 'proposal', 'meeting', 'call', 'demo'];
        foreach ($highIntent as $kw) {
            if (str_contains($haystack, $kw)) {
                $score += 15;
                break;
            }
        }

        // Page signals
        if (! empty($lead->page_url)) {
            if (str_contains($lead->page_url, '/services')) $score += 10;
            if (str_contains($lead->page_url, '/contact'))  $score += 10;
            if (str_contains($lead->page_url, '/careers'))  $score -= 5;
        }

        // Multi-session engagement
        if (! empty($lead->email)) {
            $otherSessions = static::where('email', $lead->email)
                ->when($lead->exists, fn($q) => $q->where('id', '!=', $lead->id))
                ->distinct('session_id')
                ->count('session_id');
            if ($otherSessions > 0) $score += 10;
        }

        return max(0, min(100, $score));
    }

    /**
     * Re-compute and store the score. Useful after merging new intent
     * data into an existing lead.
     */
    public function rescore(): void
    {
        $this->score = static::computeScore($this);
        $this->save();
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

    public function scopeOfService($q, string $service)
    {
        // JSON search — works on MySQL 5.7+ and SQLite
        return $q->where(function ($qq) use ($service) {
            $qq->whereJsonContains('services_interested', $service)
                ->orWhere('services_interested', 'like', '%' . $service . '%');
        });
    }

    public function scopeWithUrgency($q, string $urgency)
    {
        return $q->where('urgency', $urgency);
    }

    public function scopeInIndustry($q, string $industry)
    {
        return $q->where('industry', $industry);
    }

    /**
     * Distinct list of every service label that has been captured.
     * Used to build the filter dropdown.
     */
    public static function allKnownServices(): array
    {
        return static::query()
            ->whereNotNull('services_interested')
            ->pluck('services_interested')
            ->flatMap(fn($json) => is_array($json) ? $json : (json_decode($json, true) ?: []))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public static function allKnownIndustries(): array
    {
        return static::query()
            ->whereNotNull('industry')
            ->where('industry', '!=', '')
            ->distinct()
            ->orderBy('industry')
            ->pluck('industry')
            ->toArray();
    }
}

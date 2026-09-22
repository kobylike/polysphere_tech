<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Application extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'vacancy_id',
        'tracking_token',
        'name',
        'email',
        'phone',
        'location',
        'country',
        'linkedin_url',
        'portfolio_url',
        'github_url',
        'personal_site_url',
        'behance_url',
        'dribbble_url',
        'writing_samples_url',
        'current_role',
        'current_company',
        'years_experience',
        'availability',
        'salary_expectation',
        'timezone',
        'work_authorization',
        'cv_path',
        'cv_original_name',
        'cover_letter',
        'branch_answers',
        'source',
        'referrer_name',
        'gdpr_consent',
        'ip_address',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'decided_at',
    ];

    protected $casts = [
        'branch_answers'   => 'array',
        'gdpr_consent'     => 'boolean',
        'reviewed_at'      => 'datetime',
        'decided_at'       => 'datetime',
        'years_experience' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Application $app) {
            if (empty($app->tracking_token)) {
                $app->tracking_token = Str::random(64);
            }
        });
    }

    // ─── Activity log ──────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        $candidate = $this->name ?: $this->email ?: "ID: {$this->id}";
        $role      = $this->vacancy?->title ?? 'a role';

        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->logExcept(['tracking_token', 'updated_at', 'ip_address', 'reviewed_by'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Application from {$candidate} for {$role} was received",
                'updated' => "Application from {$candidate} for {$role} was updated",
                'deleted' => "Application from {$candidate} for {$role} was deleted",
                default   => "Application from {$candidate} for {$role} was {$eventName}",
            })
            ->useLogName('application');
    }

    // ─── Relationships ─────────────────────────────────────

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Scopes ────────────────────────────────────────────

    public function scopeWithStatus($q, ApplicationStatus|string $status)
    {
        return $q->where('status', $status instanceof ApplicationStatus ? $status->value : $status);
    }

    public function scopeForVacancy($query, int $vacancyId)
    {
        return $query->where('vacancy_id', $vacancyId);
    }

    public function scopeForEmail($query, string $email)
    {
        return $query->whereRaw('LOWER(email) = ?', [strtolower(trim($email))]);
    }

    public function scopeBlockingReapplication($query)
    {
        $statuses = collect(ApplicationStatus::cases())
            ->filter(fn(ApplicationStatus $s) => $s->blocksReapplication())
            ->map(fn(ApplicationStatus $s) => $s->value)
            ->all();

        return $query->whereIn('status', $statuses);
    }

    // ─── Helpers ───────────────────────────────────────────

    public function statusEnum(): ApplicationStatus
    {
        return ApplicationStatus::from($this->status);
    }

    public function blocksReapplication(): bool
    {
        return $this->statusEnum()->blocksReapplication();
    }

    public function statusUrl(): string
    {
        return route('applications.status', $this->tracking_token);
    }

    public function cvDownloadUrl(): string
    {
        return route('admin.applications.cv', $this->id);
    }

    public function markAs(ApplicationStatus $status, ?User $reviewer = null, ?string $notes = null): void
    {
        $this->status = $status->value;

        if ($reviewer) {
            $this->reviewed_by = $reviewer->id;
            $this->reviewed_at = now();
        }

        if ($status->isTerminal()) {
            $this->decided_at = now();
        }

        if ($notes !== null) {
            $this->admin_notes = $notes;
        }

        $this->save();
    }

    // ─── Accessors ─────────────────────────────────────────

    public function getYearsExperienceLabelAttribute(): string
    {
        return match (true) {
            $this->years_experience === null => '—',
            $this->years_experience === 0    => 'Less than a year',
            $this->years_experience === 1    => '1 year',
            default                          => "{$this->years_experience} years",
        };
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->map(fn($p) => mb_substr($p, 0, 1))
            ->take(2)
            ->implode('');
    }
}

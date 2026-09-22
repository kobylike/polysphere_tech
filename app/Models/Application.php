<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory, SoftDeletes;

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
        'branch_answers'     => 'array',
        'gdpr_consent'       => 'boolean',
        'reviewed_at'        => 'datetime',
        'decided_at'         => 'datetime',
        'years_experience'   => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Application $app) {
            if (empty($app->tracking_token)) {
                $app->tracking_token = Str::random(64);
            }
        });
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

    // ─── Helpers ───────────────────────────────────────────

    public function statusEnum(): ApplicationStatus
    {
        return ApplicationStatus::from($this->status);
    }

    public function statusUrl(): string
    {
        return route('applications.status', $this->tracking_token);
    }

    public function cvDownloadUrl(): string
    {
        // signed temporary URL so only the admin can download – see admin view
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

    /** Extract an int for the years of experience, best-effort. */
    public function getYearsExperienceLabelAttribute(): string
    {
        return match (true) {
            $this->years_experience === null => '—',
            $this->years_experience === 0    => 'Less than a year',
            $this->years_experience === 1    => '1 year',
            default                          => "{$this->years_experience} years",
        };
    }

    // ─── Accessors ─────────────────────────────────────────

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->map(fn($p) => mb_substr($p, 0, 1))
            ->take(2)
            ->implode('');
    }
    public function scopeForVacancy($query, int $vacancyId)
    {
        return $query->where('vacancy_id', $vacancyId);
    }


    public function scopeForEmail($query, string $email)
    {
        return $query->whereRaw('LOWER(email) = ?', [strtolower(trim($email))]);
    }

    /**
     * Applications whose current state should block a new submission
     * to the same vacancy by the same candidate.
     */
    public function scopeBlockingReapplication($query)
    {
        $statuses = collect(ApplicationStatus::cases())
            ->filter(fn(ApplicationStatus $s) => $s->blocksReapplication())
            ->map(fn(ApplicationStatus $s) => $s->value)
            ->all();

        return $query->whereIn('status', $statuses);
    }
    public function blocksReapplication(): bool
    {
        return $this->statusEnum()->blocksReapplication();
    }
}

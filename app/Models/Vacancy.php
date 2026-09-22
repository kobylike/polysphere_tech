<?php

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\VacancyStatus;
use App\Enums\WorkplaceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'department_id',
        'title',
        'slug',
        'summary',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'employment_type',
        'experience_level',
        'workplace_type',
        'location',
        'country',
        'salary_min',
        'salary_max',
        'salary_currency',
        'is_salary_visible',
        'positions_available',
        'status',
        'is_featured',
        'published_at',
        'closing_date',
        'meta_title',
        'meta_description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'employment_type'    => EmploymentType::class,
        'experience_level'   => ExperienceLevel::class,
        'workplace_type'     => WorkplaceType::class,
        'status'             => VacancyStatus::class,
        'salary_min'         => 'decimal:2',
        'salary_max'         => 'decimal:2',
        'is_salary_visible'  => 'boolean',
        'is_featured'        => 'boolean',
        'positions_available' => 'integer',
        'views_count'        => 'integer',
        'applications_count' => 'integer',
        'published_at'       => 'datetime',
        'closing_date'       => 'datetime',
    ];

    protected $appends = [
        'salary_range',
        'is_open',
    ];

    /*
    |--------------------------------------------------------------------------
    | Route model binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Activity log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        $title = $this->title ?: $this->slug ?: "ID: {$this->id}";

        return LogOptions::defaults()
            ->logOnly([
                'department_id',
                'title',
                'slug',
                'summary',
                'employment_type',
                'experience_level',
                'workplace_type',
                'location',
                'country',
                'salary_min',
                'salary_max',
                'salary_currency',
                'is_salary_visible',
                'positions_available',
                'status',
                'is_featured',
                'published_at',
                'closing_date',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Vacancy '{$title}' was created",
                'updated' => "Vacancy '{$title}' was updated",
                'deleted' => "Vacancy '{$title}' was deleted",
                default   => "Vacancy '{$title}' was {$eventName}",
            })
            ->useLogName('vacancy');
    }
    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (Vacancy $vacancy) {
            if (empty($vacancy->slug)) {
                $vacancy->slug = static::generateUniqueSlug($vacancy->title);
            }

            if (Auth::check() && empty($vacancy->created_by)) {
                $vacancy->created_by = Auth::id();
            }
        });

        static::updating(function (Vacancy $vacancy) {
            if ($vacancy->isDirty('title') && ! $vacancy->isDirty('slug')) {
                $vacancy->slug = static::generateUniqueSlug($vacancy->title, $vacancy->id);
            }

            if (Auth::check()) {
                $vacancy->updated_by = Auth::id();
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'vacancy';
        $slug = $base;
        $counter = 1;

        while (
            static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn(Builder $q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', VacancyStatus::Published)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', VacancyStatus::Draft);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', VacancyStatus::Closed);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', VacancyStatus::Archived);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOpenForApplications(Builder $query): Builder
    {
        return $query->published()
            ->where(function (Builder $q) {
                $q->whereNull('closing_date')->orWhere('closing_date', '>=', now());
            });
    }

    public function scopeInDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeOfType(Builder $query, EmploymentType|string $type): Builder
    {
        return $query->where('employment_type', $type instanceof EmploymentType ? $type->value : $type);
    }

    public function scopeOfWorkplace(Builder $query, WorkplaceType|string $type): Builder
    {
        return $query->where('workplace_type', $type instanceof WorkplaceType ? $type->value : $type);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('summary', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getSalaryRangeAttribute(): ?string
    {
        if (! $this->is_salary_visible || (! $this->salary_min && ! $this->salary_max)) {
            return null;
        }

        $format = fn($value) => number_format((float) $value, 0);

        if ($this->salary_min && $this->salary_max) {
            return "{$this->salary_currency} {$format($this->salary_min)} - {$format($this->salary_max)}";
        }

        $value = $this->salary_min ?: $this->salary_max;

        return "{$this->salary_currency} {$format($value)}+";
    }

    public function getIsOpenAttribute(): bool
    {
        if ($this->status !== VacancyStatus::Published) {
            return false;
        }

        if ($this->closing_date && $this->closing_date->isPast()) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Behaviour
    |--------------------------------------------------------------------------
    */

    public function publish(): void
    {
        $this->update([
            'status'       => VacancyStatus::Published,
            'published_at' => $this->published_at ?? now(),
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => VacancyStatus::Closed]);
    }

    public function archive(): void
    {
        $this->update(['status' => VacancyStatus::Archived]);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}

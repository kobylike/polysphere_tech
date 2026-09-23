<?php

namespace App\Models;

use App\Enums\VacancyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Department extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'color',
        'icon',
        'parent_id',
        'head_id',
        'display_order',
        'email',
        'phone',
        'location',
        'budget',
        'headcount_target',
        'founded_at',
        'is_customer_facing',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'is_customer_facing' => 'boolean',
        'budget'             => 'decimal:2',
        'headcount_target'   => 'integer',
        'display_order'      => 'integer',
        'founded_at'         => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Department $department) {
            if (empty($department->slug)) {
                $department->slug = static::generateUniqueSlug($department->name);
            }
        });

        static::updating(function (Department $department) {
            if ($department->isDirty('name') && ! $department->isDirty('slug')) {
                $department->slug = static::generateUniqueSlug($department->name, $department->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'department';
        $slug = $base;
        $counter = 1;

        while (
            static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    // ─── Activity log ───────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? "ID: {$this->id}";

        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'code',
                'description',
                'color',
                'icon',
                'parent_id',
                'head_id',
                'display_order',
                'email',
                'phone',
                'location',
                'budget',
                'headcount_target',
                'founded_at',
                'is_customer_facing',
                'notes',
                'is_active',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Department '{$name}' was created",
                'updated' => "Department '{$name}' was updated",
                'deleted' => "Department '{$name}' was deleted",
                default   => "Department '{$name}' was {$eventName}",
            })
            ->useLogName('department');
    }

    // ─── Route binding ──────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Relationships ──────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    public function publishedVacancies(): HasMany
    {
        return $this->vacancies()->where('status', VacancyStatus::Published->value);
    }

    /**
     * HR employees (UserProfile rows with is_employee = true) belonging
     * to this department. Shared source of truth with the Vacancies module.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(UserProfile::class)->where('is_employee', true);
    }

    // ─── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // ─── Accessors ──────────────────────────────────────────

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', (string) $this->name))
            ->filter()
            ->map(fn($p) => mb_substr($p, 0, 1))
            ->take(2)
            ->implode('');
    }

    public function getEffectiveColorAttribute(): string
    {
        return $this->color ?: '#2f6fed';
    }

    public function getEffectiveIconAttribute(): string
    {
        return $this->icon ?: 'fa-solid fa-building';
    }

    public function getFullPathAttribute(): string
    {
        $chain = [$this->name];
        $cursor = $this->parent;
        $depth = 0;

        while ($cursor && $depth < 10) {
            array_unshift($chain, $cursor->name);
            $cursor = $cursor->parent;
            $depth++;
        }

        return implode(' › ', $chain);
    }

    // ─── Helpers ────────────────────────────────────────────

    public function activeEmployeesCount(): int
    {
        return $this->employees()->count();
    }

    public function isAncestorOf(Department $other): bool
    {
        $cursor = $other->parent;
        $depth = 0;

        while ($cursor && $depth < 20) {
            if ($cursor->id === $this->id) {
                return true;
            }
            $cursor = $cursor->parent;
            $depth++;
        }

        return false;
    }

    public function descendantIds(): array
    {
        $ids = [];
        $stack = [$this->id];

        while ($stack) {
            $current = array_pop($stack);
            $children = static::where('parent_id', $current)->pluck('id')->all();
            foreach ($children as $child) {
                $ids[] = $child;
                $stack[] = $child;
            }
            if (count($ids) > 500) break; // safety
        }

        return $ids;
    }
}

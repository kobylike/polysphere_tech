<?php

namespace App\Models;

use App\Enums\VacancyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Department $department) {
            if (empty($department->slug)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }

    // ─── Activity log ───────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? "ID: {$this->id}";

        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'description',
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

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    public function publishedVacancies(): HasMany
    {
        return $this->vacancies()->where('status', VacancyStatus::Published->value);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(UserProfile::class)->where('is_employee', true);
    }

    // ─── Helpers ────────────────────────────────────────────

    public function activeEmployeesCount(): int
    {
        return $this->employees()->count();
    }
}

<?php

namespace App\Models;

use App\Enums\VacancyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;

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

    public function getRouteKeyName(): string
    {
        return 'slug';
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

    public function activeEmployeesCount(): int
    {
        return $this->employees()->count();
    }
}

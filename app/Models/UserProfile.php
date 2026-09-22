<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class UserProfile extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'about_me',
        'skills',
        'education',
        'social_links',
        'position',
        'gender',
        'is_featured_team',
        'is_spotlight',
        'display_order',
        'employee_id',
        'department_id', // ← was 'department' (string); now a real FK
        'hire_date',
        'employment_type',
        'is_employee',
        'emergency_contact_name',
        'emergency_contact_phone',
        'date_of_birth',
        'country_code',
        'city',
    ];

    protected $casts = [
        'skills' => 'array',
        'education' => 'array',
        'social_links' => 'array',
        'is_featured_team' => 'boolean',
        'is_spotlight' => 'boolean',
        'hire_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $user = $this->user?->name ?? 'User';

        return LogOptions::defaults()
            ->logOnly([
                'position',
                'department_id',
                'employment_type',
                'hire_date',
                'is_featured_team',
                'is_spotlight',
                'is_employee',
                'employee_id',
                'gender',
                'date_of_birth',
                'country_code',
                'city',
                'emergency_contact_name',
                'emergency_contact_phone',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Profile for {$user} was created",
                'updated' => "Profile for {$user} was updated",
                'deleted' => "Profile for {$user} was deleted",
                default   => "Profile for {$user} was {$eventName}",
            })
            ->useLogName('user_profile');
    }
    // ─── Relationships ───────────────────────────────────────────────

    public function scopeSpotlight($query)
    {
        return $query->where('is_spotlight', true)->orderBy('display_order', 'asc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    /**
     * Backward-compat accessor: any older code/view that still does
     * $profile->department expecting a plain string (instead of the new
     * belongsTo relation) gets the department's name instead of an error.
     * Safe to remove once every reference has been migrated to
     * $profile->department?->name or $profile->department_id.
     */
    public function getDepartmentNameAttribute(): ?string
    {
        return $this->department?->name;
    }

    /**
     * Get a specific social link by platform.
     */
    public function getSocialLink(string $platform): ?string
    {
        return $this->social_links[$platform] ?? null;
    }

    /**
     * Get skills as a flat array of names.
     */
    public function getSkillNamesAttribute(): array
    {
        return array_column($this->skills ?? [], 'name');
    }

    /**
     * Get skills as an array of name => level.
     */
    public function getSkillLevelsAttribute(): array
    {
        $result = [];
        foreach ($this->skills ?? [] as $skill) {
            if (!is_array($skill) || !isset($skill['name'])) {
                continue;
            }
            $result[$skill['name']] = $skill['level'] ?? 0;
        }
        return $result;
    }

    public function getCountryNameAttribute(): ?string
    {
        if (empty($this->country_code)) {
            return null;
        }

        $path = public_path('countries-full.json');
        if (!file_exists($path)) {
            $path = public_path('countries.json');
        }

        if (file_exists($path)) {
            $json = file_get_contents($path);
            $countries = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($countries)) {
                foreach ($countries as $country) {
                    if (isset($country['code']) && $country['code'] === $this->country_code) {
                        return $country['name'];
                    }
                    if (isset($country['iso']) && $country['iso'] === $this->country_code) {
                        return $country['name'];
                    }
                }
            }
        }

        return $this->country_code;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'about_me',
        'skills',
        'education',
        'social_links',
        'position',
        'is_featured_team',
        'display_order',

    ];

    protected $casts = [
        'skills' => 'array',
        'education' => 'array',
        'social_links' => 'array',
        'is_featured_team' => 'boolean',

    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

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
            $result[$skill['name']] = $skill['level'] ?? 0;
        }
        return $result;
    }
}

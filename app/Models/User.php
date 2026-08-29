<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'google_id',
    'name',
    'username',
    'email',
    'password',
    'status',
    'phone',
    'avatar',
    'email_verified_at',
    'email_verification_token',
    'email_verification_sent_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        \Illuminate\Support\Facades\Notification::send(
            $this,
            new \App\Notifications\VerifyEmailNotification()
        );
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function publishedPosts()
    {
        return $this->hasMany(Post::class, 'author_id')->where('status', 'published');
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // ─── Profile Field Accessors ────────────────────────────────────

    /**
     * Get the user's "About Me" from their profile.
     */
    public function getAboutMeAttribute(): ?string
    {
        return $this->profile?->about_me;
    }

    /**
     * Get the user's skills from their profile.
     */
    public function getSkillsAttribute(): ?array
    {
        return $this->profile?->skills;
    }

    /**
     * Get the user's education from their profile.
     */
    public function getEducationAttribute(): ?array
    {
        return $this->profile?->education;
    }

    /**
     * Get the user's social links from their profile.
     */
    public function getSocialLinksAttribute(): ?array
    {
        return $this->profile?->social_links;
    }

    /**
     * Get the user's position/title from their profile.
     */
    public function getPositionAttribute(): ?string
    {
        return $this->profile?->position;
    }

    /**
     * Check if the user is a featured team member.
     */
    public function getIsFeaturedTeamAttribute(): bool
    {
        return $this->profile?->is_featured_team ?? false;
    }

    /**
     * Get or create a profile for the user.
     */
    public function getOrCreateProfile(): UserProfile
    {
        if ($this->profile) {
            return $this->profile;
        }

        return $this->profile()->create([]);
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Get the user's avatar URL with UI Avatars fallback.
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=3b82f6&color=fff&size=100';
    }

    /**
     * Get the user's initials (e.g. "SO" for Samuel Ofosu).
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->name ?? '';
        $parts = explode(' ', trim($name));
        $initials = '';
        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(mb_substr($part, 0, 1));
            }
        }
        return $initials ?: '?';
    }
}

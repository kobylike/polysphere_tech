<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

use App\Models\UserActivity;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OverviewTab extends Component
{
    public $user;
    public $stats;
    public $recentActivities;
    public $profile;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Eager-load the nested department relation so $profile->department
        // resolves without a lazy query per request.
        $this->user = $user->load('profile.department', 'roles');

        // A user may have no profile row yet. Fall back to a blank model so
        // `$profile->position` etc. return null instead of throwing.
        $this->profile = $this->user->profile ?? new UserProfile();

        $this->stats = $this->getStats();
        $this->recentActivities = $this->getRecentActivities();
    }

    private function getStats(): array
    {
        $user = $this->user;

        $accountAgeDays    = $user->created_at->diffInDays(now());
        $accountAgeDisplay = $this->formatAccountAge($accountAgeDays);

        // Real "this month" counts — used for truthful context under each card
        $thisMonth = [
            'start' => now()->startOfMonth(),
            'end'   => now()->endOfMonth(),
        ];

        $postsThisMonth = $user->posts()
            ->whereBetween('created_at', [$thisMonth['start'], $thisMonth['end']])
            ->count();

        $publishedThisMonth = $user->publishedPosts()
            ->whereBetween('created_at', [$thisMonth['start'], $thisMonth['end']])
            ->count();

        return [
            'total_posts'          => $user->posts()->count(),
            'published_posts'      => $user->publishedPosts()->count(),
            'draft_posts'          => $user->posts()->where('status', 'draft')->count(),
            'posts_this_month'     => $postsThisMonth,
            'published_this_month' => $publishedThisMonth,
            'account_age_days'     => $accountAgeDays,
            'account_age_display'  => $accountAgeDisplay,
            'last_login'           => $user->last_login_at ?? null,
        ];
    }

    /**
     * Format account age in a compact, human-readable way.
     */
    private function formatAccountAge(int $days): string
    {
        if ($days < 1) {
            return '< 1 day';
        }
        if ($days < 30) {
            return $days . ' day' . ($days > 1 ? 's' : '');
        }
        if ($days < 365) {
            $months = floor($days / 30);
            return $months . ' month' . ($months > 1 ? 's' : '');
        }
        $years         = floor($days / 365);
        $remainingDays = $days % 365;
        if ($remainingDays > 0) {
            $months = floor($remainingDays / 30);
            return $years . 'y ' . $months . 'm';
        }
        return $years . ' year' . ($years > 1 ? 's' : '');
    }

    private function getRecentActivities()
    {
        return UserActivity::where('user_id', $this->user->id)
            ->latest()
            ->take(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.users.account.tabs.overview-tab', [
            'user'             => $this->user,
            'profile'          => $this->profile,
            'stats'            => $this->stats,
            'recentActivities' => $this->recentActivities,
        ]);
    }
}

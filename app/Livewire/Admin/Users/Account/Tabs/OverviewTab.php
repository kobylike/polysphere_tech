<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

use App\Models\UserActivity;
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
        $this->user = $user->load('profile', 'roles');
        $this->profile = $this->user->profile;
        $this->stats = $this->getStats();
        $this->recentActivities = $this->getRecentActivities();
    }

    private function getStats()
    {
        $user = $this->user;

        // Calculate account age in days
        $accountAgeDays = $user->created_at->diffInDays(now());
        $accountAgeDisplay = $this->formatAccountAge($accountAgeDays);

        return [
            'total_posts' => $user->posts()->count(),
            'published_posts' => $user->publishedPosts()->count(),
            'draft_posts' => $user->posts()->where('status', 'draft')->count(),
            'account_age_days' => $accountAgeDays,
            'account_age_display' => $accountAgeDisplay,
            'last_login' => $user->last_login_at ?? null,
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
        $years = floor($days / 365);
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
            'user' => $this->user,
            'profile' => $this->profile,
            'stats' => $this->stats,
            'recentActivities' => $this->recentActivities,
        ]);
    }
}

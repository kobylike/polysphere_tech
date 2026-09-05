<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Project;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.users')]
class UserDashboardComponent extends Component
{
    public string $activityChartPeriod = 'week';

    // ─── Chart data ──────────────────────────────────────────────────
    public array $activityChartData = [];

    public function mount()
    {
        $this->refreshChartData();
    }

    public function refreshChartData()
    {
        $this->activityChartData = $this->getActivityChartData();
    }

    public function updatedActivityChartPeriod()
    {
        $this->refreshChartData();
        $this->dispatch('updateUserCharts', [
            'activityChartData' => $this->activityChartData,
        ]);
    }

    // ─── Logout ──────────────────────────────────────────────────────
    public function logout()
    {
        try {
            Auth::logout();
            session()->regenerateToken();
            session()->invalidate();

            return redirect()->route('login');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return redirect()->route('login');
        }
    }

    // ─── Computed Properties ────────────────────────────────────────
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function profile()
    {
        return $this->user->profile ?? new UserProfile();
    }

    #[Computed]
    public function profileCompletion()
    {
        $fields = [
            'name' => !empty($this->user->name),
            'email' => !empty($this->user->email),
            'phone' => !empty($this->user->phone),
            'avatar' => !empty($this->user->avatar),
            'about_me' => !empty($this->profile->about_me),
            'position' => !empty($this->profile->position),
            'skills' => !empty($this->profile->skills) && count($this->profile->skills) > 0,
            'social_links' => !empty($this->profile->social_links) && count(array_filter($this->profile->social_links)) > 0,
        ];

        $filled = count(array_filter($fields));
        $total = count($fields);
        return round(($filled / $total) * 100);
    }

    #[Computed]
    public function notifications()
    {
        return $this->user->notifications()->latest()->limit(5)->get();
    }

    #[Computed]
    public function unreadCount()
    {
        return $this->user->unreadNotifications()->count();
    }

    #[Computed]
    public function recentActivities()
    {
        return Activity::where('causer_id', $this->user->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'description' => $activity->description,
                    'log_name' => $activity->log_name,
                    'created_at' => $activity->created_at->diffForHumans(),
                    'properties' => $activity->properties->toArray(),
                ];
            });
    }

    #[Computed]
    public function projects()
    {
        // If you have a many-to-many relation for user->projects, use that.
        // For now, we show projects where user is the author.
        return Project::where('author_id', $this->user->id)
            ->latest()
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total_projects' => Project::where('author_id', $this->user->id)->count(),
            'total_activities' => Activity::where('causer_id', $this->user->id)->count(),
            'member_since' => $this->user->created_at->format('M Y'),
            'role' => $this->user->roles->first()?->name ?? 'User',
        ];
    }

    // ─── Chart Data ──────────────────────────────────────────────────
    private function getActivityChartData()
    {
        $end = now();
        $start = match ($this->activityChartPeriod) {
            'week'  => now()->subWeek(),
            'month' => now()->subMonth(),
            'year'  => now()->subYear(),
            default => now()->subDays(30),
        };

        $activities = Activity::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
            DB::raw('COUNT(*) as count')
        )
            ->where('causer_id', $this->user->id)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $activities->pluck('date')->toArray();
        $data = $activities->pluck('count')->toArray();

        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    // ─── Render ──────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.admin.dashboard.user-dashboard-component', [
            'user' => $this->user,
            'profile' => $this->profile,
            'profileCompletion' => $this->profileCompletion,
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
            'recentActivities' => $this->recentActivities,
            'projects' => $this->projects,
            'stats' => $this->stats,
            'activityChartData' => $this->activityChartData,
        ]);
    }
}

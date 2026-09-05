<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.users')]
class DashboardComponent extends Component
{
    public string $projectChartPeriod = 'week';
    public string $earningChartPeriod = 'day';

    // ─── Chart data stored as public properties for Alpine ──────────
    public array $projectChartData = [];
    public array $projectStatusData = [];
    public array $userRegistrationsData = [];

    // ─── Mount: check permissions and initialize data ──────────────
    public function mount()
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            // Redirect to a user-friendly page (e.g., 'user-dashboard' or 'home')
            return redirect()->route('dashboard.user')->with('error', 'You do not have access to the executive dashboard.');
        }

        $this->refreshChartData();
    }

    public function refreshChartData()
    {
        $this->projectChartData = $this->getProjectChartData();
        $this->projectStatusData = $this->getProjectStatusData();
        $this->userRegistrationsData = $this->getUserRegistrationsData();
    }

    // ─── Livewire hooks ──────────────────────────────────────────────
    public function updatedProjectChartPeriod()
    {
        $this->refreshChartData();
        $this->dispatch('updateCharts', [
            'projectChartData' => $this->projectChartData,
            'projectStatusData' => $this->projectStatusData,
            'userRegistrationsData' => $this->userRegistrationsData,
        ]);
    }

    public function updatedEarningChartPeriod()
    {
        $this->refreshChartData();
        $this->dispatch('updateCharts', [
            'projectChartData' => $this->projectChartData,
            'projectStatusData' => $this->projectStatusData,
            'userRegistrationsData' => $this->userRegistrationsData,
        ]);
    }

    // ─── KPI Stats ──────────────────────────────────────────────────
    #[Computed]
    public function stats()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $totalProjects = Project::count();
        $publishedProjects = Project::where('status', 'published')->count();
        $draftProjects = Project::where('status', 'draft')->count();

        $totalServices = Service::count();
        $activeServices = Service::where('status', 'active')->count();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $unreadNotifications = $user->unreadNotifications()->count();

        $twoFactorEnabled = User::whereNotNull('two_factor_confirmed_at')->count();
        $twoFactorAdoption = $totalUsers > 0 ? round(($twoFactorEnabled / $totalUsers) * 100, 1) : 0;

        $spotlightCount = UserProfile::where('is_spotlight', true)->count();

        // Replace with actual health check if you have one
        $systemStatus = 'healthy';

        return [
            'total_users'           => $totalUsers,
            'active_users'          => $activeUsers,
            'new_users_today'       => $newUsersToday,
            'new_users_week'        => $newUsersThisWeek,
            'total_projects'        => $totalProjects,
            'published_projects'    => $publishedProjects,
            'draft_projects'        => $draftProjects,
            'total_services'        => $totalServices,
            'active_services'       => $activeServices,
            'unread_notifications'  => $unreadNotifications,
            'two_factor_adoption'   => $twoFactorAdoption,
            'spotlight_count'       => $spotlightCount,
            'system_status'         => $systemStatus,
        ];
    }

    // ─── Activity Feed ──────────────────────────────────────────────
    #[Computed]
    public function recentActivities()
    {
        return Activity::with('causer')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                $causerName = $activity->causer?->name ?? 'System';
                $description = $activity->description ?? 'Performed an action';
                $logName = $activity->log_name ?? 'default';
                $properties = $activity->properties->toArray();

                $icon = match ($logName) {
                    'user'          => 'fa-user',
                    'project'       => 'fa-project-diagram',
                    'service'       => 'fa-cogs',
                    'role'          => 'fa-user-tag',
                    'permission'    => 'fa-lock',
                    'auth'          => 'fa-shield-alt',
                    'profile'       => 'fa-id-card',
                    'notification'  => 'fa-bell',
                    default         => 'fa-info-circle',
                };

                $color = match ($logName) {
                    'user'          => 'primary',
                    'project'       => 'success',
                    'service'       => 'info',
                    'role'          => 'warning',
                    'permission'    => 'danger',
                    'auth'          => 'secondary',
                    default         => 'secondary',
                };

                $entityName = $properties['name'] ?? $properties['title'] ?? $activity->subject?->name ?? null;

                return [
                    'id'            => $activity->id,
                    'causer_name'   => $causerName,
                    'description'   => $description,
                    'entity_name'   => $entityName,
                    'log_name'      => $logName,
                    'icon'          => $icon,
                    'color'         => $color,
                    'created_at'    => $activity->created_at->diffForHumans(),
                    'created_at_raw' => $activity->created_at->toDateTimeString(),
                ];
            });
    }

    // ─── Spotlight Team ──────────────────────────────────────────────
    #[Computed]
    public function spotlightTeam()
    {
        return User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_spotlight', true)
            ->orderBy('user_profiles.display_order', 'asc')
            ->orderBy('users.id', 'asc')
            ->select('users.*', 'user_profiles.position')
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'avatar'   => $user->avatar_url,
                    'position' => $user->position ?? 'Team Member',
                ];
            });
    }

    // ─── Notifications ──────────────────────────────────────────────
    #[Computed]
    public function notifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $unread = $user->unreadNotifications()->latest()->limit(5)->get();
        $read = $user->notifications()
            ->whereNotNull('read_at')
            ->latest()
            ->limit(5)
            ->get();

        return ['unread' => $unread, 'read' => $read];
    }

    // ─── Chart Data Getters ──────────────────────────────────────────
    private function getProjectChartData()
    {
        $end = now();
        $start = match ($this->projectChartPeriod) {
            'week'  => now()->subWeek(),
            'month' => now()->subMonth(),
            'year'  => now()->subYear(),
            default => now()->subDays(30),
        };

        $projects = Project::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $projects->pluck('date')->toArray();
        $data = $projects->pluck('count')->toArray();

        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getProjectStatusData()
    {
        $statusCounts = Project::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = $statusCounts->pluck('status')->map(fn($s) => ucfirst($s))->toArray();
        $data = $statusCounts->pluck('count')->toArray();

        $colors = [
            'published' => '#3AC977',
            'draft'     => '#FF9F00',
            'pending'   => '#FF5E5E',
            'private'   => '#0D99FF',
            'trash'     => '#6C757D',
        ];

        $bgColors = array_map(fn($s) => $colors[$s] ?? '#6C757D', $statusCounts->pluck('status')->toArray());

        return ['labels' => $labels, 'data' => $data, 'bgColors' => $bgColors];
    }

    private function getUserRegistrationsData()
    {
        $end = now();
        $start = match ($this->earningChartPeriod) {
            'day'   => now()->subDay(),
            'week'  => now()->subWeek(),
            'month' => now()->subMonth(),
            'year'  => now()->subYear(),
            default => now()->subDays(30),
        };

        $period = Carbon::parse($start);
        $labels = [];
        $data = [];

        while ($period <= $end) {
            $labels[] = $period->format('Y-m-d');
            $data[] = User::whereDate('created_at', $period->toDateString())->count();
            $period->addDay();
        }

        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    // ─── System Health ──────────────────────────────────────────────
    #[Computed]
    public function systemHealth()
    {
        $rawSize = $this->getDirectorySize(storage_path('app/public'));
        return [
            'queued_jobs'   => DB::table('jobs')->count(),
            'cache_usage'   => 'N/A',
            'storage_usage' => $this->formatBytes($rawSize),
            'last_backup'   => '2025-03-15 02:00',
        ];
    }

    private function getDirectorySize($path)
    {
        $size = 0;
        foreach (glob(rtrim($path, '/') . '/*', GLOB_NOSORT) as $each) {
            if (is_file($each)) {
                $size += filesize($each);
            } else {
                $size += $this->getDirectorySize($each);
            }
        }
        return $size;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if (!is_numeric($bytes) || $bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // ─── Render ──────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-component', [
            'stats'                       => $this->stats,
            'recentActivities'            => $this->recentActivities,
            'spotlightTeam'               => $this->spotlightTeam,
            'notifications'               => $this->notifications,
            'systemHealth'                => $this->systemHealth,
            'initialProjectChartData'     => $this->projectChartData,
            'initialProjectStatusData'    => $this->projectStatusData,
            'initialUserRegistrationsData' => $this->userRegistrationsData,
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityTab extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $user;
    public $filterAction = '';
    public $filterDate = 'all'; // all, today, week, month, custom
    public $dateFrom = '';
    public $dateTo = '';
    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'filterAction' => ['except' => ''],
        'filterDate' => ['except' => 'all'],
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
    ];

    public function mount()
    {

        $this->user =  Auth::user();
    }

    public function getStatsProperty()
    {
        $query = UserActivity::where('user_id', $this->user->id);
        $total = $query->count();
        $loginAttempts = UserActivity::where('user_id', $this->user->id)->where('action', 'login')->count();
        $adminActions = UserActivity::where('user_id', $this->user->id)->where('action', 'like', 'admin_%')->count();
        $lastLogin = UserActivity::where('user_id', $this->user->id)->where('action', 'login')->latest()->first();

        return [
            'total' => $total,
            'login_attempts' => $loginAttempts,
            'admin_actions' => $adminActions,
            'last_login' => $lastLogin?->created_at,
        ];
    }

    public function getActivitiesProperty()
    {
        $query = UserActivity::where('user_id', $this->user->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                    ->orWhere('action', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterAction) {
            $query->where('action', $this->filterAction);
        }

        if ($this->filterDate == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->filterDate == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->filterDate == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($this->filterDate == 'custom' && $this->dateFrom && $this->dateTo) {
            $query->whereBetween('created_at', [Carbon::parse($this->dateFrom)->startOfDay(), Carbon::parse($this->dateTo)->endOfDay()]);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    public function getDistinctActionsProperty()
    {
        return UserActivity::where('user_id', $this->user->id)
            ->distinct()
            ->pluck('action')
            ->filter()
            ->values();
    }

    public function getTimelineActivitiesProperty()
    {
        return UserActivity::where('user_id', $this->user->id)
            ->latest()
            ->take(10)
            ->get();
    }

    public function resetFilters()
    {
        $this->reset(['filterAction', 'filterDate', 'dateFrom', 'dateTo', 'search', 'perPage']);
        $this->resetPage();
    }

    public function exportCsv()
    {
        $activities = UserActivity::where('user_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($activities->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No data',
                'message' => 'There are no activities to export.',
            ]);
            return;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity-log-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($activities) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Action', 'Description', 'IP Address']);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->action,
                    $activity->description,
                    $activity->ip_address,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.admin.users.account.tabs.activity-tab', [
            'stats' => $this->stats,
            'activities' => $this->activities,
            'actions' => $this->distinctActions,
            'timelineActivities' => $this->timelineActivities,
        ]);
    }
}

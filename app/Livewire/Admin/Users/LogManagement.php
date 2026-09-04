<?php

namespace App\Livewire\Admin\Users;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.users')]
class LogManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Filters ──────────────────────────────────────────────────────────
    public string $search = '';
    public string $logName = '';
    public string $event = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $perPage = 15;
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    // ─── Modal ────────────────────────────────────────────────────────────
    public bool $showPropertyModal = false;
    public ?Activity $selectedLog = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'logName' => ['except' => ''],
        'event' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->authorize('View Activity Logs');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'logName', 'event', 'dateFrom', 'dateTo', 'perPage']);
        $this->resetPage();
    }

    public function openPropertyModal($id)
    {
        $this->selectedLog = Activity::findOrFail($id);
        $this->showPropertyModal = true;
    }

    public function closePropertyModal()
    {
        $this->showPropertyModal = false;
        $this->selectedLog = null;
    }

    public function getLogsProperty()
    {
        $query = Activity::with('causer');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere('log_name', 'like', '%' . $this->search . '%')
                    ->orWhere('properties', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->logName) {
            $query->where('log_name', $this->logName);
        }

        if ($this->event) {
            $query->where('event', $this->event);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        return [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->count(),
            'log_names' => Activity::distinct('log_name')->pluck('log_name'),
            'events' => Activity::distinct('event')->pluck('event')->filter(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.users.log-management', [
            'logs' => $this->logs,
            'stats' => $this->stats,
        ]);
    }
}

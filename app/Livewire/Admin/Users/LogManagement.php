<?php

namespace App\Livewire\Admin\Users;

use App\Helpers\ActivityLogger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.users')]
class LogManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Filters ─────────────────────────────────────────────
    public string $search = '';
    public string $logName = '';
    public string $event = '';
    public string $causerFilter = '';         // user id
    public string $subjectTypeFilter = '';    // model class
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $datePreset = '';           // today, yesterday, 7d, 30d, custom
    public int $perPage = 15;
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    // ─── Property modal ──────────────────────────────────────
    public bool $showPropertyModal = false;
    public ?Activity $selectedLog = null;
    public array $relatedActivity = [];
    public array $diffRows = [];

    // ─── Bulk ────────────────────────────────────────────────
    public array $selectedLogs = [];
    public bool $selectAll = false;
    public bool $showPruneModal = false;
    public int $pruneDays = 90;

    protected $queryString = [
        'search'            => ['except' => ''],
        'logName'           => ['except' => ''],
        'event'             => ['except' => ''],
        'causerFilter'      => ['except' => ''],
        'subjectTypeFilter' => ['except' => ''],
        'dateFrom'          => ['except' => ''],
        'dateTo'            => ['except' => ''],
        'datePreset'        => ['except' => ''],
        'perPage'           => ['except' => 15],
        'sortBy'            => ['except' => 'created_at'],
        'sortDir'           => ['except' => 'desc'],
    ];

    public function mount(): void
    {
        $this->authorize('View Activity Logs');
    }

    // ─── Filters / sorting ───────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingLogName(): void
    {
        $this->resetPage();
    }
    public function updatingEvent(): void
    {
        $this->resetPage();
    }
    public function updatingCauserFilter(): void
    {
        $this->resetPage();
    }
    public function updatingSubjectTypeFilter(): void
    {
        $this->resetPage();
    }
    public function updatingDateFrom(): void
    {
        $this->datePreset = '';
        $this->resetPage();
    }
    public function updatingDateTo(): void
    {
        $this->datePreset = '';
        $this->resetPage();
    }
    public function updatingDatePreset(): void
    {
        $this->applyDatePreset();
        $this->resetPage();
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function applyDatePreset(): void
    {
        $today = now()->startOfDay();

        match ($this->datePreset) {
            'today'      => [$this->dateFrom = $today->toDateString(), $this->dateTo = $today->toDateString()],
            'yesterday'  => [$this->dateFrom = $today->copy()->subDay()->toDateString(), $this->dateTo = $today->copy()->subDay()->toDateString()],
            '7d'         => [$this->dateFrom = $today->copy()->subDays(6)->toDateString(), $this->dateTo = $today->toDateString()],
            '30d'        => [$this->dateFrom = $today->copy()->subDays(29)->toDateString(), $this->dateTo = $today->toDateString()],
            'this_month' => [$this->dateFrom = $today->copy()->startOfMonth()->toDateString(), $this->dateTo = $today->toDateString()],
            default      => null,
        };
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'logName',
            'event',
            'causerFilter',
            'subjectTypeFilter',
            'dateFrom',
            'dateTo',
            'datePreset',
            'selectedLogs',
            'selectAll',
        ]);
        $this->sortBy  = 'created_at';
        $this->sortDir = 'desc';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function clearFilter(string $key): void
    {
        if (property_exists($this, $key)) {
            $this->{$key} = '';
            $this->resetPage();
        }
    }

    public function getActiveFilterCountProperty(): int
    {
        return collect([
            $this->search,
            $this->logName,
            $this->event,
            $this->causerFilter,
            $this->subjectTypeFilter,
            $this->dateFrom,
            $this->dateTo,
        ])->filter(fn($v) => ! empty($v))->count();
    }

    // ─── Selection ───────────────────────────────────────────
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedLogs = $value
            ? $this->getLogsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function updatedSelectedLogs(): void
    {
        $total = $this->getLogsQuery()->count();
        $this->selectAll = $total > 0 && count($this->selectedLogs) === $total;
    }

    // ─── Property modal ──────────────────────────────────────
    public function openPropertyModal(int $id): void
    {
        $log = Activity::with(['causer', 'subject'])->findOrFail($id);

        $this->selectedLog = $log;
        $this->diffRows    = $this->buildDiffRows($log);
        $this->relatedActivity = $this->loadRelatedActivity($log);
        $this->showPropertyModal = true;
    }

    public function closePropertyModal(): void
    {
        $this->showPropertyModal = false;
        $this->selectedLog = null;
        $this->diffRows = [];
        $this->relatedActivity = [];
    }

    /**
     * Turn Spatie's `attributes` + `old` payload into a clean before/after
     * array the view can render side-by-side.
     *
     * @return array<int, array{key:string, label:string, old:mixed, new:mixed, type:string}>
     */
    protected function buildDiffRows(Activity $log): array
    {
        $props   = $log->properties ?? collect();
        $new     = (array) ($props['attributes'] ?? []);
        $old     = (array) ($props['old'] ?? []);
        $rows    = [];

        // Union of keys, preserving 'new' order first
        $keys = array_unique(array_merge(array_keys($new), array_keys($old)));

        foreach ($keys as $key) {
            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            // Skip unchanged values
            if ($oldVal === $newVal) {
                continue;
            }

            $rows[] = [
                'key'   => $key,
                'label' => $this->humanizeKey($key),
                'old'   => $this->formatValue($oldVal),
                'new'   => $this->formatValue($newVal),
                'type'  => is_bool($newVal) ? 'boolean' : (is_numeric($newVal) ? 'number' : 'text'),
            ];
        }

        return $rows;
    }

    protected function humanizeKey(string $key): string
    {
        return ucfirst(str_replace(['_', '.'], [' ', ' › '], $key));
    }

    protected function formatValue(mixed $value): string
    {
        return match (true) {
            $value === null, $value === '' => '—',
            is_bool($value)                => $value ? 'Yes' : 'No',
            is_array($value)               => json_encode($value, JSON_UNESCAPED_SLASHES) ?: '—',
            default                        => (string) $value,
        };
    }

    /**
     * Last 10 activities on the same subject, excluding this one.
     */
    protected function loadRelatedActivity(Activity $log): array
    {
        if (! $log->subject_type || ! $log->subject_id) {
            return [];
        }

        return Activity::query()
            ->where('subject_type', $log->subject_type)
            ->where('subject_id', $log->subject_id)
            ->where('id', '!=', $log->id)
            ->with('causer')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn(Activity $a) => [
                'id'          => $a->id,
                'event'       => $a->event,
                'description' => $a->description,
                'causer'      => $a->causer?->name ?? 'System',
                'when'        => $a->created_at->diffForHumans(),
                'timestamp'   => $a->created_at->format('M d, Y g:i A'),
            ])
            ->toArray();
    }

    // ─── Query builders ──────────────────────────────────────
    protected function getLogsQuery()
    {
        return Activity::query()
            ->with(['causer', 'subject'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('description', 'like', $term)
                        ->orWhere('log_name', 'like', $term)
                        ->orWhere('event', 'like', $term)
                        ->orWhere('properties', 'like', $term);
                });
            })
            ->when($this->logName, fn($q) => $q->where('log_name', $this->logName))
            ->when($this->event, fn($q) => $q->where('event', $this->event))
            ->when($this->causerFilter, fn($q) => $q->where('causer_id', $this->causerFilter))
            ->when($this->subjectTypeFilter, fn($q) => $q->where('subject_type', $this->subjectTypeFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDir);
    }
    public function getLogsProperty()
    {
        return $this->getLogsQuery()->paginate($this->perPage);
    }

    // ─── Stats ───────────────────────────────────────────────
    public function getStatsProperty(): array
    {
        return [
            'total'      => Activity::count(),
            'today'      => Activity::whereDate('created_at', today())->count(),
            'this_week'  => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'log_names'  => Activity::query()->distinct()->pluck('log_name')->filter()->values(),
            'events'     => Activity::query()->distinct()->pluck('event')->filter()->values(),
            'causers'    => Activity::query()
                ->whereNotNull('causer_id')
                ->with('causer')
                ->get()
                ->pluck('causer')
                ->filter()
                ->unique('id')
                ->values(),
            'subject_types' => Activity::query()
                ->whereNotNull('subject_type')
                ->distinct()
                ->pluck('subject_type')
                ->filter()
                ->values(),
        ];
    }

    /**
     * Last 14 days of activity counts — used for the mini trend chart.
     */
    public function getTimelineProperty(): array
    {
        $days = collect(range(13, 0))->map(fn($i) => now()->subDays($i)->toDateString());

        $counts = Activity::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        return $days->map(fn($day) => [
            'day'   => $day,
            'label' => Carbon::parse($day)->format('M j'),
            'count' => (int) ($counts[$day] ?? 0),
        ])->toArray();
    }

    // ─── Export CSV ──────────────────────────────────────────
    public function export(): StreamedResponse
    {
        $this->authorize('View Activity Logs');

        $logs     = $this->getLogsQuery()->get();
        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'When',
                'Log name',
                'Event',
                'Description',
                'Causer',
                'Causer email',
                'Subject type',
                'Subject ID',
            ]);

            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->created_at->toDateTimeString(),
                    $log->log_name,
                    $log->event,
                    $log->description,
                    $log->causer?->name,
                    $log->causer?->email,
                    $log->subject_type,
                    $log->subject_id,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ─── Prune ───────────────────────────────────────────────
    public function confirmPrune(): void
    {
        $this->authorize('View Activity Logs');
        $this->showPruneModal = true;
    }

    public function pruneOldLogs(): void
    {
        $this->authorize('View Activity Logs');

        $cutoff = now()->subDays(max(1, $this->pruneDays));

        $deleted = Activity::where('created_at', '<', $cutoff)->delete();

        try {
            ActivityLogger::log('Activity logs pruned', [
                'cutoff'     => $cutoff->toDateTimeString(),
                'prune_days' => $this->pruneDays,
                'deleted'    => $deleted,
                'pruned_by'  => Auth::id(),
            ], 'system');
        } catch (\Throwable $e) {
            report($e);
        }

        $this->showPruneModal = false;
        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Pruned',
            'message' => "{$deleted} log entries older than {$this->pruneDays} days were removed.",
        ]);
    }

    // ─── Render ──────────────────────────────────────────────
    public function render()
    {
        return view('livewire.admin.users.log-management', [
            'logs'     => $this->logs,
            'stats'    => $this->stats,
            'timeline' => $this->timeline,
        ]);
    }
}

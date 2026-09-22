<div class="position-relative">

    {{-- ─── PAGE TITLES ─────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Activity Logs</h5>
            </li>
            <li class="breadcrumb-item"><a wire:navigate.hover href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Audit Trail</li>
        </ol>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" wire:click="export" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export">
                    <i class="fa-regular fa-file-export me-1"></i> Export CSV
                </span>
                <span wire:loading wire:target="export">
                    <i class="fa-regular fa-spinner fa-spin me-1"></i> Exporting…
                </span>
            </button>
            <button class="btn btn-outline-danger btn-sm" wire:click="confirmPrune">
                <i class="fa-regular fa-broom me-1"></i> Prune Old
            </button>
        </div>
    </div>

    <div class="container-fluid">

        {{-- ─── STATS ───────────────────────────────────────── --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Total Logs</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fas fa-history fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Today</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['today']) }}</h3>
                        </div>
                        <i class="fas fa-calendar-day fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">This Week</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['this_week']) }}</h3>
                        </div>
                        <i class="fas fa-calendar-week fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">This Month</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['this_month']) }}</h3>
                        </div>
                        <i class="fas fa-calendar-alt fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── MINI TIMELINE ───────────────────────────────── --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="mb-0 fw-bold">Activity — last 14 days</h6>
                        <span class="text-muted small">
                            Peak:
                            {{ collect($timeline)->sortByDesc('count')->first()['label'] }}
                            ({{ collect($timeline)->max('count') }} events)
                        </span>
                    </div>
                    <span class="badge bg-secondary light border-0">
                        {{ array_sum(array_column($timeline, 'count')) }} events
                    </span>
                </div>
                @php
                    $max = max(1, collect($timeline)->max('count'));
                @endphp
                <div class="activity-spark">
                    @foreach($timeline as $day)
                        <div class="activity-spark__bar" style="height: {{ max(4, ($day['count'] / $max) * 100) }}%;"
                            title="{{ $day['label'] }} — {{ $day['count'] }} events">
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span class="text-muted small">{{ $timeline[0]['label'] }}</span>
                    <span class="text-muted small">{{ $timeline[count($timeline) - 1]['label'] }}</span>
                </div>
            </div>
        </div>

        {{-- ─── FILTERS ─────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 g-md-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold small">Search</label>
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                            placeholder="Description, log name, properties...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Log Name</label>
                        <select class="form-select" wire:model.live="logName">
                            <option value="">All</option>
                            @foreach($stats['log_names'] as $name)
                                <option value="{{ $name }}">{{ ucfirst(str_replace('_', ' ', $name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Event</label>
                        <select class="form-select" wire:model.live="event">
                            <option value="">All</option>
                            @foreach($stats['events'] as $evt)
                                <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Causer</label>
                        <select class="form-select" wire:model.live="causerFilter">
                            <option value="">Anyone</option>
                            @foreach($stats['causers'] as $causer)
                                <option value="{{ $causer->id }}">{{ $causer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold small">Subject Type</label>
                        <select class="form-select" wire:model.live="subjectTypeFilter">
                            <option value="">All models</option>
                            @foreach($stats['subject_types'] as $type)
                                <option value="{{ $type }}">
                                    {{ class_basename($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold small">Date range</label>
                        <select class="form-select" wire:model.live="datePreset">
                            <option value="">Custom</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="7d">Last 7 days</option>
                            <option value="30d">Last 30 days</option>
                            <option value="this_month">This month</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">From</label>
                        <input type="date" class="form-control" wire:model.live="dateFrom">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">To</label>
                        <input type="date" class="form-control" wire:model.live="dateTo">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Per page</label>
                        <select class="form-select" wire:model.live="perPage">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 text-end">
                        <label class="form-label fw-semibold small invisible d-block">.</label>
                        <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                            <i class="fas fa-undo me-1"></i> Reset filters
                        </button>
                    </div>
                </div>

                {{-- Active filter chips --}}
                @if($this->activeFilterCount > 0)
                    <div class="mt-3 pt-3 border-top d-flex flex-wrap align-items-center gap-2">
                        <span class="text-muted small fw-semibold">
                            <i class="fa-regular fa-filter me-1"></i>
                            {{ $this->activeFilterCount }} active filter(s):
                        </span>

                        @if($search)
                            <button class="filter-chip" wire:click="clearFilter('search')">
                                Search: "{{ \Illuminate\Support\Str::limit($search, 20) }}"
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                        @if($logName)
                            <button class="filter-chip" wire:click="clearFilter('logName')">
                                Log: {{ $logName }} <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                        @if($event)
                            <button class="filter-chip" wire:click="clearFilter('event')">
                                Event: {{ $event }} <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                        @if($causerFilter)
                            <button class="filter-chip" wire:click="clearFilter('causerFilter')">
                                Causer:
                                {{ $stats['causers']->firstWhere('id', (int) $causerFilter)?->name ?? 'Unknown' }}
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                        @if($subjectTypeFilter)
                            <button class="filter-chip" wire:click="clearFilter('subjectTypeFilter')">
                                Subject: {{ class_basename($subjectTypeFilter) }}
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                        @if($dateFrom || $dateTo)
                            <button class="filter-chip"
                                wire:click="clearFilter('dateFrom'); clearFilter('dateTo'); clearFilter('datePreset')">
                                Dates: {{ $dateFrom ?: '—' }} → {{ $dateTo ?: '—' }}
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── BULK TOOLBAR ────────────────────────────────── --}}
        @if(count($selectedLogs) > 0)
            <div class="alert alert-dark d-flex align-items-center justify-content-between mb-3">
                <span>
                    <i class="fa-regular fa-check-square me-2"></i>
                    <strong>{{ count($selectedLogs) }}</strong> log(s) selected
                </span>
                <button class="btn btn-sm btn-outline-light"
                    wire:click="$set('selectedLogs', []); $set('selectAll', false)">
                    Clear selection
                </button>
            </div>
        @endif

        {{-- ─── TABLE ───────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0">
                    <i class="fas fa-list-ul text-primary me-2"></i> Activity Log
                    <span class="badge bg-secondary light border-0 ms-2">
                        {{ $logs->total() }} entries
                    </span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle logs-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll"
                                            wire:model.live="selectAll">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th wire:click="sort('created_at')" style="cursor:pointer; min-width:130px;">
                                    When
                                    <span class="ms-1">
                                        @if($sortBy === 'created_at' && $sortDir === 'asc')
                                            <i class="fa-regular fa-sort-up"></i>
                                        @elseif($sortBy === 'created_at' && $sortDir === 'desc')
                                            <i class="fa-regular fa-sort-down"></i>
                                        @else
                                            <i class="fa-regular fa-sort"></i>
                                        @endif
                                    </span>
                                </th>
                                <th style="min-width:180px;">Causer</th>
                                <th>Log</th>
                                <th>Event</th>
                                <th style="min-width:220px;">Description</th>
                                <th style="min-width:160px;">Subject</th>
                                <th class="text-center" style="width:80px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $rowClass = $log->event === 'deleted' ? 'row-deleted'
                                        : ($log->event === 'created' ? 'row-created' : '');
                                    $causerColors = ['primary', 'success', 'warning', 'info', 'secondary', 'danger'];
                                    $causerColor = $log->causer
                                        ? $causerColors[crc32($log->causer->name) % count($causerColors)]
                                        : 'secondary';
                                @endphp
                                <tr
                                    class="{{ $rowClass }} {{ in_array((string) $log->id, $selectedLogs) ? 'table-active' : '' }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model.live="selectedLogs"
                                                value="{{ $log->id }}">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">{{ $log->created_at->format('M d, Y') }}</div>
                                        <div class="text-muted" style="font-size:11.5px;">
                                            {{ $log->created_at->format('g:i A') }}
                                            · {{ $log->created_at->diffForHumans(null, true, true) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->causer)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm" aria-hidden="true">
                                                    <span class="avatar-text bg-{{ $causerColor }}">
                                                        {{ strtoupper(mb_substr($log->causer->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div class="min-width-0">
                                                    <div class="small fw-semibold text-truncate">{{ $log->causer->name }}</div>
                                                    <div class="text-muted text-truncate" style="font-size:11.5px;">
                                                        {{ $log->causer->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small"><i class="fa-regular fa-robot me-1"></i>
                                                System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary light border-0">
                                            {{ ucfirst(str_replace('_', ' ', $log->log_name ?? 'default')) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $eventColors = [
                                                'created' => 'success',
                                                'updated' => 'warning',
                                                'deleted' => 'danger',
                                                'restored' => 'info',
                                            ];
                                            $eventColor = $eventColors[$log->event] ?? 'secondary';
                                        @endphp
                                        @if($log->event)
                                            <span class="badge badge-{{ $eventColor }} light border-0">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">{{ $log->description }}</div>
                                    </td>
                                    <td>
                                        @if($log->subject_type)
                                            <div class="small">
                                                <i class="fa-regular fa-cube me-1 text-muted"></i>
                                                {{ class_basename($log->subject_type) }}
                                                @if($log->subject_id)
                                                    <span class="text-muted">#{{ $log->subject_id }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($log->properties && $log->properties->count())
                                            <button class="btn btn-sm btn-outline-primary"
                                                wire:click="openPropertyModal({{ $log->id }})" title="View change details">
                                                <i class="fa-regular fa-code-compare"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary"
                                                wire:click="openPropertyModal({{ $log->id }})" title="View entry">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fs-2 d-block text-muted mb-2"></i>
                                        <h5>No logs found</h5>
                                        <p class="text-muted mb-0">
                                            Try adjusting the filters, or clear them to see the full trail.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="text-muted small">
                            Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }}
                        </span>
                    </div>
                    <div class="col-md-6 text-end">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── PROPERTY / DIFF MODAL ───────────────────────────── --}}
    @if($showPropertyModal && $selectedLog)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">
                                {{ $selectedLog->description ?: 'Log entry' }}
                            </h5>
                            <div class="text-muted small">
                                {{ $selectedLog->created_at->format('F j, Y · g:i A') }}
                                ({{ $selectedLog->created_at->diffForHumans() }})
                            </div>
                        </div>
                        <button type="button" class="btn-close" wire:click="closePropertyModal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Meta row --}}
                        <div class="log-meta mb-4">
                            <div class="log-meta__item">
                                <span class="log-meta__label">Log name</span>
                                <span class="log-meta__value">{{ $selectedLog->log_name ?? '—' }}</span>
                            </div>
                            <div class="log-meta__item">
                                <span class="log-meta__label">Event</span>
                                <span class="log-meta__value">{{ ucfirst($selectedLog->event ?? '—') }}</span>
                            </div>
                            <div class="log-meta__item">
                                <span class="log-meta__label">Causer</span>
                                <span class="log-meta__value">
                                    {{ $selectedLog->causer?->name ?? 'System' }}
                                    @if($selectedLog->causer?->email)
                                        <span class="text-muted small d-block">{{ $selectedLog->causer->email }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="log-meta__item">
                                <span class="log-meta__label">Subject</span>
                                <span class="log-meta__value">
                                    {{ class_basename($selectedLog->subject_type) ?: '—' }}
                                    @if($selectedLog->subject_id)
                                        <span class="text-muted small d-block">ID {{ $selectedLog->subject_id }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Diff table --}}
                        @if(count($diffRows) > 0)
                            <h6 class="fw-bold mb-2 small text-uppercase text-muted">Changed fields</h6>
                            <div class="diff-table mb-4">
                                <div class="diff-table__head">
                                    <div class="diff-table__col">Field</div>
                                    <div class="diff-table__col">Before</div>
                                    <div class="diff-table__col">After</div>
                                </div>
                                @foreach($diffRows as $row)
                                    <div class="diff-table__row">
                                        <div class="diff-table__col diff-table__field">{{ $row['label'] }}</div>
                                        <div class="diff-table__col diff-table__old">
                                            <span class="diff-pill diff-pill--old">{{ $row['old'] }}</span>
                                        </div>
                                        <div class="diff-table__col diff-table__new">
                                            <span class="diff-pill diff-pill--new">{{ $row['new'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border mb-4">
                                <i class="fa-regular fa-circle-info me-1"></i>
                                No field changes recorded — this entry has context only.
                            </div>
                        @endif

                        {{-- Raw JSON (collapsible) --}}
                        <details class="log-raw mb-4">
                            <summary class="log-raw__summary">
                                <i class="fa-regular fa-code me-1"></i>
                                Raw payload
                            </summary>
                            <div class="log-raw__body">
                                <button type="button" class="btn btn-sm btn-outline-secondary mb-2" x-data x-on:click="
                                            navigator.clipboard.writeText(@js(json_encode($selectedLog->properties, JSON_PRETTY_PRINT)));
                                            $dispatch('notify', { type: 'success', title: 'Copied', message: 'Raw JSON copied to clipboard.' });
                                        ">
                                    <i class="fa-regular fa-copy me-1"></i> Copy JSON
                                </button>
                                <pre
                                    class="log-raw__pre">{{ json_encode($selectedLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </details>

                        {{-- Related activity --}}
                        @if(count($relatedActivity) > 0)
                            <h6 class="fw-bold mb-2 small text-uppercase text-muted">Other activity on this record</h6>
                            <div class="log-related">
                                @foreach($relatedActivity as $rel)
                                    <div class="log-related__row">
                                        <span class="log-related__dot log-related__dot--{{ $rel['event'] ?? 'default' }}"></span>
                                        <div class="log-related__body">
                                            <div class="log-related__desc">{{ $rel['description'] }}</div>
                                            <div class="log-related__meta">
                                                {{ $rel['causer'] }} · {{ $rel['when'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closePropertyModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── PRUNE MODAL ─────────────────────────────────────── --}}
    @if($showPruneModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.6);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fa-regular fa-triangle-exclamation me-2"></i> Prune old log entries?
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showPruneModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">
                            This permanently removes log entries older than the threshold. It does not affect any
                            vacancies, users, applications or other records — only the audit trail.
                        </p>
                        <label class="form-label fw-bold small">Delete logs older than</label>
                        <select class="form-select" wire:model="pruneDays">
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90">90 days</option>
                            <option value="180">6 months</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showPruneModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="pruneOldLogs" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="pruneOldLogs">
                                <i class="fa-regular fa-broom"></i> Prune
                            </span>
                            <span wire:loading wire:target="pruneOldLogs">
                                <i class="fa-regular fa-spinner fa-spin"></i> Pruning…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    /* ─── Avatars ─── */
    .avatar-sm {
        display: inline-block;
        width: 30px;
        height: 30px;
        flex-shrink: 0;
    }

    .avatar-text {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-weight: 700;
        color: #fff;
        border-radius: 50%;
        font-size: 12px;
    }

    .min-width-0 {
        min-width: 0;
    }

    /* ─── Table row tinting ─── */
    .logs-table tbody tr.row-created {
        background-color: rgba(16, 185, 129, .04);
    }

    .logs-table tbody tr.row-deleted {
        background-color: rgba(239, 68, 68, .04);
    }

    .logs-table tbody tr.row-created:hover {
        background-color: rgba(16, 185, 129, .08);
    }

    .logs-table tbody tr.row-deleted:hover {
        background-color: rgba(239, 68, 68, .08);
    }

    /* ─── Filter chips ─── */
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 999px;
        padding: 4px 10px 4px 12px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s ease;
    }

    .filter-chip:hover {
        background: #e0e7ff;
        color: #312e81;
    }

    .filter-chip i {
        font-size: 11px;
        opacity: .7;
    }

    /* ─── Sparkline ─── */
    .activity-spark {
        display: flex;
        align-items: flex-end;
        gap: 4px;
        height: 60px;
        margin-top: 12px;
    }

    .activity-spark__bar {
        flex: 1;
        background: linear-gradient(180deg, #6366f1, #4338ca);
        border-radius: 4px 4px 0 0;
        min-height: 4px;
        transition: transform .15s ease, opacity .15s ease;
        cursor: pointer;
    }

    .activity-spark__bar:hover {
        transform: scaleY(1.05);
        opacity: .85;
    }

    /* ─── Log meta grid ─── */
    .log-meta {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px 24px;
        padding: 16px 20px;
        background: #f8fafc;
        border: 1px solid #e7e9f2;
        border-radius: 12px;
    }

    .log-meta__item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .log-meta__label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 700;
        color: #94a3b8;
    }

    .log-meta__value {
        font-size: 14px;
        font-weight: 600;
        color: #0d1b2e;
    }

    /* ─── Diff table ─── */
    .diff-table {
        border: 1px solid #e7e9f2;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .diff-table__head,
    .diff-table__row {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1.2fr;
    }

    .diff-table__head {
        background: #f8fafc;
        border-bottom: 1px solid #e7e9f2;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 700;
        color: #94a3b8;
    }

    .diff-table__row {
        border-bottom: 1px solid #f1f5f9;
    }

    .diff-table__row:last-child {
        border-bottom: none;
    }

    .diff-table__col {
        padding: 12px 16px;
        font-size: 13.5px;
    }

    .diff-table__field {
        font-weight: 600;
        color: #0d1b2e;
    }

    .diff-table__old,
    .diff-table__new {
        display: flex;
        align-items: flex-start;
    }

    .diff-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        max-width: 100%;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.5;
    }

    .diff-pill--old {
        background: #fef2f2;
        color: #b91c1c;
        text-decoration: line-through;
        text-decoration-color: rgba(185, 28, 28, .4);
    }

    .diff-pill--new {
        background: #ecfdf5;
        color: #047857;
    }

    /* ─── Raw payload ─── */
    .log-raw__summary {
        cursor: pointer;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1px solid #e7e9f2;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #475467;
        list-style: none;
        user-select: none;
    }

    .log-raw__summary::-webkit-details-marker {
        display: none;
    }

    .log-raw__summary::after {
        content: '\f078';
        font-family: 'Font Awesome 6 Pro', 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 11px;
        float: right;
        color: #94a3b8;
        transition: transform .2s ease;
    }

    .log-raw[open] .log-raw__summary::after {
        transform: rotate(180deg);
    }

    .log-raw__body {
        padding: 12px 0 0;
    }

    .log-raw__pre {
        background: #0f172a;
        color: #e2e8f0;
        padding: 16px;
        border-radius: 10px;
        font-family: 'SF Mono', Menlo, Consolas, monospace;
        font-size: 12.5px;
        line-height: 1.6;
        max-height: 320px;
        overflow: auto;
        margin: 0;
    }

    /* ─── Related activity ─── */
    .log-related {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .log-related__row {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 10px 14px;
        border-radius: 10px;
        background: #fbfcfe;
        border: 1px solid #f1f5f9;
    }

    .log-related__dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 6px;
        background: #94a3b8;
    }

    .log-related__dot--created {
        background: #10b981;
    }

    .log-related__dot--updated {
        background: #f59e0b;
    }

    .log-related__dot--deleted {
        background: #ef4444;
    }

    .log-related__body {
        min-width: 0;
    }

    .log-related__desc {
        font-size: 13.5px;
        color: #0d1b2e;
        font-weight: 500;
    }

    .log-related__meta {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
    }

    @media (max-width: 767.98px) {

        .diff-table__head,
        .diff-table__row {
            grid-template-columns: 1fr;
        }

        .diff-table__head {
            display: none;
        }

        .diff-table__col {
            padding: 8px 14px;
        }

        .diff-table__old::before {
            content: 'Before: ';
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-right: 8px;
        }

        .diff-table__new::before {
            content: 'After: ';
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-right: 8px;
        }

        .log-meta {
            grid-template-columns: 1fr;
        }
    }
</style>
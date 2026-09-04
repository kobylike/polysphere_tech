<div x-data="logHandler()" @notify.window="showToast($event.detail)" class="position-relative">

    {{-- Toast --}}
    <div x-show="toastVisible" x-cloak x-transition:enter.duration.300ms.opacity.scale
        x-transition:leave.duration.200ms.opacity.scale class="position-fixed top-0 end-0 p-3"
        style="z-index: 9999; max-width: 420px; width: 100%;">
        <div class="d-flex align-items-center p-3 rounded-4 shadow-lg border-0 text-white gap-3"
            :class="toastType === 'success' ? 'bg-gradient-success' : 'bg-gradient-danger'"
            style="backdrop-filter: blur(8px); background: linear-gradient(135deg, #10b981, #059669);">
            <div class="flex-shrink-0">
                <i class="fas fa-2x" :class="toastType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold" style="color: #ffffff;" x-text="toastTitle"></h6>
                <p class="mb-0 small" style="color: #ffffff; opacity: 0.9;" x-html="toastMessage"></p>
            </div>
            <button @click="dismissToast()" class="btn btn-sm btn-link text-white p-0 opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- PAGE TITLES --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li><h5 class="bc-title">Activity Logs</h5></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Audit Trail</li>
        </ol>
    </div>

    <div class="container-fluid">
        {{-- Stats Row --}}
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

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-2 g-md-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold small">Search</label>
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Description, log name, properties...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Log Name</label>
                        <select class="form-select" wire:model.live="logName">
                            <option value="">All</option>
                            @foreach($stats['log_names'] as $name)
                                <option value="{{ $name }}">{{ ucfirst($name) }}</option>
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
                        <label class="form-label fw-semibold small">From</label>
                        <input type="date" class="form-control" wire:model.live="dateFrom">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">To</label>
                        <input type="date" class="form-control" wire:model.live="dateTo">
                    </div>
                    <div class="col-6 col-md-1">
                        <label class="form-label fw-semibold small">Per Page</label>
                        <select class="form-select" wire:model.live="perPage">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-1 text-end">
                        <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h6 class="card-title fw-bold"><i class="fas fa-list-ul text-primary me-2"></i> Activity Log</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sort('created_at')" style="cursor:pointer; min-width:140px;">
                                    Date/Time
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
                                <th>User</th>
                                <th>Log Name</th>
                                <th>Event</th>
                                <th>Description</th>
                                <th>Properties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $log->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $log->created_at->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($log->causer)
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $log->causer->avatar_url }}" alt="" class="rounded-circle" style="width:32px; height:32px; object-fit:cover;">
                                                <div>
                                                    <div>{{ $log->causer->name }}</div>
                                                    <small class="text-muted">{{ $log->causer->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary light border-0">{{ $log->log_name ?? 'default' }}</span></td>
                                    <td>
                                        @if($log->event)
                                            <span class="badge bg-{{ $log->event === 'created' ? 'success' : ($log->event === 'updated' ? 'warning' : ($log->event === 'deleted' ? 'danger' : 'info')) }} light border-0">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        @if($log->properties && $log->properties->count())
                                            <button class="btn btn-sm btn-outline-secondary" wire:click="openPropertyModal({{ $log->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-inbox fs-2 d-block text-muted mb-2"></i>
                                        <p class="text-muted">No logs found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    {{-- Property Modal --}}
    @if($showPropertyModal && $selectedLog)
        <div class="modal fade show d-block" id="propertyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title">Log Properties</h5>
                        <button type="button" class="btn-close" wire:click="closePropertyModal"></button>
                    </div>
                    <div class="modal-body">
                        <pre class="bg-light p-3 rounded" style="max-height:400px; overflow-y:auto;">{{ json_encode($selectedLog->properties, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closePropertyModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('logHandler', () => ({
            toastVisible: false,
            toastType: 'success',
            toastTitle: '',
            toastMessage: '',
            toastTimeout: null,
            init() {
                window.addEventListener('livewire:navigate', () => {
                    this.toastVisible = false;
                    clearTimeout(this.toastTimeout);
                });
            },
            showToast(detail) {
                this.toastType = detail.type || 'success';
                this.toastTitle = detail.title || (this.toastType === 'success' ? 'Success!' : 'Error!');
                this.toastMessage = detail.message || '';
                this.toastVisible = true;
                clearTimeout(this.toastTimeout);
                this.toastTimeout = setTimeout(() => {
                    this.dismissToast();
                }, 4000);
            },
            dismissToast() {
                this.toastVisible = false;
                clearTimeout(this.toastTimeout);
            }
        }));
    });
</script>
@endpush

<style>
    .bg-gradient-success { background: linear-gradient(135deg, #10b981, #059669); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
    [x-cloak] { display: none !important; }
</style>
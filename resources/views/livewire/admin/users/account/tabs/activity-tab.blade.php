<div x-data="activityHandler()" @notify.window="showToast($event.detail)" class="position-relative">

    {{-- ─── Toast ────────────────────────────────────────────────────────── --}}
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

    {{-- ─── STATS ROW ─────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Total Activities --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-primary">
                <div class="stats-card-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Total Activities</span>
                    <h3 class="stats-card-value">{{ number_format($stats['total']) }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-arrow-up"></i> Active
                    </span>
                </div>
            </div>
        </div>

        {{-- Login Attempts --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-success">
                <div class="stats-card-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Login Attempts</span>
                    <h3 class="stats-card-value">{{ number_format($stats['login_attempts']) }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-arrow-up"></i> Successful
                    </span>
                </div>
            </div>
        </div>

        {{-- Admin Actions --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-warning">
                <div class="stats-card-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Admin Actions</span>
                    <h3 class="stats-card-value">{{ number_format($stats['admin_actions']) }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-arrow-up"></i> Changes
                    </span>
                </div>
            </div>
        </div>

        {{-- Last Login --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-info">
                <div class="stats-card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Last Login</span>
                    <h3 class="stats-card-value">
                        {{ $stats['last_login'] ? $stats['last_login']->diffForHumans() : 'Never' }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-calendar-check"></i> Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Filters ───────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                            placeholder="Search...">
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-2">
                    <label class="form-label fw-semibold small">Action</label>
                    <select class="form-select form-select-sm" wire:model.live="filterAction">
                        <option value="">All</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md-2">
                    <label class="form-label fw-semibold small">Date</label>
                    <select class="form-select form-select-sm" wire:model.live="filterDate">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                @if($filterDate == 'custom')
                    <div class="col-6 col-sm-6 col-md-2">
                        <label class="form-label fw-semibold small">From</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="dateFrom">
                    </div>
                    <div class="col-6 col-sm-6 col-md-2">
                        <label class="form-label fw-semibold small">To</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="dateTo">
                    </div>
                @endif
                <div class="col-6 col-sm-6 col-md-1">
                    <label class="form-label fw-semibold small">Per Page</label>
                    <select class="form-select form-select-sm" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md-1 text-end">
                    <button class="btn btn-outline-secondary btn-sm w-100" wire:click="resetFilters" title="Reset">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Main Content ─────────────────────────────────────────────────── --}}
    <div class="row g-4">
        {{-- Left Sidebar: Timeline (hidden on small screens, shown on md+) --}}
        <div class="col-xl-3 col-lg-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title fw-bold"><i class="fas fa-clock text-primary me-2"></i> Recent</h6>
                </div>
                <div class="card-body">
                    @if($timelineActivities->count() > 0)
                        <div class="widget-timeline-status">
                            <ul class="timeline ps-0">
                                @foreach($timelineActivities as $activity)
                                    <li class="d-flex align-items-start mb-2">
                                        <span class="timeline-status me-2">{{ $activity->created_at->format('H:i') }}</span>
                                        <span
                                            class="timeline-badge border-{{ $activity->action === 'login' ? 'success' : ($activity->action === 'logout' ? 'danger' : 'primary') }} me-2"></span>
                                        <div class="timeline-panel">
                                            <span class="text-secondary fs-14">
                                                {{ $activity->description ?? ucfirst(str_replace('_', ' ', $activity->action)) }}
                                            </span>
                                            <div class="text-muted small">{{ $activity->created_at->diffForHumans() }}</div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-muted fs-2 d-block mb-2"></i>
                            <p class="text-muted">No activities yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Table --}}
        <div class="col-xl-9 col-lg-8 col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title fw-bold"><i class="fas fa-list-ul text-primary me-2"></i> Activity Log</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 140px;">Date & Time</th>
                                    <th style="min-width: 100px;">Action</th>
                                    <th>Description</th>
                                    <th class="d-none d-md-table-cell">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $activity->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $activity->created_at->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $activity->action === 'login' ? 'success' : ($activity->action === 'logout' ? 'danger' : 'primary') }} light border-0">
                                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->description ?? '—' }}</td>
                                        <td class="d-none d-md-table-cell">
                                            <code class="small">{{ $activity->ip_address ?? '—' }}</code>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="fas fa-inbox text-muted fs-2 d-block mb-2"></i>
                                            <p class="text-muted">No activities found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($activities->hasPages())
                    <div class="card-footer bg-transparent border-0">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-muted small">
                                Showing {{ $activities->firstItem() ?? 0 }}–{{ $activities->lastItem() ?? 0 }}
                                of {{ $activities->total() }} activities
                            </div>
                            <div class="col-md-6 text-end">
                                {{ $activities->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ─── Styles ─────────────────────────────────────────────────────────────── --}}
<style>
    [x-cloak] {
        display: none !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .stats-card {
        position: relative;
        padding: 1.25rem 1rem;
        border-radius: 1rem;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.3);
        min-height: 90px;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.06;
        transform: translate(30%, -30%);
        pointer-events: none;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .stats-card-primary::before {
        background: #4f46e5;
    }

    .stats-card-success::before {
        background: #10b981;
    }

    .stats-card-warning::before {
        background: #f59e0b;
    }

    .stats-card-info::before {
        background: #06b6d4;
    }

    .stats-card-icon {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .stats-card:hover .stats-card-icon {
        transform: scale(1.05) rotate(-3deg);
    }

    .stats-card-primary .stats-card-icon {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
    }

    .stats-card-success .stats-card-icon {
        background: linear-gradient(135deg, #34d399, #10b981);
    }

    .stats-card-warning .stats-card-icon {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
    }

    .stats-card-info .stats-card-icon {
        background: linear-gradient(135deg, #22d3ee, #06b6d4);
    }

    .stats-card-content {
        flex: 1;
        min-width: 0;
    }

    .stats-card-label {
        display: block;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #94a3b8;
        margin-bottom: 0.1rem;
    }

    .stats-card-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 0.1rem;
        letter-spacing: -0.02em;
    }

    .stats-card-trend {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.65rem;
        font-weight: 600;
        color: #94a3b8;
        padding: 0.05rem 0.4rem;
        border-radius: 20px;
        background: #f1f5f9;
    }

    .stats-card-trend .fa-arrow-up {
        color: #10b981;
    }

    .stats-card-trend .fa-arrow-down {
        color: #ef4444;
    }

    .stats-card-trend .fa-calendar-check {
        color: #6366f1;
    }

    [data-theme-version="dark"] .stats-card {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.04);
    }

    [data-theme-version="dark"] .stats-card-value {
        color: #f1f5f9;
    }

    [data-theme-version="dark"] .stats-card-trend {
        background: #334155;
        color: #94a3b8;
    }

    .timeline-status {
        font-weight: 600;
        font-size: 0.75rem;
        color: #94a3b8;
        min-width: 40px;
    }

    .timeline-badge {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid;
        display: inline-block;
        margin-right: 10px;
        flex-shrink: 0;
        margin-top: 4px;
    }

    .timeline-panel {
        flex: 1;
    }

    .timeline li {
        margin-bottom: 12px;
    }

    @media (max-width: 575.98px) {
        .stats-card {
            padding: 0.75rem 0.5rem;
            min-height: 70px;
            gap: 0.5rem;
            border-radius: 0.75rem;
        }

        .stats-card-icon {
            width: 36px;
            height: 36px;
            font-size: 0.95rem;
            border-radius: 8px;
        }

        .stats-card-value {
            font-size: 1.1rem;
        }

        .stats-card-label {
            font-size: 0.55rem;
        }

        .stats-card-trend {
            font-size: 0.55rem;
            padding: 0.05rem 0.3rem;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .form-control-sm {
            font-size: 0.8rem;
            padding: 0.25rem 0.4rem;
        }
    }

    @media (min-width: 576px) and (max-width: 767.98px) {
        .stats-card {
            padding: 1rem;
            min-height: 80px;
        }

        .stats-card-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .stats-card-value {
            font-size: 1.2rem;
        }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
        .stats-card {
            padding: 1.25rem;
            min-height: 90px;
        }

        .stats-card-icon {
            width: 44px;
            height: 44px;
            font-size: 1.1rem;
        }

        .stats-card-value {
            font-size: 1.3rem;
        }
    }
</style>

{{-- ─── Alpine Handler ────────────────────────────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('activityHandler', () => ({
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
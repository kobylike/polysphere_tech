<div x-data="notificationHandler()" @notify.window="showToast($event.detail)" class="position-relative">

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

    {{-- ─── STATS ROW ─────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-primary">
                <div class="stats-card-icon"><i class="fas fa-bell"></i></div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Total</span>
                    <h3 class="stats-card-value">{{ number_format($stats['total']) }}</h3>
                    <span class="stats-card-trend"><i class="fas fa-arrow-up"></i> All time</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-warning">
                <div class="stats-card-icon"><i class="fas fa-envelope"></i></div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Unread</span>
                    <h3 class="stats-card-value">{{ number_format($stats['unread']) }}</h3>
                    <span class="stats-card-trend"><i class="fas fa-arrow-up"></i> Pending</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-success">
                <div class="stats-card-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Read</span>
                    <h3 class="stats-card-value">{{ number_format($stats['read']) }}</h3>
                    <span class="stats-card-trend"><i class="fas fa-arrow-up"></i> Done</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-info">
                <div class="stats-card-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Today</span>
                    <h3 class="stats-card-value">{{ number_format($stats['today']) }}</h3>
                    <span class="stats-card-trend"><i class="fas fa-calendar-check"></i> New</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── FILTERS ─────────────────────────────────────────────────────────--}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                            placeholder="Title, body, type...">
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-2">
                    <label class="form-label fw-semibold small">Status</label>
                    <select class="form-select form-select-sm" wire:model.live="filterStatus">
                        <option value="">All</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
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
            @if($stats['unread'] > 0)
                <div class="mt-3">
                    <button class="btn btn-primary btn-sm" wire:click="markAllAsRead">
                        <i class="fas fa-check-double me-1"></i> Mark all as read
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ─── TABLE ─────────────────────────────────────────────────────────--}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <h6 class="card-title fw-bold"><i class="fas fa-bell text-primary me-2"></i> Notifications</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 140px;">Date</th>
                            <th style="min-width: 100px;">Type</th>
                            <th>Title / Body</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th class="text-center" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr class="{{ $notification->isUnread() ? 'fw-semibold bg-light' : '' }}"
                                style="cursor: pointer;"
                                onclick="window.Livewire.dispatch('open-notification-detail', { id: {{ $notification->id }} })">
                                <td>
                                    <div class="fw-semibold">{{ $notification->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $notification->created_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $notification->type === 'warning' ? 'warning' : ($notification->type === 'danger' ? 'danger' : ($notification->type === 'success' ? 'success' : 'info')) }} light border-0">
                                        <i class="fas {{ $notification->icon ?? 'fa-circle-info' }} me-1"></i>
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $notification->title ?? 'Notification' }}</div>
                                    <div class="text-muted small">{{ $notification->body }}</div>
                                    @if($notification->link)
                                        <a href="{{ $notification->link }}" class="small text-primary" wire:navigate>
                                            <i class="fas fa-arrow-right me-1"></i> View
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($notification->isUnread())
                                        <span class="badge bg-warning text-white">Unread</span>
                                    @else
                                        <span class="badge bg-success">Read</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($notification->isUnread())
                                        <button class="btn btn-sm btn-outline-primary"
                                            wire:click.stop="markAsRead({{ $notification->id }})" title="Mark as read">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-secondary"
                                        wire:click.stop="openDetailModal({{ $notification->id }})" title="View details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-bell-slash text-muted fs-2 d-block mb-2"></i>
                                    <p class="text-muted">No notifications found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer bg-transparent border-0">
                <div class="row align-items-center">
                    <div class="col-md-6 text-muted small">
                        Showing {{ $notifications->firstItem() ?? 0 }}–{{ $notifications->lastItem() ?? 0 }}
                        of {{ $notifications->total() }} notifications
                    </div>
                    <div class="col-md-6 text-end">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ─── DETAIL MODAL ───────────────────────────────────────────────────--}}
    @if($showDetailModal && $selectedNotification)
        <div class="modal fade show d-block" id="notificationDetailModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header"
                        style="border-bottom: 3px solid {{ $selectedNotification->type === 'warning' ? '#f59e0b' : ($selectedNotification->type === 'danger' ? '#ef4444' : ($selectedNotification->type === 'success' ? '#10b981' : '#6366f1')) }};">
                        <h5 class="modal-title">
                            <i class="fas {{ $selectedNotification->icon ?? 'fa-circle-info' }} me-2"
                                style="color: {{ $selectedNotification->type === 'warning' ? '#f59e0b' : ($selectedNotification->type === 'danger' ? '#ef4444' : ($selectedNotification->type === 'success' ? '#10b981' : '#6366f1')) }};"></i>
                            {{ $selectedNotification->title ?? 'Notification' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Type</div>
                                <span
                                    class="badge bg-{{ $selectedNotification->type === 'warning' ? 'warning' : ($selectedNotification->type === 'danger' ? 'danger' : ($selectedNotification->type === 'success' ? 'success' : 'info')) }} light border-0 mt-1">
                                    {{ ucfirst($selectedNotification->type) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Status</div>
                                <div class="mt-1">
                                    @if($selectedNotification->isUnread())
                                        <span class="badge bg-warning text-white">Unread</span>
                                    @else
                                        <span class="badge bg-success">Read</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Received</div>
                                <div class="mt-1">{{ $selectedNotification->created_at->format('F d, Y g:i A') }}</div>
                                <div class="text-muted small">{{ $selectedNotification->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Sender</div>
                                <div class="mt-1">{{ $selectedNotification->sender_name ?? 'System' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="fw-bold text-muted small">Message</div>
                                <div class="p-3 bg-light rounded-3 mt-1">{{ $selectedNotification->body }}</div>
                            </div>
                            @if($selectedNotification->link)
                                <div class="col-12">
                                    <div class="fw-bold text-muted small">Link</div>
                                    <a href="{{ $selectedNotification->link }}" class="mt-1 d-inline-block" wire:navigate>
                                        <i class="fas fa-arrow-right me-1"></i> {{ $selectedNotification->link }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeDetailModal">Close</button>
                        <button class="btn btn-{{ $selectedNotification->isUnread() ? 'success' : 'warning' }}"
                            wire:click="toggleReadStatus">
                            <i class="fas {{ $selectedNotification->isUnread() ? 'fa-check' : 'fa-undo' }} me-1"></i>
                            {{ $selectedNotification->isUnread() ? 'Mark as read' : 'Mark as unread' }}
                        </button>
                        @if($selectedNotification->link)
                            <a href="{{ $selectedNotification->link }}" class="btn btn-primary" wire:navigate>
                                <i class="fas fa-arrow-right me-1"></i> Go to link
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
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
</style>

{{-- ─── Alpine Handler ────────────────────────────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationHandler', () => ({
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
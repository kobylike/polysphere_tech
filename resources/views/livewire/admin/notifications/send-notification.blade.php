<div x-data="notificationSender()" @notify.window="showToast($event.detail)" class="position-relative">

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
                <p class="mb-0 small" style="color: #ffffff; opacity: 0.9;" x-text="toastMessage"></p>
            </div>
            <button @click="dismissToast()" class="btn btn-sm btn-link text-white p-0 opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- ─── PAGE TITLES ─────────────────────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li><h5 class="bc-title">Send Notification</h5></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Send Notification</li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row g-4">
            {{-- Compose Form --}}
            <div class="col-xl-5 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h4 class="card-title mb-0"><i class="fas fa-paper-plane text-primary me-2"></i>Compose Notification</h4>
                    </div>
                    <div class="card-body p-4">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="send">
                            {{-- Recipient Type --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Recipient Type</label>
                                <select class="form-select" wire:model.live="recipientType">
                                    <option value="all">🌐 All Users</option>
                                    <option value="employees">👔 All Employees</option>
                                    <option value="roles">👥 Specific Roles</option>
                                    <option value="positions">💼 Specific Positions</option>
                                    <option value="users">👤 Specific Users</option>
                                    <option value="custom">🎯 Custom (Users + Roles + Positions)</option>
                                </select>
                            </div>

                            {{-- Roles --}}
                            @if(in_array($recipientType, ['roles', 'custom']))
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Roles</label>
                                    <select class="form-select" wire:model="selectedRoles" multiple size="3">
                                        @foreach($this->roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedRoles') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- Positions --}}
                            @if(in_array($recipientType, ['positions', 'custom']))
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Positions</label>
                                    <select class="form-select" wire:model="selectedPositions" multiple size="3">
                                        @foreach($this->positionsList as $position)
                                            <option value="{{ $position }}">{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedPositions') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- Users --}}
                            @if(in_array($recipientType, ['users', 'custom']))
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Users</label>
                                    <select class="form-select" wire:model="selectedUsers" multiple size="3">
                                        @foreach($this->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('selectedUsers') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Title <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" class="form-control" wire:model="title" placeholder="e.g. System Maintenance">
                            </div>

                            {{-- Body --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Body <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="5" wire:model="body" placeholder="Write your message..."></textarea>
                                @error('body') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Type & Icon --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Type</label>
                                    <select class="form-select" wire:model="type">
                                        <option value="info">ℹ️ Info</option>
                                        <option value="success">✅ Success</option>
                                        <option value="warning">⚠️ Warning</option>
                                        <option value="danger">❌ Danger</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Icon (FontAwesome)</label>
                                    <input type="text" class="form-control" wire:model="icon" placeholder="fa-bell">
                                    <small class="text-muted">e.g. fa-bell, fa-envelope, fa-star</small>
                                </div>
                            </div>

                            {{-- Link --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Link (optional)</label>
                                <input type="url" class="form-control" wire:model="link" placeholder="https://example.com">
                                @error('link') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Submit --}}
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill" wire:loading.attr="disabled">
                                    <span wire:loading.remove><i class="fas fa-paper-plane me-2"></i>Send Notification</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Sending...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sent History --}}
            <div class="col-xl-7 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0"><i class="fas fa-history text-primary me-2"></i>Sent History</h4>
                        <div class="d-flex gap-2">
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm" placeholder="Search..."
                                       wire:model.live.debounce.300ms="search" style="width:200px; padding-left: 30px;">
                                <i class="fas fa-search position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            </div>
                            <select class="form-select form-select-sm" wire:model.live="statusFilter" style="width:130px;">
                                <option value="">All</option>
                                <option value="read">Read</option>
                                <option value="unread">Unread</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($sentNotifications->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Title / Body</th>
                                            <th>Recipient</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Sent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sentNotifications as $index => $notif)
                                            <tr>
                                                <td>{{ $sentNotifications->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $notif->title ?? 'No title' }}</div>
                                                    <div class="text-muted small text-truncate" style="max-width:200px;">{{ $notif->body }}</div>
                                                </td>
                                                <td>
                                                    @if($notif->notifiable_type === 'App\Models\User')
                                                        {{ $notif->notifiable?->name ?? 'Deleted User' }}
                                                    @else
                                                        <span class="badge bg-secondary">System</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $notif->type === 'warning' ? 'warning' : ($notif->type === 'danger' ? 'danger' : ($notif->type === 'success' ? 'success' : 'info')) }}">
                                                        {{ ucfirst($notif->type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($notif->isRead())
                                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Read</span>
                                                    @else
                                                        <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>Unread</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="small">{{ $notif->created_at->format('d M Y') }}</div>
                                                    <div class="text-muted small">{{ $notif->created_at->diffForHumans() }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-transparent">
                                {{ $sentNotifications->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No notifications sent yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Styles ─────────────────────────────────────────────────────────────── --}}
<style>
    [x-cloak] { display: none !important; }

    .bg-gradient-success { background: linear-gradient(135deg, #10b981, #059669); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

    .rounded-4 { border-radius: 1rem; }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.25);
    }

    .btn-primary:active {
        transform: scale(0.98);
    }

    .table {
        font-size: 0.9rem;
    }

    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge.bg-success { background-color: #10b981 !important; }
    .badge.bg-warning { background-color: #f59e0b !important; color: #000 !important; }
    .badge.bg-danger { background-color: #ef4444 !important; }
    .badge.bg-info { background-color: #06b6d4 !important; }

    .pagination .page-link {
        color: #6366f1;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
    }

    .pagination .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-color: #6366f1;
        color: #fff;
    }

    .pagination .page-item.disabled .page-link {
        color: #94a3b8;
        background-color: #f8fafc;
    }

    @media (max-width: 575.98px) {
        .card-body { padding: 1.25rem !important; }
        .table { font-size: 0.8rem; }
        .table td, .table th { padding: 0.4rem 0.4rem; }
        .form-select, .form-control { font-size: 0.85rem; }
        .btn { font-size: 0.85rem; padding: 0.4rem 1rem; }
        .btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
    }
</style>

{{-- ─── Alpine Handler ────────────────────────────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationSender', () => ({
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
<div>
    {{-- ─── PAGE TITLES ─────────────────────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li><h5 class="bc-title">Newsletter Subscribers</h5></li>
            <li class="breadcrumb-item"><a wire:navigate.hover href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Subscribers</li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    {{-- ─── Card Header ───────────────────────────────────────── --}}
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-envelope-open-text text-primary me-2"></i>
                            Subscribers
                            <span class="badge bg-primary ms-2">{{ $subscribers->total() }}</span>
                        </h4>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            {{-- Search --}}
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm"
                                       placeholder="Search email..."
                                       wire:model.live.debounce.300ms="search"
                                       style="width:200px; padding-left: 30px;">
                                <i class="fas fa-search position-absolute"
                                   style="left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            </div>

                            {{-- Status filter --}}
                            <select class="form-select form-select-sm" wire:model.live="statusFilter" style="width:130px;">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="unsubscribed">Unsubscribed</option>
                            </select>

                            {{-- Export --}}
                            <button wire:click="exportCsv" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-export me-1"></i> Export CSV
                            </button>

                            {{-- Send Newsletter --}}
                            <button wire:click="openNewsletterModal" class="btn btn-sm btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Send Newsletter
                            </button>

                            {{-- Bulk actions --}}
                            @if(count($selectedIds) > 0)
                                <button wire:click="bulkDelete" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete selected subscribers permanently?')">
                                    <i class="fas fa-trash-alt me-1"></i> Delete ({{ count($selectedIds) }})
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- ─── Card Body ────────────────────────────────────────── --}}
                    <div class="card-body p-0">
                        @if($subscribers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 40px;">
                                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                            </th>
                                            <th>ID</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Subscribed At</th>
                                            <th>IP Address</th>
                                            <th style="width: 200px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscribers as $subscriber)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $subscriber->id }}" class="form-check-input">
                                                </td>
                                                <td>#{{ $subscriber->id }}</td>
                                                <td>
                                                    <span class="fw-semibold">{{ $subscriber->email }}</span>
                                                    @if($subscriber->isPending())
                                                        <span class="badge bg-warning ms-1">unverified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($subscriber->isActive())
                                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>
                                                    @elseif($subscriber->isPending())
                                                        <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                                    @else
                                                        <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>Unsubscribed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($subscriber->subscribed_at)
                                                        <div class="small">{{ $subscriber->subscribed_at->format('d M Y H:i') }}</div>
                                                        <div class="text-muted small">{{ $subscriber->subscribed_at->diffForHumans() }}</div>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="font-monospace small">{{ $subscriber->subscribed_ip ?? '—' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        @if($subscriber->isPending())
                                                            <button wire:click="resendVerification({{ $subscriber->id }})"
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    title="Resend verification email">
                                                                <i class="fas fa-envelope"></i>
                                                            </button>
                                                            <button wire:click="markActive({{ $subscriber->id }})"
                                                                    class="btn btn-sm btn-outline-success"
                                                                    title="Mark as active">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                        @if($subscriber->isActive())
                                                            <button wire:click="markUnsubscribed({{ $subscriber->id }})"
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    title="Mark as unsubscribed">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        <button wire:click="delete({{ $subscriber->id }})"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Delete this subscriber permanently?')"
                                                                title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- ─── Pagination ────────────────────────────────── --}}
                            <div class="card-footer bg-transparent border-top">
                                {{ $subscribers->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">
                                    @if($search || $statusFilter)
                                        No subscribers match your filters.
                                    @else
                                        No subscribers yet.
                                    @endif
                                </p>
                                @if($search || $statusFilter)
                                    <button wire:click="$set('search', '')" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Clear filters
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── NEWSLETTER MODAL ─────────────────────────────────────────────────── --}}
    @if($showNewsletterModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form wire:submit.prevent="sendNewsletter">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-paper-plane text-primary me-2"></i>
                                Send Newsletter
                            </h5>
                            <button type="button" class="btn-close" wire:click="$set('showNewsletterModal', false)"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="newsletterSubject" placeholder="e.g. Our Latest Insights">
                                @error('newsletterSubject') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Body <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="10" wire:model="newsletterBody" placeholder="Write your newsletter content..."></textarea>
                                @error('newsletterBody') <span class="text-danger">{{ $message }}</span> @enderror
                                <small class="text-muted">HTML is supported.</small>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                This will send to <strong>{{ \App\Models\Subscriber::where('status', 'active')->count() }}</strong> active subscribers.
                                Emails will be queued and sent in the background.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$set('showNewsletterModal', false)">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-paper-plane me-1"></i> Send Newsletter
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-1"></i> Queuing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>

{{-- ─── Styles ─────────────────────────────────────────────────────────────── --}}
<style>
    .rounded-4 { border-radius: 1rem; }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
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
    .badge.bg-secondary { background-color: #94a3b8 !important; }
    .badge.bg-primary { background-color: #6366f1 !important; }

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

    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
    }

    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }

    @media (max-width: 575.98px) {
        .card-body { padding: 0.5rem !important; }
        .table { font-size: 0.75rem; }
        .table td, .table th { padding: 0.3rem 0.3rem; }
        .form-select, .form-control { font-size: 0.8rem; }
        .btn { font-size: 0.75rem; padding: 0.2rem 0.5rem; }
        .modal-dialog { margin: 0.5rem; }
        .modal-body { padding: 1rem; }
    }

    @media (min-width: 576px) and (max-width: 767.98px) {
        .card-body { padding: 0.75rem !important; }
        .table { font-size: 0.8rem; }
        .table td, .table th { padding: 0.4rem 0.4rem; }
    }
</style>
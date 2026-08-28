<div>
    {{-- ─── PAGE TITLES ─────────────────────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Permissions</h5>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.125 6.375L8.5 1.41667L14.875 6.375V14.1667C14.875 14.5424 14.7257 14.9027 14.4601 15.1684C14.1944 15.4341 13.8341 15.5833 13.4583 15.5833H3.54167C3.16594 15.5833 2.80561 15.4341 2.53993 15.1684C2.27426 14.9027 2.125 14.5424 2.125 14.1667V6.375Z"
                            stroke="#2C2C2C" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6.375 15.5833V8.5H10.625V15.5833" stroke="#2C2C2C" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Home
                </a>
            </li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Permissions</a></li>
        </ol>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" wire:click="openCreate">
                <i class="fa-regular fa-plus me-1"></i> New Permission
            </button>
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
                <i class="fa-regular fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    {{-- ─── STATS ROW ──────────────────────────────────────────────────────── --}}
    <div class="container-fluid">
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Total Permissions</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fa-regular fa-key fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">In Use</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['used']) }}</h3>
                        </div>
                        <i class="fa-regular fa-link fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Unused</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['unused']) }}</h3>
                        </div>
                        <i class="fa-regular fa-unlink fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Guard Types</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['guard_types']) }}</h3>
                        </div>
                        <i class="fa-regular fa-shield-halved fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── FILTERS & BULK BAR ─────────────────────────────────────────── --}}
        <div class="row align-items-center mb-3">
            <div class="col-xl-8 col-lg-6">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm" placeholder="Search permissions…"
                            wire:model.live.debounce.300ms="search">
                        <i class="fa-regular fa-search"></i>
                    </div>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="perPage">
                        <option value="10">10 / page</option>
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>
            </div>
            @if(count($selectedPermissions) > 0)
                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                        <span class="badge bg-dark text-white p-2">{{ count($selectedPermissions) }} selected</span>
                        <button class="btn btn-danger btn-sm" wire:click="confirmBulkDelete">
                            <i class="fa-regular fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- ─── TABLE ──────────────────────────────────────────────────────── --}}
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive active-projects style-1">
                            <div class="tbl-caption">
                                <h4 class="heading mb-0">Permissions</h4>
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width:40px">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll"
                                                    wire:model.live="selectAll">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th wire:click="sort('name')" style="cursor:pointer;">
                                            Permission
                                            <span class="ms-1">
                                                @if($sortBy === 'name' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'name' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th wire:click="sort('guard_name')" style="cursor:pointer;">
                                            Guard
                                            <span class="ms-1">
                                                @if($sortBy === 'guard_name' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'guard_name' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th>Roles</th>
                                        <th wire:click="sort('created_at')" style="cursor:pointer;">
                                            Created
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
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permissions as $perm)
                                        @php
                                            $isUsed = $perm->roles_count > 0;
                                        @endphp
                                        <tr
                                            class="{{ in_array((string) $perm->id, $selectedPermissions) ? 'table-active' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="selectedPermissions" value="{{ $perm->id }}"
                                                        @if($isUsed) disabled @endif>
                                                    <label class="form-check-label"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge"
                                                        style="background: #6366f1; width:12px; height:12px; border-radius:4px;"></span>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $perm->name }}
                                                            @if($isUsed)
                                                                <span class="badge bg-success text-white ms-1">In Use</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-secondary light border-0">{{ $perm->guard_name }}</span>
                                            </td>
                                            <td>
                                                @if($perm->roles_count > 0)
                                                    <span class="badge badge-success light border-0">{{ $perm->roles_count }}
                                                        role(s)</span>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $perm->created_at->format('M d, Y') }}</div>
                                                    <div class="text-muted small">{{ $perm->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-sm btn-primary"
                                                        wire:click="viewPermission({{ $perm->id }})" title="View">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning"
                                                        wire:click="openEdit({{ $perm->id }})" title="Edit">
                                                        <i class="fa-regular fa-pen"></i>
                                                    </button>
                                                    @if(!$isUsed)
                                                        <button class="btn btn-sm btn-danger"
                                                            wire:click="confirmDelete({{ $perm->id }})" title="Delete">
                                                            <i class="fa-regular fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled
                                                            title="Used by roles — cannot delete">
                                                            <i class="fa-regular fa-lock"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fa-regular fa-key fs-2 d-block mb-2 text-muted"></i>
                                                <h5>No permissions found</h5>
                                                <p class="text-muted">Try adjusting your search, or create your first
                                                    permission.</p>
                                                <button class="btn btn-primary btn-sm" wire:click="openCreate">
                                                    <i class="fa-regular fa-plus"></i> Create First Permission
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="row align-items-center p-3">
                            <div class="col-md-6">
                                <span>Showing {{ $permissions->firstItem() ?? 0 }}–{{ $permissions->lastItem() ?? 0 }}
                                    of {{ $permissions->total() }} permissions</span>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ $permissions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────────────────────────────────── --}}
    {{-- MODALS --}}
    {{-- ────────────────────────────────────────────────────────────────────── --}}

    {{-- ── VIEW PERMISSION MODAL ──────────────────────────────────────────── --}}
    @if($showViewModal && $viewingPermission)
        <div class="modal fade show d-block" id="viewPermissionModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: 3px solid #6366f1;">
                        <h5 class="modal-title">
                            <i class="fa-regular fa-key" style="color: #6366f1;"></i>
                            {{ $viewingPermission->name }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Guard Name</div>
                                <div>{{ $viewingPermission->guard_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Roles Using It</div>
                                <div>{{ $viewingPermission->roles->count() }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Created</div>
                                <div>{{ $viewingPermission->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Updated</div>
                                <div>{{ $viewingPermission->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @if($viewingPermission->roles->isNotEmpty())
                            <hr>
                            <div class="fw-bold text-muted small mb-2">Assigned to Roles</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($viewingPermission->roles as $role)
                                    <span class="badge bg-secondary light border-0">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showViewModal', false)">Close</button>
                        <button class="btn btn-primary"
                            wire:click="openEdit({{ $viewingPermission->id }}); $set('showViewModal', false)">
                            <i class="fa-regular fa-pen"></i> Edit Permission
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── CREATE / EDIT PERMISSION MODAL ────────────────────────────────── --}}
    @if($showPermissionModal)
        <div class="modal fade show d-block" id="permissionModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditing ? 'Edit Permission' : 'Create New Permission' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showPermissionModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Permission Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                                placeholder="e.g. edit users">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Guard Name</label>
                            <input type="text" class="form-control @error('guard_name') is-invalid @enderror"
                                wire:model="guard_name" placeholder="web">
                            @error('guard_name')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showPermissionModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="savePermission" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular {{ $isEditing ? 'fa-save' : 'fa-plus' }}"></i>
                                {{ $isEditing ? 'Save Changes' : 'Create Permission' }}</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── DELETE PERMISSION MODAL ────────────────────────────────────────── --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" id="deleteModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Permission?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-key-slash fa-3x text-danger mb-3"></i>
                        <p>This is <strong>permanent</strong>. Any roles using this permission will lose it immediately.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Keep</button>
                        <button class="btn btn-danger" wire:click="deletePermission" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-trash"></i> Yes, Delete</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── BULK DELETE MODAL ────────────────────────────────────────────── --}}
    @if($showBulkDeleteModal)
        <div class="modal fade show d-block" id="bulkDeleteModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete {{ count($selectedPermissions) }} permissions?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-key-slash fa-3x text-danger mb-3"></i>
                        <p>All <strong>{{ count($selectedPermissions) }} permissions</strong> will be removed. Affected
                            roles will lose those rights immediately.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showBulkDeleteModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="bulkDelete" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-trash"></i> Delete All</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- ─── STYLES ─────────────────────────────────────────────────────────────── --}}
<style>
    .search-box {
        position: relative;
    }

    .search-box input {
        padding-left: 2.2rem;
    }

    .search-box i {
        position: absolute;
        left: .75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .form-check-input:disabled {
        opacity: .6;
    }

    .badge.border-0 {
        border: none !important;
    }

    .badge.light {
        opacity: .9;
    }
</style>
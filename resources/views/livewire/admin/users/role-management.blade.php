<div>
    {{-- ─── PAGE TITLES ─────────────────────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Roles & Permissions</h5>
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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Roles</a></li>
        </ol>
        <div class="d-flex gap-2">
            @can('manage-roles')
                <button class="btn btn-primary btn-sm" wire:click="openCreate">
                    <i class="fa-regular fa-plus me-1"></i> New Role
                </button>
            @endcan
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
                            <span class="fs-14">Total Roles</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fa-regular fa-shield-halved fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Roles In Use</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['with_users']) }}</h3>
                        </div>
                        <i class="fa-regular fa-users-gear fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Permissions</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['permissions']) }}</h3>
                        </div>
                        <i class="fa-regular fa-key fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Super Admin</span>
                            <h3 class="text-white mb-0">{{ $stats['super_admin'] ? '✓ Set' : '— None' }}</h3>
                        </div>
                        <i class="fa-regular fa-user-shield fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── FILTERS & BULK BAR ─────────────────────────────────────────── --}}
        <div class="row align-items-center mb-3">
            <div class="col-xl-8 col-lg-6">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm" placeholder="Search roles…"
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
            @if(count($selectedRoles) > 0)
                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                        <span class="badge bg-dark text-white p-2">{{ count($selectedRoles) }} selected</span>
                        @can('manage-roles')
                            <button class="btn btn-danger btn-sm" wire:click="confirmBulkDelete">
                                <i class="fa-regular fa-trash"></i> Delete
                            </button>
                        @endcan
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
                                <h4 class="heading mb-0">Roles</h4>
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
                                            Role
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
                                        <th>Permissions</th>
                                        <th wire:click="sort('users_count')" style="cursor:pointer;">
                                            Users
                                            <span class="ms-1">
                                                <i class="fa-regular fa-sort"></i>
                                            </span>
                                        </th>
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
                                    @forelse($roles as $role)
                                        @php
                                            $roleColor = $role->color ?? '#6366f1';
                                            $isProtected = in_array($role->name, $protectedRoles);
                                            $permCount = $role->permissions_count;
                                        @endphp
                                        <tr
                                            class="{{ in_array((string) $role->id, $selectedRoles) ? 'table-active' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="selectedRoles" value="{{ $role->id }}"
                                                        @if($isProtected) disabled @endif>
                                                    <label class="form-check-label"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge"
                                                        style="background: {{ $roleColor }}; width:12px; height:12px; border-radius:4px;"></span>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ ucfirst($role->name) }}
                                                            @if($isProtected)
                                                                <span class="badge bg-secondary text-white ms-1">System</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted small">
                                                            {{ $role->description ?? 'No description' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($permCount > 0)
                                                    <span class="badge badge-info light border-0">
                                                        <i class="fa-regular fa-key"></i> {{ $permCount }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No permissions</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($role->users_count > 0)
                                                    <span class="badge badge-success light border-0">{{ $role->users_count }}
                                                        user(s)</span>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $role->created_at->format('M d, Y') }}</div>
                                                    <div class="text-muted small">{{ $role->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    @can('manage-roles')
                                                        <button class="btn btn-sm btn-primary"
                                                            wire:click="viewRole({{ $role->id }})" title="View">
                                                            <i class="fa-regular fa-eye"></i>
                                                        </button>
                                                        @if(!$isProtected)
                                                            <button class="btn btn-sm btn-warning"
                                                                wire:click="openEdit({{ $role->id }})" title="Edit">
                                                                <i class="fa-regular fa-pen"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-secondary" disabled
                                                                title="System role – cannot edit">
                                                                <i class="fa-regular fa-lock"></i>
                                                            </button>
                                                        @endif
                                                        @if(!$isProtected)
                                                            <button class="btn btn-sm btn-danger"
                                                                wire:click="confirmDelete({{ $role->id }})" title="Delete">
                                                                <i class="fa-regular fa-trash"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-secondary" disabled
                                                                title="System role – cannot delete">
                                                                <i class="fa-regular fa-lock"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fa-regular fa-shield-halved fs-2 d-block mb-2 text-muted"></i>
                                                <h5>No roles found</h5>
                                                <p class="text-muted">Try adjusting your search, or create your first role.
                                                </p>
                                                @can('manage-roles')
                                                    <button class="btn btn-primary btn-sm" wire:click="openCreate">
                                                        <i class="fa-regular fa-plus"></i> Create First Role
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="row align-items-center p-3">
                            <div class="col-md-6">
                                <span>Showing {{ $roles->firstItem() ?? 0 }}–{{ $roles->lastItem() ?? 0 }} of
                                    {{ $roles->total() }} roles</span>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ $roles->links() }}
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

    {{-- ── VIEW ROLE MODAL ────────────────────────────────────────────────── --}}
    @if($showViewModal && $viewingRole)
        @php
            $vColor = $viewingRole->color ?? '#6366f1';
            $vIsProtected = in_array($viewingRole->name, $protectedRoles);
        @endphp
        <div class="modal fade show d-block" id="viewRoleModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: 3px solid {{ $vColor }};">
                        <h5 class="modal-title">
                            <i class="fa-regular fa-shield-halved" style="color: {{ $vColor }};"></i>
                            {{ ucfirst($viewingRole->name) }}
                            @if($vIsProtected)
                                <span class="badge bg-secondary text-white ms-1">System</span>
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Description</div>
                                <div>{{ $viewingRole->description ?? 'No description set' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Users Assigned</div>
                                <div>{{ $viewingRole->users_count ?? $viewingRole->users->count() }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Permissions</div>
                                <div>{{ $viewingRole->permissions->count() }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Created</div>
                                <div>{{ $viewingRole->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                        @if($viewingRole->permissions->isNotEmpty())
                            <hr>
                            <div class="fw-bold text-muted small mb-2">Granted Permissions</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($viewingRole->permissions as $perm)
                                    <span class="badge bg-success light border-0">{{ $perm->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showViewModal', false)">Close</button>
                        @can('manage-roles')
                            @if(!$vIsProtected)
                                <button class="btn btn-primary"
                                    wire:click="openEdit({{ $viewingRole->id }}); $set('showViewModal', false)">
                                    <i class="fa-regular fa-pen"></i> Edit Role
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── CREATE / EDIT ROLE MODAL ──────────────────────────────────────── --}}
    @if($showRoleModal)
        <div class="modal fade show d-block" id="roleModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditing ? 'Edit Role' : 'Create New Role' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showRoleModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Role Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="e.g. editor">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Accent Color</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color"
                                            style="width:60px; padding:2px;" wire:model="color" value="{{ $color }}">
                                        <span class="badge"
                                            style="background: {{ $color }}; width:30px; height:30px; border-radius:4px;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Description <span class="text-muted small fw-normal">—
                                    optional</span></label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                wire:model="description" placeholder="Brief description of this role…">
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">
                                Permissions
                                <span class="badge bg-secondary text-white ms-1">{{ count($selectedPermissions) }}
                                    selected</span>
                            </label>
                            @if($allPermissions->isEmpty())
                                <div class="alert alert-info">No permissions found. Please seed permissions first.</div>
                            @else
                                <div class="border rounded p-2" style="max-height:300px; overflow-y:auto;">
                                    @foreach($allPermissions as $group => $perms)
                                        <div class="mb-2">
                                            <div class="fw-bold small text-muted text-uppercase">{{ ucfirst($group) }}</div>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($perms as $perm)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="perm_{{ $perm->id }}"
                                                            wire:model="selectedPermissions" value="{{ $perm->id }}">
                                                        <label class="form-check-label small"
                                                            for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showRoleModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveRole" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular {{ $isEditing ? 'fa-save' : 'fa-plus' }}"></i>
                                {{ $isEditing ? 'Save Changes' : 'Create Role' }}</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── DELETE ROLE MODAL ────────────────────────────────────────────── --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" id="deleteModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Role?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-shield-slash fa-3x text-danger mb-3"></i>
                        <p>This is <strong>permanent</strong>. Users assigned this role will lose its permissions
                            immediately.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Keep Role</button>
                        <button class="btn btn-danger" wire:click="deleteRole" wire:loading.attr="disabled">
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
                        <h5 class="modal-title">Delete {{ count($selectedRoles) }} roles?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-shield-slash fa-3x text-danger mb-3"></i>
                        <p>All <strong>{{ count($selectedRoles) }} roles</strong> will be permanently removed. Affected
                            users will lose those permissions immediately.</p>
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

{{-- ─── STYLES – minor tweaks ─────────────────────────────────────────────── --}}
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
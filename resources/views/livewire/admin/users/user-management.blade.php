<div x-data="userHandler()" @notify.window="showToast($event.detail)" class="position-relative">

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

    {{-- ─── PAGE TITLES ─────────────────────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">User Management</h5>
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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">User Management</a></li>
        </ol>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" wire:click="openInviteModal">
                <i class="fa-regular fa-envelope me-1"></i> Invite
            </button>
            <button class="btn btn-secondary btn-sm" wire:click="openCreate">
                <i class="fa-regular fa-user-plus me-1"></i> Add User
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
                            <span class="fs-14">Total Users</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fa-regular fa-users fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Active</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['active']) }}</h3>
                        </div>
                        <i class="fa-regular fa-user-check fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Suspended</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['suspended']) }}</h3>
                        </div>
                        <i class="fa-regular fa-user-slash fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Unverified</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['unverified']) }}</h3>
                        </div>
                        <i class="fa-regular fa-envelope-circle-check fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── FILTERS & BULK BAR ─────────────────────────────────────────── --}}
        <div class="row align-items-center mb-3">
            <div class="col-xl-8 col-lg-6">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm" placeholder="Search name or email…"
                            wire:model.live.debounce.300ms="search">
                        <i class="fa-regular fa-search"></i>
                    </div>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="roleFilter">
                        <option value="">All Roles</option>
                        @foreach($availableRoles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="verifiedFilter">
                        <option value="">All Verification</option>
                        <option value="verified">Verified only</option>
                        <option value="unverified">Unverified only</option>
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="perPage">
                        <option value="10">10 / page</option>
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>
            </div>
            @if(count($selectedUsers) > 0)
                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                        <span class="badge bg-dark text-white p-2">{{ count($selectedUsers) }} selected</span>
                        <button class="btn btn-success btn-sm" wire:click="bulkActivate"><i class="fa-regular fa-check"></i>
                            Activate</button>
                        <button class="btn btn-warning btn-sm" wire:click="bulkSuspend"><i class="fa-regular fa-ban"></i>
                            Suspend</button>
                        <button class="btn btn-info btn-sm text-white" wire:click="confirmBulkVerifyResend"><i
                                class="fa-regular fa-paper-plane"></i> Resend Verification</button>
                        <button class="btn btn-danger btn-sm" wire:click="confirmBulkDelete"><i
                                class="fa-regular fa-trash"></i> Delete</button>
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
                                <h4 class="heading mb-0">Users</h4>
                                <div>
                                    <button class="btn btn-primary btn-sm" wire:click="openInviteModal">
                                        <i class="fa-regular fa-envelope"></i> Invite
                                    </button>
                                    <button class="btn btn-secondary btn-sm" wire:click="openCreate">
                                        <i class="fa-regular fa-user-plus"></i> Add User
                                    </button>
                                </div>
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
                                            User
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
                                        <th>Role</th>
                                        <th wire:click="sort('position')" style="cursor:pointer;">
                                            Position
                                            <span class="ms-1">
                                                @if($sortBy === 'position' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'position' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th wire:click="sort('status')" style="cursor:pointer;">
                                            Status
                                            <span class="ms-1">
                                                @if($sortBy === 'status' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'status' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th wire:click="sort('email_verified_at')" style="cursor:pointer;">
                                            Verified
                                            <span class="ms-1">
                                                @if($sortBy === 'email_verified_at' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'email_verified_at' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th wire:click="sort('created_at')" style="cursor:pointer;">
                                            Joined
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
                                    @forelse($users as $user)
                                        @php
                                            $userRoles = $user->roles->pluck('name')->toArray();
                                            $isSelf = $user->id === auth()->id();
                                            $isVerified = (bool) $user->email_verified_at;
                                            $avatarColors = ['primary', 'success', 'warning', 'info', 'secondary'];
                                            $avatarColor = $avatarColors[crc32($user->name) % count($avatarColors)];
                                        @endphp
                                        <tr
                                            class="{{ in_array((string) $user->id, $selectedUsers) ? 'table-active' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="selectedUsers" value="{{ $user->id }}" @if($isSelf)
                                                        disabled @endif>
                                                    <label class="form-check-label"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md me-2">
                                                        <span
                                                            class="avatar-text bg-{{ $avatarColor }}">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                                        <span class="text-muted small">{{ $user->email }}</span>
                                                        @if($isVerified)
                                                            <i class="fa-regular fa-circle-check text-success ms-1"
                                                                title="Verified"></i>
                                                        @endif
                                                        @if($user->is_featured_team)
                                                            <span class="badge bg-primary light border-0 ms-1">Featured</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(count($userRoles) > 0)
                                                    @foreach($userRoles as $role)
                                                        <span
                                                            class="badge badge-secondary light border-0">{{ ucfirst($role) }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No roles</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->position ?? '—' }}</td>
                                            <td>
                                                <button
                                                    class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }} light border-0"
                                                    style="cursor:pointer; border:none;"
                                                    wire:click="confirmToggleStatus({{ $user->id }})"
                                                    title="Click to toggle status">
                                                    {{ ucfirst($user->status ?? 'active') }}
                                                </button>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button
                                                        class="badge {{ $isVerified ? 'badge-success' : 'badge-secondary' }} light border-0"
                                                        style="cursor:pointer; border:none;"
                                                        wire:click="confirmToggleVerify({{ $user->id }})"
                                                        title="{{ $isVerified ? 'Verified – click to unverify' : 'Unverified – click to verify' }}">
                                                        <i
                                                            class="fa-regular {{ $isVerified ? 'fa-shield-check' : 'fa-shield-halved' }}"></i>
                                                        {{ $isVerified ? 'Verified' : 'Unverified' }}
                                                    </button>
                                                    @if(!$isVerified)
                                                        <button class="btn btn-sm btn-outline-info"
                                                            wire:click="resendVerification({{ $user->id }})"
                                                            title="Resend verification">
                                                            <i class="fa-regular fa-paper-plane"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $user->created_at->format('M d, Y') }}</span>
                                                    <span
                                                        class="text-muted small">{{ $user->created_at->diffForHumans() }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-sm btn-primary"
                                                        wire:click="viewUser({{ $user->id }})" title="View">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning"
                                                        wire:click="openEdit({{ $user->id }})" title="Edit">
                                                        <i class="fa-regular fa-pen"></i>
                                                    </button>
                                                    @if(!$isSelf)
                                                        <button class="btn btn-sm btn-danger"
                                                            wire:click="confirmDelete({{ $user->id }})" title="Delete">
                                                            <i class="fa-regular fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled
                                                            title="Cannot delete your own account">
                                                            <i class="fa-regular fa-lock"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <i class="fa-regular fa-users-slash fs-2 d-block mb-2 text-muted"></i>
                                                <h5>No users found</h5>
                                                <p class="text-muted">Try adjusting your search filters, or add a new user.
                                                </p>
                                                <button class="btn btn-primary btn-sm" wire:click="openCreate">
                                                    <i class="fa-regular fa-user-plus me-1"></i> Add First User
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="row align-items-center p-3">
                            <div class="col-md-6">
                                <span>Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of
                                    {{ $users->total() }} users</span>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ $users->links() }}
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

    {{-- ── VIEW USER MODAL ────────────────────────────────────────────────── --}}
    @if($showViewModal && $viewingUser)
        @php
            $viewingRoles = $viewingUser->roles->pluck('name')->toArray();
            $viewingVerified = (bool) $viewingUser->email_verified_at;
        @endphp
        <div class="modal fade show d-block" id="viewUserModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">User Details</h5>
                        <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar avatar-lg">
                                <span
                                    class="avatar-text bg-primary">{{ strtoupper(substr($viewingUser->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $viewingUser->name }}</h5>
                                <span class="text-muted">{{ $viewingUser->email }}</span>
                                @if($viewingUser->is_featured_team)
                                    <span class="badge bg-primary light border-0 ms-1">Featured</span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Roles</div>
                                <div>
                                    @if(count($viewingRoles) > 0)
                                        @foreach($viewingRoles as $role)
                                            <span class="badge badge-secondary light border-0">{{ ucfirst($role) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No roles</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Position</div>
                                <div>{{ $viewingUser->position ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Status</div>
                                <span
                                    class="badge {{ $viewingUser->status === 'active' ? 'badge-success' : 'badge-danger' }} light border-0">
                                    {{ ucfirst($viewingUser->status ?? 'active') }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Verification</div>
                                <span
                                    class="badge {{ $viewingVerified ? 'badge-success' : 'badge-secondary' }} light border-0">
                                    <i
                                        class="fa-regular {{ $viewingVerified ? 'fa-shield-check' : 'fa-shield-halved' }}"></i>
                                    {{ $viewingVerified ? 'Verified' : 'Unverified' }}
                                </span>
                                @if($viewingVerified)
                                    <div class="text-muted small">{{ $viewingUser->email_verified_at->format('M d, Y g:i A') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Joined</div>
                                <div>{{ $viewingUser->created_at->format('F d, Y g:i A') }}</div>
                            </div>
                        </div>
                        @if(count($recentActivities) > 0)
                            <hr>
                            <div class="fw-bold text-muted small mb-2">Recent Activity</div>
                            <div style="max-height:150px; overflow-y:auto;">
                                @foreach($recentActivities as $activity)
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <span class="badge bg-primary rounded-circle p-1 mt-1"
                                            style="width:8px; height:8px;"></span>
                                        <div>
                                            <div class="small">{{ $activity['description'] }}</div>
                                            <div class="text-muted small">
                                                {{ \Illuminate\Support\Carbon::parse($activity['created_at'])->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showViewModal', false)">Close</button>
                        <button class="btn btn-primary"
                            wire:click="openEdit({{ $viewingUser->id }}); $set('showViewModal', false)">
                            <i class="fa-regular fa-pen"></i> Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showUserModal)
        <div class="modal fade show d-block" id="userModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditing ? 'Edit User' : 'Add New User' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showUserModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        {{-- We'll use a div with wire:key to force re-render --}}
                        <div wire:key="user-form-{{ $isEditing ? 'edit' : 'create' }}">
                            {{-- First Name --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        wire:model="first_name" placeholder="e.g. Kwame">
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        wire:model="last_name" placeholder="e.g. Mensah">
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email" placeholder="e.g. kwame@email.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Roles --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Roles</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($assignableRoles as $roleOption)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $roleOption }}"
                                                wire:model="selectedRoles" id="role_{{ $roleOption }}">
                                            <label class="form-check-label" for="role_{{ $roleOption }}">
                                                <span class="badge badge-secondary light border-0">
                                                    <i
                                                        class="fa-regular {{ strtolower($roleOption) === 'admin' ? 'fa-shield-alt' : (strtolower($roleOption) === 'agent' ? 'fa-headset' : 'fa-user') }}"></i>
                                                    {{ $roleOption }}
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedRoles')
                                <div class="text-danger small">{{ $message }}</div> @enderror
                                <small class="text-muted d-block mt-1">Every user gets the <strong>User</strong> role
                                    automatically; the <strong>{{ $protectedRole }}</strong> role can't be assigned
                                    here.</small>
                            </div>

                            {{-- Position --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Job Title</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    wire:model="position" list="positionList" placeholder="e.g. Lead Developer">
                                <datalist id="positionList">
                                    <option value="CEO / Founder">
                                    <option value="CTO / Head of Technology">
                                    <option value="Lead Developer">
                                    <option value="Senior Developer">
                                    <option value="Junior Developer">
                                    <option value="Product Manager">
                                    <option value="Marketing Manager">
                                    <option value="Sales Director">
                                    <option value="UX/UI Designer">
                                    <option value="DevOps Engineer">
                                    <option value="Data Scientist">
                                    <option value="Project Manager">
                                    <option value="Content Creator">
                                </datalist>
                                @error('position')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Featured Team --}}
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured_team"
                                        wire:model="is_featured_team">
                                    <label class="form-check-label fw-bold small" for="is_featured_team">Featured Team
                                        Member</label>
                                    <div class="text-muted small">Display on the public team page.</div>
                                </div>
                            </div>

                            @if($isEditing)
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Password <span class="text-muted small fw-normal">—
                                            leave blank to keep current</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        wire:model="password" placeholder="Leave blank to keep current">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endif

                            {{-- Validation errors summary --}}
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showUserModal', false)">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="saveUser" wire:loading.attr="disabled">
                            <span wire:loading.remove><i
                                    class="fa-regular {{ $isEditing ? 'fa-save' : 'fa-user-plus' }}"></i>
                                {{ $isEditing ? 'Save Changes' : 'Create User' }}</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- ── DELETE USER MODAL ────────────────────────────────────────────── --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" id="deleteModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete User?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-trash-alt fa-3x text-danger mb-3"></i>
                        <p>This action is <strong>permanent</strong> and cannot be undone. All associated data will be
                            removed.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Keep User</button>
                        <button class="btn btn-danger" wire:click="deleteUser" wire:loading.attr="disabled">
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
                        <h5 class="modal-title">Delete {{ count($selectedUsers) }} users?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-users-slash fa-3x text-danger mb-3"></i>
                        <p>You're about to permanently delete <strong>{{ count($selectedUsers) }} accounts</strong>. This
                            cannot be reversed.</p>
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

    {{-- ── TOGGLE STATUS MODAL ──────────────────────────────────────────── --}}
    @if($showToggleStatusModal)
        <div class="modal fade show d-block" id="toggleStatusModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change User Status?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showToggleStatusModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <p>
                            You are about to <strong>
                                @php
                                    $targetUser = App\Models\User::find($toggleUserId);
                                @endphp
                                {{ $targetUser && $targetUser->status === 'active' ? 'suspend' : 'activate' }}
                            </strong> this user's account. This action can be reversed later.
                        </p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showToggleStatusModal', false)">Cancel</button>
                        <button class="btn btn-warning" wire:click="toggleStatusConfirmed" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-check"></i> Confirm</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Updating…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── TOGGLE VERIFICATION MODAL ─────────────────────────────────────── --}}
    @if($showToggleVerifyModal)
        <div class="modal fade show d-block" id="toggleVerifyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Verification Status?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showToggleVerifyModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-shield-halved fa-3x text-info mb-3"></i>
                        <p>
                            You are about to manually mark this user's email as <strong>
                                @php
                                    $targetVerifyUser = App\Models\User::find($verifyUserId);
                                @endphp
                                {{ $targetVerifyUser && $targetVerifyUser->email_verified_at ? 'unverified' : 'verified' }}
                            </strong>. This overrides the normal email verification flow.
                        </p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showToggleVerifyModal', false)">Cancel</button>
                        <button class="btn btn-info text-white" wire:click="toggleVerifyConfirmed"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-check"></i> Confirm</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Updating…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── BULK VERIFY MODAL ─────────────────────────────────────────────── --}}
    @if($showBulkVerifyModal)
        <div class="modal fade show d-block" id="bulkVerifyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Resend verification emails?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkVerifyModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-paper-plane fa-3x text-info mb-3"></i>
                        <p>
                            This will send a new verification link to every <strong>unverified</strong> user among your
                            {{ count($selectedUsers) }} selected accounts. Already-verified users will be skipped.
                        </p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showBulkVerifyModal', false)">Cancel</button>
                        <button class="btn btn-info text-white" wire:click="bulkResendVerification"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-paper-plane"></i> Send Emails</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Sending…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── INVITE MODAL ──────────────────────────────────────────────────── --}}
    @if($showInviteModal)
        <div class="modal fade show d-block" id="inviteModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Invite User</h5>
                        <button type="button" class="btn-close" wire:click="$set('showInviteModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email Address</label>
                            <input type="email" class="form-control @error('inviteEmail') is-invalid @enderror"
                                wire:model="inviteEmail" placeholder="e.g. friend@email.com">
                            @error('inviteEmail')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Role</label>
                            <select class="default-select style-1 form-control" wire:model="inviteRoleId">
                                <option value="">— Default (User) —</option>
                                @foreach($assignableRoles as $role)
                                    <option value="{{ \Spatie\Permission\Models\Role::where('name', $role)->first()->id }}">
                                        {{ $role }}
                                    </option>
                                @endforeach
                            </select>
                            @error('inviteRoleId')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Job Title</label>
                            <input type="text" class="form-control @error('invitePosition') is-invalid @enderror"
                                wire:model="invitePosition" list="invitePositionList" placeholder="e.g. Lead Developer">
                            <datalist id="invitePositionList">
                                <option value="CEO / Founder">
                                <option value="CTO / Head of Technology">
                                <option value="Lead Developer">
                                <option value="Senior Developer">
                                <option value="Junior Developer">
                                <option value="Product Manager">
                                <option value="Marketing Manager">
                                <option value="Sales Director">
                                <option value="UX/UI Designer">
                                <option value="DevOps Engineer">
                                <option value="Data Scientist">
                                <option value="Project Manager">
                                <option value="Content Creator">
                            </datalist>
                            @error('invitePosition')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Optional — pre-fills their job title once they accept.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Expiry (days)</label>
                            <input type="number" class="form-control @error('inviteExpiryDays') is-invalid @enderror"
                                wire:model="inviteExpiryDays" min="1" max="30">
                            @error('inviteExpiryDays')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Link expires after this many days.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showInviteModal', false)">Cancel</button>
                        <button class="btn btn-success" wire:click="sendInvitation" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-paper-plane"></i> Send Invitation</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Sending…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- ─── STYLES ─────────────────────────────────────────────────────────────── --}}
<style>
    [x-cloak] {
        display: none !important;
    }

    .avatar-text {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-weight: 700;
        color: #fff;
    }

    .avatar .avatar-text {
        border-radius: 50%;
    }

    .avatar-md .avatar-text {
        width: 38px;
        height: 38px;
        font-size: 14px;
    }

    .avatar-lg .avatar-text {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }

    .bg-primary-light {
        background-color: rgba(99, 102, 241, 0.12);
        color: #6366f1;
    }

    .bg-success-light {
        background-color: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .bg-warning-light {
        background-color: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .bg-info-light {
        background-color: rgba(56, 189, 248, 0.14);
        color: #0284c7;
    }

    .bg-secondary-light {
        background-color: #f1f5f9;
        color: #64748b;
    }

    .badge.border-0 {
        border: none;
    }

    .badge.light {
        opacity: 0.9;
    }

    .form-check .form-check-input:disabled {
        opacity: 0.6;
    }

    .badge[style*="cursor:pointer"]:hover {
        opacity: 0.8;
        transform: scale(0.97);
        transition: all 0.1s ease;
    }
</style>

{{-- ─── Alpine Handler ────────────────────────────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userHandler', () => ({
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

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            document.querySelectorAll('[x-data]').forEach((el) => {
                if (el.__x && 'toastVisible' in el.__x.$data) {
                    el.__x.$data.toastVisible = false;
                    clearTimeout(el.__x.$data.toastTimeout);
                }
            });
        }
    });
</script>
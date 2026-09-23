<div class="position-relative">

    {{-- PAGE TITLES --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Departments</h5>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Departments</a></li>
        </ol>
        <div class="d-flex gap-2">
            @can('viewAny', App\Models\Department::class)
                <button class="btn btn-outline-primary btn-sm" wire:click="export" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="export"><i class="fa-solid fa-file-export me-1"></i> Export
                        CSV</span>
                    <span wire:loading wire:target="export"><i class="fa-solid fa-spinner fa-spin me-1"></i>
                        Exporting…</span>
                </button>
            @endcan
            @can('create', App\Models\Department::class)
                <button class="btn btn-primary btn-sm" wire:click="openCreate">
                    <i class="fa-solid fa-plus me-1"></i> New Department
                </button>
            @endcan
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
                <i class="fa-solid fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="container-fluid">
        {{-- STATS --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Total Departments</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fa-solid fa-building fs-24"></i>
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
                        <i class="fa-solid fa-circle-check fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Total Staff</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total_staff']) }}</h3>
                        </div>
                        <i class="fa-solid fa-users fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Open Vacancies</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['open_vacancies']) }}</h3>
                        </div>
                        <i class="fa-solid fa-briefcase fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="row align-items-center mb-3">
            <div class="col-xl-8 col-lg-6">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm"
                            placeholder="Search name, code, location…" wire:model.live.debounce.300ms="search">
                        <i class="fa-solid fa-search"></i>
                    </div>
                    <select class="form-control form-control-sm w-auto" wire:model.live="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                    <select class="form-control form-control-sm w-auto" wire:model.live="parentFilter">
                        <option value="">All Parents</option>
                        @foreach($parentOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-control form-control-sm w-auto" wire:model.live="specialFilter">
                        <option value="">Any Department</option>
                        <option value="no_head">No head assigned</option>
                        <option value="has_head">Has head</option>
                        <option value="customer_facing">Customer-facing</option>
                        <option value="under_staffed">Under-staffed</option>
                    </select>
                    <select class="form-control form-control-sm w-auto" wire:model.live="perPage">
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>
            </div>
            @if(count($selectedDepartments) > 0)
                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                        <span class="badge bg-dark text-white p-2">{{ count($selectedDepartments) }} selected</span>
                        @can('update', App\Models\Department::class)
                            <button class="btn btn-success btn-sm" wire:click="bulkActivate">
                                <i class="fa-solid fa-circle-check"></i> Activate
                            </button>
                            <button class="btn btn-dark btn-sm" wire:click="bulkDeactivate">
                                <i class="fa-solid fa-box-archive"></i> Archive
                            </button>
                        @endcan
                        @can('delete', App\Models\Department::class)
                            <button class="btn btn-danger btn-sm" wire:click="confirmBulkDelete">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        @endcan
                    </div>
                </div>
            @endif
        </div>

        {{-- TABLE --}}
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive active-projects style-1">
                            <div class="tbl-caption">
                                <h4 class="heading mb-0">All Departments</h4>
                            </div>
                            <table class="table dept-table">
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
                                            Department
                                            <span class="ms-1">
                                                @if($sortBy === 'name' && $sortDir === 'asc') <i
                                                    class="fa-solid fa-sort-up"></i>
                                                @elseif($sortBy === 'name' && $sortDir === 'desc') <i
                                                    class="fa-solid fa-sort-down"></i>
                                                @else <i class="fa-solid fa-sort"></i> @endif
                                            </span>
                                        </th>
                                        <th>Head</th>
                                        <th class="text-center" style="width:150px;">Team</th>
                                        <th class="text-center" style="width:100px;">Openings</th>
                                        <th wire:click="sort('budget')" style="cursor:pointer;">
                                            Budget
                                            <span class="ms-1">
                                                @if($sortBy === 'budget' && $sortDir === 'asc') <i
                                                    class="fa-solid fa-sort-up"></i>
                                                @elseif($sortBy === 'budget' && $sortDir === 'desc') <i
                                                    class="fa-solid fa-sort-down"></i>
                                                @else <i class="fa-solid fa-sort"></i> @endif
                                            </span>
                                        </th>
                                        <th>Status</th>
                                        <th class="text-center" style="min-width:200px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departments as $dept)
                                        @php
                                            $headcountProgress = null;
                                            if ($dept->headcount_target && $dept->headcount_target > 0) {
                                                $headcountProgress = min(100, round(($dept->employees_count / $dept->headcount_target) * 100));
                                            }
                                        @endphp
                                        <tr
                                            class="{{ in_array((string) $dept->id, $selectedDepartments) ? 'table-active' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="selectedDepartments" value="{{ $dept->id }}">
                                                    <label class="form-check-label"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="dept-avatar"
                                                        style="background: {{ $dept->effective_color }};">
                                                        <i class="{{ $dept->effective_icon }}"></i>
                                                    </div>
                                                    <div class="min-width-0">
                                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                                            <span class="fw-semibold">{{ $dept->name }}</span>
                                                            @if($dept->code)
                                                                <span class="dept-code">{{ $dept->code }}</span>
                                                            @endif
                                                            @if($dept->is_customer_facing)
                                                                <span class="badge bg-info light border-0"
                                                                    title="Customer-facing">CF</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted small text-truncate">
                                                            @if($dept->parent)
                                                                <i
                                                                    class="fa-solid fa-sitemap me-1"></i>{{ $dept->parent->name }}
                                                                ›
                                                            @endif
                                                            @if($dept->location)
                                                                <i
                                                                    class="fa-solid fa-location-dot ms-1 me-1"></i>{{ $dept->location }}
                                                            @else
                                                                {{ \Illuminate\Support\Str::limit($dept->description, 50) ?: 'No description' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($dept->head)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $dept->head->avatar_url }}" class="avatar"
                                                            style="width:28px;height:28px;border-radius:50%;object-fit:cover;"
                                                            alt="">
                                                        <div class="min-width-0">
                                                            <div class="small fw-semibold text-truncate">{{ $dept->head->name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted small"><i
                                                            class="fa-solid fa-user-slash me-1"></i>Unassigned</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <span class="small fw-semibold">
                                                        {{ $dept->employees_count }}@if($dept->headcount_target) /
                                                        {{ $dept->headcount_target }}@endif
                                                    </span>
                                                    @if($headcountProgress !== null)
                                                        <div class="progress w-100" style="max-width:80px;height:4px;">
                                                            <div class="progress-bar {{ $headcountProgress >= 80 ? 'bg-success' : ($headcountProgress >= 40 ? 'bg-warning' : 'bg-danger') }}"
                                                                style="width: {{ $headcountProgress }}%"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($dept->vacancies_count > 0)
                                                    <a href="{{ route('admin.applications') }}"
                                                        class="badge bg-primary text-white text-decoration-none">
                                                        {{ $dept->vacancies_count }}
                                                    </a>
                                                @else
                                                    <span class="text-muted small">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($dept->budget)
                                                    <span class="small">${{ number_format((float) $dept->budget, 0) }}</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('update', $dept)
                                                    <button
                                                        class="badge {{ $dept->is_active ? 'badge-success' : 'badge-secondary' }} light border-0"
                                                        style="cursor:pointer;border:none;"
                                                        wire:click="toggleActive({{ $dept->id }})"
                                                        title="Click to toggle status">
                                                        {{ $dept->is_active ? 'Active' : 'Archived' }}
                                                    </button>
                                                @else
                                                    <span
                                                        class="badge {{ $dept->is_active ? 'badge-success' : 'badge-secondary' }} light border-0">
                                                        {{ $dept->is_active ? 'Active' : 'Archived' }}
                                                    </span>
                                                @endcan
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    @can('view', $dept)
                                                        <button type="button" class="btn btn-sm btn-primary action-btn"
                                                            wire:click="viewDepartment({{ $dept->id }})" title="View">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </button>
                                                    @endcan
                                                    @can('update', $dept)
                                                        <button type="button" class="btn btn-sm btn-warning action-btn"
                                                            wire:click="openEdit({{ $dept->id }})" title="Edit">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete', $dept)
                                                        <button type="button" class="btn btn-sm btn-danger action-btn"
                                                            wire:click="confirmDelete({{ $dept->id }})" title="Delete">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <i class="fa-solid fa-building fs-2 d-block mb-2 text-muted"></i>
                                                <h5>No departments found</h5>
                                                <p class="text-muted">Try adjusting filters, or create the first department.
                                                </p>
                                                @can('create', App\Models\Department::class)
                                                    <button class="btn btn-primary btn-sm" wire:click="openCreate">
                                                        <i class="fa-solid fa-plus me-1"></i> New Department
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
                                <span>Showing {{ $departments->firstItem() ?? 0 }}–{{ $departments->lastItem() ?? 0 }}
                                    of {{ $departments->total() }}</span>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ $departments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM MODAL --}}
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $selectedDepartmentId ? 'Edit Department' : 'New Department' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                    </div>
                    <div class="modal-body">

                        {{-- IDENTITY --}}
                        <h6 class="border-bottom pb-2 fw-bold text-primary">
                            <i class="fa-solid fa-fingerprint me-2"></i>Identity
                        </h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="e.g. Engineering">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Code</label>
                                <input type="text" class="form-control text-uppercase @error('code') is-invalid @enderror"
                                    wire:model="code" placeholder="Auto-generated if blank" maxlength="20">
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" rows="2"
                                    wire:model="description" placeholder="What this department does"></textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Color</label>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @foreach($colorPresets as $preset)
                                        <button type="button" class="dept-swatch {{ $color === $preset ? 'is-selected' : '' }}"
                                            style="background: {{ $preset }};" wire:click="$set('color', '{{ $preset }}')"
                                            title="{{ $preset }}"></button>
                                    @endforeach
                                    <input type="text" class="form-control form-control-sm" style="width:110px;"
                                        wire:model="color" placeholder="#hex">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Icon</label>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    @foreach($iconPresets as $preset)
                                        <button type="button"
                                            class="dept-icon-pick {{ $icon === $preset ? 'is-selected' : '' }}"
                                            wire:click="$set('icon', '{{ $preset }}')" title="{{ $preset }}">
                                            <i class="{{ $preset }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                                <input type="text" class="form-control form-control-sm mt-2" wire:model="icon"
                                    placeholder="Custom: fa-solid fa-…">
                            </div>
                        </div>

                        {{-- HIERARCHY --}}
                        <h6 class="border-bottom pb-2 fw-bold text-primary mt-4">
                            <i class="fa-solid fa-sitemap me-2"></i>Hierarchy & Leadership
                        </h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Parent Department</label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" wire:model="parent_id">
                                    <option value="">— None (top-level) —</option>
                                    @foreach($parentOptions as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Department Head</label>
                                <select class="form-select @error('head_id') is-invalid @enderror" wire:model="head_id">
                                    <option value="">— Unassigned —</option>
                                    @foreach($headOptions as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                                @error('head_id')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Display Order</label>
                                <input type="number" class="form-control @error('display_order') is-invalid @enderror"
                                    wire:model="display_order" min="0" max="9999">
                                @error('display_order')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- CONTACT --}}
                        <h6 class="border-bottom pb-2 fw-bold text-primary mt-4">
                            <i class="fa-solid fa-address-book me-2"></i>Contact
                        </h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email" placeholder="department@company.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- PHONE PICKER --}}
                            <div class="col-md-4" wire:key="dept-phone-field">
                                <label class="form-label fw-bold small">Phone</label>
                                <div class="phone-wrapper position-relative">
                                    <div class="input-group">
                                        <button type="button" wire:click="togglePhoneCountryDropdown"
                                            class="btn btn-outline-secondary d-flex align-items-center gap-2 phone-country-btn"
                                            style="border-radius: 0.5rem 0 0 0.5rem; border-right: none; background: #f8fafc; padding: 0.45rem 0.65rem; white-space: nowrap;">
                                            <img src="{{ asset('flags/' . $phone_flag) }}" class="rounded-1"
                                                style="width: 22px; height: 15px; object-fit: cover; flex-shrink: 0;">
                                            <span class="fw-semibold text-dark"
                                                style="font-size: 0.82rem;">{{ $phone_code }}</span>
                                            <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.6rem;"></i>
                                        </button>
                                        <input type="tel" inputmode="numeric" wire:model.defer="phone"
                                            placeholder="{{ $phone_example ? 'e.g. ' . $phone_example : 'Phone number' }}"
                                            maxlength="{{ $phone_countryInfo['maxLength'] ?? 15 }}"
                                            class="form-control phone-number-input @error('phone') is-invalid @enderror"
                                            style="border-radius: 0 0.5rem 0.5rem 0; font-size: 0.9rem; padding: 0.45rem 0.75rem;"
                                            x-data
                                            x-on:input="let v = $el.value.replace(/[^0-9]/g, ''); let max = {{ $phone_countryInfo['maxLength'] ?? 15 }}; if (v.length > max) v = v.substring(0, max); $el.value = v; $wire.setPhone(v);">
                                    </div>
                                    @if($phone_showDropdown)
                                        <div class="dropdown-menu show p-0 mt-1 shadow-lg position-absolute phone-country-dropdown"
                                            data-lw-managed="true" x-data x-on:click.away="$wire.closePhoneCountryDropdown()">
                                            <div class="sticky-top bg-white p-2 border-bottom">
                                                <div class="position-relative">
                                                    <i class="fa-solid fa-magnifying-glass position-absolute text-muted"
                                                        style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;"></i>
                                                    <input type="text" wire:model.live.debounce.200ms="phone_search"
                                                        placeholder="Search country…" class="form-control form-control-sm"
                                                        style="border-radius: 0.4rem; font-size: 0.8rem; padding: 0.3rem 0.5rem 0.3rem 1.8rem;">
                                                </div>
                                            </div>
                                            <div class="p-1">
                                                @forelse($phone_filteredCountries as $country)
                                                    <button type="button"
                                                        wire:click="selectPhoneCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                                        class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded {{ $phone_code === $country['code'] ? 'active' : '' }}"
                                                        style="font-size: 0.8rem;">
                                                        <img src="{{ asset('flags/' . $country['flag']) }}" class="rounded-1"
                                                            style="width: 20px; height: 14px; object-fit: cover; flex-shrink: 0;">
                                                        <span
                                                            class="flex-grow-1 text-truncate text-start">{{ $country['name'] }}</span>
                                                        <span class="text-muted flex-shrink-0"
                                                            style="font-size: 0.7rem;">{{ $country['code'] }}</span>
                                                    </button>
                                                @empty
                                                    <div class="px-2 py-2 text-muted text-center" style="font-size: 0.8rem;">
                                                        No countries found</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                    @if($phone)
                                        <div class="form-text text-primary" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                            <i class="fa-solid fa-info-circle me-1"></i> Will be saved as:
                                            <strong>{{ $phone_code }}{{ $phone }}</strong>
                                        </div>
                                    @endif
                                    @error('phone')
                                        <div class="invalid-feedback d-block" style="font-size: 0.75rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Location / Office</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                    wire:model="location" placeholder="e.g. Accra HQ, Floor 3">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- OPERATIONS --}}
                        <h6 class="border-bottom pb-2 fw-bold text-primary mt-4">
                            <i class="fa-solid fa-sliders me-2"></i>Operations
                        </h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Annual Budget</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('budget') is-invalid @enderror" wire:model="budget"
                                    placeholder="0.00">
                                @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Headcount Target</label>
                                <input type="number" min="0" max="10000"
                                    class="form-control @error('headcount_target') is-invalid @enderror"
                                    wire:model="headcount_target" placeholder="e.g. 10">
                                @error('headcount_target')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Founded</label>
                                <input type="date" class="form-control @error('founded_at') is-invalid @enderror"
                                    wire:model="founded_at" max="{{ now()->format('Y-m-d') }}">
                                @error('founded_at')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small d-block">Flags</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" wire:model="is_active">
                                    <label class="form-check-label small" for="is_active">Active</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_customer_facing"
                                        wire:model="is_customer_facing">
                                    <label class="form-check-label small" for="is_customer_facing">Customer-facing</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Internal Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" rows="2"
                                    wire:model="notes" placeholder="Only visible to admins"></textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger mt-2">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>@endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showFormModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveDepartment" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-solid fa-save me-1"></i>
                                {{ $selectedDepartmentId ? 'Save Changes' : 'Create Department' }}</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin me-1"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- VIEW MODAL --}}
    @if($showViewModal && $viewingDepartment)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header" style="background: {{ $viewingDepartment->effective_color }}; color: #fff;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dept-avatar" style="background: rgba(255,255,255,.2);">
                                <i class="{{ $viewingDepartment->effective_icon }}"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-white mb-0">{{ $viewingDepartment->name }}</h5>
                                <small class="text-white" style="opacity: .85;">
                                    @if($viewingDepartment->code) {{ $viewingDepartment->code }} · @endif
                                    {{ $viewingDepartment->full_path }}
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white"
                            wire:click="$set('showViewModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Department Head</div>
                                <div>
                                    @if($viewingDepartment->head)
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $viewingDepartment->head->avatar_url }}" class="avatar"
                                                style="width:28px;height:28px;border-radius:50%;" alt="">
                                            <span>{{ $viewingDepartment->head->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Parent</div>
                                <div>{{ $viewingDepartment->parent?->name ?? 'Top-level department' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Email</div>
                                <div>{{ $viewingDepartment->email ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Phone</div>
                                <div>{{ $viewingDepartment->phone ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Location</div>
                                <div>{{ $viewingDepartment->location ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Founded</div>
                                <div>{{ $viewingDepartment->founded_at?->format('M d, Y') ?? '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Employees</div>
                                <div>{{ $viewingDepartment->employees_count ?? 0 }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Vacancies</div>
                                <div>{{ $viewingDepartment->vacancies_count ?? 0 }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Headcount Target</div>
                                <div>{{ $viewingDepartment->headcount_target ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Budget</div>
                                <div>
                                    @if($viewingDepartment->budget)
                                        ${{ number_format((float) $viewingDepartment->budget, 2) }}
                                    @else
                                        Not set
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-muted small">Flags</div>
                                <div class="d-flex gap-1">
                                    <span
                                        class="badge {{ $viewingDepartment->is_active ? 'badge-success' : 'badge-secondary' }} light border-0">
                                        {{ $viewingDepartment->is_active ? 'Active' : 'Archived' }}
                                    </span>
                                    @if($viewingDepartment->is_customer_facing)
                                        <span class="badge badge-info light border-0">Customer-facing</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($viewingDepartment->description)
                            <hr>
                            <div class="fw-bold text-muted small mb-1">Description</div>
                            <p class="mb-0">{{ $viewingDepartment->description }}</p>
                        @endif

                        @if($viewingDepartment->children->count() > 0)
                            <hr>
                            <div class="fw-bold text-muted small mb-2">Sub-departments
                                ({{ $viewingDepartment->children->count() }})</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($viewingDepartment->children as $child)
                                    <span class="badge" style="background: {{ $child->effective_color }}; color: #fff;">
                                        <i class="{{ $child->effective_icon }} me-1"></i>{{ $child->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($viewingDepartment->notes)
                            <hr>
                            <div class="fw-bold text-muted small mb-1">Internal notes</div>
                            <p class="mb-0" style="white-space: pre-line;">{{ $viewingDepartment->notes }}</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showViewModal', false)">Close</button>
                        @can('update', $viewingDepartment)
                            <button class="btn btn-primary"
                                wire:click="openEdit({{ $viewingDepartment->id }}); $set('showViewModal', false)">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DELETE MODAL --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.6);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>Delete department?
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:72px;height:72px;border-radius:50%;background:rgba(239,68,68,.1);">
                            <i class="fa-solid fa-trash-can fs-2 text-danger"></i>
                        </div>
                        <h5 class="mb-2">This cannot be undone.</h5>
                        <p class="text-muted mb-0">The department will be removed permanently.</p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteDepartment" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteDepartment"><i class="fa-solid fa-trash"></i> Yes,
                                delete</span>
                            <span wire:loading wire:target="deleteDepartment"><i class="fa-solid fa-spinner fa-spin"></i>
                                Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- BULK DELETE MODAL --}}
    @if($showBulkDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.6);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>Delete {{ count($selectedDepartments) }}
                            department(s)?
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fa-solid fa-trash-can fs-2 text-danger mb-3 d-block"></i>
                        <p class="text-muted mb-0">This permanently removes them. Departments still in use will be skipped.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showBulkDeleteModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="bulkDelete" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="bulkDelete"><i class="fa-solid fa-trash"></i> Delete
                                all</span>
                            <span wire:loading wire:target="bulkDelete"><i class="fa-solid fa-spinner fa-spin"></i>
                                Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<style>
    .dept-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25), 0 2px 6px rgba(0, 0, 0, .08);
    }

    .dept-code {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .05em;
        background: #eef2f6;
        color: #475467;
        padding: 1px 6px;
        border-radius: 4px;
    }

    .dept-swatch {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #e2e8f0;
        cursor: pointer;
        transition: transform .15s ease;
    }

    .dept-swatch:hover {
        transform: scale(1.1);
    }

    .dept-swatch.is-selected {
        box-shadow: 0 0 0 2px #2f6fed;
        transform: scale(1.1);
    }

    .dept-icon-pick {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #e7e9f2;
        background: #f8fafc;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .15s ease;
    }

    .dept-icon-pick:hover {
        background: #fff;
        color: #2f6fed;
        border-color: #2f6fed;
    }

    .dept-icon-pick.is-selected {
        background: #2f6fed;
        color: #fff;
        border-color: #2f6fed;
    }

    .min-width-0 {
        min-width: 0;
    }

    .dept-table tbody td {
        vertical-align: middle;
    }

    .dept-table th {
        vertical-align: middle;
        white-space: nowrap;
    }

    .dept-table .action-btn {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 12px;
    }

    /* ─── Phone picker ─── */
    .phone-country-dropdown {
        border-radius: 0.6rem;
        border: 1px solid #e2e8f0;
        z-index: 1050;
        overflow-y: auto;
    }

    .phone-country-dropdown .dropdown-item:hover {
        background-color: #f1f5f9;
    }

    .phone-country-dropdown .dropdown-item.active {
        background-color: #e0e7ff;
        color: #1e293b;
    }

    @media (max-width: 575.98px) {
        .phone-country-btn span {
            font-size: 0.75rem;
        }

        .phone-country-btn img {
            width: 18px;
            height: 12px;
        }

        .phone-number-input {
            font-size: 0.85rem;
            padding: 0.4rem 0.5rem;
        }

        .phone-country-dropdown {
            width: calc(100vw - 2.5rem) !important;
            left: 0 !important;
            right: auto !important;
            max-height: 200px;
        }
    }

    @media (min-width: 576px) {
        .phone-country-dropdown {
            width: 280px;
            max-width: 280px;
            max-height: 240px;
        }
    }
</style>
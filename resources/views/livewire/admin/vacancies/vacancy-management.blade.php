<div class="position-relative">

    {{-- PAGE TITLES --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Vacancy Management</h5>
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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Vacancy Management</a></li>
        </ol>
        <div class="d-flex gap-2">
            @can('viewAny', App\Models\Vacancy::class)
                <button class="btn btn-outline-primary btn-sm" wire:click="export" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="export">
                        <i class="fa-regular fa-file-export me-1"></i> Export CSV
                    </span>
                    <span wire:loading wire:target="export">
                        <i class="fa-regular fa-spinner fa-spin me-1"></i> Exporting…
                    </span>
                </button>
            @endcan
            @can('create', App\Models\Vacancy::class)
                <button class="btn btn-primary btn-sm" wire:click="openCreate">
                    <i class="fa-regular fa-plus me-1"></i> Post Vacancy
                </button>
            @endcan
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
                <i class="fa-regular fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    {{-- STATS ROW --}}
    <div class="container-fluid">
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Total Vacancies</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fa-regular fa-briefcase fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Published</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['published']) }}</h3>
                        </div>
                        <i class="fa-regular fa-circle-check fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-secondary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Draft</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['draft']) }}</h3>
                        </div>
                        <i class="fa-regular fa-file-pen fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Closed / Archived</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['closed'] + $stats['archived']) }}</h3>
                        </div>
                        <i class="fa-regular fa-box-archive fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- FEATURED SUMMARY STRIP --}}
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card border-0"
                    style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fde68a !important;">
                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width:44px; height:44px; background: linear-gradient(135deg, #f59e0b, #d97706); flex-shrink:0;">
                                <i class="fa-solid fa-star text-white fs-20"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="color:#92400e;">Featured Vacancies</h6>
                                <span class="small" style="color:#b45309;">These roles are pinned to the top of the
                                    public careers page.</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge border-0 px-3 py-2 fs-14" style="background:#f59e0b; color:#fff;">
                                {{ $stats['featured'] }} featured
                            </span>
                            <span class="badge border-0 px-3 py-2 fs-14 bg-info text-white">
                                {{ number_format($stats['total_views']) }} total views
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS & BULK BAR --}}
        <div class="row align-items-center mb-3">
            <div class="col-xl-8 col-lg-6">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm"
                            placeholder="Search title, summary, location…" wire:model.live.debounce.300ms="search">
                        <i class="fa-regular fa-search"></i>
                    </div>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="statusFilter">
                        <option value="">All Status</option>
                        @foreach($statuses as $statusCase)
                            <option value="{{ $statusCase->value }}">{{ $statusCase->label() }}</option>
                        @endforeach
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="departmentFilter">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="employmentTypeFilter">
                        <option value="">All Types</option>
                        @foreach($employmentTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="workplaceTypeFilter">
                        <option value="">All Workplaces</option>
                        @foreach($workplaceTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="featuredFilter">
                        <option value="">All Vacancies</option>
                        <option value="featured">Featured only</option>
                        <option value="not_featured">Not featured</option>
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
            @if(count($selectedVacancies) > 0)
                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                        <span class="badge bg-dark text-white p-2">{{ count($selectedVacancies) }} selected</span>
                        @can('update', App\Models\Vacancy::class)
                            <button class="btn btn-success btn-sm" wire:click="bulkPublish">
                                <i class="fa-regular fa-circle-check"></i> Publish
                            </button>
                            <button class="btn btn-warning btn-sm" wire:click="bulkClose">
                                <i class="fa-regular fa-lock"></i> Close
                            </button>
                            <button class="btn btn-dark btn-sm" wire:click="confirmBulkArchive">
                                <i class="fa-regular fa-box-archive"></i> Archive
                            </button>
                        @endcan
                        @can('delete', App\Models\Vacancy::class)
                            <button class="btn btn-danger btn-sm" wire:click="confirmBulkDelete">
                                <i class="fa-regular fa-trash"></i> Delete
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
                                <h4 class="heading mb-0">Vacancies</h4>
                                <div>
                                    @can('create', App\Models\Vacancy::class)
                                        <button class="btn btn-primary btn-sm" wire:click="openCreate">
                                            <i class="fa-regular fa-plus"></i> Post Vacancy
                                        </button>
                                    @endcan
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
                                        <th wire:click="sort('title')" style="cursor:pointer;">
                                            Role
                                            <span class="ms-1">
                                                @if($sortBy === 'title' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'title' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th>Department</th>
                                        <th>Type / Workplace</th>
                                        <th class="text-center" style="width:90px;" title="Featured on careers page">
                                            <i class="fa-solid fa-star text-warning"></i> Featured
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
                                        <th wire:click="sort('views_count')" style="cursor:pointer;">
                                            Views
                                            <span class="ms-1">
                                                @if($sortBy === 'views_count' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'views_count' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th wire:click="sort('applications_count')" style="cursor:pointer;">
                                            Applications
                                            <span class="ms-1">
                                                @if($sortBy === 'applications_count' && $sortDir === 'asc')
                                                    <i class="fa-regular fa-sort-up"></i>
                                                @elseif($sortBy === 'applications_count' && $sortDir === 'desc')
                                                    <i class="fa-regular fa-sort-down"></i>
                                                @else
                                                    <i class="fa-regular fa-sort"></i>
                                                @endif
                                            </span>
                                        </th>
                                        <th wire:click="sort('created_at')" style="cursor:pointer;">
                                            Posted
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
                                    @forelse($vacancies as $vacancy)
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'published' => 'success',
                                                'closed' => 'warning',
                                                'archived' => 'dark',
                                            ];
                                            $statusColor = $statusColors[$vacancy->status->value] ?? 'secondary';
                                            $appCount = $vacancy->applications_count ?? 0;
                                        @endphp
                                        <tr
                                            class="{{ in_array((string) $vacancy->id, $selectedVacancies) ? 'table-active' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="selectedVacancies" value="{{ $vacancy->id }}">
                                                    <label class="form-check-label"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-0">{{ $vacancy->title }}</h6>
                                                    <span
                                                        class="text-muted small">{{ $vacancy->location ?: 'No location set' }}</span>
                                                    @if($vacancy->salary_range)
                                                        <span class="text-muted small">· {{ $vacancy->salary_range }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $vacancy->department?->name ?? '—' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-secondary light border-0">{{ $vacancy->employment_type->label() }}</span>
                                                <span
                                                    class="badge badge-secondary light border-0">{{ $vacancy->workplace_type->label() }}</span>
                                            </td>
                                            <td class="text-center">
                                                @can('update', $vacancy)
                                                    <button
                                                        class="btn btn-sm {{ $vacancy->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                        style="border-radius: 50%; width: 34px; height: 34px; padding: 0;"
                                                        wire:click="toggleFeatured({{ $vacancy->id }})"
                                                        title="{{ $vacancy->is_featured ? 'Featured — click to remove' : 'Click to feature' }}">
                                                        <i
                                                            class="fa-{{ $vacancy->is_featured ? 'solid' : 'regular' }} fa-star"></i>
                                                    </button>
                                                @endcan
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $statusColor }} light border-0">
                                                    {{ $vacancy->status->label() }}
                                                </span>
                                                @if($vacancy->closing_date)
                                                    <div class="text-muted small">Closes
                                                        {{ $vacancy->closing_date->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ number_format($vacancy->views_count) }}</td>
                                            <td>
                                                @if($appCount > 0)
                                                    <a href="{{ route('admin.applications') }}?vacancyFilter={{ $vacancy->id }}"
                                                        class="badge bg-primary text-white text-decoration-none"
                                                        title="View applications for this role">
                                                        {{ number_format($appCount) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted small">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $vacancy->created_at->format('M d, Y') }}</span>
                                                    <span
                                                        class="text-muted small">{{ $vacancy->created_at->diffForHumans() }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                    @can('view', $vacancy)
                                                        <button class="btn btn-sm btn-primary"
                                                            wire:click="viewVacancy({{ $vacancy->id }})" title="View">
                                                            <i class="fa-regular fa-eye"></i>
                                                        </button>
                                                    @endcan

                                                    @can('update', $vacancy)
                                                        <button class="btn btn-sm btn-warning"
                                                            wire:click="openEdit({{ $vacancy->id }})" title="Edit">
                                                            <i class="fa-regular fa-pen"></i>
                                                        </button>

                                                        @if($vacancy->status->value === 'draft' || $vacancy->status->value === 'closed')
                                                            <button class="btn btn-sm btn-success"
                                                                wire:click="confirmPublish({{ $vacancy->id }})" title="Publish">
                                                                <i class="fa-regular fa-circle-check"></i>
                                                            </button>
                                                        @endif

                                                        @if($vacancy->status->value === 'published')
                                                            <button class="btn btn-sm btn-secondary"
                                                                wire:click="confirmClose({{ $vacancy->id }})" title="Close">
                                                                <i class="fa-regular fa-lock"></i>
                                                            </button>
                                                        @endif

                                                        @if($vacancy->status->value !== 'archived')
                                                            <button class="btn btn-sm btn-dark"
                                                                wire:click="confirmArchive({{ $vacancy->id }})" title="Archive">
                                                                <i class="fa-regular fa-box-archive"></i>
                                                            </button>
                                                        @endif
                                                    @endcan

                                                    @can('create', App\Models\Vacancy::class)
                                                        <button class="btn btn-sm btn-info text-white"
                                                            wire:click="duplicateVacancy({{ $vacancy->id }})" title="Duplicate">
                                                            <i class="fa-regular fa-copy"></i>
                                                        </button>
                                                    @endcan

                                                    @can('delete', $vacancy)
                                                        <button class="btn btn-sm btn-danger"
                                                            wire:click="confirmDelete({{ $vacancy->id }})" title="Delete">
                                                            <i class="fa-regular fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <i class="fa-regular fa-briefcase fs-2 d-block mb-2 text-muted"></i>
                                                <h5>No vacancies found</h5>
                                                <p class="text-muted">Try adjusting your search filters, or post a new
                                                    role.</p>
                                                @can('create', App\Models\Vacancy::class)
                                                    <button class="btn btn-primary btn-sm" wire:click="openCreate">
                                                        <i class="fa-regular fa-plus me-1"></i> Post First Vacancy
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
                                <span>Showing {{ $vacancies->firstItem() ?? 0 }}–{{ $vacancies->lastItem() ?? 0 }} of
                                    {{ $vacancies->total() }} vacancies</span>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ $vacancies->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- VIEW VACANCY MODAL --}}
    @if($showViewModal && $viewingVacancy)
        <div class="modal fade show d-block" id="viewVacancyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $viewingVacancy->title }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span
                                class="badge badge-secondary light border-0">{{ $viewingVacancy->department?->name ?? 'No department' }}</span>
                            <span
                                class="badge badge-secondary light border-0">{{ $viewingVacancy->employment_type->label() }}</span>
                            <span
                                class="badge badge-secondary light border-0">{{ $viewingVacancy->experience_level->label() }}</span>
                            <span
                                class="badge badge-secondary light border-0">{{ $viewingVacancy->workplace_type->label() }}</span>
                            @if($viewingVacancy->is_featured)
                                <span class="badge border-0 text-white"
                                    style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                    <i class="fa-solid fa-star"></i> Featured
                                </span>
                            @endif
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Location</div>
                                <div>
                                    {{ $viewingVacancy->location ?: '—' }}{{ $viewingVacancy->country ? ', ' . $viewingVacancy->country : '' }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Salary</div>
                                <div>{{ $viewingVacancy->salary_range ?? 'Not disclosed' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Positions</div>
                                <div>{{ $viewingVacancy->positions_available }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Status</div>
                                <div>{{ $viewingVacancy->status->label() }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Published</div>
                                <div>{{ $viewingVacancy->published_at?->format('M d, Y') ?? 'Not published' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-muted small">Closing Date</div>
                                <div>{{ $viewingVacancy->closing_date?->format('M d, Y') ?? 'No deadline' }}</div>
                            </div>
                        </div>

                        @if($viewingVacancy->summary)
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1">Summary</div>
                                <p class="mb-0">{{ $viewingVacancy->summary }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <div class="fw-bold text-muted small mb-1">Description</div>
                            <p class="mb-0" style="white-space: pre-line;">{{ $viewingVacancy->description }}</p>
                        </div>

                        @if($viewingVacancy->responsibilities)
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1">Responsibilities</div>
                                <p class="mb-0" style="white-space: pre-line;">{{ $viewingVacancy->responsibilities }}</p>
                            </div>
                        @endif

                        @if($viewingVacancy->requirements)
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1">Requirements</div>
                                <p class="mb-0" style="white-space: pre-line;">{{ $viewingVacancy->requirements }}</p>
                            </div>
                        @endif

                        @if($viewingVacancy->benefits)
                            <div class="mb-3">
                                <div class="fw-bold text-muted small mb-1">Benefits</div>
                                <p class="mb-0" style="white-space: pre-line;">{{ $viewingVacancy->benefits }}</p>
                            </div>
                        @endif

                        <hr>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Created by {{ $viewingVacancy->creator?->name ?? 'System' }} ·
                                {{ $viewingVacancy->created_at->format('M d, Y g:i A') }}</span>
                            <span>{{ number_format($viewingVacancy->views_count) }} views</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showViewModal', false)">Close</button>
                        @can('update', $viewingVacancy)
                            <button class="btn btn-primary"
                                wire:click="openEdit({{ $viewingVacancy->id }}); $set('showViewModal', false)">
                                <i class="fa-regular fa-pen"></i> Edit
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- VACANCY FORM MODAL (Create/Edit) --}}
    @if($showVacancyModal)
        <div class="modal fade show d-block" id="vacancyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditing ? 'Edit Vacancy' : 'Post New Vacancy' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showVacancyModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div wire:key="vacancy-form-{{ $isEditing ? 'edit' : 'create' }}">

                            {{-- Core Details --}}
                            <h6 class="border-bottom pb-2 fw-bold text-primary"><i
                                    class="fa-regular fa-file-lines me-2"></i>Core Details</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small">Job Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        wire:model="title" placeholder="e.g. Senior Backend Engineer">
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Department</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select @error('department_id') is-invalid @enderror"
                                            wire:model="department_id">
                                            <option value="">Select department</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" type="button"
                                            wire:click="$toggle('showNewDepartment')" title="Add new department">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    @if($showNewDepartment)
                                        <div class="mt-2 d-flex gap-2">
                                            <input type="text"
                                                class="form-control form-control-sm @error('newDepartmentName') is-invalid @enderror"
                                                wire:model="newDepartmentName" placeholder="New department name">
                                            <button class="btn btn-sm btn-success" wire:click="addDepartment"><i
                                                    class="fas fa-check"></i></button>
                                            <button class="btn btn-sm btn-secondary"
                                                wire:click="$set('showNewDepartment', false)"><i
                                                    class="fas fa-times"></i></button>
                                        </div>
                                        @error('newDepartmentName')
                                        <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    @endif
                                    @error('department_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Short Summary</label>
                                    <input type="text" class="form-control @error('summary') is-invalid @enderror"
                                        wire:model="summary" maxlength="500" placeholder="One-line pitch shown in listings">
                                    @error('summary')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Full Description <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" rows="5"
                                        wire:model="description" placeholder="Detailed role overview..."></textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Responsibilities</label>
                                    <textarea class="form-control @error('responsibilities') is-invalid @enderror" rows="4"
                                        wire:model="responsibilities" placeholder="One per line"></textarea>
                                    @error('responsibilities')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Requirements</label>
                                    <textarea class="form-control @error('requirements') is-invalid @enderror" rows="4"
                                        wire:model="requirements" placeholder="One per line"></textarea>
                                    @error('requirements')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Benefits</label>
                                    <textarea class="form-control @error('benefits') is-invalid @enderror" rows="4"
                                        wire:model="benefits" placeholder="One per line"></textarea>
                                    @error('benefits')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Classification --}}
                            <h6 class="border-bottom pb-2 fw-bold text-primary mt-2"><i
                                    class="fa-regular fa-tags me-2"></i>Classification</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Employment Type <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('employment_type') is-invalid @enderror"
                                        wire:model="employment_type">
                                        @foreach($employmentTypes as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Experience Level <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('experience_level') is-invalid @enderror"
                                        wire:model="experience_level">
                                        @foreach($experienceLevels as $level)
                                            <option value="{{ $level->value }}">{{ $level->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('experience_level')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Workplace Type <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('workplace_type') is-invalid @enderror"
                                        wire:model.live="workplace_type">
                                        @foreach($workplaceTypes as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('workplace_type')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">
                                        Location
                                        @if(in_array($workplace_type, ['onsite', 'hybrid'])) <span
                                        class="text-danger">*</span> @endif
                                    </label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                        wire:model="location" placeholder="e.g. Accra">
                                    @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Country</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror"
                                        wire:model="country" placeholder="e.g. Ghana">
                                    @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Positions Available <span
                                            class="text-danger">*</span></label>
                                    <input type="number" min="1" max="1000"
                                        class="form-control @error('positions_available') is-invalid @enderror"
                                        wire:model="positions_available">
                                    @error('positions_available')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Compensation --}}
                            <h6 class="border-bottom pb-2 fw-bold text-primary mt-2"><i
                                    class="fa-regular fa-sack-dollar me-2"></i>Compensation</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small">Currency</label>
                                    <input type="text" maxlength="3"
                                        class="form-control text-uppercase @error('salary_currency') is-invalid @enderror"
                                        wire:model="salary_currency" placeholder="USD">
                                    @error('salary_currency')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small">Minimum</label>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('salary_min') is-invalid @enderror"
                                        wire:model="salary_min">
                                    @error('salary_min')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small">Maximum</label>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('salary_max') is-invalid @enderror"
                                        wire:model="salary_max">
                                    @error('salary_max')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" id="is_salary_visible"
                                            wire:model="is_salary_visible">
                                        <label class="form-check-label small" for="is_salary_visible">Show salary
                                            publicly</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Publishing --}}
                            <h6 class="border-bottom pb-2 fw-bold text-primary mt-2"><i
                                    class="fa-regular fa-calendar-check me-2"></i>Publishing</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                        @foreach($statuses as $statusCase)
                                            <option value="{{ $statusCase->value }}">{{ $statusCase->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Closing Date</label>
                                    <input type="datetime-local"
                                        class="form-control @error('closing_date') is-invalid @enderror"
                                        wire:model="closing_date">
                                    @error('closing_date')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" id="is_featured"
                                            wire:model="is_featured">
                                        <label class="form-check-label small fw-bold" for="is_featured">
                                            <i class="fa-solid fa-star text-warning"></i> Feature on careers page
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- SEO --}}
                            <h6 class="border-bottom pb-2 fw-bold text-primary mt-2"><i
                                    class="fa-regular fa-magnifying-glass me-2"></i>SEO (optional)</h6>
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Meta Title</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                        wire:model="meta_title" maxlength="255">
                                    @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Meta Description</label>
                                    <input type="text" class="form-control @error('meta_description') is-invalid @enderror"
                                        wire:model="meta_description" maxlength="500">
                                    @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger mt-2">
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
                            wire:click="$set('showVacancyModal', false)">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="saveVacancy" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular {{ $isEditing ? 'fa-save' : 'fa-plus' }}"></i>
                                {{ $isEditing ? 'Save Changes' : 'Post Vacancy' }}</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DELETE MODAL --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" id="deleteVacancyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Vacancy?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-trash-alt fa-3x text-danger mb-3"></i>
                        <p>This action is <strong>permanent</strong> and cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Keep Vacancy</button>
                        <button class="btn btn-danger" wire:click="deleteVacancy" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-trash"></i> Yes, Delete</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- BULK DELETE MODAL --}}
    @if($showBulkDeleteModal)
        <div class="modal fade show d-block" id="bulkDeleteVacancyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete {{ count($selectedVacancies) }} vacancies?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-box-open fa-3x text-danger mb-3"></i>
                        <p>You're about to permanently delete <strong>{{ count($selectedVacancies) }} vacancies</strong>.
                            This cannot be reversed.</p>
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

    {{-- BULK ARCHIVE MODAL --}}
    @if($showBulkArchiveModal)
        <div class="modal fade show d-block" id="bulkArchiveVacancyModal" tabindex="-1" style="background: rgba(0,0,0,.6);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            <i class="fa-regular fa-box-archive me-2"></i>
                            Archive {{ count($selectedVacancies) }} vacancies?
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkArchiveModal', false)"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:72px; height:72px; border-radius:50%; background:rgba(15,23,42,.06);">
                            <i class="fa-regular fa-box-archive fs-2 text-dark"></i>
                        </div>
                        <p class="text-muted mb-0">
                            Archived roles are hidden from the public careers page but stay in your records.
                            You can un-archive them later.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showBulkArchiveModal', false)">Cancel</button>
                        <button class="btn btn-dark" wire:click="bulkArchive" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="bulkArchive">
                                <i class="fa-regular fa-box-archive"></i> Archive all
                            </span>
                            <span wire:loading wire:target="bulkArchive">
                                <i class="fa-regular fa-spinner fa-spin"></i> Archiving…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PUBLISH MODAL --}}
    @if($showPublishModal)
        <div class="modal fade show d-block" id="publishVacancyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Publish this vacancy?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showPublishModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-circle-check fa-3x text-success mb-3"></i>
                        <p>This will make the role visible on the public careers page immediately.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showPublishModal', false)">Cancel</button>
                        <button class="btn btn-success" wire:click="publishConfirmed" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-circle-check"></i> Publish</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Publishing…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- CLOSE MODAL --}}
    @if($showCloseModal)
        <div class="modal fade show d-block" id="closeVacancyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Close this vacancy?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showCloseModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-lock fa-3x text-warning mb-3"></i>
                        <p>The role will stop accepting applications but stay in your records. You can re-publish it
                            later.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showCloseModal', false)">Cancel</button>
                        <button class="btn btn-warning" wire:click="closeConfirmed" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-lock"></i> Close Vacancy</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Closing…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ARCHIVE MODAL --}}
    @if($showArchiveModal)
        <div class="modal fade show d-block" id="archiveVacancyModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archive this vacancy?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showArchiveModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-regular fa-box-archive fa-3x text-dark mb-3"></i>
                        <p>Archived vacancies are hidden everywhere but kept for reference. This does not delete the
                            record.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showArchiveModal', false)">Cancel</button>
                        <button class="btn btn-dark" wire:click="archiveConfirmed" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-regular fa-box-archive"></i> Archive</span>
                            <span wire:loading><i class="fa-regular fa-spinner fa-spin"></i> Archiving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- STYLES --}}
<style>
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
</style>
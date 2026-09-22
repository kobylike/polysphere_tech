<div class="position-relative">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Applications</h5>
            </li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Applications</a></li>
        </ol>
        <div class="d-flex gap-2">
            @can('viewAny', App\Models\Application::class)
                <button class="btn btn-outline-primary btn-sm" wire:click="export" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="export">
                        <i class="fa-regular fa-file-export me-1"></i> Export CSV
                    </span>
                    <span wire:loading wire:target="export">
                        <i class="fa-regular fa-spinner fa-spin me-1"></i> Exporting…
                    </span>
                </button>
            @endcan
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
                <i class="fa-regular fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="container-fluid">
        {{-- ─── Stats ───────────────────────────────────────────── --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-2 col-sm-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <span class="fs-14">Total</span>
                        <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-sm-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <span class="fs-14">New</span>
                        <h3 class="text-white mb-0">{{ number_format($stats['new']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-sm-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <span class="fs-14">In progress</span>
                        <h3 class="text-white mb-0">{{ number_format($stats['in_progress']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-sm-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <span class="fs-14">Hired</span>
                        <h3 class="text-white mb-0">{{ number_format($stats['hired']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-sm-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <span class="fs-14">Rejected</span>
                        <h3 class="text-white mb-0">{{ number_format($stats['rejected']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-sm-4">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <span class="fs-14">This week</span>
                        <h3 class="text-white mb-0">{{ number_format($stats['this_week']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Filters ─────────────────────────────────────────── --}}
        <div class="row align-items-center mb-3">
            <div class="col-xl-8 col-lg-6">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm"
                            placeholder="Search name, email, company…" wire:model.live.debounce.300ms="search">
                        <i class="fa-regular fa-search"></i>
                    </div>
                    <select class="form-control form-control-sm w-auto" wire:model.live="vacancyFilter">
                        <option value="">All vacancies</option>
                        @foreach($vacancies as $v)
                            <option value="{{ $v->id }}">{{ \Illuminate\Support\Str::limit($v->title, 40) }}</option>
                        @endforeach
                    </select>
                    <select class="form-control form-control-sm w-auto" wire:model.live="statusFilter">
                        <option value="">All statuses</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <select class="form-control form-control-sm w-auto" wire:model.live="perPage">
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>
            </div>

            {{-- Bulk toolbar --}}
            @if(count($selectedApplications) > 0)
                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end align-items-center">
                        <span class="badge bg-dark text-white p-2">{{ count($selectedApplications) }} selected</span>

                        @can('update', App\Models\Application::class)
                            <select class="form-control form-control-sm w-auto"
                                wire:change="bulkSetStatus($event.target.value)">
                                <option value="">Set status…</option>
                                @foreach($statuses as $s)
                                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        @endcan

                        @can('delete', App\Models\Application::class)
                            <button class="btn btn-danger btn-sm" wire:click="confirmBulkDelete">
                                <i class="fa-regular fa-trash"></i> Delete
                            </button>
                        @endcan
                    </div>
                </div>
            @endif
        </div>

        {{-- ─── Table ───────────────────────────────────────────── --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
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
                                    Candidate
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
                                <th>Applied for</th>
                                <th>Experience</th>
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
                                <th wire:click="sort('created_at')" style="cursor:pointer;">
                                    Applied
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
                            @forelse($applications as $app)
                                @php
                                    $isNew = $app->status === 'new';
                                    $daysAgo = $app->created_at->diffInDays(now());
                                @endphp
                                <tr class="{{ in_array((string) $app->id, $selectedApplications) ? 'table-active' : '' }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model.live="selectedApplications" value="{{ $app->id }}">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md me-2 position-relative">
                                                <span class="avatar-text bg-primary">{{ strtoupper($app->initials) }}</span>
                                                @if($isNew)
                                                    <span class="position-absolute"
                                                        style="top:-2px; right:-2px; width:10px; height:10px; background:#10b981; border:2px solid #fff; border-radius:50%;"
                                                        title="New application"></span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $app->name }}</h6>
                                                <span class="text-muted small">{{ $app->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $app->vacancy?->title ?? '—' }}</td>
                                    <td>
                                        <span class="small">{{ $app->years_experience_label }}</span>
                                        @if($app->current_role)
                                            <div class="text-muted small">{{ $app->current_role }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $app->statusEnum()->color() }} light border-0">
                                            {{ $app->statusEnum()->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $app->created_at->format('M d, Y') }}</span>
                                            <span class="text-muted small">
                                                @if($daysAgo === 0)
                                                    Today
                                                @elseif($daysAgo === 1)
                                                    Yesterday
                                                @else
                                                    {{ $daysAgo }} days ago
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('view', $app)
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    wire:click="viewApplication({{ $app->id }})" title="Review">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    wire:click="downloadCv({{ $app->id }})" title="Download CV">
                                                    <i class="fa-regular fa-download"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    wire:click="openEmailModal({{ $app->id }})" title="Email candidate">
                                                    <i class="fa-regular fa-envelope"></i>
                                                </button>
                                            @endcan

                                            @can('delete', $app)
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="confirmDelete({{ $app->id }})" title="Delete">
                                                    <i class="fa-regular fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fa-regular fa-inbox fs-2 d-block mb-2 text-muted"></i>
                                        <h5>No applications yet</h5>
                                        <p class="text-muted">When candidates apply, they'll appear here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center p-3">
                    <div class="col-md-6">
                        <span>Showing {{ $applications->firstItem() ?? 0 }}–{{ $applications->lastItem() ?? 0 }} of
                            {{ $applications->total() }}</span>
                    </div>
                    <div class="col-md-6 text-end">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Review modal ─────────────────────────────────────── --}}
    @if($showViewModal && $viewingApplication)
        @can('view', $viewingApplication)
            <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Application — {{ $viewingApplication->name }}</h5>
                            <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6"><strong>Email:</strong> {{ $viewingApplication->email }}</div>
                                <div class="col-md-6"><strong>Phone:</strong> {{ $viewingApplication->phone ?: '—' }}</div>
                                <div class="col-md-6"><strong>Location:</strong>
                                    {{ trim(($viewingApplication->location ?? '') . ($viewingApplication->country ? ', ' . $viewingApplication->country : '')) ?: '—' }}
                                </div>
                                <div class="col-md-6"><strong>Experience:</strong>
                                    {{ $viewingApplication->years_experience_label }}
                                </div>
                                <div class="col-md-6"><strong>Current role:</strong>
                                    {{ $viewingApplication->current_role ?: '—' }}
                                </div>
                                <div class="col-md-6"><strong>Company:</strong>
                                    {{ $viewingApplication->current_company ?: '—' }}
                                </div>
                                <div class="col-md-6"><strong>Availability:</strong>
                                    {{ $viewingApplication->availability ?: '—' }}
                                </div>
                                <div class="col-md-6"><strong>Salary expectation:</strong>
                                    {{ $viewingApplication->salary_expectation ?: '—' }}
                                </div>
                                @if($viewingApplication->timezone)
                                    <div class="col-md-6"><strong>Timezone:</strong> {{ $viewingApplication->timezone }}</div>
                                @endif
                                @if($viewingApplication->work_authorization)
                                    <div class="col-md-6"><strong>Work authorization:</strong>
                                        {{ $viewingApplication->work_authorization }}
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Links:</strong>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @foreach([
                                            'LinkedIn' => $viewingApplication->linkedin_url,
                                            'Portfolio' => $viewingApplication->portfolio_url,
                                            'GitHub' => $viewingApplication->github_url,
                                            'Site' => $viewingApplication->personal_site_url,
                                            'Behance' => $viewingApplication->behance_url,
                                            'Dribbble' => $viewingApplication->dribbble_url,
                                            'Writing' => $viewingApplication->writing_samples_url,
                                        ] as $label => $url)
                                        @if($url)
                                            <a href="{{ $url }}" target="_blank" rel="noopener"
                                                class="badge bg-secondary light border-0 text-decoration-none">
                                                {{ $label }} <i class="fa-solid fa-external-link-alt ms-1" style="font-size:10px;"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @if($viewingApplication->cover_letter)
                                <div class="mb-3">
                                    <strong>Cover note:</strong>
                                    <div class="p-3 bg-light rounded-3 mt-1" style="white-space: pre-line; font-size: 14px;">
                                        {{ $viewingApplication->cover_letter }}
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <div class="row g-3">
                                @can('update', $viewingApplication)
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Status</label>
                                        <select class="form-select" wire:model="newStatus">
                                            @foreach($statuses as $s)
                                                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Status</label>
                                        <div>
                                            <span
                                                class="badge badge-{{ $viewingApplication->statusEnum()->color() }} light border-0">
                                                {{ $viewingApplication->statusEnum()->label() }}
                                            </span>
                                        </div>
                                    </div>
                                @endcan

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">CV</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-primary"
                                            wire:click="downloadCv({{ $viewingApplication->id }})">
                                            <i class="fa-regular fa-download"></i> Download CV
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info"
                                            wire:click="openEmailModal({{ $viewingApplication->id }}); $set('showViewModal', false)">
                                            <i class="fa-regular fa-envelope"></i> Email candidate
                                        </button>
                                    </div>
                                </div>

                                @can('update', $viewingApplication)
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Internal notes</label>
                                        <textarea class="form-control" rows="4" wire:model="adminNotes"
                                            placeholder="Interview impressions, references checked, etc. Only visible to admins."></textarea>
                                    </div>
                                @elseif($viewingApplication->admin_notes)
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Internal notes</label>
                                        <div class="p-3 bg-light rounded-3" style="white-space: pre-line; font-size: 14px;">
                                            {{ $viewingApplication->admin_notes }}
                                        </div>
                                    </div>
                                @endcan

                                @if($viewingApplication->reviewed_at)
                                    <div class="col-12">
                                        <div class="text-muted small">
                                            <i class="fa-regular fa-clock me-1"></i>
                                            Last reviewed {{ $viewingApplication->reviewed_at->diffForHumans() }}
                                            @if($viewingApplication->reviewer)
                                                by {{ $viewingApplication->reviewer->name }}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <div>
                                @can('delete', $viewingApplication)
                                    <button type="button" class="btn btn-outline-danger"
                                        wire:click="confirmDelete({{ $viewingApplication->id }}); $set('showViewModal', false)">
                                        <i class="fa-regular fa-trash"></i> Delete
                                    </button>
                                @endcan
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary"
                                    wire:click="$set('showViewModal', false)">Close</button>

                                @can('update', $viewingApplication)
                                    <button type="button" class="btn btn-primary" wire:click="saveStatus"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>Save changes</span>
                                        <span wire:loading><i class="fa-solid fa-circle-notch fa-spin"></i> Saving…</span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    @endif

    {{-- ─── Compose email modal ─────────────────────────────────── --}}
    @if($showEmailModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-lg">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">
                                <i class="fa-regular fa-paper-plane me-1"></i> Email candidate
                            </h5>
                            @if($emailsSentCount > 0)
                                <div class="text-muted small">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    {{ $emailsSentCount }} email(s) previously sent
                                    @if($lastEmailedAt) · last {{ $lastEmailedAt }} @endif
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn-close" wire:click="closeEmailModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">To</label>
                                <input type="email" class="form-control @error('emailTo') is-invalid @enderror"
                                    wire:model.blur="emailTo">
                                @error('emailTo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">From</label>
                                <input type="text" class="form-control"
                                    value="{{ Auth::user()->name }} <careers@polyspheretech.com>" readonly
                                    style="background:#f8fafc; cursor:not-allowed;">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small">Subject</label>
                                <input type="text" class="form-control @error('emailSubject') is-invalid @enderror"
                                    wire:model.blur="emailSubject">
                                @error('emailSubject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small">Message</label>
                                <textarea class="form-control @error('emailBody') is-invalid @enderror" rows="12"
                                    wire:model.blur="emailBody" style="font-family: inherit; line-height: 1.6;"></textarea>
                                @error('emailBody')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    The candidate's name and the role are pre-filled. Edit freely — this is what they'll
                                    see.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <div class="d-flex gap-2">
                            <a href="mailto:{{ $emailTo }}?subject={{ urlencode($emailSubject) }}&body={{ urlencode($emailBody) }}"
                                class="btn btn-sm btn-outline-secondary" title="Open in your desktop mail client instead">
                                <i class="fa-regular fa-arrow-up-right-from-square me-1"></i>
                                Open in mail app
                            </a>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" wire:click="closeEmailModal">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="sendCandidateEmail"
                                wire:loading.attr="disabled" wire:target="sendCandidateEmail">
                                <span wire:loading.remove wire:target="sendCandidateEmail">
                                    <i class="fa-regular fa-paper-plane"></i> Send email
                                </span>
                                <span wire:loading wire:target="sendCandidateEmail">
                                    <i class="fa-regular fa-spinner fa-spin"></i> Sending…
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Single delete modal ─────────────────────────────────── --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.6);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fa-regular fa-triangle-exclamation me-2"></i>Delete this application?
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 72px; height: 72px; border-radius: 50%; background: rgba(239,68,68,.1);">
                            <i class="fa-regular fa-trash-can fs-2 text-danger"></i>
                        </div>
                        <h5 class="mb-2">This cannot be undone.</h5>
                        <p class="text-muted mb-0">
                            The application and its uploaded CV will be permanently removed.
                            The candidate is <strong>not</strong> notified.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showDeleteModal', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteApplication"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteApplication">
                                <i class="fa-regular fa-trash"></i> Yes, delete
                            </span>
                            <span wire:loading wire:target="deleteApplication">
                                <i class="fa-regular fa-spinner fa-spin"></i> Deleting…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Bulk delete modal ───────────────────────────────────── --}}
    @if($showBulkDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.6);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fa-regular fa-triangle-exclamation me-2"></i>
                            Delete {{ count($selectedApplications) }} application(s)?
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 72px; height: 72px; border-radius: 50%; background: rgba(239,68,68,.1);">
                            <i class="fa-regular fa-trash-can fs-2 text-danger"></i>
                        </div>
                        <h5 class="mb-2">You're about to delete {{ count($selectedApplications) }} applications.</h5>
                        <p class="text-muted mb-0">
                            Every selected application and its uploaded CV will be permanently removed.
                            Candidates are <strong>not</strong> notified.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showBulkDeleteModal', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="bulkDelete" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="bulkDelete">
                                <i class="fa-regular fa-trash"></i> Delete all
                            </span>
                            <span wire:loading wire:target="bulkDelete">
                                <i class="fa-regular fa-spinner fa-spin"></i> Deleting…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .avatar-text {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-weight: 700;
        color: #fff;
    }

    .avatar-md .avatar-text {
        width: 38px;
        height: 38px;
        font-size: 14px;
        border-radius: 50%;
    }
</style>
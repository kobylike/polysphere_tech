<div class="position-relative">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Applications</h5>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Applications</a></li>
        </ol>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
                <i class="fa-regular fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="container-fluid">
        {{-- ─── Stats ──────────────────────────────────────────────── --}}
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

        {{-- ─── Filters ────────────────────────────────────────────── --}}
        <div class="row align-items-center mb-3">
            <div class="col-12">
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
                </div>
            </div>
        </div>

        {{-- ─── Table ──────────────────────────────────────────────── --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sort('name')" style="cursor:pointer;">Candidate</th>
                                <th>Applied for</th>
                                <th>Experience</th>
                                <th wire:click="sort('status')" style="cursor:pointer;">Status</th>
                                <th wire:click="sort('created_at')" style="cursor:pointer;">Applied</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md me-2">
                                                <span class="avatar-text bg-primary">{{ strtoupper($app->initials) }}</span>
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
                                            <span class="text-muted small">{{ $app->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('view', $app)
                                                <button class="btn btn-sm btn-primary"
                                                    wire:click="viewApplication({{ $app->id }})" title="Review">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            @endcan

                                            @can('view', $app)
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    wire:click="downloadCv({{ $app->id }})" title="Download CV">
                                                    <i class="fa-regular fa-download"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
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

    {{-- ─── Review modal ──────────────────────────────────────────── --}}
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
                                    {{ $viewingApplication->years_experience_label }}</div>
                                <div class="col-md-6"><strong>Current role:</strong>
                                    {{ $viewingApplication->current_role ?: '—' }}</div>
                                <div class="col-md-6"><strong>Company:</strong>
                                    {{ $viewingApplication->current_company ?: '—' }}</div>
                                <div class="col-md-6"><strong>Availability:</strong>
                                    {{ $viewingApplication->availability ?: '—' }}</div>
                                <div class="col-md-6"><strong>Salary expectation:</strong>
                                    {{ $viewingApplication->salary_expectation ?: '—' }}</div>
                                @if($viewingApplication->timezone)
                                    <div class="col-md-6"><strong>Timezone:</strong> {{ $viewingApplication->timezone }}</div>
                                @endif
                                @if($viewingApplication->work_authorization)
                                    <div class="col-md-6"><strong>Work authorization:</strong>
                                        {{ $viewingApplication->work_authorization }}</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Links:</strong>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @foreach([
                                            'LinkedIn'  => $viewingApplication->linkedin_url,
                                            'Portfolio' => $viewingApplication->portfolio_url,
                                            'GitHub'    => $viewingApplication->github_url,
                                            'Site'      => $viewingApplication->personal_site_url,
                                            'Behance'   => $viewingApplication->behance_url,
                                            'Dribbble'  => $viewingApplication->dribbble_url,
                                            'Writing'   => $viewingApplication->writing_samples_url,
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
                                {{-- Status dropdown – only if the user can update --}}
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
                                            <span class="badge badge-{{ $viewingApplication->statusEnum()->color() }} light border-0">
                                                {{ $viewingApplication->statusEnum()->label() }}
                                            </span>
                                        </div>
                                    </div>
                                @endcan

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">CV</label>
                                    <div>
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="downloadCv({{ $viewingApplication->id }})">
                                            <i class="fa-regular fa-download"></i> Download CV
                                        </button>
                                    </div>
                                </div>

                                {{-- Internal notes – only if the user can update --}}
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
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" wire:click="$set('showViewModal', false)">Close</button>

                            @can('update', $viewingApplication)
                                <button class="btn btn-primary" wire:click="saveStatus" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Save changes</span>
                                    <span wire:loading><i class="fa-solid fa-circle-notch fa-spin"></i> Saving…</span>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endcan
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
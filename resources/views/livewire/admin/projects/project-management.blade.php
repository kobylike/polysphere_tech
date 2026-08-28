<div>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">CMS</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Projects</a></li>
        </ol>
    </div>

    <div class="container-fluid">
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter -->
        <div class="filter cm-content-box box-primary">
            <div class="content-title">
                <div class="cpa"><i class="fa-solid fa-filter me-2"></i>Filter</div>
                <div class="tools">
                    <a href="javascript:void(0);" class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a>
                </div>
            </div>
            <div class="cm-content-body form excerpt">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-sm-6">
                            <input type="text" class="form-control mb-3 mb-xl-0" wire:model.live.debounce.500ms="search"
                                placeholder="Search projects...">
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                            <select class="default-select dashboard-select-2 w-100" wire:model.live="status">
                                <option value="">All Status</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                                <option value="private">Private</option>
                                <option value="pending">Pending</option>
                                <option value="trash">Trash</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                            <select class="default-select dashboard-select-2 w-100" wire:model.live="service">
                                <option value="">All Services</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <button class="btn btn-info" wire:click="$refresh"><i
                                    class="fa fa-refresh me-1"></i>Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-3">
            <ul class="d-flex align-items-center flex-wrap">
                <li><a wire:navigate.hover href="{{ route('admin.projects.create') }}" class="btn btn-primary"><i
                            class="fa-solid fa-plus me-1"></i>Add Project</a></li>
                <li class="ms-3">
                    <select class="form-select form-select-sm" wire:model.live="perPage" style="width:80px;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </li>
            </ul>
        </div>

        <!-- Bulk Actions -->
        <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-semibold me-2">Bulk Actions:</span>
            <select class="form-select form-select-sm" style="width:150px;" wire:model="bulkAction">
                <option value="">-- Select --</option>
                <option value="delete">Delete</option>
                <option value="publish">Publish</option>
                <option value="draft">Move to Draft</option>
                <option value="trash">Move to Trash</option>
            </select>
            <button class="btn btn-sm btn-primary" wire:click="applyBulkAction" wire:loading.attr="disabled">
                <span wire:loading.remove>Apply</span>
                <span wire:loading><i class="fa fa-spinner fa-spin"></i></span>
            </button>
            @if(count($selectedProjects) > 0)
                <span class="text-muted small">{{ count($selectedProjects) }} selected</span>
            @endif
        </div>

        <!-- Table -->
        <div class="filter cm-content-box box-primary">
            <div class="content-title">
                <div class="cpa"><i class="fa-solid fa-folder-open me-1"></i>Projects List <span
                        class="badge bg-primary ms-2">{{ $projects->total() }}</span></div>
                <div class="tools">
                    <a href="javascript:void(0);" class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a>
                </div>
            </div>
            <div class="cm-content-body form excerpt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-condensed flip-content">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" wire:model="selectAll"
                                            class="form-check-input"></th>
                                    <th>#</th>
                                    <th><a href="javascript:void(0);" wire:click="sortBy('title')">Title
                                            @if($sortField === 'title')<i
                                            class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif</a>
                                    </th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Author</th>
                                    <th><a href="javascript:void(0);" wire:click="sortBy('created_at')">Created
                                            @if($sortField === 'created_at')<i
                                            class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif</a>
                                    </th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr>
                                        <td><input type="checkbox" value="{{ $project->id }}" wire:model="selectedProjects"
                                                class="form-check-input"></td>
                                        <td>{{ $projects->firstItem() + $loop->index }}</td>
                                        <td>
                                            {{ Str::limit($project->title, 30) }}
                                            @if($project->featured_image)
                                                <img src="{{ asset('storage/' . $project->featured_image) }}" alt="thumb"
                                                    width="40" class="ms-2 rounded">
                                            @endif
                                        </td>
                                        <td>{{ $project->service?->name ?? 'Uncategorized' }}</td>
                                        <td><span
                                                class="badge {{ $project->status_badge }}">{{ ucfirst($project->status) }}</span>
                                        </td>
                                        <td>{{ $project->author?->name ?? 'Unknown' }}</td>
                                        <td>{{ $project->created_at->format('d M, Y') }}</td>
                                        <td class="text-end">
                                            <a wire:navigate.hover href="{{ route('admin.projects.edit', $project->id) }}"
                                                class="btn btn-warning btn-sm"><i
                                                    class="fa-regular fa-pen-to-square"></i></a>
                                            <button class="btn btn-danger btn-sm"
                                                wire:click="deleteSingle({{ $project->id }})"
                                                onclick="return confirm('Delete this project?')"><i
                                                    class="fa-regular fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-3">
                            <span>Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} of
                                {{ $projects->total() }} entries</span>
                            {{ $projects->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
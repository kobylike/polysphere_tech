<div>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">CMS</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Services</a></li>
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
                                placeholder="Search services...">
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                            <select class="default-select dashboard-select-2 w-100" wire:model.live="status">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
                <li><a wire:navigate.hover href="{{ route('admin.services.create') }}" class="btn btn-primary"><i
                            class="fa-solid fa-plus me-1"></i>Add Service</a></li>
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
                <option value="active">Activate</option>
                <option value="inactive">Deactivate</option>
            </select>
            <button class="btn btn-sm btn-primary" wire:click="applyBulkAction" wire:loading.attr="disabled">
                <span wire:loading.remove>Apply</span>
                <span wire:loading><i class="fa fa-spinner fa-spin"></i></span>
            </button>
            @if(count($selectedServices) > 0)
                <span class="text-muted small">{{ count($selectedServices) }} selected</span>
            @endif
        </div>

        <!-- Table -->
        <div class="filter cm-content-box box-primary">
            <div class="content-title">
                <div class="cpa"><i class="fa-solid fa-cubes me-1"></i>Services List <span
                        class="badge bg-primary ms-2">{{ $services->total() }}</span></div>
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
                                    <th><a href="javascript:void(0);" wire:click="sortBy('name')">Name
                                            @if($sortField === 'name')<i
                                            class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif</a>
                                    </th>
                                    <th>Slug</th>
                                    <th>Icon</th>
                                    <th>Status</th>
                                    <th><a href="javascript:void(0);" wire:click="sortBy('order')">Order
                                            @if($sortField === 'order')<i
                                            class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif</a>
                                    </th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    <tr>
                                        <td><input type="checkbox" value="{{ $service->id }}" wire:model="selectedServices"
                                                class="form-check-input"></td>
                                        <td>{{ $services->firstItem() + $loop->index }}</td>
                                        <td>
                                            {{ $service->name }}
                                            @if($service->featured_image)
                                                <img src="{{ asset('storage/' . $service->featured_image) }}" alt="thumb"
                                                    width="40" class="ms-2 rounded">
                                            @endif
                                        </td>
                                        <td>{{ $service->slug }}</td>
                                        <td><i class="{{ $service->icon ?? 'fas fa-cube' }}"></i></td>
                                        <td><span
                                                class="badge {{ $service->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($service->status) }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary"
                                                wire:click="moveUp({{ $service->id }})" {{ $loop->first ? 'disabled' : '' }}><i class="fa fa-arrow-up"></i></button>
                                            <button class="btn btn-sm btn-outline-secondary"
                                                wire:click="moveDown({{ $service->id }})" {{ $loop->last ? 'disabled' : '' }}><i class="fa fa-arrow-down"></i></button>
                                        </td>
                                        <td class="text-end">
                                            <a wire:navigate.hover href="{{ route('admin.services.edit', $service->id) }}"
                                                class="btn btn-warning btn-sm"><i
                                                    class="fa-regular fa-pen-to-square"></i></a>
                                            <button class="btn btn-danger btn-sm"
                                                wire:click="deleteSingle({{ $service->id }})"
                                                onclick="return confirm('Delete this service?')"><i
                                                    class="fa-regular fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No services found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-3">
                            <span>Showing {{ $services->firstItem() ?? 0 }} to {{ $services->lastItem() ?? 0 }} of
                                {{ $services->total() }} entries</span>
                            {{ $services->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
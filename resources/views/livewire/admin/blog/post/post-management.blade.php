<div>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">CMS</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Blog</a></li>
        </ol>
    </div>

    <div class="container-fluid">
        <!-- Alerts -->
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
                                placeholder="Search by title, content...">
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
                            <select class="default-select dashboard-select-2 w-100" wire:model.live="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="row g-2">
                                <div class="col-6"><input type="date" class="form-control" wire:model.live="date_from"
                                        placeholder="From"></div>
                                <div class="col-6"><input type="date" class="form-control" wire:model.live="date_to"
                                        placeholder="To"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-3">
            <ul class="d-flex align-items-center flex-wrap">
                <li><a wire:navigate.hover href="{{ route('create.post') }}" class="btn btn-primary"><i
                            class="fa-solid fa-plus me-1"></i>Add Blog</a></li>
                <li><a wire:navigate.hover href="{{ route('manage.categories') }}" class="btn btn-primary mx-1">Blog
                        Category</a></li>
                <li><a wire:navigate.hover href="{{ route('create.categories') }}"
                        class="btn btn-primary mt-sm-0 mt-1">Add Blog Category</a></li>
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
            @if(count($selectedPosts) > 0)
                <span class="text-muted small">{{ count($selectedPosts) }} selected</span>
            @endif
        </div>

        <!-- Posts Table -->
        <div class="filter cm-content-box box-primary">
            <div class="content-title">
                <div class="cpa"><i class="fa-solid fa-file-lines me-1"></i>Blogs List <span
                        class="badge bg-primary ms-2">{{ $posts->total() }}</span></div>
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
                                    <th class="text-black">#</th>
                                    <th class="text-black"><a href="javascript:void(0);"
                                            wire:click="sortBy('title')">Title @if($sortField === 'title')<i
                                            class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif</a>
                                    </th>
                                    <th class="text-black">Status</th>
                                    <th class="text-black">Categories</th>
                                    <th class="text-black">Author</th>
                                    <th class="text-black"><a href="javascript:void(0);"
                                            wire:click="sortBy('created_at')">Modified
                                            @if($sortField === 'created_at')<i
                                            class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif</a>
                                    </th>
                                    <th class="text-black text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($posts as $post)
                                    <tr>
                                        <td><input type="checkbox" value="{{ $post->id }}" wire:model="selectedPosts"
                                                class="form-check-input"></td>
                                        <td>{{ $posts->firstItem() + $loop->index }}</td>
                                        <td>
                                            <a wire:navigate.hover href="{{ route('blog.details', $post->slug) }}"
                                                target="_blank">
                                                {{ Str::limit($post->title, 40) }}
                                            </a>
                                        </td>
                                        <td><span
                                                class="badge {{ $post->status_badge }}">{{ ucfirst($post->status) }}</span>
                                        </td>
                                        <td>
                                            @foreach($post->categories as $cat)
                                                <span class="badge bg-secondary me-1">{{ $cat->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($post->author)
                                                <span class="text-nowrap"><i
                                                        class="fa-regular fa-user me-1"></i>{{ $post->author->name }}</span>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            {{ $post->updated_at->format('d M, Y') }}
                                            <small
                                                class="text-muted d-block">{{ $post->updated_at->format('h:i A') }}</small>
                                        </td>
                                        <td class="text-end">
                                            <a wire:navigate.hover href="{{ route('edit.post', $post->slug) }}"
                                                class="btn btn-warning btn-sm content-icon">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm content-icon" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $post->id }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <a href="{{ route('posts') }}#post-{{ $post->slug }}" target="_blank"
                                                class="btn btn-primary btn-sm content-icon"> <i class="fas fa-eye"></i></a>

                                            <!-- Delete Confirmation Modal -->
                                            <div class="modal fade" id="deleteModal{{ $post->id }}" tabindex="-1"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Delete</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <p>Are you sure you want to delete
                                                                <strong>"{{ $post->title }}"</strong>?
                                                            </p>
                                                            <p class="text-muted small">This action cannot be undone.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="button" class="btn btn-danger"
                                                                wire:click="deleteSingle({{ $post->id }})"
                                                                data-bs-dismiss="modal">Delete</button>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>


                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fa-regular fa-file-lines fa-2x d-block text-muted mb-2"></i>
                                            No posts found. <a wire:navigate.hover href="{{ route('create.post') }}">Create
                                                your first post</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-3">
                            <span>Showing {{ $posts->firstItem() ?? 0 }} to {{ $posts->lastItem() ?? 0 }} of
                                {{ $posts->total() }} entries</span>
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
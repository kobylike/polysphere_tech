<div>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">CMS</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Blog Category</a></li>
        </ol>
    </div>

    <div class="container-fluid">
        <!-- Alerts -->
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left: Form -->
            <div class="col-xl-4">
                <div class="filter cm-content-box box-primary">
                    <div class="content-title">
                        <div class="cpa">{{ $categoryId ? 'Edit' : 'Add' }} Blog Category</div>
                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                    class="fal fa-angle-down"></i></a></div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body">
                            <form wire:submit.prevent="save">
                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model.live.debounce.400ms="name" placeholder="Enter category name">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        wire:model.live.debounce.400ms="slug" placeholder="Enter slug (auto-generated)">
                                    @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" rows="8" wire:model="description"
                                        placeholder="Optional description"></textarea>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                        <span wire:loading.remove>{{ $categoryId ? 'Update' : 'Save' }}</span>
                                        <span wire:loading><i class="fa fa-spinner fa-spin"></i> Saving…</span>
                                    </button>
                                    @if($categoryId)
                                        <button type="button" class="btn btn-secondary"
                                            wire:click="cancelEdit">Cancel</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Category List -->
            <div class="col-xl-8">
                <div class="filter cm-content-box box-primary">
                    <div class="content-title">
                        <div class="cpa">Blog Categories</div>
                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                    class="fal fa-angle-down"></i></a></div>
                    </div>
                    <div class="cm-content-body publish-content form excerpt">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped verticle-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-black">#</th>
                                            <th class="text-black">Name</th>
                                            <th class="text-black">Slug</th>
                                            <th class="text-black text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($this->categories as $index => $category)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($editingId === $category->id)
                                                        <input type="text" class="form-control form-control-sm"
                                                            wire:model.live.debounce.400ms="name" placeholder="Name">
                                                    @else
                                                        {{ $category->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($editingId === $category->id)
                                                        <input type="text" class="form-control form-control-sm"
                                                            wire:model.live.debounce.400ms="slug" placeholder="Slug">
                                                    @else
                                                        {{ $category->slug }}
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($editingId === $category->id)
                                                        <button class="btn btn-success btn-sm" wire:click="save"><i
                                                                class="fa fa-check"></i></button>
                                                        <button class="btn btn-secondary btn-sm" wire:click="cancelEdit"><i
                                                                class="fa fa-times"></i></button>
                                                    @else
                                                        <a href="javascript:void(0);"
                                                            wire:click="edit({{ $category->id }})">Edit</a>
                                                        <span>|</span>
                                                        <a href="javascript:void(0);"
                                                            wire:click="moveUp({{ $category->id }})">Up</a>
                                                        <span>|</span>
                                                        <a href="javascript:void(0);"
                                                            wire:click="moveDown({{ $category->id }})">down</a>
                                                        <span>|</span>
                                                        <a href="javascript:void(0);" data-bs-toggle="modal"
                                                            data-bs-target="#deleteCategoryModal{{ $category->id }}">delete</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No categories found. Create one!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modals for each category -->
    @foreach($this->categories as $category)
        <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <p>Are you sure you want to delete <strong>"{{ $category->name }}"</strong>?</p>
                        <p class="text-muted small">This action cannot be undone. All associated posts will keep their
                            categories, but the category itself will be removed.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="delete({{ $category->id }})"
                            data-bs-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
{{-- App\Models\User::create([
'name' => 'Admin User',
'username' => 'admin',
'email' => 'admin@polyspheretech.com',
'password' => bcrypt('password123'),
'status' => 'active',
'phone' => '+1234567890',
]); --}}
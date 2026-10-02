<div>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">CMS</a></li>
            <li class="breadcrumb-item active">
                <a href="javascript:void(0)">
                    {{ $postSlug ? 'Edit' : 'Add' }} Blog Post
                </a>
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <!-- Top action buttons -->
                <div class="mb-3">
                    <ul class="d-flex align-items-center flex-wrap">
                        <li><a wire:navigate.hover href="{{ route('manage.posts') }}" class="btn btn-primary">Blog
                                List</a></li>
                        <li><a wire:navigate.hover href="{{ route('manage.categories') }}"
                                class="btn btn-primary mx-1">Blog Category</a></li>
                        <li><a wire:navigate.hover href="{{ route('create.categories') }}"
                                class="btn btn-primary me-1 mt-sm-0 mt-1">Add Blog Category</a></li>
                        <li><button class="btn btn-primary open mt-1 mt-md-0" onclick="toggleScreenOptions()">Screen
                                Option</button></li>
                    </ul>
                </div>

                <!-- Screen Options -->
                <div class="main-check" id="screenOptions" style="display:none;">
                    <div class="row">
                        <h6 class="mb-3">Show on screen</h6>
                        @php $options = ['Page Attributes', 'Featured Image', 'Excerpt', 'Custom Fields', 'Discussion', 'Slug', 'Author', 'Page Type', 'Seo']; @endphp
                        @foreach($options as $opt)
                            <div class="col-xl-2 col-lg-3 col-sm-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label mb-0 text-nowrap">{{ $opt }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Session messages -->
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session()->has('message'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('message') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Main Form -->
                @canany(['create', 'update'], App\Models\Post::class)
                    <form wire:submit.prevent="save" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-xl-8">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control w-50" wire:model.live.debounce.500ms="title"
                                        placeholder="Enter post title">
                                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- ═══════════ MARKDOWN EDITOR ═══════════ -->
                                <div class="card h-auto">
                                    <div class="card-body pt-3">
                                        <label class="form-label">
                                            Content
                                            <small class="text-muted ms-2">
                                                Markdown supported — use the toolbar or type syntax directly
                                            </small>
                                        </label>

                                        {{-- wire:ignore keeps Livewire from clobbering the editor DOM --}}
                                        <div wire:ignore wire:key="editor-{{ $postSlug ?? 'new' }}">
                                            <textarea id="markdown-editor"
                                                placeholder="Write your post content here...">{{ $content }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- ═══════════ END MARKDOWN EDITOR ═══════════ -->

                                <!-- Excerpt -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Excerpt</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Excerpt</label>
                                                <textarea class="form-control" rows="3" wire:model="excerpt"></textarea>
                                                <div class="form-text">Excerpts are optional hand-crafted summaries of your
                                                    content.</div>
                                                @error('excerpt') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Fields -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Custom Fields</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body form excerpt">
                                        <div class="card-body">
                                            <h6>Add New Custom Field:</h6>
                                            <div class="row">
                                                <div class="col-xl-6 col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Title</label>
                                                        <input type="text" class="form-control" placeholder="Field name"
                                                            wire:model.defer="custom_field_key">
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-sm-6">
                                                    <label class="form-label">Value</label>
                                                    <textarea class="form-control" rows="3" placeholder="Field value"
                                                        wire:model.defer="custom_field_value"></textarea>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm mt-3 mt-sm-0"
                                                wire:click="addCustomField">Add Custom Field</button>
                                            @if(!empty($custom_fields))
                                                <div class="mt-3">
                                                    <strong>Existing Custom Fields:</strong>
                                                    <ul>
                                                        @foreach($custom_fields as $key => $value)
                                                            <li><strong>{{ $key }}</strong>: {{ $value }} <button type="button"
                                                                    class="btn btn-danger btn-sm ms-2"
                                                                    wire:click="removeCustomField('{{ $key }}')">×</button></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <span class="mt-3 d-block">Custom fields can be used to add extra
                                                metadata.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Discussion -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Discussion</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body form excerpt">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="allowComments"
                                                    wire:model="allow_comments">
                                                <label class="form-check-label" for="allowComments">Allow comments.</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Slug -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Slug</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body form excerpt">
                                        <div class="card-body">
                                            <label class="form-label">Slug</label>
                                            <input type="text" class="form-control" wire:model="slug">
                                            @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Author -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Author</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body form excerpt">
                                        <div class="card-body">
                                            <label class="form-label">User</label>
                                            <select class="js-example-disabled form-select" disabled>
                                                <option>{{ Auth::user()->email }}</option>
                                            </select>
                                            <small class="text-muted">Only the logged-in user can be the author.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Seo</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body form excerpt">
                                        <div class="card-body">
                                            <label class="form-label">Page Title</label>
                                            <input type="text" class="form-control mb-3" wire:model="seo_title"
                                                placeholder="SEO Title">
                                            <div class="row">
                                                <div class="col-xl-6 col-sm-6">
                                                    <label class="form-label">Keywords</label>
                                                    <input type="text" class="form-control mb-sm-0 mb-3"
                                                        wire:model="seo_keywords" placeholder="Enter meta Keywords">
                                                </div>
                                                <div class="col-xl-6 col-sm-6">
                                                    <label class="form-label">Descriptions</label>
                                                    <textarea class="form-control" rows="3" wire:model="seo_description"
                                                        placeholder="Enter meta Description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Sidebar -->
                            <div class="col-xl-4">
                                <div class="right-sidebar-sticky">
                                    <!-- Publish Box -->
                                    <div class="filter cm-content-box box-primary">
                                        <div class="content-title">
                                            <div class="cpa">Published</div>
                                            <div class="tools"><a href="javascript:void(0);"
                                                    class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a>
                                            </div>
                                        </div>
                                        <div class="cm-content-body publish-content form excerpt">
                                            <div class="card-body pb-0">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" wire:model="status">
                                                        <option value="draft">Draft</option>
                                                        <option value="published">Published</option>
                                                        <option value="private">Private</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="trash">Trash</option>
                                                    </select>
                                                    @error('status') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Visibility</label>
                                                    <select class="form-select" wire:model="visibility">
                                                        <option value="public">Public</option>
                                                        <option value="password_protected">Password Protected</option>
                                                        <option value="private">Private</option>
                                                    </select>
                                                    @error('visibility') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Publish Date</label>
                                                    <input type="datetime-local" class="form-control"
                                                        wire:model="published_at">
                                                    @error('published_at') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <hr>
                                                <div class="text-end">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-sm">{{ $postSlug ? 'Update' : 'Publish' }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Categories -->
                                    <div class="filter cm-content-box box-primary">
                                        <div class="content-title">
                                            <div class="cpa">Categories</div>
                                            <div class="tools"><a href="javascript:void(0);"
                                                    class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a>
                                            </div>
                                        </div>
                                        <div class="cm-content-body publish-content form excerpt">
                                            <div class="card-body">
                                                <div class="border p-3 mb-3">
                                                    @forelse($this->categories as $cat)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="{{ $cat->id }}" id="cat_{{ $cat->id }}"
                                                                wire:model="selectedCategories">
                                                            <label class="form-check-label"
                                                                for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                                                        </div>
                                                    @empty
                                                        <p class="text-muted">No categories yet.</p>
                                                    @endforelse
                                                </div>
                                                <div class="input-group mt-3">
                                                    <input type="text" class="form-control"
                                                        wire:model.defer="newCategoryName" placeholder="New category name">
                                                    <span class="input-group-text" wire:click="addCategory"
                                                        style="cursor:pointer;">Add New</span>
                                                </div>
                                                @error('newCategoryName') <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tags -->
                                    <div class="filter cm-content-box box-primary">
                                        <div class="content-title">
                                            <div class="cpa">Tags</div>
                                            <div class="tools"><a href="javascript:void(0);"
                                                    class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a>
                                            </div>
                                        </div>
                                        <div class="cm-content-body form excerpt">
                                            <div class="card-body">
                                                <select id="multi-value-select" class="form-control"
                                                    wire:model="selectedTags" multiple>
                                                    @foreach($this->tags as $tag)
                                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="mt-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control"
                                                            wire:model.defer="newTagName" placeholder="New tag name">
                                                        <span class="input-group-text" wire:click="addTag"
                                                            style="cursor:pointer;">Add New</span>
                                                    </div>
                                                    @error('newTagName') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Featured Image -->
                                    <div class="filter cm-content-box box-primary">
                                        <div class="content-title">
                                            <div class="cpa">Featured Image</div>
                                            <div class="tools"><a href="javascript:void(0);"
                                                    class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a>
                                            </div>
                                        </div>
                                        <div class="cm-content-body publish-content form excerpt">
                                            <div class="card-body">
                                                <div class="avatar-upload d-flex align-items-center">
                                                    <div class="position-relative">
                                                        <div class="avatar-preview">
                                                            @if($existing_featured_image && !$featured_image)
                                                                <div
                                                                    style="width:150px; height:150px; background-image: url('{{ asset('storage/' . $existing_featured_image) }}'); background-size:cover; background-position:center; border-radius:8px;">
                                                                </div>
                                                            @elseif($featured_image)
                                                                <div
                                                                    style="width:150px; height:150px; background-image: url('{{ $featured_image->temporaryUrl() }}'); background-size:cover; background-position:center; border-radius:8px;">
                                                                </div>
                                                            @else
                                                                <div
                                                                    style="width:150px; height:150px; background-color:#f0f0f0; display:flex; align-items:center; justify-content:center; border-radius:8px;">
                                                                    <span class="text-muted">No image</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="change-btn d-flex align-items-center flex-wrap mt-2">
                                                            <input type="file" id="imageUpload" class="d-none"
                                                                wire:model="featured_image"
                                                                accept=".png,.jpg,.jpeg,.gif,.webp">
                                                            <label for="imageUpload" class="btn btn-light ms-0">Choose
                                                                Image</label>
                                                            @if($featured_image) <span
                                                            class="ms-2 text-success">Uploaded</span> @endif
                                                        </div>
                                                        @error('featured_image') <span
                                                        class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endcanany

                {{-- ══════════════════════════════════════════════════════════════════
                COMMENT MODERATION — inline, only on edit + only with Edit Posts
                ══════════════════════════════════════════════════════════════════ --}}
                @if($postSlug && Auth::user()->can('Edit Posts'))
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="filter cm-content-box box-primary">
                                <div class="content-title">
                                    <div class="cpa">
                                        <i class="fa-solid fa-comments me-1"></i>Comments
                                        <span class="badge bg-primary ms-2">{{ $this->comments->count() }}</span>
                                        @if($this->pendingCommentsCount > 0)
                                            <span class="badge bg-warning ms-1">
                                                {{ $this->pendingCommentsCount }} pending
                                            </span>
                                        @endif
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:void(0);" class="expand SlideToolHeader">
                                            <i class="fal fa-angle-down"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="cm-content-body form excerpt">
                                    <div class="card-body">
                                        @if($this->comments->isEmpty())
                                            <div class="text-center text-muted py-4">
                                                <i class="fa-solid fa-comments fa-2x d-block mb-2"></i>
                                                No comments on this post yet.
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle comments-table"
                                                    style="table-layout: fixed; width: 100%;">
                                                    <colgroup>
                                                        <col style="width: 24%;">
                                                        <col style="width: 36%;">
                                                        <col style="width: 10%;">
                                                        <col style="width: 15%;">
                                                        <col style="width: 15%;">
                                                    </colgroup>
                                                    <thead>
                                                        <tr>
                                                            <th>Author</th>
                                                            <th>Comment</th>
                                                            <th>Status</th>
                                                            <th>Submitted</th>
                                                            <th class="text-end">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($this->comments as $comment)
                                                            <tr wire:key="inline-comment-{{ $comment->id }}">
                                                                <td class="align-top">
                                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                                        <img src="{{ $comment->gravatar }}"
                                                                            alt="{{ $comment->author_name }}"
                                                                            class="rounded-circle flex-shrink-0" width="32"
                                                                            height="32">
                                                                        <div class="min-w-0 flex-grow-1">
                                                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                                <span class="fw-semibold text-truncate"
                                                                                    title="{{ $comment->author_name }}">
                                                                                    {{ $comment->author_name }}
                                                                                </span>
                                                                                @if($comment->is_guest)
                                                                                    <span class="badge bg-secondary"
                                                                                        title="Anonymous visitor">
                                                                                        <i
                                                                                            class="fa-solid fa-user-secret me-1"></i>Guest
                                                                                    </span>
                                                                                @else
                                                                                    <span class="badge bg-primary"
                                                                                        title="Registered user">
                                                                                        <i
                                                                                            class="fa-solid fa-circle-check me-1"></i>Registered
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                            <small class="text-muted d-block text-truncate"
                                                                                title="{{ $comment->author_email }}">
                                                                                {{ $comment->author_email ?? '—' }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                    @if($comment->ip_address)
                                                                        <small class="text-muted d-block">
                                                                            <i class="fa-solid fa-network-wired me-1"></i>IP:
                                                                            {{ $comment->ip_address }}
                                                                        </small>
                                                                    @endif
                                                                    @if($comment->user_id && $comment->user)
                                                                        <a wire:navigate.hover
                                                                            href="{{ route('users.profile', $comment->user->id) }}"
                                                                            class="small text-decoration-none">
                                                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>
                                                                            View profile
                                                                        </a>
                                                                    @endif
                                                                </td>

                                                                <td class="align-top comment-cell">
                                                                    @if($comment->parent_id)
                                                                        <span class="badge bg-light text-dark mb-1">
                                                                            <i class="fa-solid fa-reply me-1"></i>Reply
                                                                        </span>
                                                                    @endif
                                                                    <div class="comment-text"
                                                                        title="{{ strip_tags($comment->body) }}">
                                                                        {{ \Illuminate\Support\Str::limit(strip_tags($comment->body), 200) }}
                                                                    </div>
                                                                </td>

                                                                <td class="align-top">
                                                                    @if($comment->is_verified)
                                                                        <span class="badge bg-success">Approved</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Pending</span>
                                                                    @endif
                                                                </td>

                                                                <td class="align-top text-nowrap">
                                                                    {{ $comment->created_at->format('d M, Y') }}
                                                                    <small class="text-muted d-block">
                                                                        {{ $comment->created_at->diffForHumans() }}
                                                                    </small>
                                                                </td>

                                                                <td class="align-top text-end text-nowrap">
                                                                    @if($comment->is_verified)
                                                                        <button type="button"
                                                                            class="btn btn-outline-secondary btn-sm content-icon"
                                                                            wire:click="unapproveComment({{ $comment->id }})"
                                                                            title="Unapprove">
                                                                            <i class="fa-solid fa-circle-xmark"></i>
                                                                        </button>
                                                                    @else
                                                                        <button type="button"
                                                                            class="btn btn-success btn-sm content-icon"
                                                                            wire:click="approveComment({{ $comment->id }})"
                                                                            title="Approve">
                                                                            <i class="fa-solid fa-check"></i>
                                                                        </button>
                                                                    @endif

                                                                    <button type="button"
                                                                        class="btn btn-warning btn-sm content-icon"
                                                                        wire:click="openCommentEdit({{ $comment->id }})"
                                                                        title="Edit">
                                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                                    </button>

                                                                    <button type="button" class="btn btn-danger btn-sm content-icon"
                                                                        wire:click="deleteComment({{ $comment->id }})"
                                                                        wire:confirm="Delete this comment? Replies will also be deleted."
                                                                        title="Delete">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ══════════════════ EDIT COMMENT MODAL ══════════════════ --}}
                    @if($showCommentEditModal)
                        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fa-solid fa-pen-to-square me-2"></i>Edit Comment
                                        </h5>
                                        <button type="button" class="btn-close"
                                            wire:click="$set('showCommentEditModal', false)"></button>
                                    </div>
                                    <form wire:submit.prevent="saveCommentEdit">
                                        <div class="modal-body">
                                            <label class="form-label">Comment Body</label>
                                            <textarea class="form-control @error('editCommentBody') is-invalid @enderror"
                                                rows="8" wire:model.defer="editCommentBody"></textarea>
                                            @error('editCommentBody')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                wire:click="$set('showCommentEditModal', false)">Cancel</button>
                                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                                <span wire:loading.remove>Save Changes</span>
                                                <span wire:loading><i class="fa fa-spinner fa-spin"></i></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                {{-- ══════════════════ END COMMENT MODERATION ══════════════════ --}}

            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- ═══════════════════════════════════════════════════════════════════
    EasyMDE — CSS (jsDelivr primary, unpkg fallback)
    ═══════════════════════════════════════════════════════════════════ --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css"
        onerror="this.onerror=null;this.href='https://unpkg.com/easymde/dist/easymde.min.css';">

    <style>
        /* ─── EasyMDE theme ────────────────────────────────────── */
        .EasyMDEContainer {
            border-radius: 8px;
            overflow: hidden;
        }

        .EasyMDEContainer .CodeMirror {
            border: 1px solid #ced4da;
            border-radius: 0 0 8px 8px;
            font-family: 'JetBrains Mono', 'Fira Code', Consolas, Monaco, monospace;
            font-size: 14px;
            line-height: 1.65;
            min-height: 420px;
            padding: 12px 14px;
            background: #fdfdfd;
        }

        .EasyMDEContainer .CodeMirror-focused {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .15);
        }

        .EasyMDEContainer .editor-toolbar {
            background: #f8f9fa;
            border: 1px solid #ced4da;
            border-bottom: 0;
            border-radius: 8px 8px 0 0;
            padding: 4px 8px;
        }

        .EasyMDEContainer .editor-toolbar button {
            color: #495057;
            border-radius: 4px;
        }

        .EasyMDEContainer .editor-toolbar button:hover,
        .EasyMDEContainer .editor-toolbar button.active {
            background: #e9ecef;
            border-color: transparent;
        }

        .EasyMDEContainer .editor-toolbar i.separator {
            border-left-color: #ced4da;
            border-right-color: transparent;
        }

        .EasyMDEContainer .editor-preview,
        .EasyMDEContainer .editor-preview-side {
            background: #fff;
            padding: 18px 22px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 15px;
            line-height: 1.7;
            color: #212529;
        }

        .EasyMDEContainer .editor-preview h1,
        .EasyMDEContainer .editor-preview-side h1 {
            font-size: 1.9rem;
            margin-top: 1.4rem;
        }

        .EasyMDEContainer .editor-preview h2,
        .EasyMDEContainer .editor-preview-side h2 {
            font-size: 1.5rem;
            margin-top: 1.2rem;
        }

        .EasyMDEContainer .editor-preview h3,
        .EasyMDEContainer .editor-preview-side h3 {
            font-size: 1.25rem;
            margin-top: 1rem;
        }

        .EasyMDEContainer .editor-preview pre,
        .EasyMDEContainer .editor-preview-side pre {
            background: #f4f4f6;
            padding: 12px 16px;
            border-radius: 6px;
            overflow-x: auto;
        }

        .EasyMDEContainer .editor-preview blockquote,
        .EasyMDEContainer .editor-preview-side blockquote {
            border-left: 4px solid #0d6efd;
            padding-left: 14px;
            color: #495057;
            margin: 1rem 0;
        }

        .EasyMDEContainer .editor-preview img,
        .EasyMDEContainer .editor-preview-side img {
            max-width: 100%;
            border-radius: 6px;
        }

        .EasyMDEContainer .editor-statusbar {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 6px 12px;
            color: #6c757d;
            font-size: 12px;
        }

        /* ─── Comments table: prevent long strings breaking layout ─── */
        .comments-table {
            table-layout: fixed;
            width: 100%;
        }

        .comments-table td,
        .comments-table th {
            vertical-align: top;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .comments-table .comment-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.4;
        }

        .comments-table .min-w-0 {
            min-width: 0;
        }
    </style>

    {{-- ═══════════════════════════════════════════════════════════════════
    EasyMDE — JS (jsDelivr primary, unpkg fallback)
    ═══════════════════════════════════════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"
        onerror="(function(){var s=document.createElement('script');s.src='https://unpkg.com/easymde/dist/easymde.min.js';document.head.appendChild(s);})();"></script>

    <script>
        // ─── Toggle Screen Options ─────────────────────────────────────
        function toggleScreenOptions() {
            const el = document.getElementById('screenOptions');
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }

        // ═══════════════════════════════════════════════════════════════
        //  EasyMDE init — bulletproof against Livewire 3/4 timing
        // ═══════════════════════════════════════════════════════════════

        let markdownEditorInstance = null;

        function destroyMarkdownEditor() {
            const el = document.getElementById('markdown-editor');
            if (!el) return;
            if (el.__easyMDE) {
                try { el.__easyMDE.toTextArea(); } catch (e) { }
                delete el.__easyMDE;
            }
            markdownEditorInstance = null;
        }

        function initMarkdownEditor() {
            const el = document.getElementById('markdown-editor');
            if (!el) return; // textarea not on page yet

            if (typeof EasyMDE === 'undefined') {
                // CDN not ready or failed — retry shortly
                setTimeout(initMarkdownEditor, 100);
                return;
            }

            if (el.__easyMDE) return; // already initialised

            try {
                markdownEditorInstance = new EasyMDE({
                    element: el,
                    initialValue: el.value || '',
                    spellChecker: false,
                    autofocus: false,
                    autoDownloadFontAwesome: false,
                    placeholder: 'Write your post here… Markdown is supported.',
                    minHeight: '420px',
                    autoRefresh: { delay: 300 },
                    toolbar: [
                        'bold', 'italic', 'heading-1', 'heading-2', 'heading-3', '|',
                        'quote', 'unordered-list', 'ordered-list', '|',
                        'link', 'image', 'code', 'horizontal-rule', 'table', '|',
                        'preview', 'side-by-side', 'fullscreen', '|',
                        'undo', 'redo', '|', 'guide'
                    ],
                    renderingConfig: { singleLineBreaks: false, codeSyntaxHighlighting: true },
                    status: ['lines', 'words', 'cursor'],
                    autosave: {
                        enabled: true,
                        uniqueId: 'post-editor-' + @json($postSlug ?? 'new'),
                        delay: 3000,
                        text: 'Autosaved: ',
                    },
                    promptURLs: true,
                    parsingConfig: { allowAtxHeaderWithoutSpace: true },
                });

                el.__easyMDE = markdownEditorInstance;

                // ─── Sync → Livewire on every change ────────────────
                markdownEditorInstance.codemirror.on('change', () => {
                    @this.set('content', markdownEditorInstance.value());
                });

                // ─── Cmd/Ctrl+S → save the post ─────────────────────
                markdownEditorInstance.codemirror.setOption('extraKeys', {
                    'Cmd-S': () => { @this.call('save'); return false; },
                    'Ctrl-S': () => { @this.call('save'); return false; },
                });

                console.log('[EasyMDE] initialised ✓');
            } catch (err) {
                console.error('[EasyMDE] init failed:', err);
                el.style.display = 'block'; // reveal raw textarea as fallback
            }
        }

        // Fire on every lifecycle event we can think of
        ['DOMContentLoaded', 'load', 'livewire:init', 'livewire:initialized', 'livewire:navigated']
            .forEach(evt => document.addEventListener(evt, () => setTimeout(initMarkdownEditor, 50)));

        // Already past DOMContentLoaded?
        if (document.readyState !== 'loading') {
            setTimeout(initMarkdownEditor, 50);
        }

        // MutationObserver fallback: watch for the textarea appearing
        (function watchForEditor() {
            if (document.getElementById('markdown-editor')) { initMarkdownEditor(); return; }
            const mo = new MutationObserver(() => {
                if (document.getElementById('markdown-editor')) {
                    mo.disconnect();
                    initMarkdownEditor();
                }
            });
            mo.observe(document.body, { childList: true, subtree: true });
            setTimeout(() => mo.disconnect(), 30000);
        })();

        document.addEventListener('livewire:navigating', destroyMarkdownEditor);

        window.initMarkdownEditor = initMarkdownEditor;
        window.destroyMarkdownEditor = destroyMarkdownEditor;
    </script>
@endpush
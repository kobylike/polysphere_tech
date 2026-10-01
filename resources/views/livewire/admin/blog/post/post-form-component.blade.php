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

                                <!-- CKEditor Content -->
                                <div class="card h-auto">
                                    <div class="card-body pt-3">
                                        <div wire:ignore>
                                            <textarea id="ckeditor" wire:model.live.debounce.1000ms="content"
                                                style="display:none;"></textarea>
                                        </div>
                                    </div>
                                </div>

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
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function toggleScreenOptions() {
            const el = document.getElementById('screenOptions');
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }

        // ═══════════════════════════════════════════════════════════════════
        // CKEDITOR — one instance, one Livewire binding, guaranteed.
        //
        // Duplicates came from THREE callers hitting ClassicEditor.create()
        // on #ckeditor: cms.js, livewire:initialized, and livewire:navigated.
        // We patch ClassicEditor.create() so any second call returns the
        // already-existing instance — making duplicates structurally
        // impossible. Then we ALWAYS call create() ourselves, so we're
        // guaranteed a handle on the instance (whether we made it or cms.js
        // did), and we attach the Livewire content-sync listener to it.
        //
        // The previous version bailed out early if el.ckeditorInstance was
        // already set — which is exactly what happened when cms.js won the
        // race — so no change:data listener was ever attached and content
        // never reached the Livewire component. That's fixed here: we never
        // bail, we just get handed the same instance back.
        // ═══════════════════════════════════════════════════════════════════

        (function installCKEditorDuplicateGuard() {
            function patch() {
                if (!window.ClassicEditor) return false;
                if (window.ClassicEditor.__duplicateGuardInstalled) return true;

                const originalCreate = window.ClassicEditor.create.bind(window.ClassicEditor);

                window.ClassicEditor.create = function (element, config) {
                    const isTarget = element instanceof HTMLElement && element.id === 'ckeditor';

                    if (isTarget && element.ckeditorInstance) {
                        return Promise.resolve(element.ckeditorInstance);
                    }

                    // cms.js-created editor that never tagged the textarea:
                    // detect the wrapper and tear it down so we end up with
                    // exactly one editor we actually have a handle on.
                    if (isTarget) {
                        const sib = element.nextElementSibling;
                        if (sib && sib.classList && sib.classList.contains('ck-editor')) {
                            sib.remove();
                        }
                    }

                    return originalCreate(element, config).then(editor => {
                        if (isTarget) element.ckeditorInstance = editor;
                        return editor;
                    });
                };

                window.ClassicEditor.__duplicateGuardInstalled = true;
                return true;
            }

            if (!patch()) {
                const iv = setInterval(() => {
                    if (patch()) clearInterval(iv);
                }, 10);
                setTimeout(() => clearInterval(iv), 10000);
            }
        })();

        // ─── Our own init ──────────────────────────────────────────────────

        const ckConfig = {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', 'blockQuote', '|',
                'imageUpload', '|',
                'undo', 'redo'
            ],
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:alignLeft',
                    'imageStyle:alignCenter',
                    'imageStyle:alignRight'
                ],
                styles: ['alignLeft', 'alignCenter', 'alignRight']
            },
            simpleUpload: {
                uploadUrl: '{{ route('ckeditor.upload') }}',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }
        };

        function attachLivewireContentSync(editor) {
            if (editor.__lwContentSynced) return;
            editor.__lwContentSynced = true;

            // Push editor content → Livewire on every change.
            editor.model.document.on('change:data', () => {
                @this.set('content', editor.getData());
            });

            // Seed editor with whatever content Livewire already has.
            if (@this.content) {
                editor.setData(@this.content);
            }
        }

        function initCKEditor() {
            const el = document.querySelector('#ckeditor');
            if (!el) return;

            // ClassicEditor may not have loaded yet. Poll briefly until it is.
            if (typeof ClassicEditor === 'undefined') {
                setTimeout(initCKEditor, 50);
                return;
            }

            // Thanks to the patch, this returns the existing instance if one
            // exists — either ours from a prior call, or cms.js's. We always
            // end up with the real instance in .then(), so we can always
            // attach the Livewire content-sync listener to it. No early bail.
            ClassicEditor.create(el, ckConfig)
                .then(editor => {
                    el.ckeditorInstance = editor;
                    attachLivewireContentSync(editor);
                })
                .catch(err => console.error('CKEditor init error:', err));
        }

        // ─── Listen to Livewire events ─────────────────────────────────────

        document.addEventListener('livewire:initialized', initCKEditor);
        document.addEventListener('livewire:navigated', initCKEditor);

        // Kick off immediately too, in case Livewire's events already fired
        // before this script was parsed (both load near each other).
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCKEditor);
        } else {
            initCKEditor();
        }

        // Tear down on navigation away: destroy the instance AND strip any
        // wrapper nodes, so the guard's create-patch starts fresh next page.
        document.addEventListener('livewire:navigating', () => {
            const el = document.querySelector('#ckeditor');
            if (!el) return;

            const inst = el.ckeditorInstance;
            try { delete el.ckeditorInstance; } catch (e) { el.ckeditorInstance = null; }

            if (inst && typeof inst.destroy === 'function') {
                try { inst.destroy(); } catch (e) { }
            }

            // Safety net: remove any stray .ck-editor wrappers next to the
            // textarea so nothing lingers into the next page.
            let sib = el.nextElementSibling;
            while (sib && sib.classList && sib.classList.contains('ck-editor')) {
                const next = sib.nextElementSibling;
                sib.remove();
                sib = next;
            }
            el.style.display = 'none';
        });
    </script>
@endpush
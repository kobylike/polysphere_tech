<div>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">CMS</a></li>
            <li class="breadcrumb-item active">
                <a href="javascript:void(0)">
                    {{ $projectId ? 'Edit' : 'Add' }} Project
                </a>
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <!-- Action buttons -->
                <div class="mb-3">
                    <ul class="d-flex align-items-center flex-wrap">
                        <li>
                            <a wire:navigate.hover href="{{ route('admin.projects.index') }}" class="btn btn-primary">
                                <i class="fa-solid fa-list me-1"></i> Project List
                            </a>
                        </li>
                        <li>
                            <a wire:navigate.hover href="{{ route('admin.projects.create') }}"
                                class="btn btn-primary mx-1">
                                <i class="fa-solid fa-plus me-1"></i> Add Project
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Session messages -->
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Main Form -->
                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-xl-8">
                            <!-- Title -->
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control w-50" wire:model.live.debounce.500ms="title"
                                    placeholder="Enter project title">
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- CKEditor Content -->
                            <div class="card h-auto">
                                <div class="card-body pt-3">
                                    <div wire:ignore>
                                        <textarea id="ckeditor" wire:model.live.debounce.1000ms="content"
                                            style="display:none;"></textarea>
                                    </div>
                                    <div class="form-text mt-2">Write the detailed description of the project.</div>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <div class="filter cm-content-box box-primary">
                                <div class="content-title">
                                    <div class="cpa">Excerpt</div>
                                    <div class="tools">
                                        <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                class="fal fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div class="cm-content-body publish-content form excerpt">
                                    <div class="card-body">
                                        <label class="form-label">Excerpt</label>
                                        <textarea class="form-control" rows="3" wire:model="excerpt"
                                            placeholder="Short summary of the project"></textarea>
                                        <div class="form-text">A brief overview that appears on the project listing
                                            page.</div>
                                        @error('excerpt') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Slug -->
                            <div class="filter cm-content-box box-primary">
                                <div class="content-title">
                                    <div class="cpa">Slug</div>
                                    <div class="tools">
                                        <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                class="fal fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div class="cm-content-body form excerpt">
                                    <div class="card-body">
                                        <label class="form-label">Slug</label>
                                        <input type="text" class="form-control" wire:model="slug"
                                            placeholder="URL-friendly version of the title">
                                        @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                                        <div class="form-text">Automatically generated from the title.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="filter cm-content-box box-primary">
                                <div class="content-title">
                                    <div class="cpa">SEO</div>
                                    <div class="tools">
                                        <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                class="fal fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div class="cm-content-body form excerpt">
                                    <div class="card-body">
                                        <label class="form-label">SEO Title</label>
                                        <input type="text" class="form-control mb-3" wire:model="seo_title"
                                            placeholder="SEO Title (optional)">
                                        <div class="row">
                                            <div class="col-xl-6 col-sm-6">
                                                <label class="form-label">Keywords</label>
                                                <input type="text" class="form-control mb-sm-0 mb-3"
                                                    wire:model="seo_keywords" placeholder="Meta Keywords">
                                            </div>
                                            <div class="col-xl-6 col-sm-6">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" rows="3" wire:model="seo_description"
                                                    placeholder="Meta Description"></textarea>
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
                                        <div class="tools">
                                            <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a>
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
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    {{ $projectId ? 'Update' : 'Publish' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Service</div>
                                        <div class="tools">
                                            <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            <select class="form-select" wire:model="service_id">
                                                <option value="">Select a service</option>
                                                @foreach($this->services as $svc)
                                                    <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('service_id') <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Project Metadata (Year, Client, Company) -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Project Metadata</div>
                                        <div class="tools">
                                            <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            <!-- Year Range -->
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label">Start Year</label>
                                                    <select class="form-select" wire:model="start_year">
                                                        <option value="">Select year</option>
                                                        @php
                                                            $currentYear = date('Y');
                                                            $years = range(2000, $currentYear + 5);
                                                        @endphp
                                                        @foreach($years as $year)
                                                            <option value="{{ $year }}">{{ $year }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('start_year') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">End Year</label>
                                                    <select class="form-select" wire:model="end_year">
                                                        <option value="">Select year</option>
                                                        @foreach($years as $year)
                                                            <option value="{{ $year }}">{{ $year }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('end_year') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Client -->
                                            <div class="mb-3">
                                                <label class="form-label">Client</label>
                                                <input type="text" class="form-control" wire:model="client"
                                                    placeholder="e.g. Master Service">
                                                @error('client') <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Company -->
                                            <div class="mb-3">
                                                <label class="form-label">Company</label>
                                                <input type="text" class="form-control" wire:model="company"
                                                    placeholder="e.g. W3C">
                                                @error('company') <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Video (URL + File Upload with toggle) -->
                                <div class="filter cm-content-box box-primary"
                                    wire:key="video-section-{{ $videoInputType }}">
                                    <div class="content-title">
                                        <div class="cpa">Video</div>
                                        <div class="tools">
                                            <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            <!-- Choose input type -->
                                            <div class="mb-3">
                                                <label class="form-label">Video Source</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" id="videoTypeUrl"
                                                            value="url" wire:model.live="videoInputType">
                                                        <label class="form-check-label" for="videoTypeUrl">URL</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" id="videoTypeFile"
                                                            value="file" wire:model.live="videoInputType">
                                                        <label class="form-check-label" for="videoTypeFile">Upload
                                                            File</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Video URL -->
                                            @if($videoInputType === 'url')
                                                <div class="mb-3" wire:key="video-url-field">
                                                    <label class="form-label">Video URL</label>
                                                    <input type="url" class="form-control" wire:model="video_url"
                                                        placeholder="https://www.youtube.com/watch?v=...">
                                                    @error('video_url') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                    <div class="form-text">YouTube or Vimeo URL.</div>
                                                </div>
                                            @endif

                                            <!-- Video File Upload -->
                                            @if($videoInputType === 'file')
                                                <div class="mb-3" wire:key="video-file-field">
                                                    <label class="form-label">Upload Video File</label>
                                                    <input type="file" class="form-control" wire:model="video_file"
                                                        accept=".mp4,.mov,.avi,.webm">
                                                    @error('video_file') <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                    <div class="form-text">MP4, MOV, AVI, WebM (max 50MB).</div>
                                                    @if($existing_video_file)
                                                        <div class="mt-2">
                                                            <span class="text-success">Current video:
                                                                {{ basename($existing_video_file) }}</span>
                                                            <button type="button" class="btn btn-sm btn-danger ms-2"
                                                                wire:click="removeVideoFile">Remove</button>
                                                        </div>
                                                    @endif
                                                    @if($video_file)
                                                        <span class="text-success ms-2">Uploaded:
                                                            {{ $video_file->getClientOriginalName() }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Featured Image (1170x550) -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Featured Image <span class="text-muted">(1170x550)</span></div>
                                        <div class="tools">
                                            <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a>
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
                                                        <input type="file" id="featuredImageUpload" class="d-none"
                                                            wire:model="featured_image"
                                                            accept=".png,.jpg,.jpeg,.gif,.webp">
                                                        <label for="featuredImageUpload"
                                                            class="btn btn-light ms-0">Choose Image</label>
                                                        @if($featured_image) <span
                                                        class="ms-2 text-success">Uploaded</span> @endif
                                                    </div>
                                                    @error('featured_image') <span
                                                    class="text-danger">{{ $message }}</span> @enderror
                                                    <div class="form-text mt-2">Recommended size: 1170x550 pixels.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thumbnail Image (770x350) -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Video Thumbnail <span class="text-muted">(770x350)</span></div>
                                        <div class="tools">
                                            <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            <div class="avatar-upload d-flex align-items-center">
                                                <div class="position-relative">
                                                    <div class="avatar-preview">
                                                        @if($existing_thumbnail_image && !$thumbnail_image)
                                                            <div
                                                                style="width:150px; height:150px; background-image: url('{{ asset('storage/' . $existing_thumbnail_image) }}'); background-size:cover; background-position:center; border-radius:8px;">
                                                            </div>
                                                        @elseif($thumbnail_image)
                                                            <div
                                                                style="width:150px; height:150px; background-image: url('{{ $thumbnail_image->temporaryUrl() }}'); background-size:cover; background-position:center; border-radius:8px;">
                                                            </div>
                                                        @else
                                                            <div
                                                                style="width:150px; height:150px; background-color:#f0f0f0; display:flex; align-items:center; justify-content:center; border-radius:8px;">
                                                                <span class="text-muted">No image</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="change-btn d-flex align-items-center flex-wrap mt-2">
                                                        <input type="file" id="thumbnailImageUpload" class="d-none"
                                                            wire:model="thumbnail_image"
                                                            accept=".png,.jpg,.jpeg,.gif,.webp">
                                                        <label for="thumbnailImageUpload"
                                                            class="btn btn-light ms-0">Choose Image</label>
                                                        @if($thumbnail_image) <span
                                                        class="ms-2 text-success">Uploaded</span> @endif
                                                    </div>
                                                    @error('thumbnail_image') <span
                                                    class="text-danger">{{ $message }}</span> @enderror
                                                    <div class="form-text mt-2">Recommended size: 770x350 pixels.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Images (up to 2, 428x250) -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Additional Images <span class="text-muted">(max 2,
                                                428x250)</span></div>
                                        <div class="tools">
                                            <a href="javascript:void(0);" class="expand SlideToolHeader"><i
                                                    class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            @for($i = 0; $i < 2; $i++)
                                                <div class="mb-3">
                                                    <label class="form-label">Image {{ $i + 1 }} <span
                                                            class="text-muted">(optional)</span></label>
                                                    <input type="file" class="form-control"
                                                        wire:model="additional_images.{{ $i }}"
                                                        accept=".png,.jpg,.jpeg,.gif,.webp">
                                                    @error('additional_images.' . $i) <span
                                                    class="text-danger">{{ $message }}</span> @enderror
                                                    @if(isset($existing_additional_images[$i]))
                                                        <div class="mt-1">
                                                            <span class="text-success">Current image:
                                                                {{ basename($existing_additional_images[$i]) }}</span>
                                                            <button type="button" class="btn btn-sm btn-danger ms-2"
                                                                wire:click="removeAdditionalImage({{ $i }})">Remove</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endfor
                                            @error('additional_images') <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <div class="form-text">Recommended size: 428x250 pixels.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function toggleScreenOptions() {
            const el = document.getElementById('screenOptions');
            if (el) {
                el.style.display = el.style.display === 'none' ? 'block' : 'none';
            }
        }

        // ──────────────────────────────────────────────────────────────
        // CKEditor – Singleton pattern using a global flag
        // ──────────────────────────────────────────────────────────────

        (function () {
            // This flag ensures only one editor is ever created.
            window.ckeditorInitialized = false;

            function initCKEditor() {
                const editorElement = document.querySelector('#ckeditor');
                if (!editorElement) return;

                // If we already have an instance attached to this element, skip.
                if (editorElement.ckeditorInstance) return;

                // Also check the global flag to be safe.
                if (window.ckeditorInitialized) {
                    // If the global flag is true but the element has no instance,
                    // we might have a stale flag. Reset it and proceed.
                    window.ckeditorInitialized = false;
                }

                if (typeof ClassicEditor === 'undefined') return;

                ClassicEditor
                    .create(editorElement, {
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
                            styles: [
                                'alignLeft', 'alignCenter', 'alignRight'
                            ]
                        },
                        simpleUpload: {
                            uploadUrl: '{{ route('ckeditor.upload') }}',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }
                    })
                    .then(editor => {
                        editorElement.ckeditorInstance = editor;
                        window.ckeditorInitialized = true;

                        editor.model.document.on('change:data', () => {
                            @this.set('content', editor.getData());
                        });
                        if (@this.content) {
                            editor.setData(@this.content);
                        }
                    })
                    .catch(error => console.error('CKEditor error:', error));
            }

            // Run on initial page load and after Livewire navigation.
            document.addEventListener('livewire:initialized', initCKEditor);

            // Clean up before navigating away.
            document.addEventListener('livewire:navigating', function () {
                const editorElement = document.querySelector('#ckeditor');
                if (editorElement && editorElement.ckeditorInstance) {
                    editorElement.ckeditorInstance.destroy().then(() => {
                        editorElement.ckeditorInstance = null;
                        window.ckeditorInitialized = false;
                    }).catch(() => {
                        editorElement.ckeditorInstance = null;
                        window.ckeditorInitialized = false;
                    });
                }
            });

            // Re-initialize after navigation if not already done.
            document.addEventListener('livewire:navigated', function () {
                const editorElement = document.querySelector('#ckeditor');
                if (editorElement && !editorElement.ckeditorInstance && !window.ckeditorInitialized) {
                    initCKEditor();
                }
            });

            // Also re-run on any Livewire update that might replace the DOM element.
            document.addEventListener('livewire:update', function () {
                // If the editor element exists but we don't have an instance, re-init.
                const editorElement = document.querySelector('#ckeditor');
                if (editorElement && !editorElement.ckeditorInstance && !window.ckeditorInitialized) {
                    initCKEditor();
                }
            });
        })();
    </script>
@endpush
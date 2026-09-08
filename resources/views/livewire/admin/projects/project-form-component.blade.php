<div>
    <div class="card">
        <div class="card-header">
            <h4 >{{ $projectId ? 'Edit Project' : 'Create New Project' }}</h4>
        </div>
        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit.prevent="save" wire:key="project-form-{{ $projectId ?? 'new' }}">
                <div class="row">
                    {{-- ══════════════════════════════════════════════════════
                    LEFT COLUMN — mirrors the top-to-bottom flow of the
                    project-details template.
                    ══════════════════════════════════════════════════════ --}}
                    <div class="col-md-8">

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" class="form-control" wire:model="title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">
                                Slug
                                @if ($slugManuallyEdited)
                                    <span class="badge bg-secondary">Manually edited</span>
                                    <button type="button" class="btn btn-link btn-sm p-0 ms-1"
                                        wire:click="resetSlugToTitle">Reset to title</button>
                                @else
                                    <span class="badge bg-light text-dark border">Auto-generated</span>
                                @endif
                            </label>
                            <input type="text" id="slug" class="form-control" wire:model.live.debounce.400ms="slug">
                            <div class="form-text">
                                The slug follows the title automatically until you edit it yourself.
                            </div>
                            @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Service (drives the "Category" shown on the project page) -->
                        <div class="mb-3">
                            <label for="service_id" class="form-label">Service (Category)</label>
                            <select id="service_id" class="form-select" wire:model="service_id">
                                <option value="">Select Service</option>
                                @foreach($this->services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                            @error('service_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <hr>

                        <!-- ─── "Here to know about this project" ─────────── -->
                        <h5 class="mb-3">Here to know about this project</h5>
                        <div class="mb-3">
                            <label for="editor" class="form-label">Overview</label>
                            <div wire:ignore>
                                <textarea id="editor" class="form-control" wire:model="content"></textarea>
                            </div>
                            @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Excerpt <span class="text-muted small">(used for
                                    project cards / listings)</span></label>
                            <textarea id="excerpt" class="form-control" wire:model="excerpt" rows="3"></textarea>
                            @error('excerpt') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <hr>

                        <!-- ─── "The Challenge Of Project" ─────────────────── -->
                        <h5 class="mb-3">The Challenge Of Project</h5>

                        <div class="mb-3">
                            <label for="challenge_content" class="form-label">Challenge description</label>
                            <textarea id="challenge_content" class="form-control" wire:model="challenge_content"
                                rows="4"></textarea>
                            @error('challenge_content') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="challenge_image" class="form-label">Challenge Image <span
                                        class="text-muted small">(min 428x250)</span></label>
                                <input type="file" id="challenge_image" class="form-control"
                                    wire:model="challenge_image">
                                @error('challenge_image') <span class="text-danger">{{ $message }}</span> @enderror

                                @if ($existing_challenge_image && !$challenge_image)
                                    <div class="mt-2 d-flex align-items-start gap-2">
                                        <img src="{{ asset('storage/' . $existing_challenge_image) }}" alt="Challenge image"
                                            style="max-width:100%; max-height:150px;">
                                        <button type="button" class="btn btn-danger btn-sm"
                                            wire:click="removeChallengeImage"
                                            wire:confirm="Remove this image?">Remove</button>
                                    </div>
                                @endif

                                @if ($challenge_image)
                                    <div class="mt-2">
                                        <img src="{{ $challenge_image->temporaryUrl() }}" alt="Preview"
                                            style="max-width:100%; max-height:150px;">
                                        <p class="text-muted small">New image preview</p>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Checklist Items <span class="text-muted small">(the ✓ list
                                        next to the image)</span></label>
                                @foreach ($challenge_features as $index => $feature)
                                    <div class="input-group mb-2" wire:key="challenge-feature-{{ $index }}">
                                        <input type="text" class="form-control" wire:model="challenge_features.{{ $index }}"
                                            placeholder="e.g. Technology Consultancy">
                                        <button type="button" class="btn btn-outline-danger"
                                            wire:click="removeChallengeFeature({{ $index }})">&times;</button>
                                    </div>
                                @endforeach
                                @error('challenge_features') <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                                @error('challenge_features.*') <span class="text-danger d-block">{{ $message }}</span>
                                @enderror

                                @if (count($challenge_features) < 10)
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        wire:click="addChallengeFeature">+ Add item</button>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <!-- ─── "The Final View Of Project" ────────────────── -->
                        <h5 class="mb-3">The Final View Of Project</h5>

                        <div class="mb-3">
                            <label for="final_view_content" class="form-label">Final view description</label>
                            <textarea id="final_view_content" class="form-control" wire:model="final_view_content"
                                rows="4"></textarea>
                            @error('final_view_content') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail_image" class="form-label">
                                Final View Image <span class="text-muted small">(min 770x350 — also used as the video
                                    poster)</span>
                            </label>
                            <input type="file" id="thumbnail_image" class="form-control" wire:model="thumbnail_image">
                            @error('thumbnail_image') <span class="text-danger">{{ $message }}</span> @enderror

                            @if ($existing_thumbnail_image && !$thumbnail_image)
                                <div class="mt-2 d-flex align-items-start gap-2">
                                    <img src="{{ asset('storage/' . $existing_thumbnail_image) }}" alt="Final view image"
                                        style="max-width:100%; max-height:150px;">
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="removeThumbnailImage"
                                        wire:confirm="Are you sure you want to remove this image?">Remove</button>
                                </div>
                                <p class="text-muted small">Current image</p>
                            @endif

                            @if ($thumbnail_image)
                                <div class="mt-2">
                                    <img src="{{ $thumbnail_image->temporaryUrl() }}" alt="Preview"
                                        style="max-width:100%; max-height:150px;">
                                    <p class="text-muted small">New image preview</p>
                                </div>
                            @endif
                        </div>

                        <!-- Video (plays on top of the Final View image) -->
                        <div class="mb-3">
                            <label class="form-label">Video</label>
                            <div class="btn-group w-100" role="group">
                                <button type="button"
                                    class="btn btn-outline-secondary {{ $videoInputType === 'url' ? 'active' : '' }}"
                                    wire:click="$set('videoInputType', 'url')">URL</button>
                                <button type="button"
                                    class="btn btn-outline-secondary {{ $videoInputType === 'file' ? 'active' : '' }}"
                                    wire:click="$set('videoInputType', 'file')">File</button>
                            </div>

                            @if ($videoInputType === 'url')
                                <input type="url" class="form-control mt-2" wire:model="video_url"
                                    placeholder="https://...">
                                @error('video_url') <span class="text-danger">{{ $message }}</span> @enderror
                                @if ($video_url)
                                    <div class="mt-1 text-muted small">Current URL: {{ $video_url }}</div>
                                @endif
                            @else
                                <input type="file" class="form-control mt-2" wire:model="video_file" accept="video/*">
                                @error('video_file') <span class="text-danger">{{ $message }}</span> @enderror
                                @if ($existing_video_file && !$video_file)
                                    <div class="mt-1 text-muted small">
                                        Current video file: {{ basename($existing_video_file) }}
                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeVideoFile"
                                            wire:confirm="Remove this video file?">Remove</button>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Additional / gallery images -->
                        <div class="mb-3">
                            <label for="additional_images" class="form-label">Project Gallery Images (max 2, min
                                428x250)</label>
                            <input type="file" id="additional_images" class="form-control"
                                wire:model="additional_images" multiple>
                            @error('additional_images.*') <span class="text-danger">{{ $message }}</span> @enderror
                            @error('additional_images') <span class="text-danger">{{ $message }}</span> @enderror

                            @if (!empty($existing_additional_images) && empty($additional_images))
                                <div class="mt-2 row g-2">
                                    @foreach($existing_additional_images as $index => $img)
                                        <div class="col-6 position-relative">
                                            <img src="{{ asset('storage/' . $img) }}" alt="Additional image"
                                                style="width:100%; height:auto; border-radius:4px;">
                                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                wire:click="removeAdditionalImage({{ $index }})"
                                                style="border-radius:50%; padding:0 6px;"
                                                wire:confirm="Remove this image?">×</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($additional_images)
                                <div class="mt-2 row g-2">
                                    @foreach($additional_images as $img)
                                        <div class="col-6">
                                            <img src="{{ $img->temporaryUrl() }}" alt="Preview"
                                                style="width:100%; height:auto; border-radius:4px;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════
                    RIGHT COLUMN — meta / sidebar fields.
                    ══════════════════════════════════════════════════════ --}}
                    <div class="col-md-4">

                        <!-- Featured Image (page banner) -->
                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Featured Image <span
                                    class="text-muted small">(banner, min 1170x550)</span></label>
                            <input type="file" id="featured_image" class="form-control" wire:model="featured_image">
                            @error('featured_image') <span class="text-danger">{{ $message }}</span> @enderror

                            @if ($existing_featured_image && !$featured_image)
                                <div class="mt-2 d-flex align-items-start gap-2">
                                    <img src="{{ asset('storage/' . $existing_featured_image) }}" alt="Featured image"
                                        style="max-width:100%; max-height:150px;">
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="removeFeaturedImage"
                                        wire:confirm="Are you sure you want to remove this image?">Remove</button>
                                </div>
                                <p class="text-muted small">Current image</p>
                            @endif

                            @if ($featured_image)
                                <div class="mt-2">
                                    <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview"
                                        style="max-width:100%; max-height:150px;">
                                    <p class="text-muted small">New image preview</p>
                                </div>
                            @endif
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" id="location" class="form-control" wire:model="location"
                                placeholder="e.g. New York, USA">
                            @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Client & Company -->
                        <div class="mb-3">
                            <label for="client" class="form-label">Client</label>
                            <input type="text" id="client" class="form-control" wire:model="client">
                            @error('client') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" id="company" class="form-control" wire:model="company">
                            @error('company') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Year Range -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="start_year" class="form-label">Start Year</label>
                                <input type="number" id="start_year" class="form-control" wire:model="start_year"
                                    min="2000" max="{{ date('Y') + 5 }}">
                                @error('start_year') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label for="end_year" class="form-label">End Year</label>
                                <input type="number" id="end_year" class="form-control" wire:model="end_year" min="2000"
                                    max="{{ date('Y') + 5 }}">
                                @error('end_year') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" class="form-select" wire:model="status">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="private">Private</option>
                                <option value="pending">Pending</option>
                                <option value="trash">Trash</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Visibility -->
                        <div class="mb-3">
                            <label for="visibility" class="form-label">Visibility</label>
                            <select id="visibility" class="form-select" wire:model="visibility">
                                <option value="public">Public</option>
                                <option value="password_protected">Password Protected</option>
                                <option value="private">Private</option>
                            </select>
                            @error('visibility') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Published At -->
                        <div class="mb-3">
                            <label for="published_at" class="form-label">Published At</label>
                            <input type="datetime-local" id="published_at" class="form-control"
                                wire:model="published_at">
                            @error('published_at') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <hr>

                        <!-- Company File download box -->
                        <div class="mb-3">
                            <label for="attachment" class="form-label">Company File <span
                                    class="text-muted small">(sidebar download button)</span></label>
                            <input type="file" id="attachment" class="form-control" wire:model="attachment">
                            @error('attachment') <span class="text-danger">{{ $message }}</span> @enderror

                            @if ($existing_attachment && !$attachment)
                                <div
                                    class="mt-2 d-flex align-items-center justify-content-between bg-primary text-white rounded p-2">
                                    <span>
                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                        {{ $existing_attachment_name ?? basename($existing_attachment) }}
                                        <small>({{ \App\Livewire\Admin\Projects\ProjectFormComponent::formatBytes($existing_attachment_size) }})</small>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-light" wire:click="removeAttachment"
                                        wire:confirm="Remove this file?">Remove</button>
                                </div>
                            @endif

                            @if ($attachment)
                                <div class="mt-1 text-muted small">
                                    New file selected: {{ $attachment->getClientOriginalName() }}
                                </div>
                            @endif
                        </div>

                        <hr>

                        <!-- SEO Fields -->
                        <h6>SEO</h6>
                        <div class="mb-3">
                            <label for="seo_title" class="form-label">SEO Title</label>
                            <input type="text" id="seo_title" class="form-control" wire:model="seo_title">
                            @error('seo_title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="seo_description" class="form-label">SEO Description</label>
                            <textarea id="seo_description" class="form-control" wire:model="seo_description"
                                rows="2"></textarea>
                            @error('seo_description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="seo_keywords" class="form-label">SEO Keywords</label>
                            <input type="text" id="seo_keywords" class="form-control" wire:model="seo_keywords">
                            @error('seo_keywords') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            {{ $projectId ? 'Update' : 'Create' }} Project
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ─── CKEditor – single instance, never reinitialised ────────────────
            if (window.ckEditorInstance) {
                const currentContent = @this.content;
                if (currentContent !== window.ckEditorInstance.getData()) {
                    window.ckEditorInstance.setData(currentContent);
                }
                return;
            }

            const textarea = document.getElementById('editor');
            if (!textarea) return;

            ClassicEditor
                .create(textarea, {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', 'blockQuote', '|',
                        'insertTable', '|',
                        'undo', 'redo'
                    ],
                })
                .then(editor => {
                    window.ckEditorInstance = editor;
                    editor.model.document.on('change:data', () => {
                        @this.set('content', editor.getData());
                    });
                })
                .catch(error => console.error('CKEditor error:', error));
        });

        // ─── After every Livewire update, ensure editor content matches Livewire ──
        document.addEventListener('livewire:updated', function () {
            const editor = window.ckEditorInstance;
            if (!editor) return;

            const currentContent = @this.content;
            if (currentContent !== editor.getData()) {
                editor.setData(currentContent);
            }
        });
    </script>
@endpush
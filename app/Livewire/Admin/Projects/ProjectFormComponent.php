<?php

namespace App\Livewire\Admin\Projects;

use App\Helpers\ActivityLogger;
use App\Models\Service;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Auth\Access\AuthorizationException;

#[Layout('layouts.users')]
class ProjectFormComponent extends Component
{
    use WithFileUploads;

    public $projectId = null;
    public $title = '';
    public $slug = '';
    public $content = '';
    public $excerpt = '';
    public $status = 'draft';
    public $visibility = 'public';
    public $published_at = null;
    public $seo_title = '';
    public $seo_description = '';
    public $seo_keywords = '';
    public $service_id = null;
    public $video_url = '';
    public $videoInputType = 'url';

    // Year range (start + end)
    public $start_year = '';
    public $end_year = '';

    // Client & Company
    public $client = '';
    public $company = '';

    // Image uploads
    public $featured_image = null;
    public $existing_featured_image = null;

    public $additional_images = [];
    public $existing_additional_images = [];

    public $thumbnail_image = null;
    public $existing_thumbnail_image = null;

    public $video_file = null;
    public $existing_video_file = null;

    public $custom_fields = [];

    protected function rules()
    {
        $uniqueRule = 'unique:projects,slug';
        if ($this->projectId) {
            $uniqueRule .= ',' . $this->projectId;
        }

        return [
            'title'         => 'required|string|max:255',
            'slug'          => ['required', 'string', 'max:255', $uniqueRule],
            'content'       => 'nullable|string',
            'excerpt'       => 'nullable|string|max:1000',
            'status'        => 'required|in:draft,published,private,pending,trash',
            'visibility'    => 'required|in:public,password_protected,private',
            'published_at'  => 'nullable|date',
            'seo_title'     => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords'  => 'nullable|string|max:255',
            'service_id'    => 'nullable|exists:services,id',
            'video_url'     => 'nullable|url|max:500|required_if:videoInputType,url',
            'video_file'    => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200|required_if:videoInputType,file',
            'start_year'    => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'end_year'      => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'client'        => 'nullable|string|max:255',
            'company'       => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:5120|dimensions:min_width=1170,min_height=550',
            'thumbnail_image' => 'nullable|image|max:2048|dimensions:min_width=770,min_height=350',
            'additional_images' => 'nullable|array|max:2',
            'additional_images.*' => 'image|max:5120|dimensions:min_width=428,min_height=250',
            'custom_fields' => 'nullable|array',
        ];
    }

    protected function messages()
    {
        return [
            'title.required' => 'The title is required.',
            'slug.unique'    => 'This slug is already taken.',
            'featured_image.dimensions' => 'Featured image must be at least 1170x550 pixels.',
            'thumbnail_image.dimensions' => 'Thumbnail must be at least 770x350 pixels.',
            'additional_images.*.dimensions' => 'Additional images must be at least 428x250 pixels.',
            'additional_images.max' => 'You can upload a maximum of 2 additional images.',
            'video_file.max' => 'Video file must not exceed 50MB.',
            'video_url.required_if' => 'Please enter a video URL.',
            'video_file.required_if' => 'Please upload a video file.',
            'start_year.min' => 'Start year must be at least 2000.',
            'end_year.min'   => 'End year must be at least 2000.',
            'start_year.max' => 'Start year cannot be too far in the future.',
            'end_year.max'   => 'End year cannot be too far in the future.',
        ];
    }

    // ─── Mount ──────────────────────────────────────────────────────────────

    public function mount($id = null)
    {
        if ($id) {
            $this->projectId = $id;
            $project = Project::with('service')->findOrFail($id);
            $this->authorize('update', $project);

            $this->title = $project->title;
            $this->slug = $project->slug;
            $this->content = $project->content;
            $this->excerpt = $project->excerpt;
            $this->status = $project->status;
            $this->visibility = $project->visibility;
            $this->published_at = $project->published_at?->format('Y-m-d\TH:i');
            $this->seo_title = $project->seo_title;
            $this->seo_description = $project->seo_description;
            $this->seo_keywords = $project->seo_keywords;
            $this->service_id = $project->service_id;
            $this->video_url = $project->video_url;
            $this->start_year = $project->start_year;
            $this->end_year = $project->end_year;
            $this->client = $project->client ?? '';
            $this->company = $project->company ?? '';
            $this->custom_fields = $project->custom_fields ?? [];

            $this->existing_featured_image = $project->featured_image;
            $this->existing_thumbnail_image = $project->thumbnail_image;
            $this->existing_additional_images = $project->additional_images ?? [];
            $this->existing_video_file = $project->video_file;

            if ($this->existing_video_file) {
                $this->videoInputType = 'file';
            } elseif ($this->video_url) {
                $this->videoInputType = 'url';
            } else {
                $this->videoInputType = 'url';
            }
        } else {
            $this->authorize('create', Project::class);
        }

        if (empty($this->slug) && !empty($this->title)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->title));
        }
    }

    // ─── Helper: generate unique slug ──────────────────────────────────────

    protected function generateUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $counter = 1;
        while (Project::where('slug', $slug)->when($this->projectId, function ($query) {
            return $query->where('id', '!=', $this->projectId);
        })->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        return $slug;
    }

    // ─── Update slug when title changes ────────────────────────────────────

    public function updatedTitle($value)
    {
        if (empty($this->slug) || $this->slug === Str::slug($value)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($value));
        }
    }

    // ─── Video input type change ───────────────────────────────────────────

    public function updatedVideoInputType($value)
    {
        if ($value === 'url') {
            $this->video_file = null;
            $this->existing_video_file = null;
        } else {
            $this->video_url = '';
        }
    }

    // ─── Remove additional image ──────────────────────────────────────────

    public function removeAdditionalImage($index)
    {
        if (isset($this->existing_additional_images[$index])) {
            Storage::disk('public')->delete($this->existing_additional_images[$index]);
            unset($this->existing_additional_images[$index]);
            $this->existing_additional_images = array_values($this->existing_additional_images);
        }
    }

    // ─── Remove video file ─────────────────────────────────────────────────

    public function removeVideoFile()
    {
        if ($this->existing_video_file) {
            Storage::disk('public')->delete($this->existing_video_file);
            $this->existing_video_file = null;
            $this->video_file = null;
            $this->videoInputType = 'url';
        }
    }

    // ─── Save ──────────────────────────────────────────────────────────────

    public function save()
    {
        $this->validate();

        // Check permission based on edit or create
        if ($this->projectId) {
            $project = Project::findOrFail($this->projectId);
            $this->authorize('update', $project);
        } else {
            $this->authorize('create', Project::class);
        }

        // Handle featured image
        $featuredPath = null;
        if ($this->featured_image) {
            $featuredPath = $this->featured_image->store('projects/featured', 'public');
            if ($this->projectId && $this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
        }

        // Handle thumbnail
        $thumbnailPath = null;
        if ($this->thumbnail_image) {
            $thumbnailPath = $this->thumbnail_image->store('projects/thumbnails', 'public');
            if ($this->projectId && $this->existing_thumbnail_image) {
                Storage::disk('public')->delete($this->existing_thumbnail_image);
            }
        }

        // Handle additional images
        $additionalPaths = [];
        if ($this->additional_images) {
            foreach ($this->additional_images as $img) {
                $additionalPaths[] = $img->store('projects/additional', 'public');
            }
            if ($this->projectId && $this->existing_additional_images) {
                foreach ($this->existing_additional_images as $old) {
                    Storage::disk('public')->delete($old);
                }
            }
        }

        // Handle video file
        $videoFilePath = null;
        if ($this->video_file && $this->videoInputType === 'file') {
            $videoFilePath = $this->video_file->store('projects/videos', 'public');
            if ($this->projectId && $this->existing_video_file) {
                Storage::disk('public')->delete($this->existing_video_file);
            }
        }

        // Prepare data
        $data = [
            'title'          => $this->title,
            'slug'           => $this->slug,
            'content'        => $this->content,
            'excerpt'        => $this->excerpt,
            'status'         => $this->status,
            'visibility'     => $this->visibility,
            'published_at'   => $this->published_at,
            'seo_title'      => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords'   => $this->seo_keywords,
            'service_id'     => $this->service_id,
            'video_url'      => $this->videoInputType === 'url' ? $this->video_url : null,
            'start_year'     => $this->start_year,
            'end_year'       => $this->end_year,
            'client'         => $this->client,
            'company'        => $this->company,
            'custom_fields'  => $this->custom_fields,
            'author_id'      => Auth::id(),
        ];

        if ($featuredPath) {
            $data['featured_image'] = $featuredPath;
        }
        if ($thumbnailPath) {
            $data['thumbnail_image'] = $thumbnailPath;
        }
        if (!empty($additionalPaths)) {
            $data['additional_images'] = $additionalPaths;
        } elseif ($this->projectId && $this->existing_additional_images) {
            $data['additional_images'] = $this->existing_additional_images;
        } else {
            $data['additional_images'] = [];
        }
        if ($videoFilePath) {
            $data['video_file'] = $videoFilePath;
        }

        if ($this->projectId) {
            $project = Project::findOrFail($this->projectId);
            $project->update($data);

            // ─── Log update ──────────────────────────────────────────────────────
            ActivityLogger::log('Project updated', [
                'project_id' => $project->id,
                'title' => $this->title,
                'slug' => $this->slug,
                'status' => $this->status,
                'service_id' => $this->service_id,
                'client' => $this->client,
                'company' => $this->company,
            ], 'project');

            session()->flash('success', 'Project updated successfully!');
        } else {
            $project = Project::create($data);

            // ─── Log create ──────────────────────────────────────────────────────
            ActivityLogger::log('Project created', [
                'project_id' => $project->id,
                'title' => $this->title,
                'slug' => $this->slug,
                'status' => $this->status,
                'service_id' => $this->service_id,
                'client' => $this->client,
                'company' => $this->company,
            ], 'project');

            session()->flash('success', 'Project created successfully!');
        }

        return redirect()->route('admin.projects.index');
    }

    // ─── Get active services ─────────────────────────────────────────────

    public function getServicesProperty()
    {
        return Service::where('status', 'active')->orderBy('name')->get();
    }

    // ─── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.projects.project-form-component');
    }
}

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

    public $start_year = '';
    public $end_year = '';

    public $client = '';
    public $company = '';
    public $location = '';

    public $featured_image = null;
    public $existing_featured_image = null;

    public $additional_images = [];
    public $existing_additional_images = [];

    // "Final View Of Project" image — stored in `thumbnail_image` because it's
    // the same 770x350 asset used as the video poster/thumbnail in the template.
    public $thumbnail_image = null;
    public $existing_thumbnail_image = null;

    public $video_file = null;
    public $existing_video_file = null;

    // "The Challenge Of Project"
    public $challenge_content = '';
    public $challenge_features = [];
    public $challenge_image = null;
    public $existing_challenge_image = null;

    // "The Final View Of Project"
    public $final_view_content = '';

    // Sidebar "Company File" download box
    public $attachment = null;
    public $existing_attachment = null;
    public $existing_attachment_name = null;
    public $existing_attachment_size = null; // bytes

    public $custom_fields = [];

    // ─── Slug auto-generation bookkeeping ───────────────────────────────
    // True once the user has typed directly into the slug field. While false,
    // the slug keeps mirroring the title (on create AND on edit).
    public $slugManuallyEdited = false;
    // Internal guard so our own programmatic slug writes don't get mistaken
    // for a manual edit by updatedSlug().
    protected $isAutoSlugging = false;

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

            // ─── Video conditional rules ─────────────────────────────────
            // Only ONE of these two fields is ever "live" at a time, based on
            // videoInputType. The inactive field must never be format-checked
            // (no 'url', no 'mimes') — it only needs to allow null. Applying a
            // format rule ('url') to an empty string still fails validation
            // even with 'nullable' present, because Laravel's 'nullable' only
            // short-circuits on a value that is exactly null, not ''. That's
            // why we drop the format rule entirely for the inactive field
            // instead of relying on 'nullable' to save us.
            'video_url' => $this->videoInputType === 'url'
                ? ['required', 'url', 'max:500']
                : ['nullable', 'max:500'],

            'video_file' => ($this->videoInputType === 'file' && !$this->existing_video_file)
                ? ['required', 'file', 'mimes:mp4,mov,avi,webm', 'max:51200']
                : ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:51200'],

            'start_year'    => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'end_year'      => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'client'        => 'nullable|string|max:255',
            'company'       => 'nullable|string|max:255',
            'location'      => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:5120|dimensions:min_width=1170,min_height=550',
            'thumbnail_image' => 'nullable|image|max:2048|dimensions:min_width=770,min_height=350',
            'additional_images' => 'nullable|array|max:2',
            'additional_images.*' => 'image|max:5120|dimensions:min_width=428,min_height=250',

            // ─── Challenge section ────────────────────────────────────────
            'challenge_content' => 'nullable|string',
            'challenge_features' => 'nullable|array|max:10',
            'challenge_features.*' => 'nullable|string|max:255',
            'challenge_image' => 'nullable|image|max:5120|dimensions:min_width=428,min_height=250',

            // ─── Final view section ───────────────────────────────────────
            'final_view_content' => 'nullable|string',

            // ─── Company file attachment ───────────────────────────────────
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',

            'custom_fields' => 'nullable|array',
        ];
    }

    protected function messages()
    {
        return [
            'title.required' => 'The title is required.',
            'slug.unique'    => 'This slug is already taken.',
            'featured_image.dimensions' => 'Featured image must be at least 1170x550 pixels.',
            'thumbnail_image.dimensions' => 'Final view image must be at least 770x350 pixels.',
            'additional_images.*.dimensions' => 'Additional images must be at least 428x250 pixels.',
            'additional_images.max' => 'You can upload a maximum of 2 additional images.',
            'challenge_image.dimensions' => 'Challenge image must be at least 428x250 pixels.',
            'challenge_features.max' => 'You can add a maximum of 10 checklist items.',
            'video_url.required' => 'Please enter a video URL, or switch to the upload option.',
            'video_url.url' => 'The video URL field must be a valid URL.',
            'video_file.required' => 'Please upload a video file, or switch to the URL option.',
            'video_file.max' => 'Video file must not exceed 50MB.',
            'attachment.mimes' => 'Company file must be a PDF, Word document, or ZIP archive.',
            'attachment.max' => 'Company file must not exceed 10MB.',
            'start_year.min' => 'Start year must be at least 2000.',
            'end_year.min'   => 'End year must be at least 2000.',
            'start_year.max' => 'Start year cannot be too far in the future.',
            'end_year.max'   => 'End year cannot be too far in the future.',
        ];
    }

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
            $this->location = $project->location ?? '';
            $this->custom_fields = $project->custom_fields ?? [];

            $this->challenge_content = $project->challenge_content ?? '';
            $this->challenge_features = $project->challenge_features ?? [];
            $this->existing_challenge_image = $project->challenge_image;
            $this->final_view_content = $project->final_view_content ?? '';

            $this->existing_featured_image = $project->featured_image;
            $this->existing_thumbnail_image = $project->thumbnail_image;
            $this->existing_additional_images = $project->additional_images ?? [];
            $this->existing_video_file = $project->video_file;

            $this->existing_attachment = $project->attachment;
            $this->existing_attachment_name = $project->attachment_original_name;
            $this->existing_attachment_size = $project->attachment_size;

            if ($this->existing_video_file) {
                $this->videoInputType = 'file';
                $this->video_url = null;
            } else {
                $this->videoInputType = 'url';
            }
        } else {
            $this->authorize('create', Project::class);
            $this->published_at = now()->format('Y-m-d\TH:i');
        }

        if (empty($this->challenge_features)) {
            $this->challenge_features = [''];
        }

        if (empty($this->slug) && !empty($this->title)) {
            $this->autoSlug($this->title);
        }
    }

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

    /**
     * Programmatically (re)generate the slug from a title without tripping
     * the "manually edited" flag in updatedSlug().
     */
    protected function autoSlug($title)
    {
        $this->isAutoSlugging = true;
        $this->slug = $this->generateUniqueSlug(Str::slug($title));
        $this->isAutoSlugging = false;
    }

    public function updatedTitle($value)
    {
        // Keep mirroring the title into the slug — on create AND on edit —
        // right up until the user types into the slug field themselves.
        if (!$this->slugManuallyEdited) {
            $this->autoSlug($value);
        }
    }

    public function updatedSlug($value)
    {
        if ($this->isAutoSlugging) {
            return;
        }

        $this->slugManuallyEdited = true;
    }

    /**
     * Let the user explicitly reset to an auto-generated slug.
     */
    public function resetSlugToTitle()
    {
        $this->slugManuallyEdited = false;
        $this->autoSlug($this->title);
    }

    public function updatedVideoInputType($value)
    {
        // IMPORTANT: null, not '' — see the comment in rules() on why an
        // empty string still trips the 'url' format validator even under
        // 'nullable'.
        if ($value === 'url') {
            $this->video_file = null;
            $this->existing_video_file = null;
            $this->resetErrorBag('video_file');
        } else {
            $this->video_url = null;
            $this->resetErrorBag('video_url');
        }
    }

    public function addChallengeFeature()
    {
        if (count($this->challenge_features) >= 10) {
            return;
        }

        $this->challenge_features[] = '';
    }

    public function removeChallengeFeature($index)
    {
        if (isset($this->challenge_features[$index])) {
            unset($this->challenge_features[$index]);
            $this->challenge_features = array_values($this->challenge_features);
        }

        if (empty($this->challenge_features)) {
            $this->challenge_features = [''];
        }
    }

    public function removeFeaturedImage()
    {
        if ($this->existing_featured_image) {
            Storage::disk('public')->delete($this->existing_featured_image);
            $this->existing_featured_image = null;
        }
        $this->featured_image = null;
    }

    public function removeThumbnailImage()
    {
        if ($this->existing_thumbnail_image) {
            Storage::disk('public')->delete($this->existing_thumbnail_image);
            $this->existing_thumbnail_image = null;
        }
        $this->thumbnail_image = null;
    }

    public function removeChallengeImage()
    {
        if ($this->existing_challenge_image) {
            Storage::disk('public')->delete($this->existing_challenge_image);
            $this->existing_challenge_image = null;
        }
        $this->challenge_image = null;
    }

    public function removeAdditionalImage($index)
    {
        if (isset($this->existing_additional_images[$index])) {
            Storage::disk('public')->delete($this->existing_additional_images[$index]);
            unset($this->existing_additional_images[$index]);
            $this->existing_additional_images = array_values($this->existing_additional_images);
        }
    }

    public function removeVideoFile()
    {
        if ($this->existing_video_file) {
            Storage::disk('public')->delete($this->existing_video_file);
            $this->existing_video_file = null;
            $this->video_file = null;
            $this->videoInputType = 'url';
        }
    }

    public function removeAttachment()
    {
        if ($this->existing_attachment) {
            Storage::disk('public')->delete($this->existing_attachment);
            $this->existing_attachment = null;
            $this->existing_attachment_name = null;
            $this->existing_attachment_size = null;
        }
        $this->attachment = null;
    }

    public static function formatBytes($bytes, $decimals = 1)
    {
        if (empty($bytes)) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);
        $factor = min($factor, count($units) - 1);

        return sprintf("%.{$decimals}f", $bytes / (1024 ** $factor)) . $units[$factor];
    }

    public function save()
    {
        $this->validate();

        if ($this->projectId) {
            $project = Project::findOrFail($this->projectId);
            $this->authorize('update', $project);
        } else {
            $this->authorize('create', Project::class);
        }

        // Featured Image
        $featuredPath = null;
        if ($this->featured_image) {
            $featuredPath = $this->featured_image->store('projects/featured', 'public');
            if ($this->projectId && $this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
        }
        $data['featured_image'] = $featuredPath ?? $this->existing_featured_image;

        // Final View / video poster image (stored in thumbnail_image)
        $thumbnailPath = null;
        if ($this->thumbnail_image) {
            $thumbnailPath = $this->thumbnail_image->store('projects/thumbnails', 'public');
            if ($this->projectId && $this->existing_thumbnail_image) {
                Storage::disk('public')->delete($this->existing_thumbnail_image);
            }
        }
        $data['thumbnail_image'] = $thumbnailPath ?? $this->existing_thumbnail_image;

        // Challenge image
        $challengeImagePath = null;
        if ($this->challenge_image) {
            $challengeImagePath = $this->challenge_image->store('projects/challenge', 'public');
            if ($this->projectId && $this->existing_challenge_image) {
                Storage::disk('public')->delete($this->existing_challenge_image);
            }
        }
        $data['challenge_image'] = $challengeImagePath ?? $this->existing_challenge_image;

        // Additional Images
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
        $data['additional_images'] = !empty($additionalPaths)
            ? $additionalPaths
            : ($this->existing_additional_images ?? []);

        // Video File / URL — only ever persist the one matching videoInputType.
        $videoFilePath = null;
        if ($this->video_file && $this->videoInputType === 'file') {
            $videoFilePath = $this->video_file->store('projects/videos', 'public');
            if ($this->projectId && $this->existing_video_file) {
                Storage::disk('public')->delete($this->existing_video_file);
            }
        }
        if ($this->videoInputType === 'url') {
            $data['video_file'] = null;
            $data['video_url'] = $this->video_url;
        } else {
            $data['video_url'] = null;
            $data['video_file'] = $videoFilePath ?? $this->existing_video_file;
        }

        // Company file attachment
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('projects/attachments', 'public');
            if ($this->projectId && $this->existing_attachment) {
                Storage::disk('public')->delete($this->existing_attachment);
            }
            $data['attachment'] = $attachmentPath;
            $data['attachment_original_name'] = $this->attachment->getClientOriginalName();
            $data['attachment_size'] = $this->attachment->getSize();
        } else {
            $data['attachment'] = $this->existing_attachment;
            $data['attachment_original_name'] = $this->existing_attachment_name;
            $data['attachment_size'] = $this->existing_attachment_size;
        }

        // Other fields
        $data['title'] = $this->title;
        $data['slug'] = $this->slug;
        $data['content'] = $this->content;
        $data['excerpt'] = $this->excerpt;
        $data['status'] = $this->status;
        $data['visibility'] = $this->visibility;
        $data['published_at'] = $this->published_at;
        $data['seo_title'] = $this->seo_title;
        $data['seo_description'] = $this->seo_description;
        $data['seo_keywords'] = $this->seo_keywords;
        $data['service_id'] = $this->service_id;
        $data['start_year'] = $this->start_year;
        $data['end_year'] = $this->end_year;
        $data['client'] = $this->client;
        $data['company'] = $this->company;
        $data['location'] = $this->location;
        $data['custom_fields'] = $this->custom_fields;
        $data['challenge_content'] = $this->challenge_content;
        $data['challenge_features'] = array_values(array_filter($this->challenge_features, fn($item) => trim((string) $item) !== ''));
        $data['final_view_content'] = $this->final_view_content;
        $data['author_id'] = Auth::id();

        if ($this->projectId) {
            $project->update($data);
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


        return $this->redirectRoute('admin.projects.index', navigate: true);
    }

    public function getServicesProperty()
    {
        return Service::where('status', 'active')->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin.projects.project-form-component');
    }
}

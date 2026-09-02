<?php

namespace App\Livewire\Admin\Blog\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Auth\Access\AuthorizationException;

#[Layout('layouts.users')]
class PostFormComponent extends Component
{
    use WithFileUploads;

    public $postSlug = null;
    public $title = '';
    public $slug = '';
    public $content = '';
    public $excerpt = '';
    public $featured_image = null;
    public $existing_featured_image = null;
    public $status = 'draft';
    public $visibility = 'public';
    public $allow_comments = true;
    public $published_at = null;
    public $custom_fields = [];
    public $seo_title = '';
    public $seo_description = '';
    public $seo_keywords = '';

    public $selectedCategories = [];
    public $selectedTags = [];
    public $newCategoryName = '';
    public $newTagName = '';

    // ─── Dynamic validation rules ────────────────────────────────────

    protected function rules()
    {
        $uniqueRule = 'unique:posts,slug';
        if ($this->postSlug) {
            $uniqueRule .= ',' . $this->postSlug . ',slug';
        }

        return [
            'title'    => 'required|string|max:255',
            'slug'     => ['required', 'string', 'max:255', $uniqueRule],
            'content'  => 'nullable|string',
            'excerpt'  => 'nullable|string|max:1000',
            'featured_image' => 'nullable|image|max:2048',
            'status'   => 'required|in:draft,published,private,pending,trash',
            'visibility' => 'required|in:public,password_protected,private',
            'allow_comments' => 'boolean',
            'published_at' => 'nullable|date',
            'custom_fields' => 'nullable|array',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'selectedCategories' => 'nullable|array',
            'selectedTags' => 'nullable|array',
        ];
    }

    // ─── Custom error messages ──────────────────────────────────────

    protected function messages()
    {
        return [
            'title.required' => 'The title is required.',
            'slug.required'  => 'The slug is required.',
            'slug.unique'    => 'This slug is already taken. Please use a different one.',
        ];
    }

    // ─── Mount ──────────────────────────────────────────────────────

    public function mount($slug = null)
    {
        if ($slug) {
            $this->postSlug = $slug;
            $post = Post::with('categories', 'tags')->where('slug', $slug)->firstOrFail();
            $this->authorize('update', $post);

            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->content = $post->content;
            $this->excerpt = $post->excerpt;
            $this->existing_featured_image = $post->featured_image;
            $this->status = $post->status;
            $this->visibility = $post->visibility;
            $this->allow_comments = $post->allow_comments;
            $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
            $this->custom_fields = $post->custom_fields ?? [];
            $this->seo_title = $post->seo_title;
            $this->seo_description = $post->seo_description;
            $this->seo_keywords = $post->seo_keywords;

            $this->selectedCategories = $post->categories->pluck('id')->toArray();
            $this->selectedTags = $post->tags->pluck('id')->toArray();
        } else {
            $this->authorize('create', Post::class);
        }

        // Auto-generate slug if empty
        if (empty($this->slug) && !empty($this->title)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->title));
        }
    }

    // ─── Helper: generate unique slug ──────────────────────────────

    protected function generateUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $counter = 1;
        while (Post::where('slug', $slug)->when($this->postSlug, function ($query) {
            return $query->where('slug', '!=', $this->postSlug);
        })->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        return $slug;
    }

    // ─── Update slug when title changes ────────────────────────────

    public function updatedTitle($value)
    {
        if (empty($this->slug) || $this->slug === Str::slug($value)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($value));
        }
    }

    // ─── Add category ──────────────────────────────────────────────

    public function addCategory()
    {
        $this->authorize('create', Category::class);
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $this->newCategoryName,
            'slug' => Str::slug($this->newCategoryName),
        ]);

        $this->selectedCategories[] = $category->id;
        $this->newCategoryName = '';
        session()->flash('message', 'Category added successfully!');
    }

    // ─── Add tag ────────────────────────────────────────────────────

    public function addTag()
    {
        // No specific permission for tags – we assume if you can create a post you can add a tag.
        // But we'll check create permission on Tag? We can skip for now.
        $this->validate([
            'newTagName' => 'required|string|max:255|unique:tags,name',
        ]);

        $tag = Tag::create([
            'name' => $this->newTagName,
            'slug' => Str::slug($this->newTagName),
        ]);

        $this->selectedTags[] = $tag->id;
        $this->newTagName = '';
        session()->flash('message', 'Tag added successfully!');
    }

    // ─── Save ──────────────────────────────────────────────────────

    public function save()
    {
        $this->validate();

        $featuredImagePath = null;
        if ($this->featured_image) {
            $featuredImagePath = $this->featured_image->store('posts/featured_images', 'public');
        }

        $data = [
            'title'          => $this->title,
            'slug'           => $this->slug,
            'content'        => $this->content,
            'excerpt'        => $this->excerpt,
            'status'         => $this->status,
            'visibility'     => $this->visibility,
            'allow_comments' => $this->allow_comments,
            'published_at'   => $this->published_at,
            'custom_fields'  => $this->custom_fields,
            'seo_title'      => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords'   => $this->seo_keywords,
            'author_id'      => Auth::id(),
        ];

        if ($featuredImagePath) {
            $data['featured_image'] = $featuredImagePath;
            if ($this->postSlug && $this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
        }

        if ($this->postSlug) {
            $post = Post::where('slug', $this->postSlug)->firstOrFail();
            $this->authorize('update', $post);
            $post->update($data);
            $post->categories()->sync($this->selectedCategories);
            $post->tags()->sync($this->selectedTags);
            session()->flash('success', 'Post updated successfully!');
        } else {
            $this->authorize('create', Post::class);
            $post = Post::create($data);
            $post->categories()->attach($this->selectedCategories);
            $post->tags()->attach($this->selectedTags);
            session()->flash('success', 'Post created successfully!');
        }

        $this->redirectRoute('manage.posts', navigate: true);
    }

    // ─── Computed properties ──────────────────────────────────────

    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    public function getTagsProperty()
    {
        return Tag::orderBy('name')->get();
    }

    // ─── Render ────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.blog.post.post-form-component');
    }
}

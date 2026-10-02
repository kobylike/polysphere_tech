<?php

namespace App\Livewire\Admin\Blog\Post;

use App\Helpers\ActivityLogger;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Auth\Access\AuthorizationException;

#[Layout('layouts.users')]
class PostFormComponent extends Component
{
    use WithFileUploads;

    public $postSlug = null;
    public $editingPostId = null;      // ← NEW: cached post ID for comment lookups
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

    // ─── Comment moderation state ──────────────────────────────────────
    public $showCommentEditModal = false;
    public $editingCommentId = null;
    public $editCommentBody = '';

    protected function rules()
    {
        $uniqueRule = 'unique:posts,slug';
        if ($this->postSlug) {
            $uniqueRule .= ',' . $this->editingPostId . ',id';
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

    protected function messages()
    {
        return [
            'title.required' => 'The title is required.',
            'slug.required'  => 'The slug is required.',
            'slug.unique'    => 'This slug is already taken. Please use a different one.',
        ];
    }

    public function mount($slug = null)
    {
        if ($slug) {
            $this->postSlug = $slug;
            $post = Post::with('categories', 'tags')->where('slug', $slug)->firstOrFail();
            $this->authorize('update', $post);

            $this->editingPostId = $post->id;   // ← NEW

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

        if (empty($this->slug) && !empty($this->title)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->title));
        }
    }

    protected function generateUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $counter = 1;
        while (Post::where('slug', $slug)->when($this->editingPostId, function ($query) {
            return $query->where('id', '!=', $this->editingPostId);
        })->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        return $slug;
    }

    public function updatedTitle($value)
    {
        if (empty($this->slug) || $this->slug === Str::slug($value)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($value));
        }
    }

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

        ActivityLogger::log('Category added from post form', [
            'category_id' => $category->id,
            'name'        => $category->name,
        ], 'category');

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Created', 'message' => 'Category created successfully!']);
    }

    public function addTag()
    {
        $this->validate([
            'newTagName' => 'required|string|max:255|unique:tags,name',
        ]);

        $tag = Tag::create([
            'name' => $this->newTagName,
            'slug' => Str::slug($this->newTagName),
        ]);
        $this->selectedTags[] = $tag->id;
        $this->newTagName = '';

        ActivityLogger::log('Tag added from post form', [
            'tag_id' => $tag->id,
            'name'   => $tag->name,
        ], 'tag');

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Created', 'message' => 'Tag added successfully!']);
    }

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
            if ($this->editingPostId && $this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
        }

        if ($this->editingPostId) {
            $post = Post::findOrFail($this->editingPostId);
            $this->authorize('update', $post);
            $post->update($data);
            $post->categories()->sync($this->selectedCategories);
            $post->tags()->sync($this->selectedTags);

            ActivityLogger::log('Post updated', [
                'post_id' => $post->id,
                'title'   => $post->title,
                'status'  => $post->status,
            ], 'post');

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Updated', 'message' => 'Post updated successfully!']);
        } else {
            $this->authorize('create', Post::class);
            $post = Post::create($data);
            $post->categories()->attach($this->selectedCategories);
            $post->tags()->attach($this->selectedTags);

            ActivityLogger::log('Post created', [
                'post_id' => $post->id,
                'title'   => $post->title,
                'status'  => $post->status,
            ], 'post');

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Created', 'message' => 'Post created successfully!']);
        }

        $this->redirectRoute('manage.posts', navigate: true);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  COMMENT MODERATION (inline, uses Edit Posts permission)
    // ═══════════════════════════════════════════════════════════════════

    protected function guardCommentModeration(): void
    {
        if (! Auth::user()->can('Edit Posts')) {
            throw new AuthorizationException('You do not have permission to moderate comments.');
        }
        if (! $this->editingPostId) {
            throw new AuthorizationException('Comments can only be moderated on an existing post.');
        }
    }

    protected function findCommentForThisPost(int $id): Comment
    {
        return Comment::where('post_id', $this->editingPostId)->findOrFail($id);
    }

    public function approveComment(int $id): void
    {
        $this->guardCommentModeration();
        $comment = $this->findCommentForThisPost($id);

        $comment->update([
            'verified_at'        => now(),
            'verification_token' => null,
        ]);

        ActivityLogger::log('Comment approved from post edit', [
            'comment_id' => $comment->id,
            'post_id'    => $this->editingPostId,
        ], 'comment');

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Approved', 'message' => 'Comment approved.']);
    }

    public function unapproveComment(int $id): void
    {
        $this->guardCommentModeration();
        $comment = $this->findCommentForThisPost($id);

        $comment->update(['verified_at' => null]);

        ActivityLogger::log('Comment unapproved from post edit', [
            'comment_id' => $comment->id,
            'post_id'    => $this->editingPostId,
        ], 'comment');

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Unapproved', 'message' => 'Comment moved back to pending.']);
    }

    public function deleteComment(int $id): void
    {
        $this->guardCommentModeration();
        $comment = $this->findCommentForThisPost($id);

        $comment->delete(); // FK cascade removes replies

        ActivityLogger::log('Comment deleted from post edit', [
            'comment_id' => $id,
            'post_id'    => $this->editingPostId,
        ], 'comment');

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Deleted', 'message' => 'Comment deleted.']);
    }

    public function openCommentEdit(int $id): void
    {
        $this->guardCommentModeration();
        $comment = $this->findCommentForThisPost($id);

        $this->editingCommentId     = $comment->id;
        $this->editCommentBody      = $comment->body;
        $this->showCommentEditModal = true;
        $this->resetErrorBag();
    }

    public function saveCommentEdit(): void
    {
        $this->guardCommentModeration();

        $this->validate([
            'editCommentBody' => 'required|string|min:2|max:5000',
        ]);

        $comment = $this->findCommentForThisPost($this->editingCommentId);

        $comment->update(['body' => $this->editCommentBody]);

        ActivityLogger::log('Comment edited from post edit', [
            'comment_id' => $comment->id,
            'post_id'    => $this->editingPostId,
            'editor_id'  => Auth::id(),
        ], 'comment');

        $this->showCommentEditModal = false;
        $this->editingCommentId     = null;
        $this->editCommentBody      = '';

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Updated', 'message' => 'Comment updated.']);
    }

    public function getCommentsProperty()
    {
        if (! $this->editingPostId) {
            return collect();
        }

        return Comment::with(['user:id,name'])
            ->where('post_id', $this->editingPostId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getPendingCommentsCountProperty(): int
    {
        if (! $this->editingPostId) {
            return 0;
        }

        return Comment::where('post_id', $this->editingPostId)
            ->whereNull('verified_at')
            ->whereNull('user_id') // guest comments only (logged-in users are visible per your scope)
            ->count();
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    public function getTagsProperty()
    {
        return Tag::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin.blog.post.post-form-component');
    }
}

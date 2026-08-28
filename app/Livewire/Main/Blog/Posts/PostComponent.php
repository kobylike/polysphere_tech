<?php

namespace App\Livewire\Main\Blog\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class PostComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $category = ''; // category slug
    public $perPage = 6;

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    // ─── Get posts with filters ──────────────────────────────────────

    public function getPosts()
    {
        $query = Post::with(['categories', 'author'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc');

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter
        if (!empty($this->category)) {
            $query->whereHas('categories', function ($q) {
                $q->where('slug', $this->category);
            });
        }

        return $query->paginate($this->perPage);
    }

    // ─── Get categories with post counts ────────────────────────────

    public function getCategoriesWithCount()
    {
        return Category::withCount('posts')
            ->whereHas('posts', function ($q) {
                $q->where('status', 'published');
            })
            ->orderBy('name')
            ->get();
    }

    // ─── Get recent posts ────────────────────────────────────────────

    public function getRecentPosts()
    {
        return Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'featured_image', 'published_at']);
    }

    // ─── Get popular tags ────────────────────────────────────────────

    public function getPopularTags()
    {
        return Tag::withCount('posts')
            ->whereHas('posts', function ($q) {
                $q->where('status', 'published');
            })
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();
    }

    // ─── Render ──────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.main.blog.posts.post-component', [
            'posts'          => $this->getPosts(),
            'categoriesData' => $this->getCategoriesWithCount(),
            'recentPosts'    => $this->getRecentPosts(),
            'popularTags'    => $this->getPopularTags(),
        ]);
    }
}

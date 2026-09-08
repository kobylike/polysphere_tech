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
    public $isSearching = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
        $this->isSearching = !empty($this->search);
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    // ─── Get posts with advanced search ──────────────────────────────────────

    public function getPosts()
    {
        $query = Post::with(['categories', 'tags', 'author'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc');

        // ─── Advanced Search (title, content, excerpt, tags, categories) ────
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                // Search in post fields
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('excerpt', 'like', $searchTerm)
                    ->orWhere('content', 'like', $searchTerm)

                    // Search in categories
                    ->orWhereHas('categories', function ($catQuery) use ($searchTerm) {
                        $catQuery->where('name', 'like', $searchTerm)
                            ->orWhere('slug', 'like', $searchTerm);
                    })

                    // Search in tags
                    ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                        $tagQuery->where('name', 'like', $searchTerm)
                            ->orWhere('slug', 'like', $searchTerm);
                    });
            });
        }

        // ─── Category filter ──────────────────────────────────────────────────
        if (!empty($this->category)) {
            $query->whereHas('categories', function ($q) {
                $q->where('slug', $this->category);
            });
        }

        return $query->paginate($this->perPage);
    }

    // ─── Get categories with post counts ─────────────────────────────────────

    public function getCategoriesWithCount()
    {
        return Category::withCount(['posts' => function ($q) {
            $q->where('status', 'published')
                ->whereNotNull('published_at');
        }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->get();
    }

    // ─── Get recent posts ────────────────────────────────────────────────────

    public function getRecentPosts()
    {
        return Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'featured_image', 'published_at']);
    }

    // ─── Get popular tags ────────────────────────────────────────────────────

    public function getPopularTags()
    {
        return Tag::withCount(['posts' => function ($q) {
            $q->where('status', 'published')
                ->whereNotNull('published_at');
        }])
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();
    }

    // ─── Clear search ────────────────────────────────────────────────────────

    public function clearSearch()
    {
        $this->search = '';
        $this->isSearching = false;
        $this->resetPage();
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        $posts = $this->getPosts();
        $totalPosts = $posts->total();

        return view('livewire.main.blog.posts.post-component', [
            'posts'          => $posts,
            'totalPosts'     => $totalPosts,
            'categoriesData' => $this->getCategoriesWithCount(),
            'recentPosts'    => $this->getRecentPosts(),
            'popularTags'    => $this->getPopularTags(),
        ]);
    }
}

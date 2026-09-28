<?php

namespace App\Livewire\Main\Blog\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PostDetails extends Component
{
    public $post;
    public $relatedPosts;
    public $categoriesWithCount;
    public $popularTags;
    public $search = ''; // For search input

    public function mount($slug)
    {
        $this->post = Post::with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // ─── Categories with post counts ──────────────────────────────
        $this->categoriesWithCount = Category::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->get();

        // ─── Popular tags ──────────────────────────────────────────────
        $this->popularTags = Tag::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();

        // ─── Related posts (same categories) – for sidebar ────────────
        $categoryIds = $this->post->categories->pluck('id')->toArray();

        if (count($categoryIds)) {
            $this->relatedPosts = Post::where('status', 'published')
                ->where('id', '!=', $this->post->id)
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            $this->relatedPosts = Post::where('status', 'published')
                ->where('id', '!=', $this->post->id)
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->get();
        }
    }

    // ─── Search redirect with Livewire navigation ─────────────────────
    public function searchPosts()
    {
        $this->redirectRoute('posts', ['search' => $this->search], navigate: true);
    }

    private function metaDescription(): string
    {
        if (!empty($this->post->seo_description)) {
            return $this->post->seo_description;
        }

        $source = $this->post->excerpt ?: $this->post->content;

        return Str::limit(strip_tags((string) $source), 160);
    }

    private function ogImage(): ?string
    {
        return $this->post->featured_image
            ? asset('storage/' . $this->post->featured_image)
            : null;
    }

    public function render()
    {
        return view('livewire.main.blog.posts.post-details')
            ->layoutData([
                'description' => $this->metaDescription(),
                'keywords'    => $this->post->seo_keywords,
                'canonical'   => route('blog.details', $this->post->slug),
                'ogImage'     => $this->ogImage(),
                'ogType'      => 'article',
            ])
            ->title($this->post->seo_title ?: $this->post->title . ' | Polysphere Tech');
    }
}

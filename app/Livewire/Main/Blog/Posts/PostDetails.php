<?php

namespace App\Livewire\Main\Blog\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PostDetails extends Component
{
    public $post;
    public $recentPosts;
    public $categoriesWithCount;
    public $popularTags;
    public $relatedPosts;

    public function mount($slug)
    {
        $this->post = Post::with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $this->recentPosts = Post::where('status', 'published')
            ->where('id', '!=', $this->post->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'featured_image', 'published_at']);

        $this->categoriesWithCount = Category::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->get();

        $this->popularTags = Tag::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();

        $categoryIds = $this->post->categories->pluck('id')->toArray();
        $this->relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $this->post->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.main.blog.posts.post-details');
    }
}

<?php

namespace App\Livewire\Main\Search;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;


class MainGlobalSearch extends Component
{
    #[Url(as: 'q', history: true)]
    public string $query = '';

    public string $category = 'all';
    public array $categories = ['all', 'services', 'projects', 'blog', 'team'];
    public array $results = [];

    public function mount()
    {
        if (!empty($this->query)) {
            $this->performSearch();
        }
    }

    public function updatedQuery()
    {
        $this->performSearch();
    }

    public function updatedCategory()
    {
        $this->performSearch();
    }

    public function performSearch()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $term = $this->query;
        $results = [];

        // ─── Services ──────────────────────────────────────────────────────────
        if ($this->category === 'all' || $this->category === 'services') {
            $services = Service::where('name', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->where('status', 'active')
                ->limit(10)
                ->get()
                ->map(function ($service) {
                    return [
                        'id'       => $service->id,
                        'title'    => $service->name,
                        'subtitle' => Str::limit($service->description ?? $service->slug, 80),
                        'url'      => route('service.details', $service->slug),
                        'icon'     => 'fa-cogs',
                        'color'    => 'info',
                        'category' => 'services',
                    ];
                });
            $results = array_merge($results, $services->toArray());
        }

        // ─── Projects ──────────────────────────────────────────────────────────
        if ($this->category === 'all' || $this->category === 'projects') {
            $projects = Project::where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->where('status', 'published')
                ->limit(10)
                ->get()
                ->map(function ($project) {
                    return [
                        'id'       => $project->id,
                        'title'    => $project->title,
                        'subtitle' => Str::limit($project->excerpt ?? $project->slug, 80),
                        'url'      => route('project.details', $project->slug),
                        'icon'     => 'fa-folder-open',
                        'color'    => 'success',
                        'category' => 'projects',
                    ];
                });
            $results = array_merge($results, $projects->toArray());
        }

        // ─── Blog Posts ──────────────────────────────────────────────────────
        if ($this->category === 'all' || $this->category === 'blog') {
            $posts = Post::where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->where('status', 'published')
                ->limit(10)
                ->get()
                ->map(function ($post) {
                    return [
                        'id'       => $post->id,
                        'title'    => $post->title,
                        'subtitle' => Str::limit($post->excerpt ?? $post->slug, 80),
                        'url'      => route('blog.details', $post->slug),
                        'icon'     => 'fa-newspaper',
                        'color'    => 'warning',
                        'category' => 'blog',
                    ];
                });
            $results = array_merge($results, $posts->toArray());
        }

        // ─── Team Members ────────────────────────────────────────────────────
        if ($this->category === 'all' || $this->category === 'team') {
            $team = User::whereHas('profile', function ($q) {
                $q->where('is_employee', true);
            })
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhereHas('profile', function ($p) use ($term) {
                            $p->where('position', 'like', "%{$term}%")
                                ->orWhere('department', 'like', "%{$term}%");
                        });
                })
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'id'       => $user->id,
                        'title'    => $user->name,
                        'subtitle' => $user->profile?->position ?? 'Team Member',
                        'url'      => route('team.details', $user->slug ?? $user->id),
                        'icon'     => 'fa-user',
                        'color'    => 'primary',
                        'category' => 'team',
                    ];
                });
            $results = array_merge($results, $team->toArray());
        }

        $this->results = array_slice($results, 0, 50);
    }

    public function render()
    {
        return view('livewire.main.search.main-global-search');
    }
}

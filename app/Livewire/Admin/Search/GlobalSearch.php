<?php

namespace App\Livewire\Admin\Search;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.users')]
class GlobalSearch extends Component
{
    #[Url(as: 'q', history: true)]
    public string $query = '';

    public string $category = 'all';
    public array $categories = [];
    public array $results = [];

    // Available categories based on permissions
    protected function getAvailableCategories()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $categories = [];

        if ($user->can('View Users')) {
            $categories['users'] = 'Users';
        }
        if ($user->can('View Projects')) {
            $categories['projects'] = 'Projects';
        }
        if ($user->can('View Services')) {
            $categories['services'] = 'Services';
        }
        if ($user->can('View Posts')) {
            $categories['posts'] = 'Posts';
        }

        // Always allow 'all' if there is at least one category
        if (!empty($categories)) {
            // Add 'all' as the first option
            $allCategories = ['all' => 'All'];
            $categories = $allCategories + $categories;
        }

        return $categories;
    }

    public function mount()
    {
        // Build available categories based on permissions
        $this->categories = array_keys($this->getAvailableCategories());
        // If no categories, default to empty (user has no searchable permissions)
        if (empty($this->categories)) {
            // Optionally, you could redirect or show a message
            // We'll keep 'all' as fallback but it will be empty.
            $this->categories = ['all'];
        }

        // If 'all' is not in the list, but we have other categories, we should add it.
        if (!in_array('all', $this->categories) && count($this->categories) > 0) {
            array_unshift($this->categories, 'all');
        }

        // If category is not in the allowed list, set to first available (e.g., 'all')
        if (!in_array($this->category, $this->categories)) {
            $this->category = $this->categories[0] ?? 'all';
        }

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

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $term = $this->query;
        $results = [];

        // Only include categories the user has permission to view
        if ($this->category === 'all' || $this->category === 'users') {
            if ($user->can('View Users')) {
                $users = User::where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->limit(10)
                    ->get()
                    ->map(function ($userItem) {
                        return [
                            'id'       => $userItem->id,
                            'title'    => $userItem->name,
                            'subtitle' => $userItem->email,
                            'url'      => route('users') . '?search=' . urlencode($userItem->name),
                            'icon'     => 'fa-user',
                            'color'    => 'primary',
                            'category' => 'users',
                        ];
                    });
                $results = array_merge($results, $users->toArray());
            }
        }

        if ($this->category === 'all' || $this->category === 'projects') {
            if ($user->can('View Projects')) {
                $projects = Project::where('title', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%")
                    ->limit(10)
                    ->get()
                    ->map(function ($project) {
                        return [
                            'id'       => $project->id,
                            'title'    => $project->title,
                            'subtitle' => Str::limit($project->excerpt ?? $project->slug, 60),
                            'url'      => route('admin.projects.edit', $project->id),
                            'icon'     => 'fa-folder-open',
                            'color'    => 'success',
                            'category' => 'projects',
                        ];
                    });
                $results = array_merge($results, $projects->toArray());
            }
        }

        if ($this->category === 'all' || $this->category === 'services') {
            if ($user->can('View Services')) {
                $services = Service::where('name', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->limit(10)
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id'       => $service->id,
                            'title'    => $service->name,
                            'subtitle' => Str::limit($service->description ?? $service->slug, 60),
                            'url'      => route('admin.services.edit', $service->id),
                            'icon'     => 'fa-cogs',
                            'color'    => 'info',
                            'category' => 'services',
                        ];
                    });
                $results = array_merge($results, $services->toArray());
            }
        }

        if ($this->category === 'all' || $this->category === 'posts') {
            if ($user->can('View Posts')) {
                $posts = Post::where('title', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%")
                    ->limit(10)
                    ->get()
                    ->map(function ($post) {
                        return [
                            'id'       => $post->id,
                            'title'    => $post->title,
                            'subtitle' => Str::limit($post->excerpt ?? $post->slug, 60),
                            'url'      => route('edit.post', $post->slug),
                            'icon'     => 'fa-newspaper',
                            'color'    => 'warning',
                            'category' => 'posts',
                        ];
                    });
                $results = array_merge($results, $posts->toArray());
            }
        }

        $this->results = array_slice($results, 0, 50);
    }

    public function render()
    {
        return view('livewire.admin.search.global-search');
    }
}

<?php

namespace App\Livewire\Main;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Livewire\Component;

class IndexComponent extends Component
{
    public $posts;
    public $teamMembers;
    public $services;
    public $projects;

    public function mount()
    {
        $this->teamMembers = User::spotlightTeam(3);

        $this->posts = Post::with(['categories', 'author'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // ─── Active services (limit 6 for slider) ──────────────────────
        $this->services = Service::where('status', 'active')
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        // ─── Published projects (limit 4 for slider) ────────────────────
        $this->projects = Project::with('service')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.main.index-component');
    }
}

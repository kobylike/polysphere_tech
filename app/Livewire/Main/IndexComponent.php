<?php

namespace App\Livewire\Main;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;

class IndexComponent extends Component
{
    public $posts;
    public $teamMembers;

    public function mount()
    {
        $this->teamMembers = User::spotlightTeam(3);

        $this->posts = Post::with(['categories', 'author'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')->latest()->take(3)->get();
    }

    public function render()
    {
        return view('livewire.main.index-component');
    }
}

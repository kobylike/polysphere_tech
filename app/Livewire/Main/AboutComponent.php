<?php

namespace App\Livewire\Main;

use App\Models\User;
use Livewire\Component;

class AboutComponent extends Component
{
    public $teamMembers;

    public function mount()
    {
        $this->teamMembers = User::spotlightTeam(3);
    }

    public function render()
{
    return view('livewire.main.about-component')
        ->layoutData([
            'description' => 'Learn who we are, what we build, and how Polysphere Tech helps businesses scale with custom software.',
            'canonical'   => route('about'),
        ])
        ->title('About Us | Polysphere Tech');
}
}

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
        return view('livewire.main.about-component');
    }
}

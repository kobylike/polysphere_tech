<?php

namespace App\Livewire\Main;

use App\Models\User;
use Livewire\Component;

class AboutComponent extends Component
{
    public $teamMembers;
    public function mount()
    {

        $this->teamMembers = User::whereHas('profile', function ($query) {
            $query->where('is_featured_team', true);
        })
            ->with('profile')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->orderBy('user_profiles.display_order', 'asc')
            ->select('users.*')
            ->get();
    }
    public function render()
    {
        return view('livewire.main.about-component');
    }
}

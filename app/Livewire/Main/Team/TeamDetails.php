<?php

namespace App\Livewire\Main\Team;

use App\Models\User;

use Livewire\Component;

class TeamDetails extends Component
{
    public User $member;

    public function mount(?string $slug = null): void
    {
        // If no slug was supplied at all (or it's empty), bail to a real 404
        // instead of letting the container choke on a missing required param.
        abort_if(blank($slug), 404);

        $this->member = User::where('username', $slug)
            ->with('profile')
            ->whereHas('profile', function ($query) {
                $query->where('is_featured_team', true);
            })
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.main.team.team-details');
    }
}

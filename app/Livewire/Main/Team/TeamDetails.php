<?php

namespace App\Livewire\Main\Team;

use App\Models\User;

use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class TeamDetails extends Component
{
    public User $member;

    public function mount(string $slug): void
    {
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

<?php

namespace App\Livewire\Main\Team;

use App\Models\User;

use Livewire\Component;


class TeamComponent extends Component
{
    public function render()
    {
        $teamMembers = User::whereHas('profile', function ($query) {
            $query->where('is_featured_team', true);
        })
            ->with('profile')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->orderBy('user_profiles.display_order', 'asc')
            ->select('users.*')
            ->get();

        return view('livewire.main.team.team-component', [
            'teamMembers' => $teamMembers,
        ])->layoutData([
            'description' => 'Meet the engineers, designers and strategists behind Polysphere Tech.',
            'canonical'   => route('team'),
        ])->title('Our Team | Polysphere Tech');
    }
}

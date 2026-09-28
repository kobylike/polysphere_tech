<?php

namespace App\Livewire\Main\Team;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
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

    private function metaDescription(): string
    {
        if (filled($this->member->about_me)) {
            return Str::limit(strip_tags($this->member->about_me), 160);
        }

        $position = $this->member->profile?->position;

        return $position
            ? Str::limit("{$this->member->name}, {$position} at Polysphere Tech.", 160)
            : Str::limit("{$this->member->name} is part of the Polysphere Tech team.", 160);
    }

    public function render()
    {
        return view('livewire.main.team.team-details')
            ->layoutData([
                'description' => $this->metaDescription(),
                'canonical'   => route('team.details', $this->member->username),
                'ogImage'     => $this->member->avatar_url ?? null,
                'ogType'      => 'profile',
            ])
            ->title($this->member->name . ' | Polysphere Tech Team');
    }
}

<?php

namespace App\Livewire\Admin\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public $user;

    protected function getListeners()
    {
        return [
            'permissions-updated' => 'syncPermissions',
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function syncPermissions($payload = null): void
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.admin.partials.sidebar', [
            'user' => $this->user,
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Users\Account;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.users')]
class AccountSettings extends Component
{
    #[Url(as: 'tab')]
    public string $tab = 'overview';

    public $user;

    protected function getListeners()
    {
        return [
            'own-profile-updated' => 'syncProfile',
        ];
    }

    public function mount(?string $tab = null): void
    {
        if ($tab && in_array($tab, ['overview', 'profile', 'security', 'activity', 'notifications'])) {
            $this->tab = $tab;
        } else {
            $this->tab = 'overview';
        }

        $this->user = Auth::user();
    }

    public function syncProfile($payload = null): void
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.admin.users.account.account-settings', [
            'user' => $this->user,
        ]);
    }
}

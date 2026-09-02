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
        if ($tab && in_array($tab, ['overview', 'profile', 'security', 'activity'])) {
            $this->tab = $tab;
        } else {
            $this->tab = 'overview';
        }

        $this->user = Auth::user();
    }

    /**
     * Handle the real‑time profile update event.
     * The $payload is optional in case the event is dispatched without data.
     */
    public function syncProfile($payload = null): void
    {
        // Reload the user from the database – this re‑evaluates avatar_url with fresh timestamp
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.admin.users.account.account-settings', [
            'user' => $this->user,
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Users\Account;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.users')]
class AccountSettings extends Component
{
    #[Url(as: 'tab')]
    public string $tab = 'overview';

    public function mount(?string $tab = null): void
    {
        if ($tab && in_array($tab, ['overview', 'profile', 'security', 'activity'])) {
            $this->tab = $tab;
        } else {
            $this->tab = 'overview';
        }
    }

    public function render()
    {
        return view('livewire.admin.users.account.account-settings');
    }
}

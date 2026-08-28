<?php

namespace App\Livewire\Admin\Users;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.users')]
class AssignPermissionToRole extends Component
{
    public function render()
    {
        return view('livewire.admin.users.assign-permission-to-role');
    }
}

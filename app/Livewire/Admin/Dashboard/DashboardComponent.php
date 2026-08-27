<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.users')]
class DashboardComponent extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-component');
    }
}

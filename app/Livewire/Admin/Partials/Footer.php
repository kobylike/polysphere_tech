<?php

namespace App\Livewire\Admin\Partials;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.users')]
class Footer extends Component
{
    public function render()
    {
        return view('livewire.admin.partials.footer');
    }
}

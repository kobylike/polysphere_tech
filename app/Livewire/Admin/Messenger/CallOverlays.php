<?php

namespace App\Livewire\Admin\Messenger;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CallOverlays extends Component
{
    public function render()
    {

        if (! Auth::check()) {
            return view('livewire.admin.messenger.call-overlays-empty');
        }

        return view('livewire.admin.messenger.call-overlays');
    }
}

<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Title('Privacy Policy - Polysphere Tech')]
class PrivacyComponent extends Component
{
    public string $lastUpdated = 'September 11, 2026';
    public string $version = '1.0';

    public function render()
    {
        return view('livewire.auth.privacy-component');
    }
}
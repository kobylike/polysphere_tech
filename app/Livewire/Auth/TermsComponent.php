<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Title('Terms & Conditions - Polysphere Tech')]
class TermsComponent extends Component
{
    public string $lastUpdated = 'September 24, 2026';
    public string $version = '2.0';

    public function render()
    {
        return view('livewire.auth.terms-component');
    }
}

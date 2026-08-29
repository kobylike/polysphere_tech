<?php

namespace App\Livewire\Admin\Partials;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Navbar extends Component
{

    public function logout()
    {
        try {
            Auth::logout();
            session()->regenerateToken();
            session()->invalidate();

            return redirect()->route('login');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return redirect()->route('login');
        }
    }
    public function render()
    {
        return view('livewire.admin.partials.navbar');
    }
}

<?php

namespace App\Livewire\Admin\Partials;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Navbar extends Component
{
    public $user;

    protected function getListeners()
    {
        return [
            'own-profile-updated' => 'syncProfile',
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function syncProfile($payload = null): void
    {
        // Reload the user from the database – re‑evaluates avatar_url with fresh timestamp
        $this->user = Auth::user();
    }

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
        return view('livewire.admin.partials.navbar', [
            'user' => $this->user,
        ]);
    }
}

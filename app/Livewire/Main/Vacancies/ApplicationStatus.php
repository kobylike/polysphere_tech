<?php

namespace App\Livewire\Main\Vacancies;

use App\Models\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;


class ApplicationStatus extends Component
{
    public Application $application;

    public function mount(string $token): void
    {
        $this->application = Application::with('vacancy.department')
            ->where('tracking_token', $token)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.main.vacancies.application-status');
    }
}

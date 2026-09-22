<?php

namespace App\Livewire\Main\Partials;

use App\Models\Vacancy;
use Livewire\Component;

class VacancyCard extends Component
{
    public Vacancy $vacancy;
    public bool $featured = false;

    public function mount(Vacancy $vacancy, bool $featured = false): void
    {
        $this->vacancy  = $vacancy;
        $this->featured = $featured;
    }

    public function render()
    {
        return view('livewire.main.partials.vacancy-card');
    }
}

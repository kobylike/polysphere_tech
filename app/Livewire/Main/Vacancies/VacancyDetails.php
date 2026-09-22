<?php

namespace App\Livewire\Main\Vacancies;

use App\Enums\VacancyStatus;
use App\Models\Vacancy;
use Livewire\Component;

class VacancyDetails extends Component
{
    public Vacancy $vacancy;

    public function mount(string $slug): void
    {
        $this->vacancy = Vacancy::with('department')
            ->where('slug', $slug)
            ->where('status', VacancyStatus::Published->value)
            ->firstOrFail();

        // Increment view count (silent – we don't want to touch updated_at)
        Vacancy::whereKey($this->vacancy->id)->update([
            'views_count' => $this->vacancy->views_count + 1,
        ]);

        $this->vacancy->refresh();
    }

    public function render()
    {
        $related = Vacancy::with('department')
            ->where('status', VacancyStatus::Published->value)
            ->whereKeyNot($this->vacancy->id)
            ->when($this->vacancy->department_id, fn($q) => $q->where('department_id', $this->vacancy->department_id))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        if ($related->isEmpty()) {
            $related = Vacancy::with('department')
                ->where('status', VacancyStatus::Published->value)
                ->whereKeyNot($this->vacancy->id)
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->limit(3)
                ->get();
        }

        return view('livewire.main.vacancies.vacancy-details', [
            'related' => $related,
        ]);
    }
}

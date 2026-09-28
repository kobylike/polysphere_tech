<?php

namespace App\Livewire\Main\Vacancies;

use App\Enums\VacancyStatus;
use App\Models\Vacancy;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
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

    private function metaDescription(): string
    {
        if (!empty($this->vacancy->meta_description)) {
            return $this->vacancy->meta_description;
        }

        return Str::limit(strip_tags((string) $this->vacancy->summary), 160);
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
        ])
            ->layoutData([
                'description' => $this->metaDescription(),
                'canonical'   => route('vacancy.details', $this->vacancy->slug),
                'ogType'      => 'website',
                // Closed/expired listings stay reachable but drop out of search.
                // `false` is passed through intact for open vacancies.
                'noindex'     => !$this->vacancy->is_open,
            ])
            ->title($this->vacancy->meta_title ?: $this->vacancy->title . ' | Polysphere Tech Careers');
    }
}

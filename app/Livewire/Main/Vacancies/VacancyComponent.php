<?php

namespace App\Livewire\Main\Vacancies;

use App\Enums\EmploymentType;
use App\Enums\VacancyStatus;
use App\Enums\WorkplaceType;
use App\Models\Department;
use App\Models\Vacancy;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VacancyComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'dept', history: true)]
    public string $departmentFilter = '';

    #[Url(as: 'type', history: true)]
    public string $employmentTypeFilter = '';

    #[Url(as: 'workplace', history: true)]
    public string $workplaceTypeFilter = '';

    public int $perPage = 9;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingDepartmentFilter(): void
    {
        $this->resetPage();
    }
    public function updatingEmploymentTypeFilter(): void
    {
        $this->resetPage();
    }
    public function updatingWorkplaceTypeFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'departmentFilter', 'employmentTypeFilter', 'workplaceTypeFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $base = Vacancy::query()
            ->with('department')
            ->where('status', VacancyStatus::Published->value)
            ->where(function ($q) {
                $q->whereNull('closing_date')->orWhere('closing_date', '>=', now());
            });

        $vacancies = (clone $base)
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('title', 'like', $term)
                        ->orWhere('summary', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('location', 'like', $term);
                });
            })
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->employmentTypeFilter, fn($q) => $q->where('employment_type', $this->employmentTypeFilter))
            ->when($this->workplaceTypeFilter, fn($q) => $q->where('workplace_type', $this->workplaceTypeFilter))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $featuredVacancies = (clone $base)
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('livewire.main.vacancies.vacancy-component', [
            'vacancies'          => $vacancies,
            'featuredVacancies'  => $featuredVacancies,
            'departments'        => Department::orderBy('name')->get(),
            'employmentTypes'    => EmploymentType::cases(),
            'workplaceTypes'     => WorkplaceType::cases(),
            'totalOpen'          => (clone $base)->count(),
            'totalDepartments'   => (clone $base)->distinct('department_id')->count('department_id'),
            'totalRemote'        => (clone $base)->where('workplace_type', WorkplaceType::Remote->value)->count(),
        ]);
    }
}

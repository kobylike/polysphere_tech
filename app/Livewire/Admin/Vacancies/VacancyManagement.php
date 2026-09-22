<?php

namespace App\Livewire\Admin\Vacancies;

use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\VacancyStatus;
use App\Enums\WorkplaceType;
use App\Helpers\ActivityLogger;
use App\Models\Department;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.users')]
class VacancyManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Filters ──────────────────────────────────────────────────────────
    public string $search = '';
    public string $statusFilter = '';
    public string $departmentFilter = '';
    public string $employmentTypeFilter = '';
    public string $workplaceTypeFilter = '';
    public string $featuredFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    // ─── Modals ──────────────────────────────────────────────────────────
    public bool $showVacancyModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;
    public bool $showBulkArchiveModal = false;
    public bool $showPublishModal = false;
    public bool $showCloseModal = false;
    public bool $showArchiveModal = false;

    // ─── Selected entities ────────────────────────────────────────────────
    public ?int $selectedVacancyId = null;
    public ?Vacancy $viewingVacancy = null;
    public ?int $publishVacancyId = null;
    public ?int $closeVacancyId = null;
    public ?int $archiveVacancyId = null;

    // ─── Bulk selection ─────────────────────────────────────────────────
    public array $selectedVacancies = [];
    public bool $selectAll = false;

    // ─── Department quick-add ─────────────────────────────────────────────
    public string $newDepartmentName = '';
    public bool $showNewDepartment = false;

    // ─── Vacancy form fields ──────────────────────────────────────────────
    public ?int $department_id = null;
    public string $title = '';
    public string $summary = '';
    public string $description = '';
    public string $responsibilities = '';
    public string $requirements = '';
    public string $benefits = '';
    public string $employment_type = 'full_time';
    public string $experience_level = 'mid';
    public string $workplace_type = 'onsite';
    public string $location = '';
    public string $country = '';
    public ?string $salary_min = null;
    public ?string $salary_max = null;
    public string $salary_currency = 'USD';
    public bool $is_salary_visible = false;
    public int $positions_available = 1;
    public string $status = 'draft';
    public bool $is_featured = false;
    public ?string $published_at = null;
    public ?string $closing_date = null;
    public string $meta_title = '';
    public string $meta_description = '';
    public bool $isEditing = false;

    protected string $defaultStatus = 'draft';

    // ─── Mount ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->authorize('viewAny', Vacancy::class);
    }

    // ─── Validation ─────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'department_id' => ['nullable', 'exists:departments,id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string', 'min:50'],
            'responsibilities' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'employment_type' => ['required', Rule::enum(EmploymentType::class)],
            'experience_level' => ['required', Rule::enum(ExperienceLevel::class)],
            'workplace_type' => ['required', Rule::enum(WorkplaceType::class)],
            'location' => ['nullable', 'string', 'max:255', 'required_if:workplace_type,onsite,hybrid'],
            'country' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['required', 'string', 'size:3'],
            'is_salary_visible' => ['boolean'],
            'positions_available' => ['required', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', Rule::enum(VacancyStatus::class)],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'closing_date' => ['nullable', 'date', 'after:today'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'location.required_if' => 'Location is required for on-site or hybrid roles.',
            'salary_max.gte' => 'Maximum salary must be greater than or equal to the minimum salary.',
            'closing_date.after' => 'Closing date must be a future date.',
            'description.min' => 'The job description looks too short — add more detail (min. 50 characters).',
        ];
    }

    // ─── Query String ─────────────────────────────────────────────────────

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'departmentFilter' => ['except' => ''],
        'employmentTypeFilter' => ['except' => ''],
        'workplaceTypeFilter' => ['except' => ''],
        'featuredFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
        'perPage' => ['except' => 15],
    ];

    // ─── Filters / Sorting ────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingStatusFilter(): void
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
    public function updatingFeaturedFilter(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
            'departmentFilter',
            'employmentTypeFilter',
            'workplaceTypeFilter',
            'featuredFilter',
            'selectedVacancies',
            'selectAll',
        ]);
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->perPage = 15;
        $this->resetPage();
    }

    // ─── Departments (quick add) ────────────────────────────────────────

    public function addDepartment(): void
    {
        $name = trim($this->newDepartmentName);

        if (! $name) {
            $this->addError('newDepartmentName', 'Type a department name before adding it.');
            return;
        }

        if (Department::whereRaw('LOWER(name) = ?', [strtolower($name)])->exists()) {
            $this->addError('newDepartmentName', "\"{$name}\" already exists.");
            return;
        }

        $department = Department::create(['name' => $name]);

        $this->department_id = $department->id;
        $this->newDepartmentName = '';
        $this->showNewDepartment = false;

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Success', 'message' => "Department '{$name}' added."]);
    }

    // ─── Activity Log ─────────────────────────────────────────────────────

    private function logActivity(string $action, array $payload = []): void
    {
        if (class_exists(ActivityLogger::class)) {
            try {
                ActivityLogger::log($action, $payload, 'vacancy');
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    // ─── View Vacancy ─────────────────────────────────────────────────────

    public function viewVacancy(int $id): void
    {
        $vacancy = Vacancy::with(['department', 'creator', 'updater'])->findOrFail($id);
        $this->authorize('view', $vacancy);

        $this->viewingVacancy = $vacancy;
        $this->showViewModal = true;
    }

    // ─── Create / Edit ────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->authorize('create', Vacancy::class);

        $this->resetFormFields();
        $this->isEditing = false;
        $this->status = $this->defaultStatus;
        $this->showVacancyModal = true;
    }

    public function openEdit(int $id): void
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('update', $vacancy);

        $this->selectedVacancyId = $vacancy->id;
        $this->department_id = $vacancy->department_id;
        $this->title = $vacancy->title;
        $this->summary = (string) $vacancy->summary;
        $this->description = $vacancy->description;
        $this->responsibilities = (string) $vacancy->responsibilities;
        $this->requirements = (string) $vacancy->requirements;
        $this->benefits = (string) $vacancy->benefits;
        $this->employment_type = $vacancy->employment_type->value;
        $this->experience_level = $vacancy->experience_level->value;
        $this->workplace_type = $vacancy->workplace_type->value;
        $this->location = (string) $vacancy->location;
        $this->country = (string) $vacancy->country;
        $this->salary_min = $vacancy->salary_min !== null ? (string) $vacancy->salary_min : null;
        $this->salary_max = $vacancy->salary_max !== null ? (string) $vacancy->salary_max : null;
        $this->salary_currency = $vacancy->salary_currency;
        $this->is_salary_visible = $vacancy->is_salary_visible;
        $this->positions_available = $vacancy->positions_available;
        $this->status = $vacancy->status->value;
        $this->is_featured = $vacancy->is_featured;
        $this->published_at = $vacancy->published_at?->format('Y-m-d\TH:i');
        $this->closing_date = $vacancy->closing_date?->format('Y-m-d\TH:i');
        $this->meta_title = (string) $vacancy->meta_title;
        $this->meta_description = (string) $vacancy->meta_description;

        $this->isEditing = true;
        $this->showVacancyModal = true;
    }

    public function saveVacancy(): void
    {
        $data = $this->validate();

        try {
            if ($this->isEditing) {
                $vacancy = Vacancy::findOrFail($this->selectedVacancyId);
                $this->authorize('update', $vacancy);

                $wasPublished = $vacancy->status === VacancyStatus::Published;

                if ($data['status'] === VacancyStatus::Published->value && ! $wasPublished && empty($data['published_at'])) {
                    $data['published_at'] = now();
                }

                $vacancy->update($data);

                $this->logActivity('Vacancy updated', [
                    'vacancy_id' => $vacancy->id,
                    'title' => $vacancy->title,
                    'status' => $vacancy->status->value,
                    'updated_by' => Auth::id(),
                ]);

                $this->dispatch('notify', ['type' => 'success', 'title' => 'Updated!', 'message' => "\"{$vacancy->title}\" has been updated."]);
            } else {
                $this->authorize('create', Vacancy::class);

                if ($data['status'] === VacancyStatus::Published->value && empty($data['published_at'])) {
                    $data['published_at'] = now();
                }

                $vacancy = Vacancy::create($data);

                $this->logActivity('Vacancy created', [
                    'vacancy_id' => $vacancy->id,
                    'title' => $vacancy->title,
                    'status' => $vacancy->status->value,
                    'created_by' => Auth::id(),
                ]);

                $this->dispatch('notify', ['type' => 'success', 'title' => 'Created!', 'message' => "\"{$vacancy->title}\" has been created."]);
            }

            $this->showVacancyModal = false;
            $this->resetFormFields();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Save failed', 'message' => $e->getMessage()]);
        }
    }

    public function duplicateVacancy(int $id): void
    {
        $original = Vacancy::findOrFail($id);
        $this->authorize('create', Vacancy::class);

        $copy = $original->replicate(['slug', 'views_count', 'applications_count', 'published_at']);
        $copy->title = $original->title . ' (Copy)';
        $copy->status = VacancyStatus::Draft->value;
        $copy->is_featured = false;
        $copy->views_count = 0;
        $copy->applications_count = 0;
        $copy->published_at = null;
        $copy->save();

        $this->logActivity('Vacancy duplicated', [
            'source_id' => $original->id,
            'new_id' => $copy->id,
            'duplicated_by' => Auth::id(),
        ]);

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Duplicated', 'message' => "A draft copy of \"{$original->title}\" was created."]);
    }

    private function resetFormFields(): void
    {
        $this->reset([
            'department_id',
            'title',
            'summary',
            'description',
            'responsibilities',
            'requirements',
            'benefits',
            'employment_type',
            'experience_level',
            'workplace_type',
            'location',
            'country',
            'salary_min',
            'salary_max',
            'salary_currency',
            'is_salary_visible',
            'positions_available',
            'status',
            'is_featured',
            'published_at',
            'closing_date',
            'meta_title',
            'meta_description',
            'selectedVacancyId',
            'newDepartmentName',
            'showNewDepartment',
        ]);

        $this->employment_type = 'full_time';
        $this->experience_level = 'mid';
        $this->workplace_type = 'onsite';
        $this->salary_currency = 'USD';
        $this->positions_available = 1;
        $this->status = $this->defaultStatus;
    }

    // ─── Single Delete ────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('delete', $vacancy);
        $this->selectedVacancyId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteVacancy(): void
    {
        $vacancy = Vacancy::findOrFail($this->selectedVacancyId);
        $this->authorize('delete', $vacancy);
        $vacancy->delete();

        $this->logActivity('Vacancy deleted', [
            'vacancy_id' => $vacancy->id,
            'title' => $vacancy->title,
            'deleted_by' => Auth::id(),
        ]);

        $this->showDeleteModal = false;
        $this->selectedVacancyId = null;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Deleted', 'message' => 'Vacancy deleted successfully.']);
    }

    // ─── Bulk selection ──────────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedVacancies = $value
            ? $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function updatedSelectedVacancies(): void
    {
        $total = $this->getQuery()->count();
        $this->selectAll = $total > 0 && count($this->selectedVacancies) === $total;
    }

    // ─── Bulk Delete ──────────────────────────────────────────────────────

    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedVacancies)) {
            return;
        }
        $this->authorize('delete', Vacancy::class);
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        $this->authorize('delete', Vacancy::class);
        $ids = $this->selectedVacancies;

        foreach (Vacancy::whereIn('id', $ids)->get() as $vacancy) {
            $this->logActivity('Vacancy deleted (bulk)', [
                'vacancy_id' => $vacancy->id,
                'title' => $vacancy->title,
                'deleted_by' => Auth::id(),
            ]);
        }

        Vacancy::whereIn('id', $ids)->delete();

        $this->selectedVacancies = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Deleted', 'message' => count($ids) . ' vacancy(ies) deleted.']);
    }

    // ─── Bulk Publish / Close / Archive ──────────────────────────────────

    public function bulkPublish(): void
    {
        if (empty($this->selectedVacancies)) return;
        $this->authorize('update', Vacancy::class);

        Vacancy::whereIn('id', $this->selectedVacancies)->get()->each->publish();

        $this->logActivity('Vacancies published (bulk)', [
            'vacancy_ids' => $this->selectedVacancies,
            'published_by' => Auth::id(),
        ]);

        $count = count($this->selectedVacancies);
        $this->selectedVacancies = [];
        $this->selectAll = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Published', 'message' => "{$count} vacancy(ies) published."]);
    }

    public function bulkClose(): void
    {
        if (empty($this->selectedVacancies)) return;
        $this->authorize('update', Vacancy::class);

        Vacancy::whereIn('id', $this->selectedVacancies)->get()->each->close();

        $this->logActivity('Vacancies closed (bulk)', [
            'vacancy_ids' => $this->selectedVacancies,
            'closed_by' => Auth::id(),
        ]);

        $count = count($this->selectedVacancies);
        $this->selectedVacancies = [];
        $this->selectAll = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Closed', 'message' => "{$count} vacancy(ies) closed."]);
    }

    public function confirmBulkArchive(): void
    {
        if (empty($this->selectedVacancies)) {
            return;
        }
        $this->authorize('update', Vacancy::class);
        $this->showBulkArchiveModal = true;
    }

    public function bulkArchive(): void
    {
        if (empty($this->selectedVacancies)) return;
        $this->authorize('update', Vacancy::class);

        Vacancy::whereIn('id', $this->selectedVacancies)->get()->each->archive();

        $this->logActivity('Vacancies archived (bulk)', [
            'vacancy_ids' => $this->selectedVacancies,
            'archived_by' => Auth::id(),
        ]);

        $count = count($this->selectedVacancies);
        $this->selectedVacancies = [];
        $this->selectAll = false;
        $this->showBulkArchiveModal = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Archived', 'message' => "{$count} vacancy(ies) archived."]);
    }

    // ─── Publish / Close / Archive (single, confirmed) ────────────────────

    public function confirmPublish(int $id): void
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('update', $vacancy);
        $this->publishVacancyId = $id;
        $this->showPublishModal = true;
    }

    public function publishConfirmed(): void
    {
        if (! $this->publishVacancyId) return;
        $vacancy = Vacancy::findOrFail($this->publishVacancyId);
        $this->authorize('update', $vacancy);
        $vacancy->publish();

        $this->logActivity('Vacancy published', [
            'vacancy_id' => $vacancy->id,
            'title' => $vacancy->title,
            'published_by' => Auth::id(),
        ]);

        $this->showPublishModal = false;
        $this->publishVacancyId = null;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Published', 'message' => "\"{$vacancy->title}\" is now live."]);
    }

    public function confirmClose(int $id): void
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('update', $vacancy);
        $this->closeVacancyId = $id;
        $this->showCloseModal = true;
    }

    public function closeConfirmed(): void
    {
        if (! $this->closeVacancyId) return;
        $vacancy = Vacancy::findOrFail($this->closeVacancyId);
        $this->authorize('update', $vacancy);
        $vacancy->close();

        $this->logActivity('Vacancy closed', [
            'vacancy_id' => $vacancy->id,
            'title' => $vacancy->title,
            'closed_by' => Auth::id(),
        ]);

        $this->showCloseModal = false;
        $this->closeVacancyId = null;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Closed', 'message' => "\"{$vacancy->title}\" is no longer accepting applications."]);
    }

    public function confirmArchive(int $id): void
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('update', $vacancy);
        $this->archiveVacancyId = $id;
        $this->showArchiveModal = true;
    }

    public function archiveConfirmed(): void
    {
        if (! $this->archiveVacancyId) return;
        $vacancy = Vacancy::findOrFail($this->archiveVacancyId);
        $this->authorize('update', $vacancy);
        $vacancy->archive();

        $this->logActivity('Vacancy archived', [
            'vacancy_id' => $vacancy->id,
            'title' => $vacancy->title,
            'archived_by' => Auth::id(),
        ]);

        $this->showArchiveModal = false;
        $this->archiveVacancyId = null;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Archived', 'message' => "\"{$vacancy->title}\" has been archived."]);
    }

    // ─── Featured toggle ──────────────────────────────────────────────────

    public function toggleFeatured(int $id): void
    {
        $vacancy = Vacancy::findOrFail($id);
        $this->authorize('update', $vacancy);

        $vacancy->update(['is_featured' => ! $vacancy->is_featured]);

        $this->logActivity('Vacancy featured status toggled', [
            'vacancy_id' => $vacancy->id,
            'title' => $vacancy->title,
            'is_featured' => $vacancy->is_featured,
            'toggled_by' => Auth::id(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => $vacancy->is_featured ? 'Featured' : 'Unfeatured',
            'message' => $vacancy->is_featured
                ? "\"{$vacancy->title}\" is now featured."
                : "\"{$vacancy->title}\" is no longer featured.",
        ]);
    }

    // ─── Export CSV ────────────────────────────────────────────────────────

    public function export(): StreamedResponse
    {
        $this->authorize('viewAny', Vacancy::class);

        $vacancies = $this->getQuery()->get();
        $filename  = 'vacancies-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($vacancies) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'Title',
                'Department',
                'Location',
                'Country',
                'Employment type',
                'Experience level',
                'Workplace',
                'Salary min',
                'Salary max',
                'Currency',
                'Salary visible',
                'Positions',
                'Status',
                'Featured',
                'Views',
                'Applications',
                'Published at',
                'Closing date',
                'Created at',
            ]);

            foreach ($vacancies as $v) {
                fputcsv($out, [
                    $v->title,
                    $v->department?->name,
                    $v->location,
                    $v->country,
                    $v->employment_type->label(),
                    $v->experience_level->label(),
                    $v->workplace_type->label(),
                    $v->salary_min,
                    $v->salary_max,
                    $v->salary_currency,
                    $v->is_salary_visible ? 'Yes' : 'No',
                    $v->positions_available,
                    $v->status->label(),
                    $v->is_featured ? 'Yes' : 'No',
                    $v->views_count,
                    $v->applications_count ?? 0,
                    $v->published_at?->toDateTimeString(),
                    $v->closing_date?->toDateTimeString(),
                    $v->created_at->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ─── Query ─────────────────────────────────────────────────────────────

    private function getQuery()
    {
        return Vacancy::query()
            ->with(['department', 'creator'])
            ->withCount('applications')
            ->search($this->search)
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->employmentTypeFilter, fn($q) => $q->where('employment_type', $this->employmentTypeFilter))
            ->when($this->workplaceTypeFilter, fn($q) => $q->where('workplace_type', $this->workplaceTypeFilter))
            ->when($this->featuredFilter === 'featured', fn($q) => $q->where('is_featured', true))
            ->when($this->featuredFilter === 'not_featured', fn($q) => $q->where('is_featured', false))
            ->orderBy($this->sortBy, $this->sortDir);
    }

    // ─── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.vacancies.vacancy-management', [
            'vacancies' => $this->getQuery()->paginate($this->perPage),
            'departments' => Department::orderBy('name')->get(),
            'stats' => [
                'total' => Vacancy::count(),
                'published' => Vacancy::where('status', VacancyStatus::Published->value)->count(),
                'draft' => Vacancy::where('status', VacancyStatus::Draft->value)->count(),
                'closed' => Vacancy::where('status', VacancyStatus::Closed->value)->count(),
                'archived' => Vacancy::where('status', VacancyStatus::Archived->value)->count(),
                'featured' => Vacancy::where('is_featured', true)->count(),
                'total_views' => (int) Vacancy::sum('views_count'),
            ],
            'statuses' => VacancyStatus::cases(),
            'employmentTypes' => EmploymentType::cases(),
            'experienceLevels' => ExperienceLevel::cases(),
            'workplaceTypes' => WorkplaceType::cases(),
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.users')]
class ApplicationManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Filters ─────────────────────────────────────────────────────
    public string $search = '';
    public string $vacancyFilter = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    // ─── Review modal ────────────────────────────────────────────────
    public bool $showViewModal = false;
    public ?Application $viewingApplication = null;
    public string $adminNotes = '';
    public string $newStatus = '';

    // ─── Single-delete modal ────────────────────────────────────────
    public bool $showDeleteModal = false;
    public ?int $deleteApplicationId = null;

    // ─── Bulk-delete modal ──────────────────────────────────────────
    public bool $showBulkDeleteModal = false;

    // ─── Bulk selection ─────────────────────────────────────────────
    public array $selectedApplications = [];
    public bool $selectAll = false;

    // ─── Mount ──────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->authorize('viewAny', Application::class);
    }

    // ─── Filters / sorting ──────────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingVacancyFilter(): void
    {
        $this->resetPage();
    }
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'vacancyFilter', 'statusFilter', 'selectedApplications', 'selectAll']);
        $this->sortBy  = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    // ─── Bulk selection ─────────────────────────────────────────────
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedApplications = $value
            ? $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function updatedSelectedApplications(): void
    {
        $total = $this->getFilteredQuery()->count();
        $this->selectAll = $total > 0 && count($this->selectedApplications) === $total;
    }

    // ─── View ───────────────────────────────────────────────────────
    public function viewApplication(int $id): void
    {
        $application = Application::with(['vacancy.department', 'reviewer'])->findOrFail($id);
        $this->authorize('view', $application);

        $this->viewingApplication = $application;
        $this->adminNotes         = (string) $application->admin_notes;
        $this->newStatus          = $application->status;
        $this->showViewModal      = true;
    }

    // ─── Save status + notes ────────────────────────────────────────
    public function saveStatus(): void
    {
        if (! $this->viewingApplication) return;
        $this->authorize('update', $this->viewingApplication);

        $this->validate([
            'newStatus'  => 'required|string',
            'adminNotes' => 'nullable|string|max:5000',
        ]);

        $status = ApplicationStatus::from($this->newStatus);
        $this->viewingApplication->markAs($status, Auth::user(), $this->adminNotes);

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Updated',
            'message' => 'Application status updated.',
        ]);
        $this->showViewModal = false;
    }

    // ─── Bulk status change ─────────────────────────────────────────
    public function bulkSetStatus(string $status): void
    {
        if (empty($this->selectedApplications)) return;

        $enum = ApplicationStatus::tryFrom($status);
        if (! $enum) return;

        $applications = Application::whereIn('id', $this->selectedApplications)->get();
        foreach ($applications as $app) {
            $this->authorize('update', $app);
            $app->markAs($enum, Auth::user());
        }

        $count = count($this->selectedApplications);
        $this->selectedApplications = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Updated',
            'message' => "{$count} application(s) moved to {$enum->label()}.",
        ]);
    }

    // ─── Single delete ──────────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $application = Application::findOrFail($id);
        $this->authorize('delete', $application);
        $this->deleteApplicationId = $id;
        $this->showDeleteModal     = true;
    }

    public function deleteApplication(): void
    {
        if (! $this->deleteApplicationId) return;

        $application = Application::findOrFail($this->deleteApplicationId);
        $this->authorize('delete', $application);

        // Remove the CV file from private storage (soft-deleted rows stay in DB)
        if ($application->cv_path && Storage::disk('private')->exists($application->cv_path)) {
            Storage::disk('private')->delete($application->cv_path);
        }

        $application->delete();

        $this->showDeleteModal     = false;
        $this->deleteApplicationId = null;
        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Deleted',
            'message' => 'Application deleted.',
        ]);
    }

    // ─── Bulk delete ────────────────────────────────────────────────
    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedApplications)) return;
        $this->authorize('delete', Application::class);
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        $this->authorize('delete', Application::class);

        $ids = $this->selectedApplications;
        $applications = Application::whereIn('id', $ids)->get();

        foreach ($applications as $app) {
            if ($app->cv_path && Storage::disk('private')->exists($app->cv_path)) {
                Storage::disk('private')->delete($app->cv_path);
            }
            $app->delete();
        }

        $count = count($ids);
        $this->selectedApplications = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Deleted',
            'message' => "{$count} application(s) deleted.",
        ]);
    }

    // ─── Download CV ────────────────────────────────────────────────
    public function downloadCv(int $id): StreamedResponse
    {
        $application = Application::findOrFail($id);
        $this->authorize('view', $application);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('private');
        abort_unless($disk->exists($application->cv_path), 404);

        return $disk->download(
            $application->cv_path,
            $application->cv_original_name ?: ('cv-' . $application->id . '.pdf'),
        );
    }

    // ─── Export CSV ─────────────────────────────────────────────────
    public function export(): StreamedResponse
    {
        $this->authorize('viewAny', Application::class);

        $applications = $this->getFilteredQuery()->get();
        $filename     = 'applications-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($applications) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Applied at',
                'Name',
                'Email',
                'Phone',
                'Location',
                'Country',
                'Vacancy',
                'Department',
                'Current role',
                'Current company',
                'Years of experience',
                'Availability',
                'Salary expectation',
                'Timezone',
                'Work authorization',
                'LinkedIn',
                'Portfolio',
                'GitHub',
                'Personal site',
                'Behance',
                'Dribbble',
                'Writing samples',
                'Source',
                'Referrer',
                'Status',
            ]);

            foreach ($applications as $a) {
                fputcsv($out, [
                    $a->created_at->toDateTimeString(),
                    $a->name,
                    $a->email,
                    $a->phone,
                    $a->location,
                    $a->country,
                    $a->vacancy?->title,
                    $a->vacancy?->department?->name,
                    $a->current_role,
                    $a->current_company,
                    $a->years_experience,
                    $a->availability,
                    $a->salary_expectation,
                    $a->timezone,
                    $a->work_authorization,
                    $a->linkedin_url,
                    $a->portfolio_url,
                    $a->github_url,
                    $a->personal_site_url,
                    $a->behance_url,
                    $a->dribbble_url,
                    $a->writing_samples_url,
                    $a->source,
                    $a->referrer_name,
                    $a->statusEnum()->label(),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ─── Filtered query (shared by paginate + export + select-all) ──
    protected function getFilteredQuery()
    {
        return Application::query()
            ->with(['vacancy.department', 'reviewer'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('current_role', 'like', $term)
                        ->orWhere('current_company', 'like', $term);
                });
            })
            ->when($this->vacancyFilter, fn($q) => $q->where('vacancy_id', $this->vacancyFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDir);
    }

    // ─── Render ─────────────────────────────────────────────────────
    public function render()
    {
        $applications = $this->getFilteredQuery()->paginate($this->perPage);

        return view('livewire.admin.applications.application-management', [
            'applications' => $applications,
            'vacancies'    => Vacancy::orderBy('title')->get(['id', 'title']),
            'statuses'     => ApplicationStatus::cases(),
            'stats'        => [
                'total'       => Application::count(),
                'new'         => Application::where('status', 'new')->count(),
                'in_progress' => Application::whereIn('status', ['reviewing', 'shortlisted', 'interviewing'])->count(),
                'hired'       => Application::where('status', 'hired')->count(),
                'rejected'    => Application::where('status', 'rejected')->count(),
                'this_week'   => Application::where('created_at', '>=', now()->subWeek())->count(),
            ],
        ]);
    }
}

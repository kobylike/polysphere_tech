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

    public string $search = '';
    public string $vacancyFilter = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    public bool $showViewModal = false;
    public ?Application $viewingApplication = null;

    public string $adminNotes = '';
    public string $newStatus = '';

    // ─── Mount-time gate ──────────────────────────────────────────────
    public function mount(): void
    {
        $this->authorize('viewAny', Application::class);
    }

    // ─── Filters ──────────────────────────────────────────────────────
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
        $this->reset(['search', 'vacancyFilter', 'statusFilter']);
        $this->sortBy  = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    // ─── View ─────────────────────────────────────────────────────────
    public function viewApplication(int $id): void
    {
        $application = Application::with(['vacancy.department', 'reviewer'])->findOrFail($id);
        $this->authorize('view', $application);

        $this->viewingApplication = $application;
        $this->adminNotes = (string) $application->admin_notes;
        $this->newStatus  = $application->status;
        $this->showViewModal = true;
    }

    // ─── Update ───────────────────────────────────────────────────────
    public function saveStatus(): void
    {
        if (! $this->viewingApplication) {
            return;
        }

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

    // ─── Download CV ──────────────────────────────────────────────────
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

    // ─── Render ───────────────────────────────────────────────────────
    public function render()
    {
        $applications = Application::query()
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
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

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

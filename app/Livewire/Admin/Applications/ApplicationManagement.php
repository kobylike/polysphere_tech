<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Helpers\ActivityLogger;
use App\Helpers\NotificationHelper;
use App\Mail\ApplicationCandidateReplyMail;
use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
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

    // ─── Compose email modal ────────────────────────────────────────
    public bool $showEmailModal = false;
    public ?int $emailApplicationId = null;
    public string $emailTo = '';
    public string $emailSubject = '';
    public string $emailBody = '';
    public bool $emailSending = false;

    // ─── Sent-email tracker (read from activity log) ────────────────
    public int $emailsSentCount = 0;
    public ?string $lastEmailedAt = null;

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

        $status    = ApplicationStatus::from($this->newStatus);
        $oldStatus = $this->viewingApplication->status;
        $candidate = $this->viewingApplication->name;
        $roleTitle = $this->viewingApplication->vacancy?->title ?? 'the role';

        $this->viewingApplication->markAs($status, Auth::user(), $this->adminNotes);

        try {
            ActivityLogger::log('Application status updated', [
                'application_id' => $this->viewingApplication->id,
                'vacancy_title'  => $roleTitle,
                'candidate'      => $candidate,
                'old_status'     => $oldStatus,
                'new_status'     => $status->value,
                'updated_by'     => Auth::id(),
            ], 'application');
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            NotificationHelper::sendToUser(Auth::user(), [
                'title' => 'Status updated',
                'body'  => "{$candidate}'s application for {$roleTitle} is now {$status->label()}.",
                'type'  => 'success',
                'icon'  => 'fa-clipboard-check',
                'link'  => route('admin.applications'),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            $others = User::role(['Super Admin', 'Admin'])
                ->where('id', '!=', Auth::id())
                ->get();

            if ($others->isNotEmpty()) {
                NotificationHelper::sendToUsers($others, [
                    'title' => 'Application status updated',
                    'body'  => Auth::user()->name . " moved {$candidate} to {$status->label()} for {$roleTitle}.",
                    'type'  => 'info',
                    'icon'  => 'fa-user-pen',
                    'link'  => route('admin.applications'),
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $this->showViewModal = false;
    }

    // ─── Bulk status change ─────────────────────────────────────────
    public function bulkSetStatus(string $status): void
    {
        if (empty($this->selectedApplications)) return;

        $enum = ApplicationStatus::tryFrom($status);
        if (! $enum) return;

        $applications = Application::whereIn('id', $this->selectedApplications)->get();
        $count        = $applications->count();

        foreach ($applications as $app) {
            $this->authorize('update', $app);
            $app->markAs($enum, Auth::user());
        }

        try {
            ActivityLogger::log('Applications bulk status updated', [
                'application_ids' => $applications->pluck('id')->all(),
                'new_status'      => $enum->value,
                'count'           => $count,
                'updated_by'      => Auth::id(),
            ], 'application');
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            NotificationHelper::sendToUser(Auth::user(), [
                'title' => 'Bulk status update',
                'body'  => "{$count} application(s) moved to {$enum->label()}.",
                'type'  => 'success',
                'icon'  => 'fa-layer-group',
                'link'  => route('admin.applications'),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        $this->selectedApplications = [];
        $this->selectAll            = false;
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

        $snapshot = [
            'application_id' => $application->id,
            'vacancy_title'  => $application->vacancy?->title,
            'candidate'      => $application->name,
            'email'          => $application->email,
            'deleted_by'     => Auth::id(),
        ];

        if ($application->cv_path && Storage::disk('private')->exists($application->cv_path)) {
            Storage::disk('private')->delete($application->cv_path);
        }

        $application->delete();

        try {
            ActivityLogger::log('Application deleted', $snapshot, 'application');
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            NotificationHelper::sendToUser(Auth::user(), [
                'title' => 'Application deleted',
                'body'  => "{$snapshot['candidate']}'s application was removed.",
                'type'  => 'warning',
                'icon'  => 'fa-trash',
                'link'  => route('admin.applications'),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        $this->showDeleteModal     = false;
        $this->deleteApplicationId = null;
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

        $ids          = $this->selectedApplications;
        $applications = Application::whereIn('id', $ids)->get();

        foreach ($applications as $app) {
            if ($app->cv_path && Storage::disk('private')->exists($app->cv_path)) {
                Storage::disk('private')->delete($app->cv_path);
            }
            $app->delete();
        }

        $count = $applications->count();

        try {
            ActivityLogger::log('Applications bulk deleted', [
                'application_ids' => $applications->pluck('id')->all(),
                'emails'          => $applications->pluck('email')->all(),
                'count'           => $count,
                'deleted_by'      => Auth::id(),
            ], 'application');
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            NotificationHelper::sendToUser(Auth::user(), [
                'title' => 'Applications deleted',
                'body'  => "{$count} application(s) were removed.",
                'type'  => 'warning',
                'icon'  => 'fa-trash',
                'link'  => route('admin.applications'),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        $this->selectedApplications = [];
        $this->selectAll            = false;
        $this->showBulkDeleteModal  = false;
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

    // ─── Compose / send email ───────────────────────────────────────
    public function openEmailModal(int $id): void
    {
        $application = Application::with('vacancy.department')->findOrFail($id);
        $this->authorize('view', $application);

        $this->emailApplicationId = $id;
        $this->emailTo            = $application->email;
        $this->emailSubject       = $this->defaultSubjectFor($application);
        $this->emailBody          = $this->defaultBodyFor($application);

        $this->emailsSentCount = Activity::query()
            ->where('log_name', 'application_email')
            ->where('subject_type', Application::class)
            ->where('subject_id', $application->id)
            ->count();

        $this->lastEmailedAt = Activity::query()
            ->where('log_name', 'application_email')
            ->where('subject_type', Application::class)
            ->where('subject_id', $application->id)
            ->latest()
            ->first()?->created_at?->diffForHumans();

        $this->showEmailModal = true;
    }

    protected function defaultSubjectFor(Application $application): string
    {
        $role = $application->vacancy?->title ?? 'the role';

        return match ($application->status) {
            'new'          => "Your application for {$role} — we've got it",
            'reviewing'    => "Update on your {$role} application",
            'shortlisted'  => "Good news — your {$role} application",
            'interviewing' => "Interview invitation — {$role}",
            'offer'        => "Your offer for {$role}",
            'rejected'     => "Update on your application for {$role}",
            default        => "Regarding your {$role} application",
        };
    }

    protected function defaultBodyFor(Application $application): string
    {
        $role = $application->vacancy?->title ?? 'the role';
        $dept = $application->vacancy?->department?->name;

        return match ($application->status) {
            'new' => "Thanks for applying for {$role}"
                . ($dept ? " in {$dept}" : '') . ".\n\n"
                . "We've received your application and it's now in our review queue. A member of our hiring team "
                . "will read it personally, and we'll get back to you within a few working days.",

            'reviewing' => "Quick update on your {$role} application.\n\n"
                . "Our team is currently reviewing your materials. We haven't made a decision yet, but we'll be in "
                . "touch as soon as we do.",

            'shortlisted' => "Good news — you've been shortlisted for {$role}.\n\n"
                . "We were impressed by your application and would like to move forward. Our team will be in touch "
                . "shortly to arrange the next step.",

            'interviewing' => "We'd love to schedule an interview for the {$role} role.\n\n"
                . "Please let us know two or three time slots that work for you this week or next, and we'll set "
                . "something up. Interviews are typically 45 minutes and can be done remotely.",

            'offer' => "We're delighted to extend an offer for the {$role} position.\n\n"
                . "Please review the attached details and let us know if you have any questions. We're excited about "
                . "the possibility of you joining the team.",

            'rejected' => "Thank you for applying for {$role}"
                . ($dept ? " in {$dept}" : '') . ".\n\n"
                . "We've decided to move forward with candidates whose experience more closely matches what the role "
                . "needs right now. We genuinely appreciated your interest, and we'll keep your details on file for "
                . "future openings.",

            default => "Thank you for your interest in {$role}. We'll be in touch soon.",
        };
    }

    public function sendCandidateEmail(): void
    {
        if (! $this->emailApplicationId) {
            return;
        }

        $this->authorize('view', Application::class);

        $this->validate([
            'emailTo'      => 'required|email:rfc,dns|max:255',
            'emailSubject' => 'required|string|max:200',
            'emailBody'    => 'required|string|min:10|max:10000',
        ]);

        $application = Application::with('vacancy')->findOrFail($this->emailApplicationId);

        $this->emailSending = true;

        try {
            // sendNow() bypasses the queue so the admin gets immediate feedback.
            // For production, swap back to ->queue(...) and keep a worker running.
            Mail::to($this->emailTo)->sendNow(new ApplicationCandidateReplyMail(
                application: $application,
                subjectLine: $this->emailSubject,
                body: $this->emailBody,
                senderName: Auth::user()->name,
            ));

            try {
                activity('application_email')
                    ->performedOn($application)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'to'      => $this->emailTo,
                        'subject' => $this->emailSubject,
                        'body'    => $this->emailBody,
                    ])
                    ->log("Email sent to {$application->name}");
            } catch (\Throwable $e) {
                report($e);
            }

            try {
                NotificationHelper::sendToUser(Auth::user(), [
                    'title' => 'Email sent',
                    'body'  => "Your email to {$application->name} was sent.",
                    'type'  => 'success',
                    'icon'  => 'fa-paper-plane',
                    'link'  => route('admin.applications'),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }

            // ── The only toast we keep — confirmation of the send ──
            $this->dispatch(
                'notify',
                type: 'success',
                title: 'Email sent',
                message: "Message delivered to {$application->name}.",
            );

            $this->showEmailModal = false;
            $this->emailSending   = false;
            $this->reset(['emailApplicationId', 'emailTo', 'emailSubject', 'emailBody']);
        } catch (\Throwable $e) {
            report($e);
            $this->emailSending = false;

            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Could not send',
                message: 'Something went wrong sending the email. Check logs for details.',
            );
        }
    }

    public function closeEmailModal(): void
    {
        $this->showEmailModal = false;
        $this->reset(['emailApplicationId', 'emailTo', 'emailSubject', 'emailBody']);
        $this->resetErrorBag();
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

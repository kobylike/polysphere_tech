<?php

namespace App\Livewire\Main\Vacancies;

use App\Enums\VacancyStatus;
use App\Enums\WorkplaceType;
use App\Mail\ApplicationReceivedMail;
use App\Mail\ApplicationSubmittedAdminMail;
use App\Models\Application;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;


class ApplicationWizard extends Component
{
    use WithFileUploads;

    public Vacancy $vacancy;

    public int $step = 1;
    public int $totalSteps = 5;
    public array $branches = [];   // which extra field groups are active

    // ─── Step 1: Basics ─────────────────────────────────────
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $location = '';
    public string $country = '';

    // ─── Step 2: Links ──────────────────────────────────────
    public string $linkedin_url = '';
    public string $portfolio_url = '';
    public string $github_url = '';
    public string $personal_site_url = '';
    public string $behance_url = '';
    public string $dribbble_url = '';
    public string $writing_samples_url = '';

    // ─── Step 3: Experience ─────────────────────────────────
    public string $current_role = '';
    public string $current_company = '';
    public ?int $years_experience = null;
    public string $availability = '';
    public string $salary_expectation = '';
    public string $timezone = '';
    public string $work_authorization = '';

    // ─── Step 4: Documents ──────────────────────────────────
    public $cv = null;
    public string $cover_letter = '';

    // ─── Step 5: Meta ───────────────────────────────────────
    public string $source = '';
    public string $referrer_name = '';
    public bool $gdpr_consent = false;

    public function mount(string $slug): void
    {
        $this->vacancy = Vacancy::with('department')
            ->where('slug', $slug)
            ->where('status', VacancyStatus::Published->value)
            ->firstOrFail();

        $this->branches = $this->computeBranches();

        // Pre-fill location from the vacancy for convenience
        $this->location = $this->vacancy->location ?? '';
        $this->country  = $this->vacancy->country ?? '';
    }

    /**
     * Decide which "branches" of questions apply for this vacancy.
     * Branches are keyword-matched against the department name/slug.
     */
    protected function computeBranches(): array
    {
        $needle = strtolower(
            ($this->vacancy->department?->slug ?? '') . ' ' .
                ($this->vacancy->department?->name ?? '')
        );

        $branches = [];

        if (preg_match('/engineer|software|tech|develop|data|devops|security|it\b|platform|infra/', $needle)) {
            $branches[] = 'engineering';
        }
        if (preg_match('/design|ux|ui|product|creative|brand|art/', $needle)) {
            $branches[] = 'design';
        }
        if (preg_match('/market|content|seo|social|growth|communication|copy/', $needle)) {
            $branches[] = 'marketing';
        }
        if (preg_match('/sales|business|account|partner|bd\b|revenue/', $needle)) {
            $branches[] = 'sales';
        }
        if ($this->vacancy->workplace_type === WorkplaceType::Remote) {
            $branches[] = 'remote';
        }

        return array_values(array_unique($branches));
    }

    public function hasBranch(string $branch): bool
    {
        return in_array($branch, $this->branches, true);
    }

    // ─── Navigation ─────────────────────────────────────────

    public function nextStep(): void
    {
        $this->validateStep($this->step);

        if ($this->step < $this->totalSteps) {
            $this->step++;
            $this->dispatch('application-step-changed', step: $this->step);
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
            $this->dispatch('application-step-changed', step: $this->step);
        }
    }

    public function goToStep(int $step): void
    {
        // Only allow going back to a previously completed step (or the current one).
        if ($step >= 1 && $step <= $this->step) {
            $this->step = $step;
        }
    }

    protected function validateStep(int $step): void
    {
        $this->validate($this->rulesForStep($step), $this->messages());
    }

    // ─── Rules ──────────────────────────────────────────────

    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'name'     => 'required|string|min:2|max:120',
                'email'    => 'required|email:rfc,dns|max:190',
                'phone'    => 'required|string|min:6|max:40',
                'location' => 'nullable|string|max:120',
                'country'  => 'nullable|string|max:120',
            ],
            2 => $this->rulesForLinks(),
            3 => $this->rulesForExperience(),
            4 => [
                'cv'           => 'required|file|mimes:pdf,doc,docx|max:10240',
                'cover_letter' => 'nullable|string|max:4000',
            ],
            5 => [
                'source'        => 'nullable|string|max:120',
                'referrer_name' => 'nullable|string|max:120',
                'gdpr_consent'  => 'accepted',
            ],
            default => [],
        };
    }

    protected function rulesForLinks(): array
    {
        $rules = [
            'linkedin_url'     => 'nullable|url|max:255',
            'portfolio_url'    => 'nullable|url|max:255',
            'personal_site_url' => 'nullable|url|max:255',
        ];

        if ($this->hasBranch('engineering')) {
            $rules['github_url'] = 'required|url|max:255';
        }
        if ($this->hasBranch('design')) {
            $rules['portfolio_url'] = 'required|url|max:255';
            $rules['behance_url']   = 'nullable|url|max:255';
            $rules['dribbble_url']  = 'nullable|url|max:255';
        }
        if ($this->hasBranch('marketing')) {
            $rules['writing_samples_url'] = 'required|url|max:255';
        }

        return $rules;
    }

    protected function rulesForExperience(): array
    {
        $rules = [
            'current_role'       => 'nullable|string|max:120',
            'current_company'    => 'nullable|string|max:120',
            'years_experience'   => 'required|integer|min:0|max:60',
            'availability'       => 'required|string|max:120',
            'salary_expectation' => 'nullable|string|max:120',
        ];

        if ($this->hasBranch('remote')) {
            $rules['timezone']           = 'required|string|max:60';
            $rules['work_authorization'] = 'required|string|max:255';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'cv.mimes'         => 'Please upload a PDF or Word document — that helps us read it properly.',
            'cv.max'           => 'Your CV is larger than 10 MB. Try compressing it, or export as PDF.',
            'cv.required'      => 'We need your CV to consider your application.',
            'github_url.required'         => 'For engineering roles, we really do look at your GitHub — a link is required.',
            'portfolio_url.required'      => 'Please share a portfolio so we can see your work.',
            'writing_samples_url.required' => 'Please share a link to two or three writing samples.',
            'timezone.required'           => 'Please tell us your timezone — we schedule interviews around it.',
            'gdpr_consent.accepted'       => 'Please confirm you\'re happy for us to process your application.',
            'availability.required'       => 'Let us know when you could start.',
        ];
    }

    // ─── Submit ─────────────────────────────────────────────

    public function submit()
    {
        // Validate every step in order.
        for ($i = 1; $i <= $this->totalSteps; $i++) {
            try {
                $this->validateStep($i);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->step = $i;   // jump back to the failing step
                throw $e;
            }
        }

        // Store the CV on the private disk.
        $path = $this->cv->store("applications/{$this->vacancy->id}", 'private');

        $application = Application::create([
            'vacancy_id'            => $this->vacancy->id,

            'name'                  => $this->name,
            'email'                 => strtolower($this->email),
            'phone'                 => $this->phone,
            'location'              => $this->location ?: null,
            'country'               => $this->country ?: null,

            'linkedin_url'          => $this->linkedin_url ?: null,
            'portfolio_url'         => $this->portfolio_url ?: null,
            'github_url'            => $this->github_url ?: null,
            'personal_site_url'     => $this->personal_site_url ?: null,
            'behance_url'           => $this->behance_url ?: null,
            'dribbble_url'          => $this->dribbble_url ?: null,
            'writing_samples_url'   => $this->writing_samples_url ?: null,

            'current_role'          => $this->current_role ?: null,
            'current_company'       => $this->current_company ?: null,
            'years_experience'      => $this->years_experience,
            'availability'          => $this->availability ?: null,
            'salary_expectation'    => $this->salary_expectation ?: null,

            'timezone'              => $this->timezone ?: null,
            'work_authorization'    => $this->work_authorization ?: null,

            'cv_path'               => $path,
            'cv_original_name'      => $this->cv->getClientOriginalName(),
            'cover_letter'          => $this->cover_letter ?: null,

            'branch_answers'        => [
                'branches' => $this->branches,
            ],

            'source'                => $this->source ?: null,
            'referrer_name'         => $this->referrer_name ?: null,
            'gdpr_consent'          => $this->gdpr_consent,
            'ip_address'            => request()->ip(),

            'status'                => 'new',
        ]);

        // Fire off both emails.
        try {
            Mail::to($application->email)->queue(new ApplicationReceivedMail($application));
            Mail::to('careers@polyspheretech.com')->queue(new ApplicationSubmittedAdminMail($application));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('applications.status', $application->tracking_token);
    }

    public function render()
    {
        return view('livewire.main.vacancies.application-wizard');
    }
}

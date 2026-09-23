<?php

namespace App\Livewire\Main\Vacancies;

use App\Enums\ApplicationStatus;
use App\Enums\VacancyStatus;
use App\Enums\WorkplaceType;
use App\Helpers\ActivityLogger;
use App\Helpers\NotificationHelper;
use App\Mail\ApplicationReceivedMail;
use App\Mail\ApplicationSubmittedAdminMail;
use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

use Livewire\Component;
use Livewire\WithFileUploads;


class ApplicationWizard extends Component
{
    use WithFileUploads;

    public Vacancy $vacancy;

    public int $step = 1;
    public int $totalSteps = 5;
    public array $branches = [];

    // ─── Duplicate-application blocking ─────────────────────
    public bool $showDuplicateBlock = false;
    public ?Application $existingApplication = null;
    public bool $trackingLinkResent = false;

    // ─── Step 1: Basics ─────────────────────────────────────
    public string $name = '';
    public string $email = '';

    public string $phone = '';
    public string $countryCode = '+233';
    public string $selectedFlag = 'gh.png';
    public array $countries = [];
    public array $filteredCountries = [];
    public array $countryInfo = [];
    public string $phoneExample = '';
    public string $countrySearch = '';
    public bool $showCountryDropdown = false;

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
    public array $timezoneGroups = [];
    public string $work_authorization = '';

    // ─── Step 4: Documents ──────────────────────────────────
    public $cv = null;
    public string $cover_letter = '';

    // ─── Step 5: Meta ───────────────────────────────────────
    public string $source = '';
    public string $referrer_name = '';
    public bool $gdpr_consent = false;

    // ─── Mount ──────────────────────────────────────────────
    public function mount(string $slug): void
    {
        $this->vacancy = Vacancy::with('department')
            ->where('slug', $slug)
            ->where('status', VacancyStatus::Published->value)
            ->firstOrFail();

        $this->branches = $this->computeBranches();

        $this->loadCountries();
        $this->updateCountryInfo();
        $this->loadTimezones();

        $this->location = $this->vacancy->location ?? '';
        $this->country  = $this->vacancy->country ?? '';
    }

    // ─── Branches ───────────────────────────────────────────
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

    // ─── Duplicate detection ────────────────────────────────
    protected function findExistingApplication(string $email): ?Application
    {
        $email = strtolower(trim($email));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return Application::query()
            ->forVacancy($this->vacancy->id)
            ->forEmail($email)
            ->blockingReapplication()
            ->latest()
            ->first();
    }

    protected function flagAsDuplicate(Application $existing): void
    {
        $this->existingApplication = $existing;
        $this->showDuplicateBlock  = true;

        Log::info('Duplicate application blocked', [
            'vacancy_id'              => $this->vacancy->id,
            'email'                   => $existing->email,
            'existing_application_id' => $existing->id,
            'existing_status'         => $existing->status,
            'ip'                      => request()->ip(),
        ]);
    }

    // ─── Resend tracking link ───────────────────────────────
    public function resendTrackingLink(): void
    {
        if (! $this->existingApplication) {
            return;
        }

        $key = 'resend-tracking:' . $this->existingApplication->id . ':' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 2)) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'title'   => 'Please wait a moment',
                'message' => 'You\'ve requested the tracking link a few times already. Try again in 5 minutes.',
            ]);
            return;
        }

        RateLimiter::hit($key, 300); // 5-minute decay

        Mail::to($this->existingApplication->email)
            ->queue(new ApplicationReceivedMail($this->existingApplication));

        $this->trackingLinkResent = true;

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Tracking link sent',
            'message' => 'Check your inbox — we\'ve re-sent the link to ' . $this->existingApplication->email,
        ]);
    }

    // ─── Reset to try a different email ─────────────────────
    public function resetWizard(): void
    {
        $this->showDuplicateBlock  = false;
        $this->existingApplication = null;
        $this->trackingLinkResent  = false;
        $this->email               = '';
        $this->step                = 1;
        $this->resetErrorBag();
    }

    // ─── Country / Phone logic ──────────────────────────────
    public function loadCountries(): void
    {
        $path = public_path('countries-full.json');
        if (! file_exists($path)) {
            $path = public_path('countries.json');
        }

        if (file_exists($path)) {
            $json = file_get_contents($path);
            $countries = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($countries)) {
                usort($countries, fn($a, $b) => strcmp($a['name'], $b['name']));
                $this->countries = $countries;
                $this->filteredCountries = $countries;
                return;
            }
        }

        $this->countries = $this->filteredCountries = [
            ['code' => '+233', 'name' => 'Ghana',          'flag' => 'gh.png', 'pattern' => '^[0-9]{9}$',    'minLength' => 9,  'maxLength' => 9,  'example' => '201234567'],
            ['code' => '+1',   'name' => 'United States',  'flag' => 'us.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '2025550123'],
            ['code' => '+44',  'name' => 'United Kingdom', 'flag' => 'gb.png', 'pattern' => '^[0-9]{10,11}$', 'minLength' => 10, 'maxLength' => 11, 'example' => '7912345678'],
            ['code' => '+91',  'name' => 'India',          'flag' => 'in.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '9876543210'],
            ['code' => '+234', 'name' => 'Nigeria',        'flag' => 'ng.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '8012345678'],
        ];
    }

    public function updateCountryInfo(): void
    {
        $country = collect($this->countries)->firstWhere('code', $this->countryCode);
        if ($country) {
            $this->countryInfo = $country;
            $this->phoneExample = $country['example'] ?? '';
        } else {
            $this->countryInfo = [
                'name'      => 'Ghana',
                'pattern'   => '^[0-9]{9}$',
                'minLength' => 9,
                'maxLength' => 9,
                'example'   => '201234567',
            ];
            $this->phoneExample = '201234567';
        }
    }

    public function selectPhoneCountry(string $code, string $flag): void
    {
        $this->countryCode = $code;
        $this->selectedFlag = $flag;
        $this->updateCountryInfo();
        $this->phone = '';
        $this->showCountryDropdown = false;
        $this->countrySearch = '';
        $this->filteredCountries = $this->countries;
    }

    public function toggleCountryDropdown(): void
    {
        $this->showCountryDropdown = ! $this->showCountryDropdown;
        if ($this->showCountryDropdown) {
            $this->countrySearch = '';
            $this->filteredCountries = $this->countries;
        }
    }

    public function closeCountryDropdown(): void
    {
        $this->showCountryDropdown = false;
        $this->countrySearch = '';
        $this->filteredCountries = $this->countries;
    }

    public function searchCountries(string $searchTerm): void
    {
        $this->countrySearch = $searchTerm;
        $this->filteredCountries = collect($this->countries)
            ->filter(
                fn($c) =>
                stripos($c['name'], $this->countrySearch) !== false ||
                    stripos($c['code'], $this->countrySearch) !== false
            )
            ->values()
            ->toArray();
    }

    public function setPhone(string $value): void
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        $max   = $this->countryInfo['maxLength'] ?? 15;

        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
        }

        $this->phone = $clean;
    }

    public function fullPhone(): string
    {
        $clean = ltrim($this->phone, '0');
        return $this->countryCode . $clean;
    }

    // ─── Timezones ──────────────────────────────────────────
    public function loadTimezones(): void
    {
        $regions = [
            'Africa'     => DateTimeZone::AFRICA,
            'America'    => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Arctic'     => DateTimeZone::ARCTIC,
            'Asia'       => DateTimeZone::ASIA,
            'Atlantic'   => DateTimeZone::ATLANTIC,
            'Australia'  => DateTimeZone::AUSTRALIA,
            'Europe'     => DateTimeZone::EUROPE,
            'Indian'     => DateTimeZone::INDIAN,
            'Pacific'    => DateTimeZone::PACIFIC,
            'UTC'        => DateTimeZone::UTC,
        ];

        $groups = [];

        foreach ($regions as $label => $mask) {
            $zones = DateTimeZone::listIdentifiers($mask);
            $entries = [];

            foreach ($zones as $zone) {
                $short = str_contains($zone, '/')
                    ? substr($zone, strpos($zone, '/') + 1)
                    : $zone;

                $entries[] = [
                    'value' => $zone,
                    'label' => str_replace('_', ' ', $short),
                ];
            }

            if (! empty($entries)) {
                $groups[$label] = $entries;
            }
        }

        $this->timezoneGroups = $groups;
    }

    // ─── Navigation ─────────────────────────────────────────
    public function nextStep(): void
    {
        $this->validateStep($this->step);

        // ── Duplicate check fires after step 1 (email captured) ──
        if ($this->step === 1) {
            $existing = $this->findExistingApplication($this->email);
            if ($existing) {
                $this->flagAsDuplicate($existing);
                return;
            }
        }

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
                'phone'    => [
                    'required',
                    'string',
                    'regex:/' . ($this->countryInfo['pattern'] ?? '^[0-9]{9}$') . '/',
                ],
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
            'linkedin_url'      => 'nullable|url|max:255',
            'portfolio_url'     => 'nullable|url|max:255',
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
            $rules['timezone']           = 'required|string|max:100';
            $rules['work_authorization'] = 'required|string|max:100';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'cv.mimes'         => 'Please upload a PDF or Word document — that helps us read it properly.',
            'cv.max'           => 'Your CV is larger than 10 MB. Try compressing it, or export as PDF.',
            'cv.required'      => 'We need your CV to consider your application.',
            'phone.regex'      => 'That doesn\'t look like a valid number for the selected country.',
            'github_url.required'          => 'For engineering roles, we really do look at your GitHub — a link is required.',
            'portfolio_url.required'       => 'Please share a portfolio so we can see your work.',
            'writing_samples_url.required' => 'Please share a link to two or three writing samples.',
            'timezone.required'            => 'Please pick your timezone — we schedule interviews around it.',
            'work_authorization.required'  => 'Please tell us about your work authorization.',
            'gdpr_consent.accepted'        => 'Please confirm you\'re happy for us to process your application.',
            'availability.required'        => 'Let us know when you could start.',
        ];
    }

    // ─── Submit ─────────────────────────────────────────────
    public function submit()
    {
        // 1. Validate every step in order
        for ($i = 1; $i <= $this->totalSteps; $i++) {
            try {
                $this->validateStep($i);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->step = $i;
                throw $e;
            }
        }

        // 2. Defense in depth — re-check duplicates before creating
        $existing = $this->findExistingApplication($this->email);
        if ($existing) {
            $this->flagAsDuplicate($existing);
            return;
        }

        // 3. Store the CV
        $path = $this->cv->store("applications/{$this->vacancy->id}", 'private');

        // 4. Create the application (Spatie logs this automatically)
        $application = Application::create([
            'vacancy_id' => $this->vacancy->id,

            'name'     => $this->name,
            'email'    => strtolower($this->email),
            'phone'    => $this->fullPhone(),
            'location' => $this->location ?: null,
            'country'  => $this->country ?: null,

            'linkedin_url'        => $this->linkedin_url ?: null,
            'portfolio_url'       => $this->portfolio_url ?: null,
            'github_url'          => $this->github_url ?: null,
            'personal_site_url'   => $this->personal_site_url ?: null,
            'behance_url'         => $this->behance_url ?: null,
            'dribbble_url'        => $this->dribbble_url ?: null,
            'writing_samples_url' => $this->writing_samples_url ?: null,

            'current_role'       => $this->current_role ?: null,
            'current_company'    => $this->current_company ?: null,
            'years_experience'   => $this->years_experience,
            'availability'       => $this->availability ?: null,
            'salary_expectation' => $this->salary_expectation ?: null,

            'timezone'           => $this->timezone ?: null,
            'work_authorization' => $this->work_authorization ?: null,

            'cv_path'          => $path,
            'cv_original_name' => $this->cv->getClientOriginalName(),
            'cover_letter'     => $this->cover_letter ?: null,

            'branch_answers' => ['branches' => $this->branches],

            'source'        => $this->source ?: null,
            'referrer_name' => $this->referrer_name ?: null,
            'gdpr_consent'  => $this->gdpr_consent,
            'ip_address'    => request()->ip(),

            'status' => ApplicationStatus::New->value,
        ]);

        // ─── 5. System-wide activity log ─────────────────────────
        try {
            ActivityLogger::log('Application submitted', [
                'application_id' => $application->id,
                'vacancy_id'     => $application->vacancy_id,
                'vacancy_title'  => $this->vacancy->title,
                'candidate'      => $application->name,
                'email'          => $application->email,
                'source'         => $application->source,
                'ip'             => request()->ip(),
            ], 'application');
        } catch (\Throwable $e) {
            report($e);
        }

        // ─── 6. Notify the hiring team in-app ────────────────────
        try {
            $admins = User::role(['Super Admin', 'Admin'])->get();

            if ($admins->isNotEmpty()) {
                NotificationHelper::sendToUsers($admins, [
                    'title' => 'New application received',
                    'body'  => "{$application->name} applied for {$this->vacancy->title}.",
                    'type'  => 'info',
                    'icon'  => 'fa-user-plus',
                    'link'  => route('admin.applications'),
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // ─── 7. Emails ───────────────────────────────────────────
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

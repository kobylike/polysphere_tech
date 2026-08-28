<?php

namespace App\Livewire\Auth;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Register extends Component
{
    // ─── Invitation ──────────────────────────────────────────────
    public $token;
    public $invitation;

    // ─── Form fields ──────────────────────────────────────────────
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $show_password = false;
    public $terms = false;

    // ─── Password strength ────────────────────────────────────────
    public array $passwordStrength = ['score' => 0, 'label' => 'Very Weak', 'color' => '#dc2626', 'percentage' => 0];
    public array $passwordRequirements = [];

    // ─── Phone ────────────────────────────────────────────────────
    public $phone = '';
    public $countryCode = '+233';
    public $selectedFlag = 'gh.png';
    public $countries = [];
    public $filteredCountries = [];
    public $countryInfo = [];
    public $phoneExample = '';
    public $search = '';
    public $showCountryDropdown = false;

    // ─── Email availability ──────────────────────────────────────
    public bool $emailAvailable = false;
    public bool $checkingEmail = false;

    // ─── Mount ──────────────────────────────────────────────────

    public function mount($token = null)
    {
        // If no token, redirect to login (public sign‑up disabled)
        if (!$token) {
            return redirect()->route('login')->with('error', 'Public registration is disabled.');
        }

        $this->token = $token;
        $this->invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$this->invitation) {
            session()->flash('error', 'This invitation link is invalid or has expired.');
            return redirect()->route('login');
        }

        // Pre‑fill email from invitation
        $this->email = $this->invitation->email;

        // Load country data
        $this->loadCountries();
        $this->updateCountryInfo();
        $this->passwordStrength = $this->calculatePasswordStrength('');
        $this->passwordRequirements = $this->calculatePasswordRequirements('');
        $this->emailAvailable = false;
        $this->checkingEmail = false;
    }

    // ─── Country / Phone logic ──────────────────────────────────

    public function loadCountries()
    {
        $path = public_path('countries-full.json');
        if (!file_exists($path)) {
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

        // Fallback
        $this->countries = $this->filteredCountries = [
            ['code' => '+233', 'name' => 'Ghana',          'flag' => 'gh.png', 'pattern' => '^[0-9]{9}$',    'minLength' => 9,  'maxLength' => 9,  'example' => '201234567'],
            ['code' => '+1',   'name' => 'United States',  'flag' => 'us.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '2025550123'],
            ['code' => '+44',  'name' => 'United Kingdom', 'flag' => 'gb.png', 'pattern' => '^[0-9]{10,11}$', 'minLength' => 10, 'maxLength' => 11, 'example' => '7912345678'],
            ['code' => '+91',  'name' => 'India',          'flag' => 'in.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '9876543210'],
            ['code' => '+234', 'name' => 'Nigeria',        'flag' => 'ng.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '8012345678'],
        ];
    }

    public function updateCountryInfo()
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

    public function selectCountry($code, $flag)
    {
        $this->countryCode = $code;
        $this->selectedFlag = $flag;
        $this->updateCountryInfo();
        $this->phone = '';
        $this->showCountryDropdown = false;
        $this->search = '';
        $this->filteredCountries = $this->countries;
    }

    public function toggleCountryDropdown()
    {
        $this->showCountryDropdown = !$this->showCountryDropdown;
        if ($this->showCountryDropdown) {
            $this->search = '';
            $this->filteredCountries = $this->countries;
        }
    }

    public function closeCountryDropdown()
    {
        $this->showCountryDropdown = false;
        $this->search = '';
        $this->filteredCountries = $this->countries;
    }

    public function searchCountries($searchTerm)
    {
        $this->search = $searchTerm;
        $this->filteredCountries = collect($this->countries)
            ->filter(
                fn($c) =>
                stripos($c['name'], $this->search) !== false ||
                    stripos($c['code'], $this->search) !== false
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

    // ─── Password strength ──────────────────────────────────────

    private function calculatePasswordStrength(string $password): array
    {
        if (empty($password)) {
            return ['score' => 0, 'label' => 'Very Weak', 'color' => '#dc2626', 'percentage' => 0];
        }

        $score = 0;
        $length = strlen($password);

        if ($length >= 8) $score++;
        if ($length >= 12) $score++;
        if ($length >= 16) $score++;

        if (preg_match('/[a-z]/', $password)) $score++;
        if (preg_match('/[A-Z]/', $password)) $score++;
        if (preg_match('/[0-9]/', $password)) $score++;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $score++;

        $score = min($score, 5);
        $percentage = ($score / 5) * 100;

        $labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Excellent'];
        $colors = ['#dc2626', '#ef4444', '#f59e0b', '#fbbf24', '#34d399', '#10b981'];

        return [
            'score' => $score,
            'label' => $labels[$score] ?? 'Very Weak',
            'color' => $colors[$score] ?? '#dc2626',
            'percentage' => $percentage,
        ];
    }

    private function calculatePasswordRequirements(string $password): array
    {
        return [
            'length' => [
                'label' => '8+ characters',
                'met'   => strlen($password) >= 8,
            ],
            'mixed_case' => [
                'label' => 'Mixed case',
                'met'   => preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password),
            ],
            'number' => [
                'label' => 'Number',
                'met'   => (bool) preg_match('/\d/', $password),
            ],
            'symbol' => [
                'label' => 'Special char',
                'met'   => (bool) preg_match('/[^A-Za-z0-9]/', $password),
            ],
        ];
    }

    // ─── Email live check ──────────────────────────────────────

    public function handleEmailUpdate($email)
    {
        if (empty($email)) {
            $this->emailAvailable = false;
            $this->checkingEmail = false;
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->emailAvailable = false;
            $this->checkingEmail = false;
            return;
        }

        $this->checkingEmail = true;
        $this->emailAvailable = false;

        try {
            $exists = User::where('email', $email)->exists();
            $this->emailAvailable = !$exists;
        } catch (\Exception $e) {
            Log::error('Email check failed: ' . $e->getMessage());
            $this->emailAvailable = false;
        } finally {
            $this->checkingEmail = false;
        }
    }

    // ─── Username generator ────────────────────────────────────

    private function generateUsername(string $firstName, string $lastName): string
    {
        // Combine first and last name, remove spaces, lowercase
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . $lastName));
        $username = $base;
        $counter  = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    // ─── Toggle password visibility ────────────────────────────

    public function togglePasswordVisibility(): void
    {
        $this->show_password = !$this->show_password;
    }

    // ─── Validation rules ──────────────────────────────────────

    public function rules(): array
    {
        $min     = $this->countryInfo['minLength'] ?? 5;
        $max     = $this->countryInfo['maxLength'] ?? 15;
        $pattern = $this->countryInfo['pattern'] ?? '^[0-9]{' . $min . ',' . $max . '}$';

        return [
            'first_name' => 'required|string|min:2|max:100',
            'last_name'  => 'required|string|min:2|max:100',
            'email'      => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password'   => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'password_confirmation' => 'required|same:password',
            'terms'     => 'accepted',
            'phone'     => [
                'required',
                'string',
                'regex:/' . $pattern . '/',
                function ($attribute, $value, $fail) {
                    $full = $this->fullPhone();
                    if (User::where('phone', $full)->exists()) {
                        $fail('The phone number has already been taken.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        $country = $this->countryInfo['name'] ?? 'your country';
        $min     = $this->countryInfo['minLength'] ?? 5;
        $max     = $this->countryInfo['maxLength'] ?? 15;
        $example = !empty($this->phoneExample) ? " Example: {$this->phoneExample}" : '';

        return [
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'email.required'      => 'Email address is required.',
            'email.email'         => 'Please enter a valid email address.',
            'email.unique'        => 'This email is already registered.',
            'password.required'   => 'Password is required.',
            'password.confirmed'  => 'Passwords do not match.',
            'password.min'        => 'Password must be at least 8 characters.',
            'phone.required'      => 'Phone number is required.',
            'phone.regex'         => "Enter a valid {$country} number ({$min}–{$max} digits).{$example}",
            'phone.unique'        => 'This phone number is already registered.',
            'terms.accepted'      => 'You must accept the terms and conditions.',
        ];
    }

    // ─── Livewire updated hook ─────────────────────────────────

    public function updated($property)
    {
        if ($property === 'search') {
            $this->searchCountries($this->search);
            return;
        }

        if ($property === 'password') {
            $this->passwordStrength = $this->calculatePasswordStrength($this->password);
            $this->passwordRequirements = $this->calculatePasswordRequirements($this->password);
            $this->validateOnly($property);
            return;
        }

        if ($property === 'email') {
            $this->handleEmailUpdate($this->email);
            $this->resetErrorBag('email');
            return;
        }

        if (in_array($property, ['first_name', 'last_name', 'password_confirmation', 'terms', 'phone'])) {
            $this->validateOnly($property);
        }
    }

    // ─── Register ──────────────────────────────────────────────

    public function register()
    {
        // Rate limiting
        $rateLimitKey = 'register:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $this->addError('email', 'Too many registration attempts. Please try again later.');
            return;
        }

        $this->validate();

        // Extra: ensure the invitation hasn’t been used in the meantime
        if (!$this->invitation || $this->invitation->accepted_at) {
            $this->addError('email', 'This invitation has already been used.');
            return;
        }

        // Extra email check
        if (User::where('email', $this->email)->exists()) {
            $this->addError('email', 'This email is already registered.');
            return;
        }

        // Extra phone check
        $fullPhone = $this->fullPhone();
        if (User::where('phone', $fullPhone)->exists()) {
            $this->addError('phone', 'This phone number is already registered.');
            return;
        }

        // Concatenate first and last name
        $fullName = trim($this->first_name . ' ' . $this->last_name);

        $user = User::create([
            'name'     => $fullName,
            'username' => $this->generateUsername($this->first_name, $this->last_name),
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'phone'    => $fullPhone,
        ]);

        // Assign role from invitation
        if ($this->invitation->role_id) {
            $user->assignRole($this->invitation->role_id);
        } else {
            $user->assignRole('User');
        }

        // Mark invitation as accepted
        $this->invitation->update(['accepted_at' => now()]);

        // Log
        try {
            if (method_exists($user, 'updateLastLogin')) {
                $user->updateLastLogin();
            }
            if (method_exists($user, 'logLoginActivity')) {
                $user->logLoginActivity(request()->ip(), false, true, 'Registration via invitation');
            }
        } catch (\Exception $e) {
            Log::error('Failed to log registration login activity', [
                'user_id' => $user->id,
                'error'   => $e->getMessage()
            ]);
        }

        Auth::login($user);
        session()->regenerate();

        RateLimiter::increment($rateLimitKey);

        return redirect()->route('dashboard')->with('success', 'Welcome to Polysphere Tech!');
    }

    // ─── Render ──────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.auth.register');
    }
}

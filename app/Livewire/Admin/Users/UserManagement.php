<?php

namespace App\Livewire\Admin\Users;

use App\Mail\AccountCreatedMail;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.users')]
class UserManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Filters ──────────────────────────────────────────────────────
    public string $search = '';
    public string $statusFilter = '';
    public string $roleFilter = '';
    public string $verifiedFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    // ─── Invite ──────────────────────────────────────────────────────
    public string $inviteEmail = '';
    public $inviteRoleId = null;
    public string $invitePosition = '';
    public int $inviteExpiryDays = 7;

    // ─── Modals ──────────────────────────────────────────────────────
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;
    public bool $showUserModal = false;
    public bool $showViewModal = false;
    public bool $showToggleStatusModal = false;
    public bool $showToggleVerifyModal = false;
    public bool $showBulkVerifyModal = false;
    public bool $showInviteModal = false;
    public bool $showSpotlightModal = false;
    public bool $showCredentialsModal = false;
    public string $createdUserName = '';
    public string $createdUserEmail = '';
    public string $createdUserPassword = '';

    // ─── Convert to Employee Modal ──────────────────────────────────
    public bool $showConvertEmployeeModal = false;
    public ?int $convertUserId = null;

    // Employee fields for conversion
    public string $emp_employee_id = '';
    public string $emp_department = '';
    public string $emp_position = '';
    public string $emp_employment_type = 'full-time';
    public string $emp_hire_date = '';
    public string $emp_gender = '';
    public string $emp_emergency_contact_name = '';
    public string $emp_emergency_contact_phone = '';

    // Emergency phone country logic (mirroring HrDashboard)
    public string $emp_emergency_countryCode = '+233';
    public string $emp_emergency_selectedFlag = 'gh.png';
    public array $emp_emergency_countries = [];
    public array $emp_emergency_filteredCountries = [];
    public array $emp_emergency_countryInfo = [];
    public string $emp_emergency_phoneExample = '';
    public string $emp_emergency_countrySearch = '';
    public bool $emp_emergency_showCountryDropdown = false;

    // ─── Departments / Positions for conversion ──────────────────────
    public array $emp_departmentsList = [];
    public array $emp_positionsList = [];
    public string $emp_newDepartment = '';
    public string $emp_newPosition = '';
    public bool $emp_showNewDepartment = false;
    public bool $emp_showNewPosition = false;

    // ─── Selected user ──────────────────────────────────────────────
    public ?int $selectedUserId = null;
    public ?User $viewingUser = null;
    public ?int $toggleUserId = null;
    public ?int $verifyUserId = null;
    public ?int $spotlightUserId = null;

    // ─── Activity log ────────────────────────────────────────────────
    public array $recentActivities = [];

    // ─── Bulk selection ─────────────────────────────────────────────
    public array $selectedUsers = [];
    public bool $selectAll = false;

    // ─── User form fields ────────────────────────────────────────────
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];
    public string $formStatus = 'active';
    public bool $isEditing = false;
    public string $position = '';
    public bool $is_featured_team = false;
    public bool $is_spotlight = false;

    // ─── Generated password ──────────────────────────────────────────
    public ?string $generatedPassword = null;

    // ─── Available roles ─────────────────────────────────────────────
    public array $availableRoles = [];
    protected string $protectedRole = 'Super Admin';
    protected string $defaultRole = 'User';
    protected int $maxSpotlight = 3;

    // ─── Mount ────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->loadAvailableRoles();
        $this->loadCountries();
        $this->emp_emergency_updateCountryInfo();
        $this->emp_loadDepartmentsAndPositions();
    }

    // ─── Roles ────────────────────────────────────────────────────────

    private function loadAvailableRoles(): void
    {
        $this->ensureDefaultRoles();
        $this->availableRoles = Role::orderBy('name')->pluck('name')->toArray();
        if (empty($this->availableRoles)) {
            $this->availableRoles = ['Admin', 'Agent', 'User'];
        }
    }

    private function ensureDefaultRoles(): void
    {
        $defaultRoles = ['Super Admin', 'Admin', 'Agent', 'User'];
        foreach ($defaultRoles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }

    public function getAssignableRolesProperty(): array
    {
        return array_values(array_filter(
            $this->availableRoles,
            fn($role) => $role !== $this->protectedRole && $role !== $this->defaultRole
        ));
    }

    // ─── Departments & Positions for conversion ──────────────────────

    private function emp_loadDepartmentsAndPositions()
    {
        $depts = UserProfile::where('is_employee', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->toArray();

        $positions = UserProfile::where('is_employee', true)
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position')
            ->toArray();

        $defaultDepts = ['Engineering', 'Marketing', 'Sales', 'HR', 'Finance', 'Operations', 'Design'];
        $defaultPositions = ['CEO', 'CTO', 'Lead Developer', 'Senior Developer', 'Developer', 'Designer', 'Marketing Manager'];

        $this->emp_departmentsList = array_values(array_unique(array_merge($defaultDepts, $depts)));
        $this->emp_positionsList = array_values(array_unique(array_merge($defaultPositions, $positions)));
    }

    public function emp_addDepartment()
    {
        $name = trim($this->emp_newDepartment);
        if (!$name) {
            $this->addError('emp_newDepartment', 'Type a department name before adding it.');
            return;
        }
        if (in_array($name, $this->emp_departmentsList)) {
            $this->addError('emp_newDepartment', "\"{$name}\" already exists.");
            return;
        }
        $this->emp_departmentsList[] = $name;
        $this->emp_department = $name;
        $this->emp_newDepartment = '';
        $this->emp_showNewDepartment = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Success', 'message' => "Department '{$name}' added."]);
    }

    public function emp_addPosition()
    {
        $name = trim($this->emp_newPosition);
        if (!$name) {
            $this->addError('emp_newPosition', 'Type a position name before adding it.');
            return;
        }
        if (in_array($name, $this->emp_positionsList)) {
            $this->addError('emp_newPosition', "\"{$name}\" already exists.");
            return;
        }
        $this->emp_positionsList[] = $name;
        $this->emp_position = $name;
        $this->emp_newPosition = '';
        $this->emp_showNewPosition = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Success', 'message' => "Position '{$name}' added."]);
    }

    // ─── Employee ID Generator ────────────────────────────────────────

    private function generateEmployeeId(): string
    {
        $max = UserProfile::where('is_employee', true)
            ->whereNotNull('employee_id')
            ->pluck('employee_id')
            ->map(fn($id) => (int) preg_replace('/[^0-9]/', '', $id))
            ->max();

        $next = ($max ?? 0) + 1;
        return 'EMP-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    // ─── Country / Phone logic (mirroring HrDashboard) ───────────────

    private function loadCountries()
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
                $this->emp_emergency_countries = $countries;
                $this->emp_emergency_filteredCountries = $countries;
                return;
            }
        }

        // Fallback
        $this->emp_emergency_countries = $this->emp_emergency_filteredCountries = [
            ['code' => '+233', 'name' => 'Ghana',          'flag' => 'gh.png', 'pattern' => '^[0-9]{9}$',    'minLength' => 9,  'maxLength' => 9,  'example' => '201234567'],
            ['code' => '+1',   'name' => 'United States',  'flag' => 'us.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '2025550123'],
            ['code' => '+44',  'name' => 'United Kingdom', 'flag' => 'gb.png', 'pattern' => '^[0-9]{10,11}$', 'minLength' => 10, 'maxLength' => 11, 'example' => '7912345678'],
            ['code' => '+91',  'name' => 'India',          'flag' => 'in.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '9876543210'],
            ['code' => '+234', 'name' => 'Nigeria',        'flag' => 'ng.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '8012345678'],
        ];
    }

    public function emp_emergency_updateCountryInfo()
    {
        $country = collect($this->emp_emergency_countries)->firstWhere('code', $this->emp_emergency_countryCode);
        if ($country) {
            $this->emp_emergency_countryInfo = $country;
            $this->emp_emergency_phoneExample = $country['example'] ?? '';
        } else {
            $this->emp_emergency_countryInfo = ['name' => 'Ghana', 'pattern' => '^[0-9]{9}$', 'minLength' => 9, 'maxLength' => 9, 'example' => '201234567'];
            $this->emp_emergency_phoneExample = '201234567';
        }
    }

    public function emp_emergency_selectCountry($code, $flag)
    {
        $this->emp_emergency_countryCode = $code;
        $this->emp_emergency_selectedFlag = $flag;
        $this->emp_emergency_updateCountryInfo();
        $this->emp_emergency_contact_phone = '';
        $this->emp_emergency_showCountryDropdown = false;
        $this->emp_emergency_countrySearch = '';
        $this->emp_emergency_filteredCountries = $this->emp_emergency_countries;
    }

    public function emp_emergency_toggleCountryDropdown()
    {
        $this->emp_emergency_showCountryDropdown = !$this->emp_emergency_showCountryDropdown;
        if ($this->emp_emergency_showCountryDropdown) {
            $this->emp_emergency_countrySearch = '';
            $this->emp_emergency_filteredCountries = $this->emp_emergency_countries;
        }
    }

    public function emp_emergency_closeCountryDropdown()
    {
        $this->emp_emergency_showCountryDropdown = false;
        $this->emp_emergency_countrySearch = '';
        $this->emp_emergency_filteredCountries = $this->emp_emergency_countries;
    }

    public function emp_emergency_searchCountries($searchTerm)
    {
        $this->emp_emergency_countrySearch = $searchTerm;
        $this->emp_emergency_filteredCountries = collect($this->emp_emergency_countries)
            ->filter(fn($c) => stripos($c['name'], $this->emp_emergency_countrySearch) !== false || stripos($c['code'], $this->emp_emergency_countrySearch) !== false)
            ->values()
            ->toArray();
    }

    public function emp_emergency_setPhone(string $value): void
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        $max = $this->emp_emergency_countryInfo['maxLength'] ?? 15;
        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
        }
        $this->emp_emergency_contact_phone = $clean;
    }

    public function emp_emergency_getFullPhone(): string
    {
        $clean = ltrim($this->emp_emergency_contact_phone, '0');
        return $this->emp_emergency_countryCode . $clean;
    }

    private function emp_emergency_parsePhoneNumber(?string $phone): void
    {
        if (empty($phone)) {
            $this->emp_emergency_contact_phone = '';
            $this->emp_emergency_countryCode = '+233';
            $this->emp_emergency_selectedFlag = 'gh.png';
            $this->emp_emergency_updateCountryInfo();
            return;
        }
        $matchedCountry = null;
        $matchedCode = '';
        foreach ($this->emp_emergency_countries as $country) {
            $code = $country['code'];
            if (str_starts_with($phone, $code) && strlen($code) > strlen($matchedCode)) {
                $matchedCode = $code;
                $matchedCountry = $country;
            }
        }
        if ($matchedCountry) {
            $this->emp_emergency_countryCode = $matchedCode;
            $this->emp_emergency_selectedFlag = $matchedCountry['flag'];
            $this->emp_emergency_contact_phone = substr($phone, strlen($matchedCode));
        } else {
            $this->emp_emergency_countryCode = '+233';
            $this->emp_emergency_selectedFlag = 'gh.png';
            $this->emp_emergency_contact_phone = $phone;
        }
        $this->emp_emergency_updateCountryInfo();
    }

    // ─── Validation Rules ─────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|min:2|max:50',
            'last_name'  => 'required|string|min:2|max:50',
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($this->selectedUserId)],
            'formStatus' => 'required|in:active,inactive,suspended',
            'selectedRoles' => 'array',
            'selectedRoles.*' => ['string', Rule::in($this->availableRoles)],
            'position'   => 'nullable|string|max:255',
            'is_featured_team' => 'boolean',
            'is_spotlight' => 'boolean',
            'password'   => 'nullable|min:8',
        ];
    }

    // ─── Query String ─────────────────────────────────────────────────

    protected $queryString = [
        'search'         => ['except' => ''],
        'statusFilter'   => ['except' => ''],
        'roleFilter'     => ['except' => ''],
        'verifiedFilter' => ['except' => ''],
        'sortBy'         => ['except' => 'created_at'],
        'sortDir'        => ['except' => 'desc'],
        'perPage'        => ['except' => 15],
    ];

    // ─── Filters / Sorting ────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }
    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }
    public function updatingVerifiedFilter(): void
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
        $this->reset(['search', 'statusFilter', 'roleFilter', 'verifiedFilter', 'sortBy', 'sortDir', 'perPage']);
        $this->sortBy  = 'created_at';
        $this->sortDir = 'desc';
        $this->perPage = 15;
        $this->resetPage();
    }

    // ─── Activity Log ─────────────────────────────────────────────────

    private function logActivity(int $userId, string $action, string $description): void
    {
        if (class_exists(UserActivity::class)) {
            try {
                UserActivity::create([
                    'user_id'     => $userId,
                    'action'      => $action,
                    'description' => $description,
                    'ip_address'  => request()->ip(),
                    'user_agent'  => substr((string) request()->userAgent(), 0, 255),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    // ─── View User ────────────────────────────────────────────────────

    public function viewUser(int $id): void
    {
        $this->viewingUser = User::with('roles', 'profile')->findOrFail($id);
        $this->recentActivities = class_exists(UserActivity::class)
            ? UserActivity::where('user_id', $id)->latest()->take(10)->get()->toArray()
            : [];
        $this->showViewModal = true;
    }

    // ─── Create / Edit User ──────────────────────────────────────────

    public function openCreate(): void
    {
        $this->reset(['first_name', 'last_name', 'email', 'password', 'selectedRoles', 'formStatus', 'selectedUserId', 'position', 'is_featured_team', 'is_spotlight',  'generatedPassword']);
        $this->isEditing     = false;
        $this->formStatus    = 'active';
        $this->selectedRoles = [$this->defaultRole];
        $this->showUserModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::with('roles', 'profile')->findOrFail($id);
        $this->selectedUserId = $user->id;

        $nameParts = explode(' ', $user->name, 2);
        $this->first_name = $nameParts[0] ?? '';
        $this->last_name  = $nameParts[1] ?? '';
        $this->email      = $user->email;
        $this->formStatus = $user->status ?? 'active';
        $this->password   = '';
        $this->isEditing  = true;

        $this->position         = $user->profile?->position ?? '';
        $this->is_featured_team = $user->profile?->is_featured_team ?? false;
        $this->is_spotlight     = $user->profile?->is_spotlight ?? false;

        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        if (empty($this->selectedRoles)) {
            $this->selectedRoles = [$this->defaultRole];
        }
        $this->selectedRoles = array_values(array_filter(
            $this->selectedRoles,
            fn($role) => $role !== $this->protectedRole
        ));

        $this->showUserModal = true;
    }

    private function generateUsername(string $firstName, string $lastName): string
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . $lastName));
        $base = $base !== '' ? $base : 'user';
        $username = $base;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        return $username;
    }

    private function generateSecurePassword(): string
    {
        $length = 12;
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        $charactersLength = strlen($characters);
        do {
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $password .= $characters[random_int(0, $charactersLength - 1)];
            }
        } while (!(preg_match('/[a-z]/', $password) &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[^A-Za-z0-9]/', $password)));

        return $password;
    }

    public function saveUser(): void
    {
        $this->validate();

        // Enforce the spotlight cap before doing anything else
        if ($this->is_spotlight) {
            $alreadyUsed = UserProfile::where('is_spotlight', true)
                ->when($this->isEditing && $this->selectedUserId, function ($q) {
                    $q->where('user_id', '!=', $this->selectedUserId);
                })
                ->count();

            if ($alreadyUsed >= $this->maxSpotlight) {
                $this->addError('is_spotlight', "Spotlight is full ({$this->maxSpotlight}/{$this->maxSpotlight}). Remove someone else from the spotlight first.");
                return;
            }
        }

        $fullName = trim($this->first_name . ' ' . $this->last_name);

        $roleNames = array_values(array_filter(
            $this->selectedRoles,
            fn($role) => $role !== $this->protectedRole
        ));
        if (empty($roleNames)) {
            $roleNames = [$this->defaultRole];
        }

        $data = [
            'name'   => $fullName,
            'email'  => $this->email,
            'status' => $this->formStatus,
        ];

        try {
            if ($this->isEditing) {
                $password = $this->password ? Hash::make($this->password) : null;

                if ($password) {
                    $data['password'] = $password;
                }

                $user = User::findOrFail($this->selectedUserId);
                $emailChanged = $user->email !== $this->email;

                if ($emailChanged) {
                    $data['email_verified_at'] = null;
                    $data['email_verification_token'] = Str::random(64);
                    $data['email_verification_sent_at'] = null;
                }

                $user->update($data);
                $user->syncRoles($roleNames);

                $profile = $user->profile ?: $user->profile()->create([]);
                $profile->position = $this->position;
                $profile->is_featured_team = $this->is_featured_team;
                $profile->is_spotlight = $this->is_spotlight;
                $profile->save();

                $this->logActivity($user->id, 'admin_update', "Account updated by admin ({$fullName}, {$this->email}).");
                if ($emailChanged) {
                    $this->logActivity($user->id, 'email_changed', 'Email address changed by admin; verification reset.');
                }

                $this->dispatch('notify', [
                    'type' => 'success',
                    'title' => 'User updated!',
                    'message' => "{$fullName}'s account has been updated.",
                ]);
                $this->generatedPassword = null;

                $this->showUserModal = false;
                $this->reset(['first_name', 'last_name', 'email', 'password', 'selectedRoles', 'formStatus', 'selectedUserId', 'position', 'is_featured_team', 'is_spotlight']);
            } else {
                $data['username'] = $this->generateUsername($this->first_name, $this->last_name);
                $data['email_verified_at'] = now();

                // Capture the plaintext password *before* hashing, whichever
                // source it came from — manual entry or auto-generated — so
                // it can be shown to the admin and emailed to the new user.
                if (!empty($this->password)) {
                    $plainPassword = $this->password;
                    $data['password'] = Hash::make($plainPassword);
                } else {
                    $plainPassword = $this->generateSecurePassword();
                    $data['password'] = Hash::make($plainPassword);
                }

                $user = User::create($data);
                $user->assignRole($roleNames);

                $user->profile()->create([
                    'position' => $this->position,
                    'is_featured_team' => $this->is_featured_team,
                    'is_spotlight' => $this->is_spotlight,
                ]);

                $this->logActivity($user->id, 'admin_create', "Account created by admin ({$fullName}, {$this->email}).");

                // Email the new user their login details.
                try {
                    Mail::to($user->email)->queue(
                        new AccountCreatedMail($user->fresh('roles'), $plainPassword, Auth::user()?->name)
                    );
                    $this->logActivity($user->id, 'account_created_email_sent', "Welcome email with credentials sent to {$user->email}.");
                } catch (\Throwable $e) {
                    report($e);
                    // Don't fail the whole save just because the email didn't
                    // go out — the credentials modal below is the fallback.
                }

                // Show a sticky credentials modal instead of a toast — a
                // 4-second auto-dismissing toast is the wrong place for a
                // one-time secret the admin needs time to read and copy.
                $this->createdUserName = $fullName;
                $this->createdUserEmail = $user->email;
                $this->createdUserPassword = $plainPassword;

                $this->generatedPassword = null;
                $this->showUserModal = false;
                $this->reset(['first_name', 'last_name', 'email', 'password', 'selectedRoles', 'formStatus', 'selectedUserId', 'position', 'is_featured_team', 'is_spotlight']);

                $this->showCredentialsModal = true;

                $this->dispatch('notify', [
                    'type' => 'success',
                    'title' => 'User Created!',
                    'message' => "{$fullName} has been added and emailed their login details.",
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Save failed',
                'message' => $e->getMessage(),
            ]);
            return;
        }
    }

    // ─── Convert to Employee ─────────────────────────────────────────

    public function openConvertToEmployee(int $userId): void
    {
        $user = User::with('profile')->findOrFail($userId);

        // Prevent converting an already-employed user (just in case)
        if ($user->profile && $user->profile->is_employee) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'This user is already an employee.']);
            return;
        }

        $this->convertUserId = $userId;
        $this->emp_employee_id = $this->generateEmployeeId();

        // Pre-fill from existing profile if available
        $profile = $user->profile;
        if ($profile) {
            $this->emp_position = $profile->position ?? '';
            $this->emp_department = $profile->department ?? '';
            $this->emp_employment_type = $profile->employment_type ?? 'full-time';
            $this->emp_hire_date = $profile->hire_date?->format('Y-m-d') ?? '';
            $this->emp_gender = $profile->gender ?? '';
            $this->emp_emergency_contact_name = $profile->emergency_contact_name ?? '';
            $this->emp_emergency_contact_phone = $profile->emergency_contact_phone ?? '';
            $this->emp_emergency_parsePhoneNumber($profile->emergency_contact_phone ?? '');
        } else {
            // Reset fields if no profile
            $this->emp_position = '';
            $this->emp_department = '';
            $this->emp_employment_type = 'full-time';
            $this->emp_hire_date = '';
            $this->emp_gender = '';
            $this->emp_emergency_contact_name = '';
            $this->emp_emergency_contact_phone = '';
            $this->emp_emergency_countryCode = '+233';
            $this->emp_emergency_selectedFlag = 'gh.png';
            $this->emp_emergency_updateCountryInfo();
        }

        $this->emp_loadDepartmentsAndPositions();
        $this->showConvertEmployeeModal = true;
    }

    public function saveConvertedEmployee(): void
    {
        $this->validate([
            'emp_employee_id' => 'required|string|max:50|unique:user_profiles,employee_id,' .
                ($this->convertUserId ? UserProfile::where('user_id', $this->convertUserId)->first()?->id : 'NULL'),
            'emp_department'  => 'required|string|max:255',
            'emp_position'    => 'required|string|max:255',
            'emp_employment_type' => 'required|in:full-time,part-time,contract,intern',
            'emp_hire_date'   => 'required|date|before_or_equal:today',
            'emp_gender'      => 'required|in:male,female,other',
            'emp_emergency_contact_name' => 'required|string|min:2|max:255',
            'emp_emergency_contact_phone' => [
                'required',
                'string',
                'regex:/' . ($this->emp_emergency_countryInfo['pattern'] ?? '^[0-9]{9}$') . '/'
            ],
        ], [
            'emp_employee_id.required' => 'An employee ID is required.',
            'emp_employee_id.unique'   => 'This employee ID is already assigned.',
            'emp_department.required'  => 'Please select or add a department.',
            'emp_position.required'    => 'Please select or add a job title.',
            'emp_employment_type.required' => 'Please choose an employment type.',
            'emp_hire_date.required'   => 'Please provide the hire date.',
            'emp_hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'emp_gender.required'      => 'Please select a gender.',
            'emp_emergency_contact_name.required' => 'Please provide an emergency contact name.',
            'emp_emergency_contact_phone.required' => 'Please provide an emergency contact phone number.',
            'emp_emergency_contact_phone.regex' => 'That doesn\'t look like a valid number for the selected country.',
        ]);

        $user = User::findOrFail($this->convertUserId);
        $profile = $user->profile ?: $user->profile()->create([]);

        $profile->update([
            'is_employee' => true,
            'employee_id' => $this->emp_employee_id,
            'department'  => $this->emp_department,
            'position'    => $this->emp_position,
            'employment_type' => $this->emp_employment_type,
            'hire_date'   => Carbon::parse($this->emp_hire_date),
            'gender'      => $this->emp_gender,
            'emergency_contact_name'  => $this->emp_emergency_contact_name,
            'emergency_contact_phone' => $this->emp_emergency_getFullPhone(),
        ]);

        // Assign the default 'User' role if the user has no roles (or keep existing)
        if ($user->roles->count() === 0) {
            $user->assignRole($this->defaultRole);
        }

        $this->logActivity($user->id, 'converted_to_employee', "User converted to employee (ID: {$this->emp_employee_id}) by admin.");

        $this->showConvertEmployeeModal = false;
        $this->convertUserId = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Employee Added!',
            'message' => "{$user->name} is now an employee with ID {$this->emp_employee_id}.",
        ]);

        // Reset fields
        $this->reset([
            'emp_employee_id',
            'emp_department',
            'emp_position',
            'emp_employment_type',
            'emp_hire_date',
            'emp_gender',
            'emp_emergency_contact_name',
            'emp_emergency_contact_phone'
        ]);
    }

    // ─── Single Delete ───────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        if ($id === Auth::id()) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'You cannot delete your own account.']);
            return;
        }
        $this->selectedUserId  = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->selectedUserId === Auth::id()) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'You cannot delete your own account.']);
            $this->showDeleteModal = false;
            return;
        }
        User::findOrFail($this->selectedUserId)->delete();
        $this->showDeleteModal = false;
        $this->selectedUserId  = null;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Deleted', 'message' => 'User deleted successfully.']);
    }

    // ─── Bulk Actions ─────────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedUsers = $value
            ? $this->getQuery()
            ->pluck('users.id')
            ->reject(fn($id) => $id === Auth::id())
            ->map(fn($id) => (string) $id)
            ->toArray()
            : [];
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedUsers)) return;
        $this->selectedUsers = array_filter($this->selectedUsers, fn($id) => (int) $id !== Auth::id());
        if (empty($this->selectedUsers)) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'You cannot delete your own account.']);
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        $ids = array_filter($this->selectedUsers, fn($id) => (int) $id !== Auth::id());
        if (empty($ids)) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'You cannot delete your own account.']);
            $this->showBulkDeleteModal = false;
            return;
        }
        User::whereIn('id', $ids)->delete();
        $this->selectedUsers       = [];
        $this->selectAll           = false;
        $this->showBulkDeleteModal = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Deleted', 'message' => count($ids) . ' user(s) deleted.']);
    }

    public function bulkActivate(): void
    {
        $ids = $this->selectedUsers;
        User::whereIn('id', $ids)->update(['status' => 'active']);
        foreach ($ids as $id) {
            $this->logActivity((int) $id, 'bulk_activate', 'Account activated via bulk admin action.');
        }
        $this->selectedUsers = [];
        $this->selectAll     = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Activated', 'message' => 'Selected users activated.']);
    }

    public function bulkSuspend(): void
    {
        $ids = $this->selectedUsers;
        User::whereIn('id', $ids)->update(['status' => 'suspended']);
        foreach ($ids as $id) {
            $this->logActivity((int) $id, 'bulk_suspend', 'Account suspended via bulk admin action.');
        }
        $this->selectedUsers = [];
        $this->selectAll     = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Suspended', 'message' => 'Selected users suspended.']);
    }

    // ─── Toggle Status ────────────────────────────────────────────────

    public function confirmToggleStatus(int $id): void
    {
        if ($id === Auth::id()) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'You cannot change your own status.']);
            return;
        }
        $this->toggleUserId = $id;
        $this->showToggleStatusModal = true;
    }
    public function confirmToggleSpotlight(int $id): void
    {
        $this->spotlightUserId = $id;
        $this->showSpotlightModal = true;
    }

    public function toggleSpotlightConfirmed(): void
    {
        if (!$this->spotlightUserId) return;

        $user = User::with('profile')->findOrFail($this->spotlightUserId);
        $profile = $user->profile ?: $user->profile()->create([]);

        // Only enforce the cap when turning it ON
        if (!$profile->is_spotlight) {
            $used = UserProfile::where('is_spotlight', true)->count();
            if ($used >= $this->maxSpotlight) {
                $this->showSpotlightModal = false;
                $this->spotlightUserId = null;
                $this->dispatch('notify', [
                    'type' => 'error',
                    'title' => 'Spotlight is full',
                    'message' => "Only {$this->maxSpotlight} people can be spotlighted at once. Remove someone from the spotlight first.",
                ]);
                return;
            }
        }

        $profile->is_spotlight = !$profile->is_spotlight;
        $profile->save();

        $this->logActivity($user->id, 'spotlight_toggle', $profile->is_spotlight
            ? 'Added to homepage/About spotlight by admin.'
            : 'Removed from homepage/About spotlight by admin.');

        $this->showSpotlightModal = false;
        $this->spotlightUserId = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => $profile->is_spotlight ? 'Spotlighted!' : 'Removed',
            'message' => $profile->is_spotlight
                ? "{$user->name} now appears in the homepage/About spotlight."
                : "{$user->name} was removed from the spotlight.",
        ]);
    }
    public function toggleStatusConfirmed(): void
    {
        if (!$this->toggleUserId) return;

        $user = User::findOrFail($this->toggleUserId);
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->status = $newStatus;
        $user->save();

        $this->logActivity($user->id, 'status_toggle', "Status changed to '{$newStatus}' by admin.");

        $this->showToggleStatusModal = false;
        $this->toggleUserId = null;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Status changed', 'message' => "User status changed to '{$newStatus}'."]);
    }

    // ─── Toggle Verification ──────────────────────────────────────────

    public function confirmToggleVerify(int $id): void
    {
        $this->verifyUserId = $id;
        $this->showToggleVerifyModal = true;
    }

    public function toggleVerifyConfirmed(): void
    {
        if (!$this->verifyUserId) return;

        $user = User::findOrFail($this->verifyUserId);

        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $action = 'unverified';
        } else {
            $user->email_verified_at = now();
            $action = 'verified';
        }
        $user->save();

        $this->logActivity($user->id, 'verification_toggle', "Email marked as {$action} by admin.");

        $this->showToggleVerifyModal = false;
        $this->verifyUserId = null;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Verification', 'message' => "User email marked as {$action}."]);
    }

    // ─── Resend Verification ──────────────────────────────────────────

    public function resendVerification(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'This user is already verified.']);
            return;
        }

        $user->email_verification_token = Str::random(64);
        $user->email_verification_sent_at = now();
        $user->save();

        $user->sendEmailVerificationNotification();

        $this->logActivity($user->id, 'verification_resent', 'Verification email resent by admin.');

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Sent', 'message' => "Verification email sent to {$user->email}."]);
    }

    // ─── Bulk Resend Verification ─────────────────────────────────────

    public function confirmBulkVerifyResend(): void
    {
        if (empty($this->selectedUsers)) return;
        $this->showBulkVerifyModal = true;
    }

    public function bulkResendVerification(): void
    {
        $users = User::whereIn('id', $this->selectedUsers)
            ->whereNull('email_verified_at')
            ->get();

        foreach ($users as $user) {
            $user->email_verification_token = Str::random(64);
            $user->email_verification_sent_at = now();
            $user->save();
            $user->sendEmailVerificationNotification();
            $this->logActivity($user->id, 'verification_resent', 'Verification email resent via bulk admin action.');
        }

        $this->selectedUsers      = [];
        $this->selectAll          = false;
        $this->showBulkVerifyModal = false;

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Sent', 'message' => $users->count() . ' verification email(s) sent.']);
    }
    public function getSpotlightInfoProperty(): array
    {
        $used = UserProfile::where('is_spotlight', true)
            ->when($this->isEditing && $this->selectedUserId, function ($q) {
                $q->where('user_id', '!=', $this->selectedUserId);
            })
            ->count();

        return [
            'used' => $used,
            'max'  => $this->maxSpotlight,
            'full' => $used >= $this->maxSpotlight,
        ];
    }
    // ─── Invitation ────────────────────────────────────────────────────

    public function openInviteModal(): void
    {
        $this->reset(['inviteEmail', 'inviteRoleId', 'invitePosition', 'inviteExpiryDays']);
        $this->inviteExpiryDays = 7;
        $this->showInviteModal = true;
    }

    public function sendInvitation(): void
    {
        $this->validate([
            'inviteEmail' => 'required|email|max:255|unique:invitations,email|unique:users,email',
            'inviteRoleId' => 'nullable|exists:roles,id',
            'invitePosition' => 'nullable|string|max:255',
            'inviteExpiryDays' => 'required|integer|min:1|max:30',
        ]);

        $token = Str::random(64);

        $invitation = Invitation::create([
            'email'      => $this->inviteEmail,
            'token'      => $token,
            'role_id'    => $this->inviteRoleId ?: null,
            'position'   => $this->invitePosition ?: null,
            'expires_at' => now()->addDays($this->inviteExpiryDays),
            'invited_by' => Auth::id(),
        ]);

        Mail::to($this->inviteEmail)->queue(new InvitationMail($invitation));

        $this->logActivity(Auth::id(), 'invitation_sent', "Invitation sent to {$this->inviteEmail}");

        $this->showInviteModal = false;
        $this->reset(['inviteEmail', 'inviteRoleId', 'invitePosition', 'inviteExpiryDays']);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Invitation sent', 'message' => "Invitation sent to {$this->inviteEmail}"]);
    }

    // ─── Query ─────────────────────────────────────────────────────────

    private function getQuery()
    {
        return User::query()
            ->with('roles', 'profile')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select('users.*', 'user_profiles.position as position', 'user_profiles.is_featured_team', 'user_profiles.is_spotlight', 'user_profiles.is_employee')
            // 👆 added is_spotlight
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('users.email', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn($q) => $q->where('users.status', $this->statusFilter))
            ->when($this->roleFilter, fn($q) => $q->whereHas('roles', function ($q) {
                $q->where('name', $this->roleFilter);
            }))
            ->when($this->verifiedFilter === 'verified', fn($q) => $q->whereNotNull('users.email_verified_at'))
            ->when($this->verifiedFilter === 'unverified', fn($q) => $q->whereNull('users.email_verified_at'))
            ->orderBy($this->sortBy === 'position' ? 'position' : 'users.' . $this->sortBy, $this->sortDir)
            ->distinct();
    }

    // ─── Render ────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.users.user-management', [
            'users' => $this->getQuery()->paginate($this->perPage),
            'stats' => [
                'total'      => User::count(),
                'active'     => User::where('status', 'active')->count(),
                'suspended'  => User::where('status', 'suspended')->count(),
                'new_today'  => User::whereDate('created_at', today())->count(),
                'unverified' => User::whereNull('email_verified_at')->count(),
                'spotlight'  => UserProfile::where('is_spotlight', true)->count(),
            ],
            'protectedRole' => $this->protectedRole,
            'availableRoles' => $this->availableRoles,
            'assignableRoles' => $this->assignableRoles,
        ]);
    }
}

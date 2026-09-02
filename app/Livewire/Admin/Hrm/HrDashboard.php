<?php

namespace App\Livewire\Admin\Hrm;

use App\Mail\InvitationMail;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.users')]
class HrDashboard extends Component
{
    use WithPagination;

    // ─── Filters ──────────────────────────────────────────────────────
    public string $search = '';
    public string $departmentFilter = '';
    public string $employmentType = '';
    public string $statusFilter = '';
    public int $perPage = 10;

    // ─── Attendance month ────────────────────────────────────────────
    public int $year;
    public int $month;

    // ─── Invite fields ───────────────────────────────────────────────
    public string $inviteEmail = '';
    public $inviteRoleId = null;
    public string $invitePosition = '';
    public int $inviteExpiryDays = 7;
    public bool $showInviteModal = false;

    // ─── Employee modal fields ───────────────────────────────────────
    public bool $showEmployeeModal = false;
    public ?int $editingEmployeeId = null;

    // ── Personal Info ────────────────────────────────────────────────
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $gender = '';
    public string $countryCode = '+233';
    public string $selectedFlag = 'gh.png';
    public array $countries = [];
    public array $filteredCountries = [];
    public array $countryInfo = [];
    public string $phoneExample = '';
    public string $countrySearch = '';
    public bool $showCountryDropdown = false;

    // ── Employment Info ──────────────────────────────────────────────
    public string $employee_id = '';
    public string $position = '';
    public string $department = '';
    public string $employment_type = 'full-time';
    public string $hire_date = '';
    public bool $is_featured_team = false;

    // 🔥 NEW PERSONAL FIELDS
    public string $date_of_birth = '';
    public string $country_code = '';
    public string $city = '';

    // ── Emergency Contact ────────────────────────────────────────────
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $emergency_countryCode = '+233';
    public string $emergency_selectedFlag = 'gh.png';
    public array $emergency_countries = [];
    public array $emergency_filteredCountries = [];
    public array $emergency_countryInfo = [];
    public string $emergency_phoneExample = '';
    public string $emergency_countrySearch = '';
    public bool $emergency_showCountryDropdown = false;

    // ── Department / Position management ────────────────────────────
    public array $departmentsList = [];
    public array $positionsList = [];
    public string $newDepartment = '';
    public string $newPosition = '';
    public bool $showNewDepartment = false;
    public bool $showNewPosition = false;

    // ── Single-employee attendance modal ─────────────────────────────
    public bool $showAttendanceModal = false;
    public ?int $attendanceUserId = null;
    public ?string $attendanceUserName = null;
    public string $attendanceDate = '';
    public string $attendanceStatus = 'present';
    public ?string $attendanceNotes = null;
    public ?string $attendanceCheckIn = null;
    public ?string $attendanceCheckOut = null;

    // ── Bulk / whole-team attendance modal ───────────────────────────
    public bool $showBulkAttendanceModal = false;
    public string $bulkAttendanceDate = '';
    public string $bulkAttendanceStatus = 'present';
    public array $bulkSelectedEmployees = [];
    public bool $bulkSelectAll = false;
    public ?string $bulkAttendanceNotes = null;

    // ── Delete confirmation ──────────────────────────────────────────
    public bool $showDeleteModal = false;
    public ?int $deleteUserId = null;

    // ── Holiday management ────────────────────────────────────────────
    public bool $showHolidayModal = false;
    public ?int $editingHolidayId = null;
    public string $holidayName = '';
    public string $holidayDate = '';
    public bool $holidayRecurring = false;

    // ── Available roles ──────────────────────────────────────────────
    public array $availableRoles = [];
    protected string $protectedRole = 'Super Admin';
    protected string $defaultRole = 'User';

    /** Cached (per-request) list of all holidays, so we don't re-query per row. */
    protected ?\Illuminate\Support\Collection $holidaysCache = null;

    // ─── Mount ──────────────────────────────────────────────────────────

    public function mount()
    {
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
        $this->loadAvailableRoles();
        $this->loadCountries();
        $this->updateCountryInfo();
        $this->emergency_updateCountryInfo();
        $this->loadDepartmentsAndPositions();
    }

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

    public function updated($property)
    {
        if (in_array($property, ['search', 'departmentFilter', 'employmentType', 'statusFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    // ─── Departments & Positions ──────────────────────────────────────

    private function loadDepartmentsAndPositions()
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

        $this->departmentsList = array_values(array_unique(array_merge($defaultDepts, $depts)));
        $this->positionsList = array_values(array_unique(array_merge($defaultPositions, $positions)));
    }

    public function addDepartment()
    {
        $name = trim($this->newDepartment);
        if (!$name) {
            $this->addError('newDepartment', 'Type a department name before adding it.');
            return;
        }
        if (in_array($name, $this->departmentsList)) {
            $this->addError('newDepartment', "\"{$name}\" already exists in the department list.");
            return;
        }
        $this->departmentsList[] = $name;
        $this->department = $name;
        $this->newDepartment = '';
        $this->showNewDepartment = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Success', 'message' => "The {$name} department is now available for assignment."]);
    }

    public function addPosition()
    {
        $name = trim($this->newPosition);
        if (!$name) {
            $this->addError('newPosition', 'Type a position name before adding it.');
            return;
        }
        if (in_array($name, $this->positionsList)) {
            $this->addError('newPosition', "\"{$name}\" already exists in the position list.");
            return;
        }
        $this->positionsList[] = $name;
        $this->position = $name;
        $this->newPosition = '';
        $this->showNewPosition = false;
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Success', 'message' => "The {$name} position is now available for assignment."]);
    }

    // ─── Statistics ──────────────────────────────────────────────────────

    public function getStatsProperty()
    {
        $employees = User::whereHas('profile', fn($q) => $q->where('is_employee', true));

        return [
            'total'           => (clone $employees)->count(),
            'active'          => (clone $employees)->where('status', 'active')->count(),
            'suspended'       => (clone $employees)->where('status', 'suspended')->count(),
            'on_leave'        => Attendance::today()->where('status', 'leave')->count(),
            'absent_today'    => Attendance::today()->where('status', 'absent')->count(),
            'unmarked_today'  => (clone $employees)->count() - Attendance::today()->count(),
        ];
    }

    // ─── Employees List ──────────────────────────────────────────────────

    public function getEmployeesProperty()
    {
        return User::whereHas('profile', fn($q) => $q->where('is_employee', true))
            ->with('profile')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhereHas('profile', fn($p) => $p->where('employee_id', 'like', "%{$this->search}%"));
            }))
            ->when($this->departmentFilter, fn($q) => $q->whereHas('profile', fn($p) => $p->where('department', $this->departmentFilter)))
            ->when($this->employmentType, fn($q) => $q->whereHas('profile', fn($p) => $p->where('employment_type', $this->employmentType)))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function getDepartmentsFilterProperty()
    {
        return UserProfile::where('is_employee', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->values()
            ->toArray();
    }

    // ─── Employee CRUD ──────────────────────────────────────────────────

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

    /** Shared reset for anything transient in the employee modal (dropdowns, "add new" inputs). */
    private function resetEmployeeModalUiState(): void
    {
        $this->showCountryDropdown = false;
        $this->countrySearch = '';
        $this->filteredCountries = $this->countries;

        $this->emergency_showCountryDropdown = false;
        $this->emergency_countrySearch = '';
        $this->emergency_filteredCountries = $this->countries;

        $this->showNewDepartment = false;
        $this->showNewPosition = false;
        $this->newDepartment = '';
        $this->newPosition = '';
    }

    public function openCreate()
    {
        $this->resetValidation();
        $this->reset([
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
            'position',
            'department',
            'employment_type',
            'hire_date',
            'employee_id',
            'emergency_contact_name',
            'emergency_contact_phone',
            'is_featured_team',
            'editingEmployeeId',
            'date_of_birth',
            'country_code',
            'city',
        ]);
        $this->employment_type = 'full-time';
        $this->countryCode = '+233';
        $this->selectedFlag = 'gh.png';
        $this->updateCountryInfo();
        $this->emergency_countryCode = '+233';
        $this->emergency_selectedFlag = 'gh.png';
        $this->emergency_updateCountryInfo();
        $this->employee_id = $this->generateEmployeeId();
        $this->resetEmployeeModalUiState();
        $this->showEmployeeModal = true;
    }

    public function openEdit($userId)
    {
        $this->resetValidation();
        $user = User::with('profile')->findOrFail($userId);
        $profile = $user->profile;

        $this->editingEmployeeId = $user->id;
        $nameParts = explode(' ', $user->name, 2);
        $this->first_name = $nameParts[0] ?? '';
        $this->last_name = $nameParts[1] ?? '';
        $this->email = $user->email;
        $this->parsePhoneNumber($user->phone);

        $this->gender = $profile->gender ?? '';
        $this->position = $profile->position ?? '';
        $this->department = $profile->department ?? '';
        $this->employment_type = $profile->employment_type ?? 'full-time';

        $this->hire_date = $profile->hire_date ? Carbon::parse($profile->hire_date)->format('Y-m-d') : '';
        $this->employee_id = $profile->employee_id ?? '';
        $this->emergency_contact_name = $profile->emergency_contact_name ?? '';
        $this->emergency_contact_phone = $profile->emergency_contact_phone ?? '';
        $this->is_featured_team = $profile->is_featured_team ?? false;

        // 🔥 NEW: load new fields
        $this->date_of_birth = $profile->date_of_birth ? Carbon::parse($profile->date_of_birth)->format('Y-m-d') : '';
        $this->country_code = $profile->country_code ?? '';
        $this->city = $profile->city ?? '';

        $this->emergency_parsePhoneNumber($profile->emergency_contact_phone);

        $this->loadDepartmentsAndPositions();
        $this->resetEmployeeModalUiState();
        $this->showEmployeeModal = true;
    }

    public function saveEmployee()
    {
        $min = $this->countryInfo['minLength'] ?? 5;
        $max = $this->countryInfo['maxLength'] ?? 15;
        $pattern = $this->countryInfo['pattern'] ?? '^[0-9]{' . $min . ',' . $max . '}$';

        $emergencyMin = $this->emergency_countryInfo['minLength'] ?? 5;
        $emergencyMax = $this->emergency_countryInfo['maxLength'] ?? 15;
        $emergencyPattern = $this->emergency_countryInfo['pattern'] ?? '^[0-9]{' . $emergencyMin . ',' . $emergencyMax . '}$';

        $this->validate([
            'first_name'  => 'required|string|min:2|max:50',
            'last_name'   => 'required|string|min:2|max:50',
            'email'       => 'required|email|unique:users,email,' . $this->editingEmployeeId,
            'gender'      => 'required|in:male,female,other',
            'position'    => 'required|string|max:255',
            'department'  => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,intern',
            'hire_date'   => 'required|date|before_or_equal:today',
            'employee_id' => 'required|string|max:50|unique:user_profiles,employee_id,' . ($this->editingEmployeeId ? UserProfile::where('user_id', $this->editingEmployeeId)->first()?->id : 'NULL'),
            'emergency_contact_name'  => 'required|string|min:2|max:255',
            'emergency_contact_phone' => ['required', 'string', 'regex:/' . $emergencyPattern . '/'],
            'is_featured_team' => 'boolean',
            'phone' => ['required', 'string', 'regex:/' . $pattern . '/'],
            // 🔥 NEW: optional fields – adjust as needed
            'date_of_birth' => 'nullable|date|before:today',
            'country_code'  => 'nullable|string|max:10',
            'city'          => 'nullable|string|max:100',
        ], [
            'first_name.required'  => 'Please enter the employee\'s first name.',
            'first_name.min'       => 'First name should be at least 2 characters.',
            'last_name.required'   => 'Please enter the employee\'s last name.',
            'last_name.min'        => 'Last name should be at least 2 characters.',
            'email.required'       => 'An email address is required to create the account.',
            'email.email'          => 'Please enter a valid email address.',
            'email.unique'         => 'This email is already registered to another employee.',
            'gender.required'      => 'Please select the employee\'s gender.',
            'position.required'    => 'Please select or add a job title.',
            'department.required'  => 'Please select or add a department.',
            'employment_type.required' => 'Please choose an employment type.',
            'hire_date.required'   => 'Please provide the hire date.',
            'hire_date.before_or_equal' => 'Hire date can\'t be in the future.',
            'employee_id.required' => 'An employee ID is required.',
            'employee_id.unique'   => 'This employee ID is already assigned to someone else.',
            'emergency_contact_name.required' => 'Please provide an emergency contact name.',
            'emergency_contact_phone.required' => 'Please provide an emergency contact phone number.',
            'emergency_contact_phone.regex' => 'That doesn\'t look like a valid number for the selected country.',
            'phone.required'       => 'A phone number is required.',
            'phone.regex'          => 'Please enter a valid phone number for the selected country' . ($this->phoneExample ? " — e.g. {$this->phoneExample}." : '.'),
            // 🔥 optional – no custom messages needed
        ]);

        $fullName = trim($this->first_name . ' ' . $this->last_name);

        if ($this->editingEmployeeId) {
            $user = User::findOrFail($this->editingEmployeeId);
            $user->update([
                'name'  => $fullName,
                'email' => $this->email,
                'phone' => $this->getFullPhone(),
            ]);

            $profile = $user->profile ?: $user->profile()->create([]);
            $profile->update([
                'gender'         => $this->gender,
                'position'       => $this->position,
                'department'     => $this->department,
                'employment_type' => $this->employment_type,
                'hire_date'      => Carbon::parse($this->hire_date),
                'employee_id'    => $this->employee_id,
                'emergency_contact_name'  => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_getFullPhone(),
                'is_featured_team' => $this->is_featured_team,
                'is_employee'    => true,
                // 🔥 NEW: save new fields
                'date_of_birth'  => $this->date_of_birth ? Carbon::parse($this->date_of_birth) : null,
                'country_code'   => $this->country_code ?: null,
                'city'           => $this->city ?: null,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Success',
                'message' => "{$fullName}'s record has been updated and is now current."
            ]);
        } else {
            $user = User::create([
                'name'     => $fullName,
                'email'    => $this->email,
                'password' => bcrypt(Str::random(16)),
                'status'   => 'active',
                'phone'    => $this->getFullPhone(),
                'email_verified_at' => now(),
            ]);

            $user->profile()->create([
                'gender'         => $this->gender,
                'position'       => $this->position,
                'department'     => $this->department,
                'employment_type' => $this->employment_type,
                'hire_date'      => Carbon::parse($this->hire_date),
                'employee_id'    => $this->employee_id,
                'emergency_contact_name'  => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_getFullPhone(),
                'is_featured_team' => $this->is_featured_team,
                'is_employee'    => true,
                // 🔥 NEW: save new fields
                'date_of_birth'  => $this->date_of_birth ? Carbon::parse($this->date_of_birth) : null,
                'country_code'   => $this->country_code ?: null,
                'city'           => $this->city ?: null,
            ]);

            $user->assignRole('User');

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Success',
                'message' => "{$fullName} has joined the roster as {$this->employee_id}."
            ]);
        }

        $this->showEmployeeModal = false;
        $this->reset([
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
            'position',
            'department',
            'employment_type',
            'hire_date',
            'employee_id',
            'emergency_contact_name',
            'emergency_contact_phone',
            'is_featured_team',
            'editingEmployeeId',
            'date_of_birth',
            'country_code',
            'city',
        ]);
        $this->loadDepartmentsAndPositions();
    }

    // ─── Delete Employee ─────────────────────────────────────────────────

    public function confirmDelete($userId)
    {
        $this->deleteUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if (!$this->deleteUserId) {
            return;
        }

        $user = User::find($this->deleteUserId);
        if ($user && $user->id !== Auth::id()) {
            $name = $user->name;
            $user->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Success',
                'message' => "{$name} has been removed from the team permanently."
            ]);
        } elseif ($user) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Error',
                'message' => "You can't delete your own account while logged in as it."
            ]);
        }

        $this->showDeleteModal = false;
        $this->deleteUserId = null;
    }

    // ─── Invitation ──────────────────────────────────────────────────────

    public function openInviteModal()
    {
        $this->resetValidation();
        $this->reset(['inviteEmail', 'inviteRoleId', 'invitePosition', 'inviteExpiryDays']);
        $this->inviteExpiryDays = 7;
        $this->showInviteModal = true;
    }

    public function sendInvitation()
    {
        $this->validate([
            'inviteEmail'      => 'required|email|max:255|unique:invitations,email|unique:users,email',
            'inviteRoleId'     => 'nullable|exists:roles,id',
            'invitePosition'   => 'nullable|string|max:255',
            'inviteExpiryDays' => 'required|integer|min:1|max:30',
        ], [
            'inviteEmail.required' => 'An email address is required to send an invitation.',
            'inviteEmail.email'    => 'Please enter a valid email address.',
            'inviteEmail.unique'   => 'An invitation or account already exists for this email.',
            'inviteRoleId.exists'  => 'Please choose a valid role.',
            'inviteExpiryDays.required' => 'Please set how many days the invite link stays valid.',
            'inviteExpiryDays.min' => 'The invite must stay valid for at least 1 day.',
            'inviteExpiryDays.max' => 'The invite can stay valid for at most 30 days.',
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

        $this->showInviteModal = false;
        $this->reset(['inviteEmail', 'inviteRoleId', 'invitePosition', 'inviteExpiryDays']);
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Success',
            'message' => "An invitation is on its way to {$this->inviteEmail}."
        ]);
    }

    // ─── Single-employee attendance ──────────────────────────────────────

    public function markAttendance($userId)
    {
        if (!$userId) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'No employee was selected. Use "Bulk Mark" to record attendance for several people at once.',
            ]);
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'That employee could not be found.']);
            return;
        }

        $today = today()->toDateString();
        $existing = Attendance::forUser($userId)->forDate($today)->first();

        $this->attendanceUserId = $userId;
        $this->attendanceUserName = $user->name;
        $this->attendanceDate = $today;
        $this->attendanceStatus = $existing->status ?? 'present';
        $this->attendanceNotes = $existing->notes ?? null;

        $this->attendanceCheckIn = $existing && $existing->check_in
            ? Carbon::parse($existing->check_in)->format('H:i')
            : now()->format('H:i');

        $this->attendanceCheckOut = $existing && $existing->check_out
            ? Carbon::parse($existing->check_out)->format('H:i')
            : null;

        $this->showAttendanceModal = true;
    }

    public function editAttendanceCell($userId, $date)
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $existing = Attendance::forUser($userId)->forDate($date)->first();

        $this->attendanceUserId = $userId;
        $this->attendanceUserName = $user->name;
        $this->attendanceDate = $date;
        $this->attendanceStatus = $existing->status ?? 'present';
        $this->attendanceNotes = $existing->notes ?? null;

        $this->attendanceCheckIn = $existing && $existing->check_in
            ? Carbon::parse($existing->check_in)->format('H:i')
            : null;

        $this->attendanceCheckOut = $existing && $existing->check_out
            ? Carbon::parse($existing->check_out)->format('H:i')
            : null;

        $this->showAttendanceModal = true;
    }

    public function saveAttendance()
    {
        $this->validate([
            'attendanceUserId'   => 'required|exists:users,id',
            'attendanceDate'     => 'required|date',
            'attendanceStatus'   => 'required|in:present,absent,leave,holiday',
            'attendanceCheckIn'  => 'nullable|date_format:H:i',
            'attendanceCheckOut' => 'nullable|date_format:H:i|after:attendanceCheckIn',
        ], [
            'attendanceUserId.required' => 'No employee was selected for this attendance record.',
            'attendanceDate.required'   => 'Please select the date being recorded.',
            'attendanceStatus.required' => 'Please choose an attendance status.',
            'attendanceCheckIn.date_format'  => 'Check-in must be a valid time (HH:MM).',
            'attendanceCheckOut.date_format' => 'Check-out must be a valid time (HH:MM).',
            'attendanceCheckOut.after'  => 'Check-out time must be later than check-in time.',
        ]);

        Attendance::updateOrCreate(
            ['user_id' => $this->attendanceUserId, 'date' => $this->attendanceDate],
            [
                'status'    => $this->attendanceStatus,
                'notes'     => $this->attendanceNotes,
                'check_in'  => $this->attendanceCheckIn ? Carbon::parse($this->attendanceDate . ' ' . $this->attendanceCheckIn) : null,
                'check_out' => $this->attendanceCheckOut ? Carbon::parse($this->attendanceDate . ' ' . $this->attendanceCheckOut) : null,
            ]
        );

        $this->showAttendanceModal = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Success',
            'message' => "{$this->attendanceUserName}'s attendance for " . Carbon::parse($this->attendanceDate)->format('M j, Y') . " is now logged as " . ucfirst($this->attendanceStatus) . '.',
        ]);
    }

    /**
     * One-click marking for today's column: cycles Present → Absent → Leave → Cleared.
     * This version is fixed and tested.
     */
    public function quickMarkToday($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'User not found.']);
            return;
        }

        $today = today()->toDateString();
        $existing = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        $cycle = ['present', 'absent', 'leave'];

        $currentIndex = $existing ? array_search(strtolower(trim($existing->status)), $cycle, true) : -1;
        $nextIndex = ($currentIndex === false) ? -1 : $currentIndex;
        $next = $cycle[$nextIndex + 1] ?? null;

        if ($next === null) {
            if ($existing) $existing->delete();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Cleared', 'message' => "Today's mark for {$user->name} has been cleared."]);
            return;
        }

        $data = [
            'status'    => $next,
            'check_in'  => $next === 'present' ? now() : null,
            'check_out' => null,
        ];

        if ($existing) {
            $existing->update($data);
        } else {
            $data['user_id'] = $userId;
            $data['date'] = $today;
            Attendance::create($data);
        }

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Marked', 'message' => "{$user->name} marked as " . ucfirst($next) . " for today."]);
    }

    // ─── Bulk / whole-team attendance ────────────────────────────────────

    public function openBulkAttendance()
    {
        $this->resetValidation();
        $this->reset(['bulkSelectedEmployees', 'bulkAttendanceNotes', 'bulkSelectAll']);
        $this->bulkAttendanceDate = today()->format('Y-m-d');
        $this->bulkAttendanceStatus = 'present';
        $this->showBulkAttendanceModal = true;
    }

    public function updatedBulkSelectAll($value)
    {
        if ($value) {
            $this->bulkSelectedEmployees = User::whereHas('profile', fn($q) => $q->where('is_employee', true))
                ->where('status', 'active')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->bulkSelectedEmployees = [];
        }
    }

    /** Keep the "select all" checkbox honest if someone manually toggles individual rows. */
    public function updatedBulkSelectedEmployees(): void
    {
        $activeCount = User::whereHas('profile', fn($q) => $q->where('is_employee', true))
            ->where('status', 'active')
            ->count();

        $this->bulkSelectAll = $activeCount > 0 && count($this->bulkSelectedEmployees) === $activeCount;
    }

    public function saveBulkAttendance()
    {
        $this->validate([
            'bulkAttendanceDate'   => 'required|date',
            'bulkAttendanceStatus' => 'required|in:present,absent,leave,holiday',
            'bulkSelectedEmployees'   => 'required|array|min:1',
            'bulkSelectedEmployees.*' => 'exists:users,id',
        ], [
            'bulkAttendanceDate.required'   => 'Please select the date being recorded.',
            'bulkAttendanceStatus.required' => 'Please choose an attendance status to apply.',
            'bulkSelectedEmployees.required' => 'Select at least one employee to mark.',
            'bulkSelectedEmployees.min'      => 'Select at least one employee to mark.',
        ]);

        $count = 0;
        DB::transaction(function () use (&$count) {
            foreach ($this->bulkSelectedEmployees as $userId) {
                Attendance::updateOrCreate(
                    ['user_id' => $userId, 'date' => $this->bulkAttendanceDate],
                    ['status' => $this->bulkAttendanceStatus, 'notes' => $this->bulkAttendanceNotes]
                );
                $count++;
            }
        });

        $this->showBulkAttendanceModal = false;
        $this->reset(['bulkSelectedEmployees', 'bulkAttendanceNotes', 'bulkSelectAll']);

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Success',
            'message' => "Attendance recorded as " . ucfirst($this->bulkAttendanceStatus) . " for {$count} " . Str::plural('employee', $count) . " — the register is up to date.",
        ]);
    }

    // ─── Month changed ──────────────────────────────────────────────────

    public function monthChanged()
    {
        $this->resetPage();
    }

    // ─── Holidays helper (recurrence-aware) ─────────────────────────────

    /** All holidays, loaded once per request. */
    private function allHolidays(): \Illuminate\Support\Collection
    {
        return $this->holidaysCache ??= Holiday::orderBy('date')->get();
    }

    /** Every calendar date (Y-m-d strings) in $year/$month that is a company holiday. */
    private function holidayDatesFor(int $year, int $month): array
    {
        return $this->allHolidays()
            ->map(fn(Holiday $h) => $h->occursOn($year, $month))
            ->filter()
            ->values()
            ->all();
    }

    // ─── Attendance Calendar Data ──────────────────────────────────────

    public function getAttendanceDataProperty()
    {
        $start = Carbon::create($this->year, $this->month, 1);
        $days = $start->daysInMonth;

        $attendance = Attendance::whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->get()
            ->groupBy('user_id');

        $holidayDates = $this->holidayDatesFor($this->year, $this->month);

        $employees = User::whereHas('profile', fn($q) => $q->where('is_employee', true))
            ->with('profile')
            ->orderBy('name')
            ->get();

        $data = [];
        foreach ($employees as $employee) {
            $userAttendance = $attendance->get($employee->id, collect())
                ->keyBy(fn($att) => $att->date->toDateString());

            $row = [
                'user' => $employee,
                'days' => [],
                'total_present' => 0,
                'total_absent'  => 0,
                'total_leave'   => 0,
            ];

            for ($i = 1; $i <= $days; $i++) {
                $date = Carbon::create($this->year, $this->month, $i);
                $dateString = $date->toDateString();
                $record = $userAttendance->get($dateString);
                $status = $record->status ?? null; // null = unmarked
                $isHoliday = in_array($dateString, $holidayDates, true);

                $row['days'][$i] = [
                    'status'     => $status,
                    'display'    => Attendance::displayFor($status, $isHoliday),
                    'is_weekend' => $date->isWeekend(),
                    'is_holiday' => $isHoliday,
                    'is_today'   => $date->isToday(),
                    'is_future'  => $date->isFuture(),
                ];

                $normalized = $status ? strtolower(trim($status)) : null;
                if ($normalized === 'present') $row['total_present']++;
                elseif ($normalized === 'absent') $row['total_absent']++;
                elseif ($normalized === 'leave') $row['total_leave']++;
            }

            $marked = $row['total_present'] + $row['total_absent'] + $row['total_leave'];
            $row['attendance_rate'] = $marked > 0
                ? (int) round(($row['total_present'] / $marked) * 100)
                : 0;

            $data[] = $row;
        }
        return $data;
    }

    // ─── Holidays ──────────────────────────────────────────────────────

    /** Upcoming holidays, correctly resolving recurring ones to their next real occurrence. */
    public function getUpcomingHolidaysProperty()
    {
        $today = today();

        return $this->allHolidays()
            ->map(function (Holiday $holiday) use ($today) {
                $holiday->next_date = $holiday->nextOccurrenceFrom($today);
                return $holiday;
            })
            ->filter(fn(Holiday $h) => $h->next_date->gte($today->copy()->startOfDay()))
            ->sortBy('next_date')
            ->take(5)
            ->values();
    }

    public function openHolidayModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['holidayName', 'holidayDate', 'holidayRecurring', 'editingHolidayId']);
        if ($id) {
            $holiday = Holiday::findOrFail($id);
            $this->editingHolidayId = $id;
            $this->holidayName = $holiday->name;
            $this->holidayDate = $holiday->date->format('Y-m-d');
            $this->holidayRecurring = $holiday->recurring ?? false;
        } else {
            $this->holidayDate = today()->format('Y-m-d');
        }
        $this->showHolidayModal = true;
    }

    public function saveHoliday()
    {
        $this->validate([
            'holidayName' => 'required|string|max:255',
            'holidayDate' => 'required|date',
            'holidayRecurring' => 'boolean',
        ]);

        Holiday::updateOrCreate(
            ['id' => $this->editingHolidayId],
            [
                'name' => $this->holidayName,
                'date' => $this->holidayDate,
                'recurring' => $this->holidayRecurring,
            ]
        );

        $this->holidaysCache = null; // invalidate cache

        $this->showHolidayModal = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Success',
            'message' => $this->editingHolidayId ? 'Holiday updated.' : 'Holiday added.',
        ]);
        $this->reset(['holidayName', 'holidayDate', 'holidayRecurring', 'editingHolidayId']);
    }

    public function deleteHoliday($id)
    {
        Holiday::findOrFail($id)->delete();
        $this->holidaysCache = null; // invalidate cache
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Deleted',
            'message' => 'Holiday removed.'
        ]);
    }

    // ─── Export Attendance Report ──────────────────────────────────────

    public function exportAttendance()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=attendance-{$this->year}-{$this->month}.csv",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee Name', 'Department', 'Total Present', 'Total Absent', 'Total Leave', 'Attendance Rate %']);

            $attendanceData = $this->attendanceData;
            foreach ($attendanceData as $row) {
                fputcsv($handle, [
                    $row['user']->name,
                    $row['user']->profile?->department ?? '—',
                    $row['total_present'],
                    $row['total_absent'],
                    $row['total_leave'],
                    $row['attendance_rate'],
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Attendance Stats ──────────────────────────────────────────────

    public function getAttendanceStatsProperty()
    {
        $total = Attendance::forMonth($this->year, $this->month)->count();

        if ($total === 0) {
            return ['present' => 0, 'absent' => 0, 'leave' => 0, 'rate' => 0];
        }

        $present = Attendance::forMonth($this->year, $this->month)->where('status', 'present')->count();
        $absent  = Attendance::forMonth($this->year, $this->month)->where('status', 'absent')->count();
        $leave   = Attendance::forMonth($this->year, $this->month)->where('status', 'leave')->count();

        return [
            'present' => $present,
            'absent'  => $absent,
            'leave'   => $leave,
            'rate'    => (int) round(($present / $total) * 100),
        ];
    }

    // ─── Country / Phone logic (primary) ────────────────────────────────

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
            $this->countryInfo = ['name' => 'Ghana', 'pattern' => '^[0-9]{9}$', 'minLength' => 9, 'maxLength' => 9, 'example' => '201234567'];
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
        $this->countrySearch = '';
        $this->filteredCountries = $this->countries;
    }

    public function toggleCountryDropdown()
    {
        $this->showCountryDropdown = !$this->showCountryDropdown;
        if ($this->showCountryDropdown) {
            $this->countrySearch = '';
            $this->filteredCountries = $this->countries;
        }
    }

    public function closeCountryDropdown()
    {
        $this->showCountryDropdown = false;
        $this->countrySearch = '';
        $this->filteredCountries = $this->countries;
    }

    public function searchCountries($searchTerm)
    {
        $this->countrySearch = $searchTerm;
        $this->filteredCountries = collect($this->countries)
            ->filter(fn($c) => stripos($c['name'], $this->countrySearch) !== false || stripos($c['code'], $this->countrySearch) !== false)
            ->values()
            ->toArray();
    }

    public function setPhone(string $value): void
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        $clean = ltrim($clean, '0') ?: $clean; // drop a leading trunk-code 0 before enforcing length
        $max = $this->countryInfo['maxLength'] ?? 15;
        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
        }
        $this->phone = $clean;
    }

    public function getFullPhone(): string
    {
        $clean = ltrim($this->phone, '0');
        return $this->countryCode . $clean;
    }

    private function parsePhoneNumber(?string $phone): void
    {
        if (empty($phone)) {
            $this->phone = '';
            $this->countryCode = '+233';
            $this->selectedFlag = 'gh.png';
            $this->updateCountryInfo();
            return;
        }
        $matchedCountry = null;
        $matchedCode = '';
        foreach ($this->countries as $country) {
            $code = $country['code'];
            if (str_starts_with($phone, $code) && strlen($code) > strlen($matchedCode)) {
                $matchedCode = $code;
                $matchedCountry = $country;
            }
        }
        if ($matchedCountry) {
            $this->countryCode = $matchedCode;
            $this->selectedFlag = $matchedCountry['flag'];
            $this->phone = substr($phone, strlen($matchedCode));
        } else {
            $this->countryCode = '+233';
            $this->selectedFlag = 'gh.png';
            $this->phone = $phone;
        }
        $this->updateCountryInfo();
    }

    // ─── Emergency Phone logic ──────────────────────────────────────────

    public function emergency_updateCountryInfo()
    {
        $country = collect($this->countries)->firstWhere('code', $this->emergency_countryCode);
        if ($country) {
            $this->emergency_countryInfo = $country;
            $this->emergency_phoneExample = $country['example'] ?? '';
        } else {
            $this->emergency_countryInfo = ['name' => 'Ghana', 'pattern' => '^[0-9]{9}$', 'minLength' => 9, 'maxLength' => 9, 'example' => '201234567'];
            $this->emergency_phoneExample = '201234567';
        }
    }

    public function emergency_selectCountry($code, $flag)
    {
        $this->emergency_countryCode = $code;
        $this->emergency_selectedFlag = $flag;
        $this->emergency_updateCountryInfo();
        $this->emergency_contact_phone = '';
        $this->emergency_showCountryDropdown = false;
        $this->emergency_countrySearch = '';
        $this->emergency_filteredCountries = $this->countries;
    }

    public function emergency_toggleCountryDropdown()
    {
        $this->emergency_showCountryDropdown = !$this->emergency_showCountryDropdown;
        if ($this->emergency_showCountryDropdown) {
            $this->emergency_countrySearch = '';
            $this->emergency_filteredCountries = $this->countries;
        }
    }

    public function emergency_closeCountryDropdown()
    {
        $this->emergency_showCountryDropdown = false;
        $this->emergency_countrySearch = '';
        $this->emergency_filteredCountries = $this->countries;
    }

    public function emergency_searchCountries($searchTerm)
    {
        $this->emergency_countrySearch = $searchTerm;
        $this->emergency_filteredCountries = collect($this->countries)
            ->filter(fn($c) => stripos($c['name'], $this->emergency_countrySearch) !== false || stripos($c['code'], $this->emergency_countrySearch) !== false)
            ->values()
            ->toArray();
    }

    public function emergency_setPhone(string $value): void
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        $clean = ltrim($clean, '0') ?: $clean;
        $max = $this->emergency_countryInfo['maxLength'] ?? 15;
        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
        }
        $this->emergency_contact_phone = $clean;
    }

    public function emergency_getFullPhone(): string
    {
        $clean = ltrim($this->emergency_contact_phone, '0');
        return $this->emergency_countryCode . $clean;
    }

    private function emergency_parsePhoneNumber(?string $phone): void
    {
        if (empty($phone)) {
            $this->emergency_contact_phone = '';
            $this->emergency_countryCode = '+233';
            $this->emergency_selectedFlag = 'gh.png';
            $this->emergency_updateCountryInfo();
            return;
        }
        $matchedCountry = null;
        $matchedCode = '';
        foreach ($this->countries as $country) {
            $code = $country['code'];
            if (str_starts_with($phone, $code) && strlen($code) > strlen($matchedCode)) {
                $matchedCode = $code;
                $matchedCountry = $country;
            }
        }
        if ($matchedCountry) {
            $this->emergency_countryCode = $matchedCode;
            $this->emergency_selectedFlag = $matchedCountry['flag'];
            $this->emergency_contact_phone = substr($phone, strlen($matchedCode));
        } else {
            $this->emergency_countryCode = '+233';
            $this->emergency_selectedFlag = 'gh.png';
            $this->emergency_contact_phone = $phone;
        }
        $this->emergency_updateCountryInfo();
    }

    // ─── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $attendanceStats = $this->attendanceStats;

        $this->dispatch(
            'chart-data-updated',
            present: $attendanceStats['present'],
            absent: $attendanceStats['absent'],
            leave: $attendanceStats['leave'],
        );

        return view('livewire.admin.hrm.hr-dashboard', [
            'stats'            => $this->stats,
            'employees'        => $this->employees,
            'departments'      => $this->departmentsFilter,
            'attendanceData'   => $this->attendanceData,
            'upcomingHolidays' => $this->upcomingHolidays,
            'attendanceStats'  => $attendanceStats,
            'daysInMonth'      => Carbon::create($this->year, $this->month)->daysInMonth,
            'assignableRoles'  => $this->assignableRoles,
            'departmentsList'  => $this->departmentsList,
            'positionsList'    => $this->positionsList,
            'allActiveEmployees' => User::whereHas('profile', fn($q) => $q->where('is_employee', true))
                ->where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}

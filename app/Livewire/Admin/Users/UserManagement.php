<?php

namespace App\Livewire\Admin\Users;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserActivity;
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

    // Filters
    public string $search = '';
    public string $statusFilter = '';
    public string $roleFilter = '';
    public string $verifiedFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    // Invite
    public string $inviteEmail = '';
    public $inviteRoleId = null;
    public string $invitePosition = '';
    public int $inviteExpiryDays = 7;

    // Modals
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;
    public bool $showUserModal = false;
    public bool $showViewModal = false;
    public bool $showToggleStatusModal = false;
    public bool $showToggleVerifyModal = false;
    public bool $showBulkVerifyModal = false;
    public bool $showInviteModal = false;

    // Selected user
    public ?int $selectedUserId = null;
    public ?User $viewingUser = null;
    public ?int $toggleUserId = null;
    public ?int $verifyUserId = null;

    // Activity log for the viewing user
    public array $recentActivities = [];

    // Bulk selection
    public array $selectedUsers = [];
    public bool $selectAll = false;

    // Form fields
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];
    public string $formStatus = 'active';
    public bool $isEditing = false;
    public string $position = '';          // ✅ Job title
    public bool $is_featured_team = false;

    // Generated password for display
    public ?string $generatedPassword = null;

    // Available roles
    public array $availableRoles = [];
    protected string $protectedRole = 'Super Admin';
    protected string $defaultRole = 'User';

    public function mount(): void
    {
        $this->loadAvailableRoles();
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
            'password'   => 'nullable|min:8',
        ];
    }

    protected $queryString = [
        'search'         => ['except' => ''],
        'statusFilter'   => ['except' => ''],
        'roleFilter'     => ['except' => ''],
        'verifiedFilter' => ['except' => ''],
        'sortBy'         => ['except' => 'created_at'],
        'sortDir'        => ['except' => 'desc'],
        'perPage'        => ['except' => 15],
    ];

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

    // ── Activity log ─────────────────────────────────────────────────
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

    // ── View user ──────────────────────────────────────────────────
    public function viewUser(int $id): void
    {
        $this->viewingUser = User::with('roles', 'profile')->findOrFail($id);
        $this->recentActivities = class_exists(UserActivity::class)
            ? UserActivity::where('user_id', $id)->latest()->take(10)->get()->toArray()
            : [];
        $this->showViewModal = true;
    }

    // ── Create / Edit ──────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->reset(['first_name', 'last_name', 'email', 'password', 'selectedRoles', 'formStatus', 'selectedUserId', 'position', 'is_featured_team', 'generatedPassword']);
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

        // ✅ Correctly set position from profile
        $this->position         = $user->profile?->position ?? '';
        $this->is_featured_team = $user->profile?->is_featured_team ?? false;

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

        $fullName = trim($this->first_name . ' ' . $this->last_name);

        if ($this->isEditing) {
            $password = $this->password ? Hash::make($this->password) : null;
        } else {
            if (empty($this->password)) {
                $this->generatedPassword = $this->generateSecurePassword();
                $password = Hash::make($this->generatedPassword);
            } else {
                $password = Hash::make($this->password);
                $this->generatedPassword = null;
            }
        }

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

        if ($password) {
            $data['password'] = $password;
        }

        try {
            if ($this->isEditing) {
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
            } else {
                $data['username'] = $this->generateUsername($this->first_name, $this->last_name);
                $data['email_verified_at'] = now();
                if (!isset($data['password'])) {
                    $this->generatedPassword = $this->generateSecurePassword();
                    $data['password'] = Hash::make($this->generatedPassword);
                }

                $user = User::create($data);
                $user->assignRole($roleNames);

                $user->profile()->create([
                    'position' => $this->position,
                    'is_featured_team' => $this->is_featured_team,
                ]);

                $this->logActivity($user->id, 'admin_create', "Account created by admin ({$fullName}, {$this->email}).");

                $this->dispatch('notify', [
                    'type' => 'success',
                    'title' => 'User Created!',
                    'message' => $this->generatedPassword
                        ? "Password for {$fullName}: <strong>{$this->generatedPassword}</strong>"
                        : "{$fullName} has been added.",
                ]);
                $this->generatedPassword = null;
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

        $this->showUserModal = false;
        $this->reset(['first_name', 'last_name', 'email', 'password', 'selectedRoles', 'formStatus', 'selectedUserId', 'position', 'is_featured_team']);
    }

    // ── Single delete ──────────────────────────────────────────────
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

    // ── Bulk actions ────────────────────────────────────────────────
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

    // ── Toggle status ─────────────────────────────────────────────
    public function confirmToggleStatus(int $id): void
    {
        if ($id === Auth::id()) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Error', 'message' => 'You cannot change your own status.']);
            return;
        }
        $this->toggleUserId = $id;
        $this->showToggleStatusModal = true;
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

    // ── Verification: manual toggle ──────────────────────────────
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

    // ── Verification: resend email ──────────────────────────────
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

    // ── Verification: bulk resend ────────────────────────────────
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

    // ── Invitation ────────────────────────────────────────────────
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

    // ── Query ──────────────────────────────────────────────────────
    private function getQuery()
    {
        return User::query()
            ->with('roles', 'profile')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select('users.*', 'user_profiles.position as position', 'user_profiles.is_featured_team')
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
            ],
            'protectedRole' => $this->protectedRole,
            'availableRoles' => $this->availableRoles,
            'assignableRoles' => $this->assignableRoles,
        ]);
    }
}

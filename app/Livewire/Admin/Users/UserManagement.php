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

    // Modals
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;
    public bool $showUserModal = false;
    public bool $showViewModal = false;
    public bool $showToggleStatusModal = false;
    public bool $showToggleVerifyModal = false;
    public bool $showBulkVerifyModal = false;
    public bool $showInviteModal = false; // NEW

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
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];
    public string $formStatus = 'active';
    public bool $isEditing = false;

    // Invitation fields
    public string $inviteEmail = '';
    public ?int $inviteRoleId = null;
    public int $inviteExpiryDays = 7;

    // Available roles
    public array $availableRoles = [];
    protected string $protectedRole = 'Super Admin';

    public function mount(): void
    {
        $this->loadAvailableRoles();
    }

    private function loadAvailableRoles(): void
    {
        $this->ensureDefaultRoles();
        $this->availableRoles = Role::orderBy('name')->pluck('name')->toArray();
        if (empty($this->availableRoles)) {
            $this->availableRoles = ['admin', 'agent', 'user'];
        }
    }

    private function ensureDefaultRoles(): void
    {
        $defaultRoles = ['Super Admin', 'admin', 'agent', 'user'];
        foreach ($defaultRoles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name'       => 'required|string|min:2|max:100',
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($this->selectedUserId)],
            'formStatus' => 'required|in:active,inactive,suspended',
            'selectedRoles' => 'array',
            'selectedRoles.*' => ['string', Rule::in(Role::pluck('name')->toArray())],
        ];

        if (!$this->isEditing) {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['password'] = 'nullable|min:8';
        }

        return $rules;
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
        // If you have a UserActivity model, log it. Otherwise, just skip.
        // For now we'll just silently ignore if the model doesn't exist.
        if (class_exists(\App\Models\UserActivity::class)) {
            try {
                \App\Models\UserActivity::create([
                    'user_id'     => $userId,
                    'action'      => $action,
                    'description' => $description,
                    'ip_address'  => request()->ip(),
                    'user_agent'  => substr((string) request()->userAgent(), 0, 255),
                ]);
            } catch (\Throwable $e) {
                // log silently
            }
        }
    }

    // ── View user ──────────────────────────────────────────────────
    public function viewUser(int $id): void
    {
        $this->viewingUser   = User::with('roles')->findOrFail($id);
        $this->recentActivities = [];
        if (class_exists(\App\Models\UserActivity::class)) {
            $this->recentActivities = \App\Models\UserActivity::where('user_id', $id)
                ->latest()
                ->take(10)
                ->get()
                ->toArray();
        }
        $this->showViewModal = true;
    }

    // ── Create / Edit ──────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->reset(['name', 'email', 'password', 'selectedRoles', 'formStatus', 'selectedUserId']);
        $this->isEditing     = false;
        $this->formStatus    = 'active';
        $this->selectedRoles = ['user'];
        $this->showUserModal = true;
    }

    public function openEdit(int $id): void
    {
        $user                  = User::with('roles')->findOrFail($id);
        $this->selectedUserId  = $user->id;
        $this->name            = $user->name;
        $this->email           = $user->email;
        $this->formStatus      = $user->status ?? 'active';
        $this->password        = '';
        $this->isEditing       = true;

        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        if (empty($this->selectedRoles)) {
            $this->selectedRoles = ['user'];
        }

        $this->selectedRoles = array_filter($this->selectedRoles, function ($role) {
            return $role !== $this->protectedRole;
        });

        $this->showUserModal   = true;
    }

    public function saveUser(): void
    {
        $this->validate();

        $roleNames = array_filter($this->selectedRoles, function ($role) {
            return $role !== $this->protectedRole;
        });

        if (empty($roleNames)) {
            $roleNames = ['user'];
        }

        $data = [
            'name'   => $this->name,
            'email'  => $this->email,
            'status' => $this->formStatus,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

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

            $this->logActivity($user->id, 'admin_update', "Account updated by admin ({$this->name}, {$this->email}).");
            if ($emailChanged) {
                $this->logActivity($user->id, 'email_changed', 'Email address changed by admin; verification reset.');
            }

            session()->flash('success', 'User updated successfully.');
        } else {
            $data['password'] = Hash::make($this->password);
            $user = User::create($data);
            $user->assignRole($roleNames);

            $this->logActivity($user->id, 'admin_create', 'Account created by admin.');

            session()->flash('success', 'User created successfully.');
        }

        $this->showUserModal = false;
        $this->reset(['name', 'email', 'password', 'selectedRoles', 'formStatus', 'selectedUserId']);
    }

    // ── Single delete ──────────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        if ($id === Auth::id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }
        $this->selectedUserId  = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->selectedUserId === Auth::id()) {
            session()->flash('error', 'You cannot delete your own account.');
            $this->showDeleteModal = false;
            return;
        }
        User::findOrFail($this->selectedUserId)->delete();
        $this->showDeleteModal = false;
        $this->selectedUserId  = null;
        session()->flash('success', 'User deleted.');
    }

    // ── Bulk actions ────────────────────────────────────────────────
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedUsers = $value
            ? $this->getQuery()
            ->pluck('id')
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
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        $ids = array_filter($this->selectedUsers, fn($id) => (int) $id !== Auth::id());
        if (empty($ids)) {
            session()->flash('error', 'You cannot delete your own account.');
            $this->showBulkDeleteModal = false;
            return;
        }
        User::whereIn('id', $ids)->delete();
        $this->selectedUsers       = [];
        $this->selectAll           = false;
        $this->showBulkDeleteModal = false;
        session()->flash('success', count($ids) . ' user(s) deleted.');
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
        session()->flash('success', 'Selected users activated.');
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
        session()->flash('success', 'Selected users suspended.');
    }

    // ── Toggle status ─────────────────────────────────────────────
    public function confirmToggleStatus(int $id): void
    {
        if ($id === Auth::id()) {
            session()->flash('error', 'You cannot change your own status.');
            return;
        }
        $this->toggleUserId = $id;
        $this->showToggleStatusModal = true;
    }

    public function toggleStatusConfirmed(): void
    {
        if (!$this->toggleUserId) {
            return;
        }

        $user = User::findOrFail($this->toggleUserId);
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->status = $newStatus;
        $user->save();

        $this->logActivity($user->id, 'status_toggle', "Status changed to '{$newStatus}' by admin.");

        $this->showToggleStatusModal = false;
        $this->toggleUserId = null;
        session()->flash('success', "User status changed to '{$newStatus}' successfully.");
    }

    // ── Verification: manual toggle ──────────────────────────────
    public function confirmToggleVerify(int $id): void
    {
        $this->verifyUserId = $id;
        $this->showToggleVerifyModal = true;
    }

    public function toggleVerifyConfirmed(): void
    {
        if (!$this->verifyUserId) {
            return;
        }

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
        session()->flash('success', "User email marked as {$action}.");
    }

    // ── Verification: resend email ──────────────────────────────
    public function resendVerification(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            session()->flash('error', 'This user is already verified.');
            return;
        }

        $user->email_verification_token = Str::random(64);
        $user->email_verification_sent_at = now();
        $user->save();

        $user->sendEmailVerificationNotification();

        $this->logActivity($user->id, 'verification_resent', 'Verification email resent by admin.');

        session()->flash('success', "Verification email sent to {$user->email}.");
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

        session()->flash('success', $users->count() . ' verification email(s) sent.');
    }

    // ── Invitation ────────────────────────────────────────────────
    public function openInviteModal(): void
    {
        $this->reset(['inviteEmail', 'inviteRoleId', 'inviteExpiryDays']);
        $this->inviteExpiryDays = 7;
        $this->showInviteModal = true;
    }

    public function sendInvitation(): void
    {
        $this->validate([
            'inviteEmail' => 'required|email|max:255|unique:invitations,email|unique:users,email',
            'inviteRoleId' => 'nullable|exists:roles,id',
            'inviteExpiryDays' => 'required|integer|min:1|max:30',
        ]);

        $token = Str::random(64);

        $invitation = Invitation::create([
            'email'      => $this->inviteEmail,
            'token'      => $token,
            'role_id'    => $this->inviteRoleId ?: null,
            'expires_at' => now()->addDays($this->inviteExpiryDays),
            'invited_by' => Auth::id(),
        ]);

        // ─── Send queued invitation email ──────────────────────────────
        Mail::to($this->inviteEmail)->queue(new InvitationMail($invitation));

        $this->logActivity(Auth::id(), 'invitation_sent', "Invitation sent to {$this->inviteEmail}");

        $this->showInviteModal = false;
        $this->reset(['inviteEmail', 'inviteRoleId', 'inviteExpiryDays']);
        session()->flash('success', "Invitation sent to {$this->inviteEmail}");
    }

    // ── Query ──────────────────────────────────────────────────────
    private function getQuery()
    {
        return User::query()
            ->with('roles')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->roleFilter, fn($q) => $q->whereHas('roles', function ($q) {
                $q->where('name', $this->roleFilter);
            }))
            ->when($this->verifiedFilter === 'verified', fn($q) => $q->whereNotNull('email_verified_at'))
            ->when($this->verifiedFilter === 'unverified', fn($q) => $q->whereNull('email_verified_at'))
            ->orderBy($this->sortBy, $this->sortDir);
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
            'availableRoles' => $this->availableRoles, // already available
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Users;

use App\Helpers\ActivityLogger; // <-- Added
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.users')]
class RoleManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Table / Filter State ───────────────────────────────────────────────
    public string  $search       = '';
    public string  $sortBy       = 'created_at';
    public string  $sortDir      = 'desc';
    public int     $perPage      = 10;

    // ── Selected ──────────────────────────────────────────────────────────
    public array $selectedRoles = [];
    public bool  $selectAll     = false;

    // ── Modals ────────────────────────────────────────────────────────────
    public bool  $showRoleModal       = false;
    public bool  $showViewModal       = false;
    public bool  $showDeleteModal     = false;
    public bool  $showBulkDeleteModal = false;
    public bool  $isEditing           = false;

    // ── Form Fields ───────────────────────────────────────────────────────
    #[Validate('required|string|min:2|max:64')]
    public string $name        = '';

    #[Validate('nullable|string|max:255')]
    public string $description = '';

    #[Validate('nullable|string|max:7')]
    public string $color       = '#6366f1';

    public array  $selectedPermissions = [];

    // ── Internal ──────────────────────────────────────────────────────────
    public ?int  $editingId   = null;
    public ?int  $deletingId  = null;
    public mixed $viewingRole = null;

    // 🔥 Protected roles – cannot be edited or deleted
    protected array $protectedRoles = ['Super Admin', 'User'];

    // ──────────────────────────────────────────────────────────────────────
    // Authorization
    // ──────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Only Super Admin can manage roles
        if (! $user->can('manage-roles')) {
            abort(403, 'You do not have permission to manage roles.');
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedRoles = $value
            ? $this->getRolesQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Sorting
    // ──────────────────────────────────────────────────────────────────────

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

    // ──────────────────────────────────────────────────────────────────────
    // Modal Helpers
    // ──────────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->authorize('manage-roles');
        $this->resetForm();
        $this->isEditing     = false;
        $this->showRoleModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->authorize('manage-roles');
        $role = Role::with('permissions')->findOrFail($id);

        // 🔥 Prevent editing protected roles
        if ($this->isProtectedRole($role->name)) {
            session()->flash('error', "The '{$role->name}' role is protected and cannot be edited.");
            return;
        }

        $this->editingId            = $id;
        $this->name                 = $role->name;
        $this->description          = $role->description ?? '';
        $this->color                = $role->color ?? '#6366f1';
        $this->selectedPermissions  = $role->permissions->pluck('id')->toArray();
        $this->isEditing            = true;
        $this->showRoleModal        = true;
    }

    public function viewRole(int $id): void
    {
        $this->authorize('manage-roles');
        $this->viewingRole    = Role::with('permissions', 'users')->findOrFail($id);
        $this->showViewModal  = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('manage-roles');
        $role = Role::findOrFail($id);

        if ($this->isProtectedRole($role->name)) {
            session()->flash('error', "The '{$role->name}' role is protected and cannot be deleted.");
            return;
        }

        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function confirmBulkDelete(): void
    {
        $this->authorize('manage-roles');
        // Filter out protected roles from the bulk selection
        $this->selectedRoles = array_filter($this->selectedRoles, function ($id) {
            $role = Role::find($id);
            return $role && !$this->isProtectedRole($role->name);
        });

        if (empty($this->selectedRoles)) {
            session()->flash('error', 'Selected roles are protected and cannot be deleted.');
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function resetFilters(): void
    {
        $this->search        = '';
        $this->sortBy        = 'created_at';
        $this->sortDir       = 'desc';
        $this->selectedRoles = [];
        $this->selectAll     = false;
        $this->resetPage();
    }

    // ──────────────────────────────────────────────────────────────────────
    // CRUD
    // ──────────────────────────────────────────────────────────────────────

    public function saveRole(): void
    {
        $this->authorize('manage-roles');
        $this->validate();

        // 🔥 Extra guard: if editing a protected role, abort
        if ($this->isEditing && $this->isProtectedRole($this->name)) {
            session()->flash('error', 'This role is protected and cannot be updated.');
            $this->showRoleModal = false;
            $this->resetForm();
            return;
        }

        $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();

        if ($this->isEditing) {
            $role = Role::findOrFail($this->editingId);
            $role->update([
                'name'        => $this->name,
                'description' => $this->description ?: null,
                'color'       => $this->color,
            ]);
            $role->syncPermissions($permissions);
            foreach ($role->users as $user) {
                $user->broadcastPermissions();
            }

            // 🔥 Log role update
            ActivityLogger::log('Role updated', [
                'role_id'     => $role->id,
                'name'        => $role->name,
                'description' => $role->description,
                'permissions' => $permissions->pluck('name')->toArray(),
                'updated_by'  => Auth::id(),
            ], 'role');

            session()->flash('success', "Role '{$role->name}' updated successfully.");
        } else {
            $role = Role::create([
                'name'        => $this->name,
                'guard_name'  => 'web',
                'description' => $this->description ?: null,
                'color'       => $this->color,
            ]);
            $role->syncPermissions($permissions);

            // 🔥 Log role creation
            ActivityLogger::log('Role created', [
                'role_id'     => $role->id,
                'name'        => $role->name,
                'description' => $role->description,
                'permissions' => $permissions->pluck('name')->toArray(),
                'created_by'  => Auth::id(),
            ], 'role');

            session()->flash('success', "Role '{$role->name}' created successfully.");
        }

        $this->showRoleModal = false;
        $this->resetForm();
    }

    public function deleteRole(): void
    {
        $this->authorize('manage-roles');
        if ($this->deletingId) {
            $role = Role::find($this->deletingId);
            if ($role && $this->isProtectedRole($role->name)) {
                session()->flash('error', "The '{$role->name}' role is protected and cannot be deleted.");
                $this->showDeleteModal = false;
                return;
            }
        }

        $role = Role::findOrFail($this->deletingId);
        $name = $role->name;

        // 🔥 Log deletion
        ActivityLogger::log('Role deleted', [
            'role_id'     => $role->id,
            'name'        => $role->name,
            'deleted_by'  => Auth::id(),
        ], 'role');

        $role->delete();

        $this->showDeleteModal = false;
        $this->deletingId      = null;
        session()->flash('success', "Role '{$name}' deleted.");
    }

    public function bulkDelete(): void
    {
        $this->authorize('manage-roles');
        // Sanitize: remove any protected roles that might have slipped through
        $ids = array_filter($this->selectedRoles, function ($id) {
            $role = Role::find($id);
            return $role && !$this->isProtectedRole($role->name);
        });

        if (empty($ids)) {
            session()->flash('error', 'No deletable roles selected.');
            $this->showBulkDeleteModal = false;
            return;
        }

        // Log each deletion
        foreach ($ids as $id) {
            $role = Role::find($id);
            if ($role) {
                ActivityLogger::log('Role deleted (bulk)', [
                    'role_id'     => $role->id,
                    'name'        => $role->name,
                    'deleted_by'  => Auth::id(),
                ], 'role');
            }
        }

        Role::whereIn('id', $ids)->delete();

        $count = count($ids);
        $this->selectedRoles = [];
        $this->selectAll     = false;
        $this->showBulkDeleteModal = false;
        session()->flash('success', "{$count} role(s) deleted.");
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    private function isProtectedRole(string $roleName): bool
    {
        return in_array($roleName, $this->protectedRoles);
    }

    private function resetForm(): void
    {
        $this->name                = '';
        $this->description         = '';
        $this->color               = '#6366f1';
        $this->selectedPermissions = [];
        $this->editingId           = null;
        $this->resetValidation();
    }

    private function getRolesQuery()
    {
        return Role::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->orderBy($this->sortBy, $this->sortDir);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────────────────

    public function render()
    {
        $roles = $this->getRolesQuery()
            ->withCount('users', 'permissions')
            ->paginate($this->perPage);

        $allPermissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return Str::before($p->name, ' ') ?: 'general';
        });

        // Check if 'Super Admin' role exists (case-sensitive)
        $superAdminExists = Role::where('name', 'Super Admin')->exists();

        $stats = [
            'total'        => Role::count(),
            'with_users'   => Role::has('users')->count(),
            'permissions'  => Permission::count(),
            'super_admin'  => $superAdminExists ? 1 : 0,
        ];

        return view('livewire.admin.users.role-management', [
            'roles' => $roles,
            'allPermissions' => $allPermissions,
            'stats' => $stats,
            'protectedRoles' => $this->protectedRoles,
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Users;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.users')]
class PermissionManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Table / Filter State ───────────────────────────────────────────────
    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int    $perPage = 10;

    // ── Selected ──────────────────────────────────────────────────────────
    public array $selectedPermissions = [];
    public bool  $selectAll = false;

    // ── Modals ────────────────────────────────────────────────────────────
    public bool $showPermissionModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;
    public bool $isEditing = false;

    // ── Form Fields ───────────────────────────────────────────────────────
    #[Validate('required|string|min:2|max:64|unique:permissions,name')]
    public string $name = '';

    #[Validate('required|string|max:64')]
    public string $guard_name = 'web';

    // ── Internal ──────────────────────────────────────────────────────────
    public ?int  $editingId = null;
    public ?int  $deletingId = null;
    public mixed $viewingPermission = null;

    // ──────────────────────────────────────────────────────────────────────
    // Authorization
    // ──────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Only Super Admin can manage permissions
        if (!$user->can('manage-permissions')) {
            abort(403, 'You do not have permission to manage permissions.');
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
        $this->selectedPermissions = $value
            ? $this->getPermissionsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
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
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    // ──────────────────────────────────────────────────────────────────────
    // Modal Helpers
    // ──────────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->authorize('manage-permissions');
        $this->resetForm();
        $this->isEditing = false;
        $this->showPermissionModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->authorize('manage-permissions');
        $permission = Permission::findOrFail($id);
        $this->editingId = $id;
        $this->name = $permission->name;
        $this->guard_name = $permission->guard_name ?? 'web';
        $this->isEditing = true;
        $this->showPermissionModal = true;
    }

    public function viewPermission(int $id): void
    {
        $this->authorize('manage-permissions');
        $this->viewingPermission = Permission::with('roles')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('manage-permissions');
        $permission = Permission::findOrFail($id);
        // Prevent deletion if the permission is in use
        if ($permission->roles()->count() > 0) {
            session()->flash('error', "Permission '{$permission->name}' is in use by roles and cannot be deleted.");
            return;
        }
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function confirmBulkDelete(): void
    {
        $this->authorize('manage-permissions');
        if (empty($this->selectedPermissions)) return;
        // Filter out permissions that are in use
        $this->selectedPermissions = array_filter($this->selectedPermissions, function ($id) {
            $perm = Permission::find($id);
            return $perm && $perm->roles()->count() === 0;
        });

        if (empty($this->selectedPermissions)) {
            session()->flash('error', 'Selected permissions are in use and cannot be deleted.');
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->selectedPermissions = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    // ──────────────────────────────────────────────────────────────────────
    // CRUD
    // ──────────────────────────────────────────────────────────────────────

    public function savePermission(): void
    {
        $this->authorize('manage-permissions');
        $this->validate();

        $data = [
            'name'       => $this->name,
            'guard_name' => $this->guard_name,
        ];

        if ($this->isEditing) {
            $permission = Permission::findOrFail($this->editingId);
            // Check if name changed and if new name already exists
            if ($permission->name !== $this->name) {
                $exists = Permission::where('name', $this->name)->where('id', '!=', $this->editingId)->exists();
                if ($exists) {
                    $this->addError('name', 'A permission with this name already exists.');
                    return;
                }
            }
            $permission->update($data);
            session()->flash('success', "Permission '{$permission->name}' updated successfully.");
        } else {
            $permission = Permission::create($data);
            session()->flash('success', "Permission '{$permission->name}' created successfully.");
        }

        $this->showPermissionModal = false;
        $this->resetForm();
    }

    public function deletePermission(): void
    {
        $this->authorize('manage-permissions');
        $permission = Permission::findOrFail($this->deletingId);
        // Extra safety check
        if ($permission->roles()->count() > 0) {
            session()->flash('error', 'This permission is in use and cannot be deleted.');
            $this->showDeleteModal = false;
            $this->deletingId = null;
            return;
        }
        $name = $permission->name;
        $permission->delete();

        $this->showDeleteModal = false;
        $this->deletingId = null;

        session()->flash('success', "Permission '{$name}' deleted.");
    }

    public function bulkDelete(): void
    {
        $this->authorize('manage-permissions');
        // Sanitize: only delete permissions not in use
        $ids = array_filter($this->selectedPermissions, function ($id) {
            $perm = Permission::find($id);
            return $perm && $perm->roles()->count() === 0;
        });

        if (empty($ids)) {
            session()->flash('error', 'No deletable permissions selected.');
            $this->showBulkDeleteModal = false;
            return;
        }

        Permission::whereIn('id', $ids)->delete();

        $count = count($ids);
        $this->selectedPermissions = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;

        session()->flash('success', "{$count} permission(s) deleted.");
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->name = '';
        $this->guard_name = 'web';
        $this->editingId = null;
        $this->resetValidation();
    }

    private function getPermissionsQuery()
    {
        return Permission::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->sortBy, $this->sortDir);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────────────────

    public function render()
    {
        $permissions = $this->getPermissionsQuery()
            ->withCount('roles')
            ->paginate($this->perPage);

        $stats = [
            'total'       => Permission::count(),
            'used'        => Permission::has('roles')->count(),
            'unused'      => Permission::doesntHave('roles')->count(),
            'guard_types' => Permission::distinct('guard_name')->count('guard_name'),
        ];

        return view('livewire.admin.users.permission-management', [
            'permissions' => $permissions,
            'stats'       => $stats,
        ]);
    }
}

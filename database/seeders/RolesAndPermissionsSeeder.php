<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissions ────────────────────────────────────────────────
        $permissions = [
            'Create User',
            'View Users',
            'Edit User',
            'Delete User',
            'toggleStatus',
            'Assign Role',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ─── Roles ──────────────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        // Super Admin gets all permissions (optional, but we rely on Gate::before)
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'Create User',
            'View Users',
            'Edit User',
            'Delete User',
            'toggleStatus',
            'Assign Role',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $userRole->syncPermissions([]); // No special permissions by default

        // ─── Assign roles to users (example) ───────────────────────────
        // Optionally assign Super Admin to first user
        if ($firstUser = User::first()) {
            $firstUser->assignRole('Super Admin');
        }
    }
}

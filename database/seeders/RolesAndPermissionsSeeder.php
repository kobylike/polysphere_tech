<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── All permissions ────────────────────────────────────────────────
        $permissions = [
            // User management
            'Create User',
            'View Users',
            'Edit User',
            'Delete User',
            'toggleStatus',
            'Assign Role',

            // HR
            'View HR Dashboard',

            // Blog
            'View Posts',
            'Create Posts',
            'Edit Posts',
            'Delete Posts',
            'Approve Posts',
            'Reject Posts',

            // Category
            'View Categories',
            'Create Categories',
            'Edit Categories',
            'Delete Categories',

            // Project
            'View Projects',
            'Create Projects',
            'Edit Projects',
            'Delete Projects',

            // Service
            'View Services',
            'Create Services',
            'Edit Services',
            'Delete Services',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ─── Roles ────────────────────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all()); // Super Admin gets EVERYTHING

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'Create User',
            'View Users',
            'Edit User',
            'Delete User',
            'toggleStatus',
            'Assign Role',
            'View HR Dashboard',
            'View Posts',
            'Create Posts',
            'Edit Posts',
            'Delete Posts',
            'Approve Posts',
            'Reject Posts',
            'View Categories',
            'Create Categories',
            'Edit Categories',
            'Delete Categories',
            'View Projects',
            'Create Projects',
            'Edit Projects',
            'Delete Projects',
            'View Services',
            'Create Services',
            'Edit Services',
            'Delete Services',
        ]);

        // Agent is removed – you said you won't use it
        // $agent = Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
        // $agent->syncPermissions(['View Users']);

        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $userRole->syncPermissions([]); // No permissions
    }
}

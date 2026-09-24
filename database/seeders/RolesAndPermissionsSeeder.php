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

            // ─── Departments ────────────────────────────────────────────
            'View Departments',
            'Create Department',
            'Edit Department',
            'Delete Department',

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

            // Notifications
            'Send Notifications',

            // Activity Logs
            'View Activity Logs',

            // Newsletter Subscribers
            'View Newsletter Subscribers',
            'Edit Newsletter Subscribers',
            'Delete Newsletter Subscribers',

            // ─── Vacancies (Careers page) ───────────────────────────────
            'View Vacancies',
            'Create Vacancy',
            'Edit Vacancy',
            'Delete Vacancy',

            // ─── Applications (candidates applying to vacancies) ────────
            'View Applications',
            'Edit Applications',
            'Delete Applications',

            // ─── Chat Leads (visitors who shared details via the chat widget)
            'View Chat Leads',
            'Edit Chat Leads',
            'Delete Chat Leads',
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

            // ─── Department permissions ──────────────────────────────────
            'View Departments',
            'Create Department',
            'Edit Department',
            'Delete Department',

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
            'Send Notifications',
            'View Activity Logs',
            'View Newsletter Subscribers',
            'Edit Newsletter Subscribers',
            'Delete Newsletter Subscribers',

            // ─── Vacancy permissions ─────────────────────────────────────
            'View Vacancies',
            'Create Vacancy',
            'Edit Vacancy',
            'Delete Vacancy',

            // ─── Application permissions ─────────────────────────────────
            'View Applications',
            'Edit Applications',
            'Delete Applications',

            // ─── Chat Lead permissions ───────────────────────────────────
            'View Chat Leads',
            'Edit Chat Leads',
            'Delete Chat Leads',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $userRole->syncPermissions([]); // No permissions
    }
}

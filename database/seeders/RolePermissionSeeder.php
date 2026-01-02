<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'مدير')->first();
        $managerRole = Role::where('name', 'إداري')->first();
        $editorRole = Role::where('name', 'محرر')->first();

        // Get all permissions
        $allPermissions = Permission::all();

        // Super Admin gets all permissions (also handled in code, but good to have in DB)
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
        }

        // Admin gets all permissions
        if ($adminRole) {
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }

        // Manager gets most permissions except user management
        if ($managerRole) {
            $managerPermissions = Permission::whereNotIn('slug', ['manage_users', 'manage_roles'])->get();
            $managerRole->permissions()->sync($managerPermissions->pluck('id'));
        }

        // Editor gets limited permissions
        if ($editorRole) {
            $editorPermissions = Permission::whereIn('slug', [
                'view_students',
                'view_subscriptions',
                'add_subscription',
                'edit_subscription',
                'activate_subscriptions',
                'view_exams',
                'add_exam',
                'edit_exam',
                'add_questions',
            ])->get();
            $editorRole->permissions()->sync($editorPermissions->pluck('id'));
        }
    }
}


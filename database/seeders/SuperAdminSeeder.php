<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'المطور - صلاحيات كاملة بما فيها إدارة الإعدادات العامة']
        );

        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin123'), // ⚠️ يجب تغييرها فوراً!
                'role_id' => $superAdminRole->id,
            ]
        );

        // Update role if user exists
        if ($superAdmin->wasRecentlyCreated === false) {
            $superAdmin->update(['role_id' => $superAdminRole->id]);
        }

        $this->command->info('Super Admin created successfully!');
        $this->command->warn('Email: superadmin@example.com');
        $this->command->warn('Password: superadmin123');
        $this->command->warn('⚠️  Please change the password immediately!');
    }
}




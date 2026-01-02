<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'المطور - صلاحيات كاملة بما فيها إدارة الإعدادات العامة',
            ],
            [
                'name' => 'مدير',
                'description' => 'مدير النظام - صلاحيات كاملة',
            ],
            [
                'name' => 'إداري',
                'description' => 'إداري - صلاحيات إدارية محدودة',
            ],
            [
                'name' => 'محرر',
                'description' => 'محرر المحتوى - صلاحيات تحرير المحتوى',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}


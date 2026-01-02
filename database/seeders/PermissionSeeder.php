<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Students Permissions
            ['name' => 'إضافة طالب', 'slug' => 'add_student', 'description' => 'إمكانية إضافة طالب جديد', 'group' => 'students'],
            ['name' => 'تعديل طالب', 'slug' => 'edit_student', 'description' => 'إمكانية تعديل بيانات الطالب', 'group' => 'students'],
            ['name' => 'حذف طالب', 'slug' => 'delete_student', 'description' => 'إمكانية حذف طالب', 'group' => 'students'],
            ['name' => 'عرض الطلاب', 'slug' => 'view_students', 'description' => 'إمكانية عرض قائمة الطلاب', 'group' => 'students'],

            // Subscriptions Permissions
            ['name' => 'إضافة اشتراك', 'slug' => 'add_subscription', 'description' => 'إمكانية إضافة اشتراك طالب', 'group' => 'subscriptions'],
            ['name' => 'تعديل اشتراك', 'slug' => 'edit_subscription', 'description' => 'إمكانية تعديل اشتراك طالب', 'group' => 'subscriptions'],
            ['name' => 'حذف اشتراك', 'slug' => 'delete_subscription', 'description' => 'إمكانية حذف اشتراك طالب', 'group' => 'subscriptions'],
            ['name' => 'عرض الاشتراكات', 'slug' => 'view_subscriptions', 'description' => 'إمكانية عرض قائمة الاشتراكات', 'group' => 'subscriptions'],
            ['name' => 'تفعيل اشتراكات', 'slug' => 'activate_subscriptions', 'description' => 'إمكانية تفعيل اشتراكات الطلاب', 'group' => 'subscriptions'],

            // Exams Permissions
            ['name' => 'إضافة امتحان', 'slug' => 'add_exam', 'description' => 'إمكانية إضافة امتحان جديد', 'group' => 'exams'],
            ['name' => 'تعديل امتحان', 'slug' => 'edit_exam', 'description' => 'إمكانية تعديل امتحان', 'group' => 'exams'],
            ['name' => 'حذف امتحان', 'slug' => 'delete_exam', 'description' => 'إمكانية حذف امتحان', 'group' => 'exams'],
            ['name' => 'عرض الامتحانات', 'slug' => 'view_exams', 'description' => 'إمكانية عرض قائمة الامتحانات', 'group' => 'exams'],
            ['name' => 'إضافة أسئلة', 'slug' => 'add_questions', 'description' => 'إمكانية إضافة أسئلة للامتحان', 'group' => 'exams'],

            // Months Permissions
            ['name' => 'إضافة شهر', 'slug' => 'add_month', 'description' => 'إمكانية إضافة شهر جديد', 'group' => 'months'],
            ['name' => 'تعديل شهر', 'slug' => 'edit_month', 'description' => 'إمكانية تعديل شهر', 'group' => 'months'],
            ['name' => 'حذف شهر', 'slug' => 'delete_month', 'description' => 'إمكانية حذف شهر', 'group' => 'months'],

            // Lectures Permissions
            ['name' => 'إضافة محاضرة', 'slug' => 'add_lecture', 'description' => 'إمكانية إضافة محاضرة جديدة', 'group' => 'lectures'],
            ['name' => 'تعديل محاضرة', 'slug' => 'edit_lecture', 'description' => 'إمكانية تعديل محاضرة', 'group' => 'lectures'],
            ['name' => 'حذف محاضرة', 'slug' => 'delete_lecture', 'description' => 'إمكانية حذف محاضرة', 'group' => 'lectures'],

            // PDFs Permissions
            ['name' => 'إضافة مذكرة', 'slug' => 'add_pdf', 'description' => 'إمكانية إضافة مذكرة جديدة', 'group' => 'pdfs'],
            ['name' => 'تعديل مذكرة', 'slug' => 'edit_pdf', 'description' => 'إمكانية تعديل مذكرة', 'group' => 'pdfs'],
            ['name' => 'حذف مذكرة', 'slug' => 'delete_pdf', 'description' => 'إمكانية حذف مذكرة', 'group' => 'pdfs'],

            // Users & Roles Permissions
            ['name' => 'إدارة المستخدمين', 'slug' => 'manage_users', 'description' => 'إمكانية إدارة المستخدمين', 'group' => 'users'],
            ['name' => 'إدارة الأدوار', 'slug' => 'manage_roles', 'description' => 'إمكانية إدارة الأدوار والصلاحيات', 'group' => 'users'],

            // Public Exams Permissions
            ['name' => 'عرض نتائج الامتحانات العامة', 'slug' => 'view_public_exam_results', 'description' => 'إمكانية عرض نتائج الامتحانات العامة', 'group' => 'public_exams'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}


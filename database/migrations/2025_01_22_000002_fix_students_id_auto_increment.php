<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إصلاح مشكلة auto-increment في جدول students
     * يجب حذف Foreign Keys أولاً ثم إعادة إنشائها
     */
    public function up(): void
    {
        // البحث عن جميع Foreign Keys التي تشير إلى students.id تلقائياً
        $foreignKeys = DB::select("
            SELECT 
                TABLE_NAME,
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME = 'students'
            AND REFERENCED_COLUMN_NAME = 'id'
        ");
        
        // حفظ معلومات Foreign Keys لإعادة إنشائها لاحقاً
        $fkInfo = [];
        
        // حذف جميع Foreign Keys
        foreach ($foreignKeys as $fk) {
            try {
                $tableName = $fk->TABLE_NAME;
                $constraintName = $fk->CONSTRAINT_NAME;
                
                // حفظ المعلومات لإعادة الإنشاء
                $fkInfo[] = [
                    'table' => $tableName,
                    'constraint' => $constraintName,
                    'column' => $fk->COLUMN_NAME,
                ];
                
                // حذف Foreign Key
                DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraintName}`");
            } catch (\Exception $e) {
                \Log::warning("Could not drop foreign key {$fk->CONSTRAINT_NAME}: " . $e->getMessage());
            }
        }
        
        // إصلاح أي قيم ID متداخلة أو أقل من 1000
        $this->fixStudentIds();
        
        // التأكد من أن ID هو auto-increment
        DB::statement('ALTER TABLE students MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        
        // التأكد من أن starting value هو 1000
        $maxId = DB::table('students')->max('id') ?? 999;
        if ($maxId < 1000) {
            DB::statement('ALTER TABLE students AUTO_INCREMENT = 1000');
        } else {
            DB::statement('ALTER TABLE students AUTO_INCREMENT = ' . ($maxId + 1));
        }
        
        // إعادة إنشاء Foreign Keys
        $this->recreateForeignKeys($fkInfo);
    }
    
    /**
     * إصلاح قيم ID المتداخلة أو الأقل من 1000
     */
    private function fixStudentIds()
    {
        // الحصول على جميع الطلاب
        $students = DB::table('students')->orderBy('id')->get();
        
        // العثور على أول ID متاح >= 1000
        $nextId = 1000;
        $usedIds = [];
        
        // جمع جميع IDs المستخدمة >= 1000
        foreach ($students as $student) {
            if ($student->id >= 1000) {
                $usedIds[] = $student->id;
            }
        }
        
        // إعادة ترقيم الطلاب الذين لديهم ID < 1000
        foreach ($students as $student) {
            if ($student->id < 1000) {
                // العثور على ID متاح
                while (in_array($nextId, $usedIds)) {
                    $nextId++;
                }
                
                // تحديث ID في جميع الجداول المرتبطة
                $this->updateStudentIdInAllTables($student->id, $nextId);
                
                $usedIds[] = $nextId;
                $nextId++;
            }
        }
    }
    
    /**
     * تحديث student_id في جميع الجداول المرتبطة
     */
    private function updateStudentIdInAllTables($oldId, $newId)
    {
        // قائمة الجداول التي تحتوي على student_id
        $tables = [
            'exam_answers',
            'exam_results',
            'student_subscriptions',
            'payments',
        ];
        
        // التحقق من وجود جدول lecture_views
        if (Schema::hasTable('lecture_views')) {
            $tables[] = 'lecture_views';
        }
        
        // تحديث ID في كل جدول
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::table($table)
                        ->where('student_id', $oldId)
                        ->update(['student_id' => $newId]);
                } catch (\Exception $e) {
                    \Log::warning("Could not update student_id in {$table}: " . $e->getMessage());
                }
            }
        }
        
        // تحديث ID في جدول students نفسه
        DB::table('students')
            ->where('id', $oldId)
            ->update(['id' => $newId]);
    }
    
    /**
     * إعادة إنشاء Foreign Keys
     */
    private function recreateForeignKeys($fkInfo)
    {
        foreach ($fkInfo as $fk) {
            try {
                // التحقق من عدم وجود Foreign Key بالفعل
                $fkExists = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND CONSTRAINT_NAME = ?
                ", [$fk['table'], $fk['constraint']]);
                
                if (empty($fkExists)) {
                    DB::statement("
                        ALTER TABLE `{$fk['table']}` 
                        ADD CONSTRAINT `{$fk['constraint']}` 
                        FOREIGN KEY (`{$fk['column']}`) 
                        REFERENCES students(id) 
                        ON DELETE CASCADE 
                        ON UPDATE CASCADE
                    ");
                }
            } catch (\Exception $e) {
                \Log::warning("Could not recreate foreign key {$fk['constraint']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا حاجة لعكس التغيير
    }
};


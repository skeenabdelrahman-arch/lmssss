<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ExamFixController extends Controller
{
    public function index()
    {
        return view('exam-fix');
    }

    public function fixConstraints()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // حذف المفاتيح القديمة
            try {
                DB::statement('ALTER TABLE exam_answers DROP FOREIGN KEY exam_answers_student_id_foreign');
            } catch (\Exception $e) {}

            try {
                DB::statement('ALTER TABLE exam_answers DROP FOREIGN KEY exam_answers_exam_id_foreign');
            } catch (\Exception $e) {}

            try {
                DB::statement('ALTER TABLE exam_answers DROP FOREIGN KEY exam_answers_question_id_foreign');
            } catch (\Exception $e) {}

            // إضافة المفاتيح الجديدة
            DB::statement('
                ALTER TABLE exam_answers
                ADD CONSTRAINT exam_answers_student_id_foreign
                FOREIGN KEY (student_id) REFERENCES students(id) 
                ON DELETE RESTRICT ON UPDATE CASCADE
            ');

            DB::statement('
                ALTER TABLE exam_answers
                ADD CONSTRAINT exam_answers_exam_id_foreign
                FOREIGN KEY (exam_id) REFERENCES exam_names(id) 
                ON DELETE RESTRICT ON UPDATE CASCADE
            ');

            DB::statement('
                ALTER TABLE exam_answers
                ADD CONSTRAINT exam_answers_question_id_foreign
                FOREIGN KEY (question_id) REFERENCES exam_questions(id) 
                ON DELETE RESTRICT ON UPDATE CASCADE
            ');

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return response()->json([
                'success' => true,
                'message' => '✅ تم إصلاح القيود الخارجية بنجاح!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkConstraints()
    {
        try {
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_NAME IN ('exam_answers', 'exam_questions', 'exam_results')
                AND REFERENCED_TABLE_NAME IS NOT NULL
                ORDER BY TABLE_NAME
            ");

            return response()->json([
                'success' => true,
                'message' => 'تم الحصول على معلومات القيود',
                'data' => $constraints
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearAnswers()
    {
        try {
            $deleted = DB::table('exam_answers')->delete();

            return response()->json([
                'success' => true,
                'message' => '🗑️ تم حذف ' . $deleted . ' إجابة قديمة',
                'data' => ['deleted_count' => $deleted]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}

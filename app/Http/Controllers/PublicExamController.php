<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamName;
use Illuminate\Support\Facades\DB;

class PublicExamController extends Controller
{
    // 1️⃣ صفحة قائمة الامتحانات العامة
    public function index() {
        $exams = ExamName::with('questions')->where('public_access', 1)->get();
        return view('front.public_exams', compact('exams'));
    }

    // 2️⃣ صفحة كتابة الاسم قبل الامتحان
    public function take($exam_id)
    {
        $exam = ExamName::with('questions')->findOrFail($exam_id);
        
        // التحقق من أن الامتحان عام
        if ($exam->public_access != 1) {
            abort(403, 'هذا الامتحان غير متاح للعامة');
        }
        
        // التحقق من أن الامتحان مفعل
        if ($exam->status != 1) {
            abort(403, 'هذا الامتحان غير مفعل حالياً');
        }
        
        $student_name = session('public_exam_student_name');
        
        // تمرير البيانات بنفس طريقة الامتحان العادي
        $exam_name = $exam;
        $exam_questions = $exam->questions;

        return view('front.public_exam_take', compact('exam', 'exam_name', 'exam_questions', 'student_name'));
    }

    public function start(Request $request, $exam_id)
    {
        $exam = ExamName::findOrFail($exam_id);
        
        // التحقق من أن الامتحان عام
        if ($exam->public_access != 1) {
            return redirect()->back()->with('error', 'هذا الامتحان غير متاح للعامة');
        }
        
        // التحقق من أن الامتحان مفعل
        if ($exam->status != 1) {
            return redirect()->back()->with('error', 'هذا الامتحان غير مفعل حالياً');
        }
        
        $request->validate([
            'student_name' => 'required|string|max:255|min:2'
        ]);

        session([
            'public_exam_student_name' => $request->student_name,
            'public_exam_start_time' => now(),
        ]);

        return redirect()->route('publicExam.take', $exam_id);
    }



    // 4️⃣ إرسال الإجابات
    public function submit(Request $request, $exam_id){
        $exam = ExamName::with('questions')->findOrFail($exam_id);
        $student_name = session('public_exam_student_name');

        if(!$student_name){
            return redirect()->route('publicExam.take', $exam_id)
                ->with('error','حدث خطأ. يرجى إدخال اسمك مرة أخرى.');
        }

        // التحقق من أن الامتحان عام
        if ($exam->public_access != 1) {
            return redirect()->back()->with('error', 'هذا الامتحان غير متاح للعامة');
        }

        // حساب الدرجة
        $total_degree = 0;
        $student_degree = 0;
        
        foreach($exam->questions as $question){
            $answer = $request->input('answer.'.$question->id, '');
            
            // حساب الدرجة الكلية
            $total_degree += (float)($question->Q_degree ?? 0);
            
            // التحقق من الإجابة الصحيحة (يدعم صح/غلط واختيار من متعدد)
            if ($answer && $answer == $question->correct_answer) {
                $student_degree += (float)($question->Q_degree ?? 0);
            }
            
            // حفظ الإجابة
            DB::table('exam_results_public')->insert([
                'exam_id' => $exam_id,
                'student_name' => $student_name,
                'question_id' => $question->id,
                'answer' => $answer,
                'is_correct' => ($answer == $question->correct_answer) ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // إنشاء كود فريد للنتيجة
        do {
            $result_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
        } while (DB::table('exam_results_public_summary')->where('result_code', $result_code)->exists());

        // حفظ النتيجة الإجمالية
        $result_id = DB::table('exam_results_public_summary')->insertGetId([
            'exam_id' => $exam_id,
            'student_name' => $student_name,
            'result_code' => $result_code,
            'total_degree' => $total_degree,
            'student_degree' => $student_degree,
            'percentage' => $total_degree > 0 ? round(($student_degree / $total_degree) * 100, 2) : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->forget(['public_exam_student_name', 'public_exam_start_time']);

        // التحقق من إخفاء الدرجة
        $exam = ExamName::findOrFail($exam_id);
        
        if ($exam->hide_public_result == 1) {
            // إذا كان إخفاء الدرجة مفعل، إعادة توجيه لصفحة شكر مع كود النتيجة
            return redirect()->route('publicExam.searchResult')->with([
                'success' => 'تم إرسال الإجابات بنجاح! سيتم مراجعة إجاباتك قريباً.',
                'result_code' => $result_code,
                'result_id' => $result_id,
            ]);
        }

        // إعادة توجيه مباشر لصفحة النتيجة التفصيلية
        return redirect()->route('publicExam.showResult', $result_id)->with([
            'success' => 'تم إرسال الإجابات بنجاح!',
            'result_code' => $result_code,
        ]);
    }

    // عرض نتائج الامتحانات العامة (للأدمن)
    public function results($exam_id = null)
    {
        if ($exam_id) {
            $results = DB::table('exam_results_public_summary')
                ->where('exam_id', $exam_id)
                ->orderBy('created_at', 'desc')
                ->get();
            $exam = ExamName::findOrFail($exam_id);
            return view('back.exam.public_exam_results', compact('results', 'exam'));
        } else {
            $exams = ExamName::where('public_access', 1)->get();
            $allResults = DB::table('exam_results_public_summary')
                ->join('exam_names', 'exam_results_public_summary.exam_id', '=', 'exam_names.id')
                ->select('exam_results_public_summary.*', 'exam_names.exam_title')
                ->orderBy('exam_results_public_summary.created_at', 'desc')
                ->get();
            return view('back.exam.public_exam_results_list', compact('exams', 'allResults'));
        }
    }

    // صفحة البحث عن النتيجة
    public function searchResult()
    {
        return view('front.public_exam_search_result');
    }

    // البحث عن النتيجة
    public function findResult(Request $request)
    {
        $request->validate([
            'result_code' => 'required|string|size:12|regex:/^[A-Z0-9]{12}$/'
        ], [
            'result_code.required' => 'يرجى إدخال كود النتيجة',
            'result_code.size' => 'الكود يجب أن يكون 12 حرف',
            'result_code.regex' => 'الكود يجب أن يحتوي على أحرف إنجليزية وأرقام فقط'
        ]);

        $result = DB::table('exam_results_public_summary')
            ->where('result_code', strtoupper($request->result_code))
            ->first();

        if ($result) {
            return redirect()->route('publicExam.showResult', $result->id);
        }

        return redirect()->back()->with('error', 'الكود غير صحيح. يرجى التحقق من الكود وإعادة المحاولة');
    }

    // عرض النتيجة بالتفصيل
    public function showResult($id)
    {
        $result = DB::table('exam_results_public_summary')
            ->join('exam_names', 'exam_results_public_summary.exam_id', '=', 'exam_names.id')
            ->select('exam_results_public_summary.*', 'exam_names.exam_title', 'exam_names.exam_description', 'exam_names.hide_public_result')
            ->where('exam_results_public_summary.id', $id)
            ->first();

        if (!$result) {
            abort(404, 'النتيجة غير موجودة');
        }

        // جلب الإجابات التفصيلية
        $answers = DB::table('exam_results_public')
            ->join('exam_questions', 'exam_results_public.question_id', '=', 'exam_questions.id')
            ->select('exam_results_public.*', 'exam_questions.question_title', 'exam_questions.correct_answer', 'exam_questions.Q_degree', 'exam_questions.ch_1', 'exam_questions.ch_2', 'exam_questions.ch_3', 'exam_questions.ch_4', 'exam_questions.img', 'exam_questions.question_type')
            ->where('exam_results_public.exam_id', $result->exam_id)
            ->where('exam_results_public.student_name', $result->student_name)
            ->orderBy('exam_questions.id')
            ->get();

        return view('front.public_exam_result_details', compact('result', 'answers'));
    }
}

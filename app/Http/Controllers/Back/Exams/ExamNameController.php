<?php

namespace App\Http\Controllers\Back\Exams;

use App\Http\Controllers\Controller;
use App\Models\ExamName;
use App\Models\ExamResult;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamNameController extends Controller
{

    public function index()
    {
        $exam_names = ExamName::all();
        return view('back.exam.exam_name',compact('exam_names'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('back.exam.create');
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    try {
        $save = new ExamName();
        $save->exam_title = $request->exam_title;
        $save->exam_description = $request->exam_description;
        $save->exam_time = $request->exam_time;
        $save->grade = $request->grade;
        $save->month_id = $request->Month;

        // حالة التفعيل
        $save->status = isset($request->status) ? 1 : 0;

        // حالة الامتحان العام
        $save->public_access = isset($request->public_access) ? 1 : 0;
        
        // إخفاء نتيجة الامتحان العام
        $save->hide_public_result = isset($request->hide_public_result) ? 1 : 0;

        // جدولة الامتحان
        $save->opens_at = $request->opens_at ? \Carbon\Carbon::parse($request->opens_at) : null;
        $save->closes_at = $request->closes_at ? \Carbon\Carbon::parse($request->closes_at) : null;
        $save->auto_show_results = isset($request->auto_show_results) ? 1 : 0;
        $save->randomize_questions = isset($request->randomize_questions) ? 1 : 0;

        $save->save();
        
        // إرسال إشعار للطلاب المشتركين إذا كان الامتحان مفعل
        if ($save->status == 1) {
            try {
                \App\Services\NotificationService::notifyNewExam($save);
            } catch (\Exception $e) {
                \Log::error('Error sending exam notification', ['exam_id' => $save->id, 'error' => $e->getMessage()]);
            }
        }
        
        return redirect()->route('exam_name.index')->with('success','تم اضافة الامتحان بنجاح');
    } catch (\Exception $e) {
        return redirect()->back()->with(['error' => $e->getMessage()]);
    }
}


    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $exam_name = ExamName::findOrFail($id);
        return view('back.exam.edit', compact('exam_name'));
    }

    public function update(Request $request, $id)
{
    try {
        $save = ExamName::findOrFail($id);
        $save->exam_title = $request->exam_title;
        $save->exam_description = $request->exam_description;
        $save->exam_time = $request->exam_time;
        $save->grade = $request->grade;
        $save->month_id = $request->Month;

        // حالة التفعيل
        $save->status = isset($request->status) ? 1 : 0;

        // حالة الامتحان العام
        $save->public_access = isset($request->public_access) ? 1 : 0;
        
        // إخفاء نتيجة الامتحان العام
        $save->hide_public_result = isset($request->hide_public_result) ? 1 : 0;

        // جدولة الامتحان
        $save->opens_at = $request->opens_at ? \Carbon\Carbon::parse($request->opens_at) : null;
        $save->closes_at = $request->closes_at ? \Carbon\Carbon::parse($request->closes_at) : null;
        $save->auto_show_results = isset($request->auto_show_results) ? 1 : 0;
        $save->randomize_questions = isset($request->randomize_questions) ? 1 : 0;

        $save->save();
        return redirect()->route('exam_name.index')->with('success','تم تعديل الامتحان بنجاح');
    } catch (\Exception $e) {
        return redirect()->back()->with(['error' => $e->getMessage()]);
    }
}


    public function delete($id)
    {
        ExamName::findorfail($id)->delete();
        return redirect()->back()->with('success','تم حذف الامتحان بنجاح');
    }
    public function deleteAllExams(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_EXAMS') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }
        
        // استخدام soft delete بدلاً من truncate
        $count = ExamName::query()->delete();
        return redirect()->back()->with('success', "تم حذف {$count} امتحان (يمكن استعادتهم من البيانات المحذوفة)");
    }

    public function add_question($exam_id)
{
    $exam_questions = ExamQuestion::where('exam_id', $exam_id)->inRandomOrder()->get();
    return view('back.exam.add_question', compact('exam_id', 'exam_questions'));
}

    public function create_question($exam_id)
{
    return view('back.exam.create_question', compact('exam_id'));
}

    public function edit_question($exam_id, $question_id)
{
    $exam_question = ExamQuestion::findOrFail($question_id);
    return view('back.exam.edit_question', compact('exam_id', 'exam_question'));
}


    public function insert_question(Request $request, $exam_id)
    {
        $save = new ExamQuestion();
        $save->exam_id = $exam_id;
        $save->question_title = $request->question_title;
        
        // حفظ نوع السؤال
        $save->question_type = $request->question_type ?? 'multiple_choice';
        
        // حفظ نموذج السؤال
        $save->model_name = $request->model_name ?? 'A';
        
        if ($save->question_type === 'true_false') {
            // لأسئلة صح/غلط
            $save->correct_answer = $request->correct_answer_true_false ?? 'صح';
            // لا نحتاج ch_1, ch_2, ch_3, ch_4 لأسئلة صح/غلط
            $save->ch_1 = null;
            $save->ch_2 = null;
            $save->ch_3 = null;
            $save->ch_4 = null;
            // clear multi-correct and bonus flag for true/false
            $save->correct_answers = null;
            $save->is_bonus = 0;
        } else {
            // لأسئلة الاختيار من متعدد
            $save->ch_1 = $request->ch_1;
            $save->ch_2 = $request->ch_2;
            $save->ch_3 = $request->ch_3;
            $save->ch_4 = $request->ch_4;
            
            // حفظ صور الإجابات
            foreach(['ch_1', 'ch_2', 'ch_3', 'ch_4'] as $ch) {
                if (!empty($request->file($ch . '_img'))) {
                    $file = $request->file($ch . '_img');
                    $ext = $file->getClientOriginalExtension();
                    $filename = strtolower($ch . '_' . time() . Str::random(10) . '.' . $ext);
                    $file->move('upload_files/', $filename);
                    $save->{$ch . '_img'} = $filename;
                }
            }
            
            // حفظ الإجابات الصحيحة المتعددة (نحفظ نصوص الإجابات)
            $selected = $request->input('correct_answers', []);
            if (!empty($selected) && is_array($selected)) {
                $correct_texts = [];
                foreach ($selected as $sel) {
                    // $sel يمثل ch_1..ch_4
                    if ($request->has($sel) && !empty($request->input($sel))) {
                        $correct_texts[] = $request->input($sel);
                    }
                }
                $save->correct_answers = $correct_texts;
                // backward compatibility: set single correct_answer to first selected text
                $save->correct_answer = $correct_texts[0] ?? null;
            } else {
                // fallback to single-field behaviour
                if ($request->correct_answer) {
                    $save->correct_answer = $request->{$request->correct_answer} ?? $request->correct_answer;
                }
                $save->correct_answers = null;
            }

            // is bonus flag
            $save->is_bonus = $request->has('is_bonus') ? 1 : 0;
        }
        
        // صورة السؤال (إن وجدت)
        if (!empty($request->file('img'))) {
            $file = $request->file('img');
            $ext = $file->getClientOriginalExtension();
            $filename = strtolower('question_' . time() . Str::random(10) . '.' . $ext);
            $file->move('upload_files/', $filename);
            $save->img = $filename;
        }
        
        $save->Q_degree = $request->Q_degree;
        $save->save();
        return redirect()->route('exam_name.add_question', $exam_id)->with('success','تم اضافة سؤال بنجاح');
    }

    public function update_question(Request $request, $Q_id)
    {
        $save = ExamQuestion::findorfail($Q_id);
        
        $save->question_title = $request->question_title;
        
        // حفظ نوع السؤال
        $save->question_type = $request->question_type ?? 'multiple_choice';
        
        if ($save->question_type === 'true_false') {
            // لأسئلة صح/غلط
            $save->correct_answer = $request->correct_answer_true_false ?? 'صح';
            // لا نحتاج ch_1, ch_2, ch_3, ch_4 لأسئلة صح/غلط
            $save->ch_1 = null;
            $save->ch_2 = null;
            $save->ch_3 = null;
            $save->ch_4 = null;
            // حذف صور الإجابات القديمة
            foreach(['ch_1', 'ch_2', 'ch_3', 'ch_4'] as $ch) {
                if ($save->{$ch . '_img'} && file_exists('upload_files/' . $save->{$ch . '_img'})) {
                    @unlink('upload_files/' . $save->{$ch . '_img'});
                }
                $save->{$ch . '_img'} = null;
            }
        } else {
            // لأسئلة الاختيار من متعدد
            $save->ch_1 = $request->ch_1;
            $save->ch_2 = $request->ch_2;
            $save->ch_3 = $request->ch_3;
            $save->ch_4 = $request->ch_4;
            
            // تحديث صور الإجابات (إذا تم رفع صورة جديدة)
            foreach(['ch_1', 'ch_2', 'ch_3', 'ch_4'] as $ch) {
                if (!empty($request->file($ch . '_img'))) {
                    // حذف الصورة القديمة إن وجدت
                    if ($save->{$ch . '_img'} && file_exists('upload_files/' . $save->{$ch . '_img'})) {
                        @unlink('upload_files/' . $save->{$ch . '_img'});
                    }
                    
                    $file = $request->file($ch . '_img');
                    $ext = $file->getClientOriginalExtension();
                    $filename = strtolower($ch . '_' . time() . Str::random(10) . '.' . $ext);
                    $file->move('upload_files/', $filename);
                    $save->{$ch . '_img'} = $filename;
                }
            }
            
            // حفظ الإجابات الصحيحة المتعددة (نحفظ نصوص الإجابات)
            $selected = $request->input('correct_answers', []);
            if (!empty($selected) && is_array($selected)) {
                $correct_texts = [];
                foreach ($selected as $sel) {
                    // $sel يمثل ch_1..ch_4
                    if ($request->has($sel) && !empty($request->input($sel))) {
                        $correct_texts[] = $request->input($sel);
                    }
                }
                $save->correct_answers = $correct_texts;
                // backward compatibility: set single correct_answer to first selected text
                $save->correct_answer = $correct_texts[0] ?? null;
            } else {
                // fallback to single-field behaviour
                if ($request->correct_answer) {
                    $save->correct_answer = $request->{$request->correct_answer} ?? $request->correct_answer;
                }
                $save->correct_answers = null;
            }

            // is bonus flag
            $save->is_bonus = $request->has('is_bonus') ? 1 : 0;
        }
        
        // صورة السؤال (إن وجدت)
        if (!empty($request->file('img'))) {
            // حذف الصورة القديمة إن وجدت
            if ($save->img && file_exists('upload_files/' . $save->img)) {
                @unlink('upload_files/' . $save->img);
            }
            
            $file = $request->file('img');
            $ext = $file->getClientOriginalExtension();
            $filename = strtolower('question_' . time() . Str::random(10) . '.' . $ext);
            $file->move('upload_files/', $filename);
            $save->img = $filename;
        }
        
        $save->Q_degree = $request->Q_degree;
        $save->save();
        
        // تحديث درجات جميع الطلاب عند تعديل الإجابة الصحيحة
        $this->updateAllStudentScores($save->exam_id);
        
        $exam_id = $save->exam_id;
        return redirect()->route('exam_name.add_question', $exam_id)->with('success','تم تعديل السؤال بنجاح');
    }

    public function delete_question($id)
    {
        ExamQuestion::findorfail($id)->delete();
        return redirect()->back()->with('success','تم حذف السؤال بنجاح');
    }

    public function delete_all_question(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_QUESTIONS') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }
        
        // استخدام soft delete بدلاً من truncate
        $count = ExamQuestion::query()->delete();
        return redirect()->back()->with('success', "تم حذف {$count} سؤال (يمكن استعادتهم من البيانات المحذوفة)");
    }
    
    public function showAllDegress()
    {
        ExamResult::where('show_degree', 0)->update(['show_degree' => 1]);
        return redirect()->back()->with('success','تم  اظهار الدرجة للكل  بنجاح');
    }
    
    /**
     * تحديث درجات جميع الطلاب عند تعديل إجابة سؤال
     */
    private function updateAllStudentScores($exam_id)
    {
        $questions = ExamQuestion::where('exam_id', $exam_id)->get();
        $results = ExamResult::where('exam_id', $exam_id)->get();
        
        foreach ($results as $result) {
            $studentDegree = 0;
            
            foreach ($questions as $question) {
                // الحصول على إجابة الطالب
                $studentAnswer = \App\Models\ExamAnswer::where('exam_id', $exam_id)
                    ->where('student_id', $result->student_id)
                    ->where('question_id', $question->id)
                    ->first();
                
                // مقارنة الإجابات (يدعم صح/غلط واختيار من متعدد)
                if ($question->is_bonus) {
                    // Bonus question: everyone gets full marks for this question
                    $studentDegree += (float)$question->Q_degree;
                    continue;
                }

                if ($studentAnswer && $studentAnswer->student_answer) {
                    // Multiple-correct support: if correct_answers array exists, compare against texts
                    if (!empty($question->correct_answers) && is_array($question->correct_answers)) {
                        if (in_array($studentAnswer->student_answer, $question->correct_answers)) {
                            $studentDegree += (float)$question->Q_degree;
                        }
                    } else {
                        // fallback to single answer
                        if ($studentAnswer->student_answer == $question->correct_answer) {
                            $studentDegree += (float)$question->Q_degree;
                        }
                    }
                }
            }
            
            // تحديث درجة الطالب
            $result->degree = $studentDegree;
            $result->save();
        }
    }
    
    /**
     * تصدير أسئلة الامتحان كـ PDF مع الإجابات
     */
    public function exportQuestionsPDF($exam_id)
    {
        $exam = ExamName::findOrFail($exam_id);
        $questions = ExamQuestion::where('exam_id', $exam_id)
            ->orderBy('id', 'asc')
            ->get();
        
        // استخدام view لـ PDF
        $html = view('back.exam.questions_pdf', compact('exam', 'questions'))->render();
        
        // محاولة استخدام DomPDF إذا كان متاحاً
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOption('enable-local-file-access', true);
                $pdf->setOption('isHtml5ParserEnabled', true);
                $pdf->setOption('isRemoteEnabled', true);
                
                return $pdf->download('exam_questions_' . $exam_id . '.pdf');
            } catch (\Exception $e) {
                \Log::error('PDF generation error: ' . $e->getMessage());
            }
        }
        
        // Fallback: عرض HTML للطباعة (يمكن للطالب استخدام Print to PDF من المتصفح)
        return response($html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="exam_questions_' . $exam_id . '.html"');
    }
    
    /**
     * تصدير أسئلة الامتحان كـ PDF بدون الإجابات (للاستخدام كامتحان)
     */
    public function exportQuestionsPDFWithoutAnswers($exam_id)
    {
        $exam = ExamName::findOrFail($exam_id);
        $questions = ExamQuestion::where('exam_id', $exam_id)
            ->orderBy('id', 'asc')
            ->get();
        
        // استخدام view لـ PDF بدون إجابات
        $html = view('back.exam.questions_pdf_without_answers', compact('exam', 'questions'))->render();
        
        // محاولة استخدام DomPDF إذا كان متاحاً
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOption('enable-local-file-access', true);
                $pdf->setOption('isHtml5ParserEnabled', true);
                $pdf->setOption('isRemoteEnabled', true);
                
                return $pdf->download('exam_questions_no_answers_' . $exam_id . '.pdf');
            } catch (\Exception $e) {
                \Log::error('PDF generation error: ' . $e->getMessage());
            }
        }
        
        // Fallback: عرض HTML للطباعة
        return response($html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="exam_questions_no_answers_' . $exam_id . '.html"');
    }
}

<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use App\Models\ExamResult;
use App\Models\ExamName;
use App\Models\StudentSubscriptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExamAnswerController extends Controller
{
    public function add_question_answer(Request $request, $exam_id)
    {
        try {
            // التحقق من صلاحية الوصول للامتحان
            $exam = ExamName::findOrFail($exam_id);
            
            // التحقق من جدولة الامتحان
            if ($exam->isUpcoming()) {
                return redirect()->route('courses.index')->with('error', 'الامتحان لم يفتح بعد. سيفتح ' . $exam->time_until_open);
            }
            
            if ($exam->isClosed()) {
                return redirect()->route('courses.index')->with('error', 'الامتحان مغلق. انتهى وقت الامتحان');
            }
            
            if (!$exam->isOpen()) {
                return redirect()->route('courses.index')->with('error', 'الامتحان غير متاح حالياً');
            }
            
            if ($exam->month_id) {
                if (!\App\Policies\StudentSubscriptionPolicy::canAccessMonth($exam->month_id)) {
                    return redirect()->route('courses.index')->with('error', 'ليس لديك صلاحية للوصول لهذا الامتحان!');
                }
            }
            
            $request->validate([
                'answer.*' => 'nullable', // يمكن أن يكون إما نص أو صورة
                'answer.*.image' => 'nullable|image|max:2048' // تحقق من كونها صورة
            ]);

            $studentId = Auth::guard('student')->user()->id; // تأكد من أن المستخدم مسجل الدخول
            $f_name = Auth::guard('student')->user()->first_name ;
            $s_name = Auth::guard('student')->user()->second_name ;
            $t_name = Auth::guard('student')->user()->third_name ;

            // تحقق من وجود نتيجة مكتملة للامتحان بالفعل (ExamResult مع completed_at)
            $existingCompletedResult = ExamResult::where('exam_id', $exam_id)
                ->where('student_id', $studentId)
                ->whereNotNull('completed_at')
                ->first();
                
            if ($existingCompletedResult) {
                return redirect()->route('courses.index')->with('error', 'لقد قمت بإرسال الإجابات بالفعل!');
            }
            
            // جلب النتيجة الحالية لمعرفة النموذج المعين للطالب
            $currentResult = ExamResult::where('exam_id', $exam_id)
                ->where('student_id', $studentId)
                ->first();
            
            // إذا لم يكن هناك نتيجة، نعمل واحدة (حالة نادرة)
            if (!$currentResult) {
                $currentResult = new ExamResult();
                $currentResult->exam_id = $exam_id;
                $currentResult->student_id = $studentId;
                $currentResult->started_at = now();
                
                // اختيار نموذج عشوائي
                $availableModels = ExamQuestion::where('exam_id', $exam_id)
                    ->distinct()
                    ->pluck('model_name')
                    ->toArray();
                    
                $currentResult->assigned_model = !empty($availableModels) 
                    ? $availableModels[array_rand($availableModels)] 
                    : 'A';
                    
                $currentResult->save();
            }
            
            // جلب الأسئلة الخاصة بالنموذج المعين فقط
            $questions = ExamQuestion::where('exam_id', $exam_id)
                ->where('model_name', $currentResult->assigned_model)
                ->pluck('id');
            
            // Log request data for debugging
            \Log::info('Exam submission started', [
                'exam_id' => $exam_id,
                'student_id' => $studentId,
                'total_questions' => $questions->count(),
                'answers_received' => $request->answer ? count($request->answer) : 0
            ]);

            foreach ($questions as $questionId) {
                $studentAnswer = null; // الإعداد الافتراضي للإجابة

                // تحقق مما إذا كانت الإجابة موجودة في الطلب
                if (isset($request->answer[$questionId])) {
                    $answer = $request->answer[$questionId];

                    // إذا كانت الإجابة نص
                    if (is_string($answer)) {
                        $studentAnswer = $answer;
                    } elseif (isset($answer['image']) && !empty($answer['image'])) {
                        // إذا كانت الإجابة صورة
                        $file = $answer['image'];
                        $ext = $file->getClientOriginalExtension();
                        $filename = strtolower($questionId . Str::random(20) . '.' . $ext);
                        $file->move('upload_files/student_answer/'.$f_name .'_' .$s_name.'_' .$t_name.'/', $filename);
                        $studentAnswer = $filename; // تخزين اسم الصورة
                    }
                }
                
                // قم بتخزين الإجابة سواء كانت null أو قيمة أخرى
                try {
                    $savedAnswer = ExamAnswer::updateOrCreate(
                        [
                            'exam_id' => $exam_id,
                            'student_id' => $studentId,
                            'question_id' => $questionId,
                        ],
                        [
                            'student_answer' => $studentAnswer, // تخزين null إذا لم توجد إجابة
                        ]
                    );
                    
                    \Log::debug('Answer saved', [
                        'question_id' => $questionId,
                        'answer' => $studentAnswer,
                        'saved_id' => $savedAnswer->id
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to save answer', [
                        'question_id' => $questionId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            //////////////////////////////////////////////insert degree///////////////////////////////////////////
            $exam_degree = 0;
            $user_degree = 0;
            // جلب أسئلة النموذج المعين فقط لحساب الدرجة
            $questions = ExamQuestion::where('exam_id', $exam_id)
                ->where('model_name', $currentResult->assigned_model)
                ->get();
            
            foreach($questions as $question){
                $exam_degree += (float)$question->Q_degree;
                
                $user_answer = ExamAnswer::where('exam_id', $exam_id)
                    ->where('student_id', $studentId)
                    ->where('question_id', $question->id)
                    ->first();
                
                // Bonus question: everyone gets the points
                if ($question->is_bonus) {
                    $user_degree += (float)$question->Q_degree;
                    continue;
                }

                if($user_answer && $user_answer->student_answer) {
                    // Multiple-correct support: check correct_answers array (we store texts)
                    if (!empty($question->correct_answers) && is_array($question->correct_answers)) {
                        if (in_array($user_answer->student_answer, $question->correct_answers)) {
                            $user_degree += (float)$question->Q_degree;
                        }
                    } else {
                        // fallback to single-answer comparison (including true/false)
                        if($question->correct_answer == $user_answer->student_answer) {
                            $user_degree += (float)$question->Q_degree;
                        }
                    }
                }
            }
            
            // حفظ النتيجة في ExamResult
            $existingResult = ExamResult::where('exam_id', $exam_id)
                ->where('student_id', $studentId)
                ->first();
                
            // حساب الوقت المنقضي
            $timeElapsed = 0;
            
            // نعطي الأولوية للوقت من localStorage
            if ($request->has('time_elapsed') && $request->input('time_elapsed') > 0) {
                $timeElapsed = (int) $request->input('time_elapsed');
            } elseif ($existingResult && $existingResult->started_at) {
                // إذا لم يكن متوفر، نحسب الوقت من البداية حتى الآن
                $timeElapsed = now()->diffInSeconds($existingResult->started_at);
            }
            
            \Log::info('Calculating exam result', [
                'exam_id' => $exam_id,
                'student_id' => $studentId,
                'user_degree' => $user_degree,
                'exam_degree' => $exam_degree,
                'time_elapsed' => $timeElapsed
            ]);
            
            try {
                $degree = ExamResult::updateOrCreate(
                    [
                        'exam_id' => $exam_id,
                        'student_id' => $studentId,
                    ],
                    [
                        'degree' => $user_degree,
                        'show_degree' => 0,
                        'is_marked' => 0,
                        'completed_at' => now(),
                        'time_elapsed' => $timeElapsed,
                    ]
                );
                
                // إضافة started_at إذا لم يكن موجود (أول مرة)
                if (!$degree->started_at) {
                    // البحث عن أول إجابة للطالب في هذا الامتحان
                    $firstAnswer = ExamAnswer::where('exam_id', $exam_id)
                        ->where('student_id', $studentId)
                        ->orderBy('created_at', 'asc')
                        ->first();
                        
                    $degree->started_at = $firstAnswer ? $firstAnswer->created_at : now();
                    $degree->save();
                }
                
                \Log::info('ExamResult saved successfully', [
                    'result_id' => $degree->id,
                    'degree' => $degree->degree,
                    'completed_at' => $degree->completed_at
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to save ExamResult', [
                    'exam_id' => $exam_id,
                    'student_id' => $studentId,
                    'degree' => $user_degree,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('courses.index')->with('error', 'حدث خطأ أثناء حفظ النتيجة: ' . $e->getMessage());
            }
            
            // تحديد إظهار الدرجة بناءً على إعدادات الامتحان
            $show_degree = false;
            
            // إذا كان الامتحان مغلق وتم تفعيل الإظهار التلقائي
            if ($exam->isClosed() && $exam->auto_show_results) {
                $show_degree = true;
            }
            
            // أو إذا كان محدد كـ marked
            $is_marked = ExamResult::where('exam_id', $exam_id)->where('is_marked', 1)->exists();
            if ($is_marked) {
                $show_degree = true;
            }
            
            // تحديث show_degree
            if ($show_degree) {
                ExamResult::where('exam_id', $exam_id)->where('student_id', $studentId)->update(['show_degree' => 1, 'is_marked' => 1]);
            }
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////
            return redirect()->route('courses.index')->with('success', 'تم إرسال الإجابات بنجاح!');
        }
        catch (\Exception $e) {
            return redirect()->route('courses.index')->with(['error' => $e->getMessage()]);
        }
    }

}


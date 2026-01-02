<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\LectureQuiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * عرض صفحة إدارة الكويز
     */
    public function show($lectureId)
    {
        $lecture = Lecture::with('quiz.questions')->findOrFail($lectureId);
        return view('back.quiz.show', compact('lecture'));
    }

    /**
     * إنشاء أو تحديث الكويز
     */
    public function store(Request $request, $lectureId)
    {
        $lecture = Lecture::findOrFail($lectureId);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'nullable',
            'is_active' => 'nullable',
            'exclude_excel_students' => 'nullable',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,text',
            'questions.*.options' => 'nullable|string',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.points' => 'required|integer|min:1',
        ]);

        // إنشاء أو تحديث الكويز
        $quiz = $lecture->quiz ?? new LectureQuiz();
        $quiz->lecture_id = $lectureId;
        $quiz->title = $request->title;
        $quiz->description = $request->description;
        $quiz->is_required = $request->has('is_required') ? 1 : 0;
        $quiz->is_active = $request->has('is_active') ? 1 : 0;
        $quiz->exclude_excel_students = $request->has('exclude_excel_students') ? 1 : 0;
        $quiz->passing_score = $request->passing_score;
        $quiz->time_limit = $request->time_limit;
        $quiz->save();

        // حذف الأسئلة القديمة
        $quiz->questions()->delete();

        // إضافة الأسئلة الجديدة
        foreach ($request->questions as $index => $questionData) {
            $options = null;
            
            // معالجة الخيارات
            if ($questionData['type'] === 'multiple_choice' && isset($questionData['options']) && !empty($questionData['options'])) {
                // تقسيم النص إلى مصفوفة
                $optionsArray = array_filter(array_map('trim', explode("\n", $questionData['options'])));
                $options = !empty($optionsArray) ? array_values($optionsArray) : null;
            }
            
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'options' => $options,
                'correct_answer' => $questionData['correct_answer'],
                'points' => $questionData['points'] ?? 1,
                'order' => $index,
            ]);
        }

        return redirect()->back()->with('success', 'تم حفظ الكويز بنجاح');
    }

    /**
     * حذف الكويز
     */
    public function destroy($lectureId)
    {
        $lecture = Lecture::findOrFail($lectureId);
        
        if ($lecture->quiz) {
            $lecture->quiz->questions()->delete();
            $lecture->quiz->delete();
        }

        return redirect()->back()->with('success', 'تم حذف الكويز بنجاح');
    }

    /**
     * عرض نتائج الكويز
     */
    public function results($lectureId)
    {
        $lecture = Lecture::with('quiz')->findOrFail($lectureId);
        
        if (!$lecture->quiz) {
            return redirect()->back()->with('error', 'لا يوجد كويز لهذه المحاضرة');
        }

        $attempts = \App\Models\QuizAttempt::with('student')
            ->where('quiz_id', $lecture->quiz->id)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->paginate(50);

        return view('back.quiz.results', compact('lecture', 'attempts'));
    }

    /**
     * عرض تفاصيل محاولة طالب
     */
    public function attemptDetails($attemptId)
    {
        $attempt = \App\Models\QuizAttempt::with(['quiz.questions', 'student'])->findOrFail($attemptId);
        return view('back.quiz.attempt_details', compact('attempt'));
    }
}


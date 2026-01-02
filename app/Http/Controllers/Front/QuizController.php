<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\LectureQuiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * عرض صفحة الكويز
     */
    public function show($quizId)
    {
        $quiz = LectureQuiz::with('questions', 'lecture')->findOrFail($quizId);
        $student = Auth::guard('student')->user();

        // التحقق من إمكانية الوصول للكويز
        if (!$quiz->canStudentAccess($student)) {
            return redirect()->back()->with('error', 'غير مسموح لك بالوصول لهذا الكويز');
        }

        // السماح بإعادة المحاولة حتى النجاح
        // إذا نجح الطالب، إعادة توجيهه لصفحة النتيجة
        $previousAttempt = $quiz->getStudentAttempt($student->id);
        if ($previousAttempt && $previousAttempt->is_passed) {
            return redirect()->route('quiz.result', ['quizId' => $quizId])->with('info', 'لقد نجحت في هذا الكويز بالفعل');
        }

        // جلب آخر محاولة (سواء نجح أو فشل)
        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        return view('front.quiz.show', compact('quiz', 'attempt'));
    }

    /**
     * بدء الكويز
     */
    public function start($quizId)
    {
        $quiz = LectureQuiz::with('questions')->findOrFail($quizId);
        $student = Auth::guard('student')->user();

        // التحقق من إمكانية الوصول للكويز
        if (!$quiz->canStudentAccess($student)) {
            return redirect()->back()->with('error', 'غير مسموح لك بالوصول لهذا الكويز');
        }

        // التحقق من وجود محاولة جارية (غير مكتملة)
        $ongoingAttempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->first();

        if ($ongoingAttempt) {
            // إذا كانت هناك محاولة جارية، إرجاعها
            return redirect()->route('quiz.show', ['quizId' => $quizId]);
        }

        // التحقق إذا كان الطالب قد نجح بالفعل
        $passedAttempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->where('is_passed', true)
            ->first();

        if ($passedAttempt) {
            return redirect()->route('quiz.result', ['quizId' => $quizId])
                ->with('info', 'لقد نجحت في هذا الكويز بالفعل');
        }

        // السماح بمحاولة جديدة (حتى لو فشل سابقاً)
        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'student_id' => $student->id,
            'started_at' => now(),
            'total_score' => $quiz->questions->sum('points'),
        ]);

        return redirect()->route('quiz.show', ['quizId' => $quizId]);
    }

    /**
     * حفظ إجابات الكويز
     */
    public function submit(Request $request, $quizId)
    {
        $quiz = LectureQuiz::with('questions')->findOrFail($quizId);
        $student = Auth::guard('student')->user();

        // التحقق من إمكانية الوصول للكويز
        if (!$quiz->canStudentAccess($student)) {
            return response()->json(['success' => false, 'message' => 'غير مسموح لك بالوصول لهذا الكويز'], 403);
        }

        // جلب المحاولة
        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->firstOrFail();

        // التحقق من الوقت إذا كان محدداً
        if ($quiz->time_limit && $attempt->started_at) {
            $elapsedMinutes = now()->diffInMinutes($attempt->started_at);
            if ($elapsedMinutes > $quiz->time_limit) {
                $attempt->completed_at = now();
                $attempt->save();
                return response()->json([
                    'success' => false,
                    'message' => 'انتهى الوقت المحدد للكويز',
                    'timeout' => true
                ], 400);
            }
        }

        // معالجة الإجابات
        $answers = $request->input('answers', []);
        $score = 0;
        $totalScore = $quiz->questions->sum('points');

        foreach ($quiz->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            
            if ($studentAnswer !== null) {
                if ($question->isCorrectAnswer($studentAnswer)) {
                    $score += $question->points;
                }
            }
        }

        // حساب النسبة المئوية
        $percentage = $totalScore > 0 ? ($score / $totalScore) * 100 : 0;
        $isPassed = $percentage >= $quiz->passing_score;

        // حفظ المحاولة
        $attempt->answers = $answers;
        $attempt->score = $score;
        $attempt->total_score = $totalScore;
        $attempt->percentage = round($percentage, 2);
        $attempt->is_passed = $isPassed;
        $attempt->completed_at = now();
        $attempt->save();

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الكويز بنجاح',
            'data' => [
                'score' => $score,
                'total_score' => $totalScore,
                'percentage' => round($percentage, 2),
                'is_passed' => $isPassed,
                'passing_score' => $quiz->passing_score,
            ]
        ]);
    }

    /**
     * عرض نتائج الكويز
     */
    public function result($quizId)
    {
        $quiz = LectureQuiz::with(['questions', 'lecture'])->findOrFail($quizId);
        $student = Auth::guard('student')->user();

        // جلب المحاولة
        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNotNull('completed_at')
            ->latest()
            ->firstOrFail();

        // حساب الوقت المستغرق
        $timeSpent = null;
        if ($attempt->started_at && $attempt->completed_at) {
            $timeSpent = $attempt->started_at->diffInMinutes($attempt->completed_at);
        }

        // جلب المحاضرة التالية
        $nextLecture = null;
        if ($quiz->lecture) {
            $nextLecture = Lecture::where('month_id', $quiz->lecture->month_id)
                ->where('id', '>', $quiz->lecture->id)
                ->where('status', 1)
                ->orderBy('id')
                ->first();
        }

        return view('front.quiz.result', compact('quiz', 'attempt', 'nextLecture', 'timeSpent'));
    }

    /**
     * التحقق من إمكانية الوصول للمحاضرة التالية
     */
    public function checkLectureAccess($lectureId)
    {
        $lecture = Lecture::findOrFail($lectureId);
        $student = Auth::guard('student')->user();

        // جلب المحاضرة السابقة
        $previousLecture = Lecture::where('month_id', $lecture->month_id)
            ->where('id', '<', $lectureId)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->first();

        if ($previousLecture && $previousLecture->quiz) {
            $quiz = $previousLecture->quiz;

            // التحقق من إمكانية الوصول للكويز
            if (!$quiz->canStudentAccess($student)) {
                // إذا كان الطالب لا يستطيع الوصول للكويز أصلاً، السماح بالوصول للمحاضرة
                return response()->json([
                    'can_access' => true,
                    'message' => ''
                ]);
            }

            // إذا كان الكويز إجباري على هذا الطالب
            if ($quiz->isRequiredForStudent($student)) {
                $attempt = $quiz->getStudentAttempt($student->id);
                if (!$attempt || !$attempt->is_passed) {
                    return response()->json([
                        'can_access' => false,
                        'message' => 'يجب إكمال الكويز الخاص بالمحاضرة السابقة أولاً',
                        'quiz_id' => $quiz->id,
                        'quiz_title' => $quiz->title,
                    ]);
                }
            }
        }

        return response()->json([
            'can_access' => true,
            'message' => ''
        ]);
    }
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamName;
use App\Models\ExamQuestion;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Get all exams
     */
    public function index(Request $request)
    {
        $student = $request->user();
        
        // Get exams from subscribed courses
        $subscriptions = \App\Models\StudentSubscriptions::where('student_id', $student->id)
            ->where('is_active', 1)
            ->pluck('month_id');
        
        $exams = ExamName::whereIn('month_id', $subscriptions)
            ->orWhere('public_access', 1)
            ->get()
            ->map(function($exam) {
                return [
                    'id' => $exam->id,
                    'title' => $exam->exam_title,
                    'description' => $exam->exam_description,
                    'time' => $exam->exam_time,
                    'grade' => $exam->grade,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $exams
        ]);
    }

    /**
     * Get exam details with questions
     */
    public function show(Request $request, $id)
    {
        $student = $request->user();
        $exam = ExamName::findOrFail($id);
        
        // Check access
        $subscription = \App\Models\StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $exam->month_id)
            ->where('is_active', 1)
            ->first();

        if (!$subscription && !$exam->public_access) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الامتحان'
            ], 403);
        }

        $questions = ExamQuestion::where('exam_id', $id)->get()->map(function($question) {
            return [
                'id' => $question->id,
                'question_title' => $question->question_title,
                'question_title_formatted' => $question->question_title_formatted,
                'ch_1' => $question->ch_1,
                'ch_2' => $question->ch_2,
                'ch_3' => $question->ch_3,
                'ch_4' => $question->ch_4,
                'ch_1_img' => $question->ch_1_img ? url('upload_files/' . $question->ch_1_img) : null,
                'ch_2_img' => $question->ch_2_img ? url('upload_files/' . $question->ch_2_img) : null,
                'ch_3_img' => $question->ch_3_img ? url('upload_files/' . $question->ch_3_img) : null,
                'ch_4_img' => $question->ch_4_img ? url('upload_files/' . $question->ch_4_img) : null,
                'img' => $question->img ? url('upload_files/' . $question->img) : null,
                'Q_degree' => (float)$question->Q_degree,
                // Don't send correct_answer to prevent cheating
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => [
                    'id' => $exam->id,
                    'title' => $exam->exam_title,
                    'description' => $exam->exam_description,
                    'time' => $exam->exam_time,
                ],
                'questions' => $questions,
                'total_degree' => $questions->sum('Q_degree'),
            ]
        ]);
    }

    /**
     * Submit exam answers
     */
    public function submit(Request $request, $id)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);

        $student = $request->user();
        $exam = ExamName::findOrFail($id);
        
        // Get questions with correct answers
        $questions = ExamQuestion::where('exam_id', $id)->get();
        
        $totalDegree = 0;
        $studentDegree = 0;
        
        foreach ($questions as $question) {
            $totalDegree += $question->Q_degree;
            $studentAnswer = $request->answers[$question->id] ?? null;
            
            if ($studentAnswer && $studentAnswer == $question->correct_answer) {
                $studentDegree += $question->Q_degree;
            }
        }

        // Save result
        $result = ExamResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'exam_id' => $id,
            ],
            [
                'degree' => $studentDegree,
                'show_degree' => 1,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الإجابات بنجاح',
            'data' => [
                'result_id' => $result->id,
                'student_degree' => $studentDegree,
                'total_degree' => $totalDegree,
                'percentage' => $totalDegree > 0 ? round(($studentDegree / $totalDegree) * 100, 2) : 0,
            ]
        ]);
    }

    /**
     * Get exam results
     */
    public function results(Request $request, $id)
    {
        $student = $request->user();
        
        $result = ExamResult::where('student_id', $student->id)
            ->where('exam_id', $id)
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على نتيجة'
            ], 404);
        }

        $exam = ExamName::findOrFail($id);
        $totalDegree = ExamQuestion::where('exam_id', $id)->sum('Q_degree');

        return response()->json([
            'success' => true,
            'data' => [
                'student_degree' => (float)$result->degree,
                'total_degree' => (float)$totalDegree,
                'percentage' => $totalDegree > 0 ? round(($result->degree / $totalDegree) * 100, 2) : 0,
                'created_at' => $result->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }
}



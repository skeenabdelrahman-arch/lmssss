<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Month;
use App\Models\StudentSubscriptions;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Get all courses
     */
    public function index(Request $request)
    {
        $student = $request->user();
        
        $courses = Month::all()->map(function($course) use ($student) {
            $subscription = StudentSubscriptions::where('student_id', $student->id)
                ->where('month_id', $course->id)
                ->where('is_active', 1)
                ->first();
            
            return [
                'id' => $course->id,
                'name' => $course->name,
                'price' => (float)$course->price,
                'grade' => $course->grade,
                'is_subscribed' => $subscription ? true : false,
                'subscription_id' => $subscription ? $subscription->id : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Get course details
     */
    public function show(Request $request, $id)
    {
        $student = $request->user();
        $course = Month::findOrFail($id);
        
        $subscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $id)
            ->where('is_active', 1)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $course->id,
                'name' => $course->name,
                'price' => (float)$course->price,
                'grade' => $course->grade,
                'is_subscribed' => $subscription ? true : false,
                'subscription_id' => $subscription ? $subscription->id : null,
            ]
        ]);
    }

    /**
     * Get course content (lectures, pdfs, exams)
     */
    public function content(Request $request, $id)
    {
        $student = $request->user();
        $course = Month::findOrFail($id);
        
        // Check subscription
        $subscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$subscription && $course->price > 0) {
            return response()->json([
                'success' => false,
                'message' => 'يجب الاشتراك في الكورس أولاً'
            ], 403);
        }

        $lectures = $course->lectures()->where('status', 1)->get()->map(function($lecture) {
            return [
                'id' => $lecture->id,
                'title' => $lecture->title,
                'description' => $lecture->description,
                'video_url' => $lecture->video_url,
                'views' => $lecture->views,
            ];
        });

        $pdfs = $course->pdfs()->get()->map(function($pdf) {
            return [
                'id' => $pdf->id,
                'title' => $pdf->title,
                'file_url' => url('upload_files/' . $pdf->file),
            ];
        });

        $exams = \App\Models\ExamName::where('month_id', $course->id)->get()->map(function($exam) {
            return [
                'id' => $exam->id,
                'title' => $exam->exam_title,
                'description' => $exam->exam_description,
                'time' => $exam->exam_time,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'course' => [
                    'id' => $course->id,
                    'name' => $course->name,
                ],
                'lectures' => $lectures,
                'pdfs' => $pdfs,
                'exams' => $exams,
            ]
        ]);
    }
}


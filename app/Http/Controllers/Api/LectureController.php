<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    /**
     * Get lecture details
     */
    public function show(Request $request, $id)
    {
        $lecture = Lecture::with('month')->findOrFail($id);
        
        // Check if student has access
        $student = $request->user();
        $subscription = \App\Models\StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $lecture->month_id)
            ->where('is_active', 1)
            ->first();

        if (!$subscription && $lecture->month->price > 0) {
            return response()->json([
                'success' => false,
                'message' => 'يجب الاشتراك في الكورس أولاً'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $lecture->id,
                'title' => $lecture->title,
                'description' => $lecture->description,
                'video_url' => $lecture->video_url,
                'views' => $lecture->views,
                'month' => [
                    'id' => $lecture->month->id,
                    'name' => $lecture->month->name,
                ],
            ]
        ]);
    }

    /**
     * Mark lecture as viewed
     */
    public function markAsViewed(Request $request, $id)
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->increment('views');

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل المشاهدة'
        ]);
    }
}



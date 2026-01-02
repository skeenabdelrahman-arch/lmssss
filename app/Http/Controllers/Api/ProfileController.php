<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get student profile
     */
    public function show(Request $request)
    {
        $student = $request->user();
        
        // Get subscriptions
        $subscriptions = \App\Models\StudentSubscriptions::where('student_id', $student->id)
            ->where('is_active', 1)
            ->with('month')
            ->get()
            ->map(function($sub) {
                return [
                    'id' => $sub->month->id,
                    'name' => $sub->month->name,
                ];
            });

        // Get exam results
        $examResults = \App\Models\ExamResult::where('student_id', $student->id)
            ->with('exam')
            ->get()
            ->map(function($result) {
                $totalDegree = \App\Models\ExamQuestion::where('exam_id', $result->exam_id)->sum('Q_degree');
                return [
                    'exam_title' => $result->exam->exam_title,
                    'degree' => (float)$result->degree,
                    'total_degree' => (float)$totalDegree,
                    'percentage' => $totalDegree > 0 ? round(($result->degree / $totalDegree) * 100, 2) : 0,
                    'created_at' => $result->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'first_name' => $student->first_name,
                    'second_name' => $student->second_name,
                    'third_name' => $student->third_name,
                    'forth_name' => $student->forth_name,
                    'email' => $student->email,
                    'phone' => $student->student_phone,
                    'grade' => $student->grade,
                    'gender' => $student->gender,
                    'image' => $student->image ? url('upload_files/' . $student->image) : null,
                ],
                'subscriptions' => $subscriptions,
                'exam_results' => $examResults,
            ]
        ]);
    }

    /**
     * Update student profile
     */
    public function update(Request $request)
    {
        $student = $request->user();
        
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'second_name' => 'sometimes|string|max:255',
            'third_name' => 'sometimes|string|max:255',
            'forth_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:students,email,' . $student->id,
            'grade' => 'sometimes|string',
            'city' => 'sometimes|string',
        ]);

        $student->update($request->only([
            'first_name', 'second_name', 'third_name', 'forth_name',
            'email', 'grade', 'city'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث البيانات بنجاح',
            'data' => $student
        ]);
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $student = $request->user();
        
        // Delete old image
        if ($student->image && file_exists(public_path('upload_files/' . $student->image))) {
            @unlink(public_path('upload_files/' . $student->image));
        }

        // Upload new image
        $file = $request->file('image');
        $filename = 'student_' . $student->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('upload_files'), $filename);

        $student->update(['image' => $filename]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الصورة بنجاح',
            'data' => [
                'image_url' => url('upload_files/' . $filename)
            ]
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $student = $request->user();

        if (!Hash::check($request->current_password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 422);
        }

        $student->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث كلمة المرور بنجاح'
        ]);
    }
}



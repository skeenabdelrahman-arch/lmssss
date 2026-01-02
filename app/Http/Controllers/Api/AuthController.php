<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register new student
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'student_phone' => 'required|string|unique:students,student_phone',
            'email' => 'nullable|email|unique:students,email',
            'password' => 'required|string|min:6',
            'grade' => 'required|string',
            'gender' => 'required|in:ذكر,أنثى',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'third_name' => $request->third_name ?? '',
            'forth_name' => $request->forth_name ?? '',
            'student_phone' => $request->student_phone,
            'email' => $request->email ?? $request->student_phone . '@student.com',
            'password' => Hash::make($request->password),
            'grade' => $request->grade,
            'gender' => $request->gender,
            'city' => $request->city ?? '',
            'parent_phone' => $request->parent_phone ?? '',
        ]);

        $token = $student->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'data' => [
                'student' => $student,
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Login student
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('student_phone', $request->phone)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        $token = $student->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'student' => $student,
                'token' => $token,
            ]
        ]);
    }

    /**
     * Logout student
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $student = Student::where('email', $request->email)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني غير موجود'
            ], 404);
        }

        // Generate reset token
        $token = Str::random(60);
        $student->update(['remember_token' => $token]);

        // Send email
        try {
            \Mail::to($student->email)->send(new \App\Mail\ForgotPasswordMail($student));
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور'
        ]);
    }
}



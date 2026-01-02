<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureStudentProfileComplete
{
    public function handle(Request $request, Closure $next)
    {
        $student = Auth::guard('student')->user();

        if ($student) {
            $isIncomplete = empty(trim((string) $student->image)) || empty(trim((string) $student->email));

            if ($isIncomplete) {
                $routeName = $request->route() ? $request->route()->getName() : null;

                $allowedRoutes = [
                    'student_profile', // viewing profile
                    'updateImage',     // updating image
                    'updatePassword',  // updating password
                    'studentLogout',   // logout
                    'courses.index',   // main courses page
                ];

                if (!$routeName || !in_array($routeName, $allowedRoutes, true)) {
                    return redirect()->route('student_profile', ['force' => 1])
                        ->with('error', 'يرجى إكمال الملف الشخصي بإضافة صورة وبريد إلكتروني قبل متابعة التصفح.');
                }
            }
        }

        return $next($request);
    }
}

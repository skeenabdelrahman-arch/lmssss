<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if student is authenticated
        if (!Auth::guard('student')->check()) {
            return redirect()
                ->route('student.login')
                ->withErrors(['message' => 'يرجى تسجيل الدخول']);
        }

        

        // Continue with the request
        return $next($request);
    }
}
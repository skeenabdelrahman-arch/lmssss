<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentNotificationController extends Controller
{
    /**
     * عرض جميع الإشعارات للطالب
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login');
        }

        $notifications = Notification::where('notifiable_type', \App\Models\Student::class)
            ->where('notifiable_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('front.notifications.index', compact('notifications'));
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة (AJAX)
     */
    public function unreadCount()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return response()->json(['count' => 0]);
        }

        $count = Notification::where('notifiable_type', \App\Models\Student::class)
            ->where('notifiable_id', $student->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * الحصول على آخر الإشعارات (AJAX)
     */
    public function recent()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return response()->json(['notifications' => []]);
        }

        $notifications = Notification::where('notifiable_type', \App\Models\Student::class)
            ->where('notifiable_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead($id)
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'غير مصرح']);
        }

        $notification = Notification::where('id', $id)
            ->where('notifiable_type', \App\Models\Student::class)
            ->where('notifiable_id', $student->id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'الإشعار غير موجود']);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'غير مصرح']);
        }

        Notification::where('notifiable_type', \App\Models\Student::class)
            ->where('notifiable_id', $student->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}


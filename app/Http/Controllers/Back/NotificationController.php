<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        // عرض إشعارات الأدمن فقط (notifiable_type = null أو User)
        $notifications = Notification::where(function($query) {
                $query->whereNull('notifiable_type')
                      ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where(function($query) {
                $query->whereNull('notifiable_type')
                      ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
            })
            ->where('is_read', false)
            ->count();
        
        return view('back.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الإشعار كمقروء'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where(function($query) {
                $query->whereNull('notifiable_type')
                      ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
            })
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'تم حذف الإشعار بنجاح');
    }

    /**
     * Delete all notifications
     */
    public function deleteAll()
    {
        Notification::query()->delete();
        
        return redirect()->back()->with('success', 'تم حذف جميع الإشعارات بنجاح');
    }

    /**
     * Get unread notifications count (for AJAX)
     */
    public function getUnreadCount()
    {
        $count = Notification::where(function($query) {
                $query->whereNull('notifiable_type')
                      ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
            })
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get latest notifications (for AJAX)
     */
    public function getLatest()
    {
        $notifications = Notification::where(function($query) {
                $query->whereNull('notifiable_type')
                      ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return response()->json([
            'notifications' => $notifications
        ]);
    }
}






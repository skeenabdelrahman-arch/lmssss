<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Services\CacheService;
use Illuminate\Http\Request;

class BulkOperationsController extends Controller
{
    /**
     * عمليات جماعية على المحاضرات
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'exists:lectures,id',
        ]);
        
        $ids = $request->ids;
        $action = $request->action;
        $count = 0;
        
        switch ($action) {
            case 'delete':
                $count = Lecture::whereIn('id', $ids)->delete();
                $message = "تم حذف {$count} محاضرة بنجاح";
                break;
                
            case 'publish':
                $count = Lecture::whereIn('id', $ids)->update(['status' => 1]);
                $message = "تم نشر {$count} محاضرة بنجاح";
                break;
                
            case 'unpublish':
                $count = Lecture::whereIn('id', $ids)->update(['status' => 0]);
                $message = "تم إلغاء نشر {$count} محاضرة بنجاح";
                break;
                
            case 'feature':
                $count = Lecture::whereIn('id', $ids)->update(['is_featured' => 1]);
                $message = "تم تمييز {$count} محاضرة بنجاح";
                break;
                
            case 'unfeature':
                $count = Lecture::whereIn('id', $ids)->update(['is_featured' => 0]);
                $message = "تم إلغاء تمييز {$count} محاضرة بنجاح";
                break;
        }
        
        // Clear cache after bulk operations
        CacheService::clearLecturesCache();
        
        return redirect()->back()->with('success', $message);
    }
}


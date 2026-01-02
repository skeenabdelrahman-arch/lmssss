<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;

class CacheController extends Controller
{
    /**
     * Clear all cache
     */
    public function clearAll()
    {
        CacheService::clearCache();
        \Artisan::call('cache:clear');
        \Artisan::call('view:clear');
        \Artisan::call('config:clear');
        
        return redirect()->back()->with('success', 'تم مسح جميع الـ Cache بنجاح');
    }
    
    /**
     * Clear specific cache
     */
    public function clearSpecific(Request $request)
    {
        $type = $request->get('type');
        
        switch ($type) {
            case 'lectures':
                CacheService::clearLecturesCache();
                break;
            case 'stats':
                CacheService::clearCache('dashboard_stats');
                break;
            default:
                CacheService::clearCache();
        }
        
        return redirect()->back()->with('success', 'تم مسح الـ Cache بنجاح');
    }
}





<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    /**
     * Cache duration in seconds
     */
    const CACHE_DURATION = 3600; // 1 hour
    
    /**
     * Get cached statistics
     */
    public static function getStats($key = 'dashboard_stats')
    {
        return Cache::remember($key, self::CACHE_DURATION, function () {
            return [
                'total_students' => \App\Models\Student::count(),
                'total_lectures' => \App\Models\Lecture::count(),
                'total_exams' => \App\Models\ExamName::count(),
                'total_revenue' => \App\Models\Payment::where('status', 'paid')->sum('amount'),
            ];
        });
    }
    
    /**
     * Get cached lectures
     */
    public static function getLectures($filters = [])
    {
        $cacheKey = 'lectures_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($filters) {
            $query = \App\Models\Lecture::query();
            
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            if (isset($filters['month_id'])) {
                $query->where('month_id', $filters['month_id']);
            }
            
            if (isset($filters['grade'])) {
                $query->where('grade', $filters['grade']);
            }
            
            return $query->get();
        });
    }
    
    /**
     * Get cached featured lectures
     * @param int $limit Number of lectures to return
     */
    public static function getFeaturedLectures($limit = 6)
    {
        return Cache::remember("featured_lectures_{$limit}", self::CACHE_DURATION, function () use ($limit) {
            return \App\Models\Lecture::where('is_featured', 1)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        });
    }
    
    /**
     * Clear cache by pattern
     */
    public static function clearCache($pattern = null)
    {
        if ($pattern) {
            // Clear specific cache
            Cache::forget($pattern);
        } else {
            // Clear all cache
            Cache::flush();
        }
    }
    
    /**
     * Clear lectures cache
     */
    public static function clearLecturesCache()
    {
        // Forget the common featured cache keys (default + current per-page setting)
        $commonLimits = [6];
        if (function_exists('courses_per_page')) {
            $limit = courses_per_page();
            if (!empty($limit)) {
                $commonLimits[] = $limit;
            }
        }

        foreach (array_unique($commonLimits) as $limit) {
            Cache::forget("featured_lectures_{$limit}");
        }

        Cache::forget('analytics_dashboard_stats');
        
        // Try to clear Redis cache if available
        try {
            $store = Cache::getStore();
            if (method_exists($store, 'getRedis')) {
                $redis = $store->getRedis();
                if ($redis && method_exists($redis, 'keys')) {
                    // Clear any lecture-related and featured lecture cache keys
                    $patterns = ['*featured_lectures*', '*lectures*'];
                    foreach ($patterns as $pattern) {
                        $keys = $redis->keys($pattern);
                        foreach ($keys as $key) {
                            $cleanKey = str_replace(config('cache.prefix', 'laravel_cache') . ':', '', $key);
                            Cache::forget($cleanKey);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // If Redis is not available (using file cache), just forget the main keys
            // This is fine for file-based cache
        }
    }
}


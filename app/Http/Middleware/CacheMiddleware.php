<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $duration = 3600): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }
        
        // Generate cache key
        $cacheKey = 'page_' . md5($request->fullUrl());
        
        // Check if cached
        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey))
                ->header('X-Cache', 'HIT');
        }
        
        // Get response
        $response = $next($request);
        
        // Cache the response if it's successful
        if ($response->getStatusCode() === 200) {
            Cache::put($cacheKey, $response->getContent(), $duration);
            $response->header('X-Cache', 'MISS');
        }
        
        return $response;
    }
}





<?php

if (!function_exists('optimized_image')) {
    /**
     * Get optimized image URL
     */
    function optimized_image($path, $width = null, $height = null)
    {
        if (!$path) {
            return asset('images/placeholder.jpg');
        }
        
        $url = asset('upload_files/' . $path);
        
        // Add CDN if enabled
        if (config('cdn.enabled', false)) {
            $url = config('cdn.url') . '/' . config('cdn.images_path') . '/' . $path;
        }
        
        return $url;
    }
}

if (!function_exists('cdn_asset')) {
    /**
     * Get CDN asset URL
     */
    function cdn_asset($path)
    {
        if (config('cdn.enabled', false)) {
            return config('cdn.url') . '/' . config('cdn.assets_path') . '/' . $path;
        }
        
        return asset($path);
    }
}





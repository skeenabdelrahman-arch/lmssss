<?php

if (!function_exists('optimized_image')) {
    /**
     * Get optimized image URL with lazy loading
     */
    function optimized_image($path, $width = null, $height = null, $alt = '', $lazy = true)
    {
        if (!$path) {
            return asset('images/placeholder.jpg');
        }
        
        $url = asset('storage/' . $path);
        
        // Add CDN if enabled
        if (config('app.cdn_enabled', false)) {
            $url = config('app.cdn_url') . '/' . $path;
        }
        
        return $url;
    }
}

if (!function_exists('lazy_image')) {
    /**
     * Generate lazy loading image tag
     */
    function lazy_image($path, $alt = '', $width = null, $height = null, $class = '')
    {
        $url = optimized_image($path, $width, $height, $alt);
        $placeholder = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E';
        
        $attributes = [
            'src' => $lazy ? $placeholder : $url,
            'data-src' => $lazy ? $url : null,
            'alt' => $alt,
            'class' => 'lazy-load ' . $class,
            'loading' => $lazy ? 'lazy' : 'eager',
        ];
        
        if ($width) $attributes['width'] = $width;
        if ($height) $attributes['height'] = $height;
        
        $attrString = '';
        foreach ($attributes as $key => $value) {
            if ($value !== null) {
                $attrString .= " {$key}=\"" . htmlspecialchars($value) . "\"";
            }
        }
        
        return "<img{$attrString}>";
    }
}





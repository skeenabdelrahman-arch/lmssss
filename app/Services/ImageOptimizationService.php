<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageOptimizationService
{
    /**
     * Optimize and resize image
     */
    public static function optimize(UploadedFile $file, $width = null, $height = null, $quality = 85)
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            // Resize if dimensions provided
            if ($width || $height) {
                $image->scale($width ?: $height, $height ?: $width);
            }
            
            // Optimize quality
            $optimizedPath = storage_path('app/temp/' . uniqid() . '.jpg');
            $image->toJpeg($quality)->save($optimizedPath);
            
            return $optimizedPath;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
            return $file->getRealPath(); // Return original if optimization fails
        }
    }
    
    /**
     * Generate WebP version
     */
    public static function generateWebP($imagePath, $quality = 85)
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($imagePath);
            
            $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $imagePath);
            $image->toWebp($quality)->save($webpPath);
            
            return $webpPath;
        } catch (\Exception $e) {
            \Log::error('WebP generation failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get optimized image URL
     */
    public static function getOptimizedUrl($imagePath, $width = null, $height = null)
    {
        // If CDN is enabled, use CDN URL
        if (config('app.cdn_enabled')) {
            $cdnUrl = config('app.cdn_url');
            return $cdnUrl . '/' . $imagePath;
        }
        
        // Otherwise return local URL
        return asset('storage/' . $imagePath);
    }
}





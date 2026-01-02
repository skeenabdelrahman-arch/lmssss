<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your CDN settings here
    |
    */
    
    'enabled' => env('CDN_ENABLED', false),
    
    'url' => env('CDN_URL', 'https://cdn.example.com'),
    
    'assets_path' => env('CDN_ASSETS_PATH', 'assets'),
    
    'images_path' => env('CDN_IMAGES_PATH', 'images'),
    
    'videos_path' => env('CDN_VIDEOS_PATH', 'videos'),
];





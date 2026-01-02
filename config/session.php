<?php

use Illuminate\Support\Str;

return [
    'driver' => 'file',
    'lifetime' => env('SESSION_LIFETIME', 525600), // 525600 دقيقة = سنة واحدة (لا نهائي عملياً)
    // ملاحظة: إذا كانت القيمة 0 أو null، سيتم استخدام سنة واحدة تلقائياً
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => Str::slug('laravel', '_').'_session',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
];

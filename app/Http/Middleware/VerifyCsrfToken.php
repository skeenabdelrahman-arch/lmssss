<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhook/*', // استثناء جميع Webhook routes من CSRF
        'payments/verify/*', // استثناء route التحقق من الدفع
    'payment/callback*', // لاحظ النجمة * لتشمل أي query params
    'payment/webhook*',
    ];
}

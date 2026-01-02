<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;
use Illuminate\Http\Request;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
            'localhost',
            '127.0.0.1',
            '::1',
            '192.168.0.0/16',
            '10.0.0.0/8',
            '172.16.0.0/12',
            env('APP_URL'),
        ];
    }
}

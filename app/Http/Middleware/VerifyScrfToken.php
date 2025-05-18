<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs que deberían ser excluidos de la verificación CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'reports/*/notifications',
    ];
}

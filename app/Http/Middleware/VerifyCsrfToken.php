<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login',
        '/mobile/muestras/show',
        '/mobile/muestras/showByQR',
        '/mobile/muestras/store',
        '/mobile/muestras/update',
        '/mobile/muestras/delete',
        '/mobile/muestras/cancel',

        '/mobile/defectos/store',
        '/mobile/obtenerCalificacionMuestra',
        '*'
    ];
}

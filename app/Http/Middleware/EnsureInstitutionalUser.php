<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstitutionalUser
{
    /**
     * Middleware: Verifica que el usuario sea institucional.
     *
     * Este middleware asegura que solo los usuarios con el rol "institutional"
     * puedan acceder a las rutas protegidas por él.
     *
     * @group Middleware
     * @unauthenticated
     * 
     * @response 403 {
     *   "message": "Acceso no autorizado."
     * }
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'institutional') {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}

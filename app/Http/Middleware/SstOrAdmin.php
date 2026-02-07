<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SstOrAdmin
{
    /**
     * Gestionar una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();


        // si mas adelante se usa auth mantener esto seguro

        if (!$user || !in_array($user->rol, ['SST', 'ADMIN'])) {
            //SI NO HAY ESTA SESION ACTIVA O ROL NO PERMITIDO REDIRIGIR AL LOGUIN O DASHBOARD
            return redirect()->route('login')->withErrors(['auth' => 'Acceso denegado. Requiere rol SST o ADMIN']);
        }
        return $next($request);
    }
}

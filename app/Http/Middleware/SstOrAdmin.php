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


        // si más adelante se usa auth, mantener esto seguro

        if (!$user || !in_array($user->rol, ['SST', 'ADMIN'])) {
            // Si no hay sesión activa o el rol no está permitido, redirigir al login o al dashboard
            return redirect()->route('login')->withErrors(['auth' => 'Acceso denegado. Requiere rol SST o ADMIN']);
        }
        return $next($request);
    }
}

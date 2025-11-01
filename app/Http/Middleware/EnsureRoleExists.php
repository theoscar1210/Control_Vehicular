<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureRoleExists
{



    public function handle(Request $request, Closure $next)
    {

        $user = Auth::user();


        if (!$user) {
            return redirect()->route('login')->withErrors(['auth' => 'Por favor inicia sesión.']);
        }

        $validRoles = ['ADMIN', 'SST', 'PORTERIA'];

        if (empty($user->rol) || !in_array($user->rol, $validRoles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['rol' => 'Rol Invalido, Contacta al administador']);
        }

        //rol valido

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || Auth::user()->rol !== 'ADMIN') {
            return redirect()->route('dashboard.home')->withErrors(['auth' => 'Acceso denegado. Sólo ADMIN.']);
        }

        return $next($request);
    }
}

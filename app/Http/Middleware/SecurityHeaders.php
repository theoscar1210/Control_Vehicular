<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Headers de seguridad para proteger contra ataques comunes
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevenir que el navegador interprete archivos como un tipo diferente
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevenir clickjacking (página no puede ser embebida en iframe)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Habilitar filtro XSS del navegador
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controlar qué información de referrer se envía
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Forzar HTTPS en producción (el navegador recordará usar HTTPS por 1 año)
        if (!app()->isLocal()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Política de permisos (restringir APIs del navegador)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Content Security Policy — previene ejecución de scripts no autorizados
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com; " .
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; " .
            "font-src 'self' cdnjs.cloudflare.com fonts.gstatic.com; " .
            "img-src 'self' data:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'self';"
        );

        return $response;
    }
}

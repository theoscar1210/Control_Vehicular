<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Dominios con Estado (Stateful)
    |--------------------------------------------------------------------------
    |
    | Las solicitudes desde los siguientes dominios/hosts recibirán cookies de
    | autenticación API con estado. Normalmente, deben incluir los dominios
    | locales y de producción que acceden a su API mediante un SPA frontend.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort(),
        // Sanctum::currentRequestHost(),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Guards de Sanctum
    |--------------------------------------------------------------------------
    |
    | Este array contiene los guards de autenticación que se verificarán cuando
    | Sanctum intente autenticar una solicitud. Si ninguno de estos guards
    | puede autenticar la solicitud, Sanctum usará el token Bearer
    | presente en la solicitud entrante para la autenticación.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Minutos de Expiración
    |--------------------------------------------------------------------------
    |
    | Este valor controla el número de minutos hasta que un token emitido se
    | considera expirado. Esto anulará cualquier valor establecido en el atributo
    | "expires_at" del token, pero las sesiones de primera parte no se ven afectadas.
    |
    */

    'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 1440), // 24 horas por defecto

    /*
    |--------------------------------------------------------------------------
    | Prefijo de Token
    |--------------------------------------------------------------------------
    |
    | Sanctum puede prefijar nuevos tokens para aprovechar numerosas
    | iniciativas de análisis de seguridad mantenidas por plataformas de código abierto
    | que notifican a los desarrolladores si confirman tokens en repositorios.
    |
    | Ver: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Middleware de Sanctum
    |--------------------------------------------------------------------------
    |
    | Al autenticar su SPA de primera parte con Sanctum, puede necesitar
    | personalizar algunos de los middleware que usa Sanctum al procesar la
    | solicitud. Puede cambiar los middleware listados a continuación según sea necesario.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];

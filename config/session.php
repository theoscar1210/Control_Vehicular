<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Controlador de Sesión Predeterminado
    |--------------------------------------------------------------------------
    |
    | Esta opción determina el controlador de sesión predeterminado que se utiliza para
    | las solicitudes entrantes. Laravel admite una variedad de opciones de almacenamiento
    | para persistir los datos de sesión. La base de datos es una buena opción predeterminada.
    |
    | Compatibles: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Duración de la Sesión
    |--------------------------------------------------------------------------
    |
    | Aquí puede especificar el número de minutos que desea que la sesión
    | permanezca inactiva antes de que expire. Si desea que expiren
    | inmediatamente cuando se cierre el navegador, puede indicarlo
    | mediante la opción de configuración expire_on_close.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 30),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', true),

    /*
    |--------------------------------------------------------------------------
    | Cifrado de Sesión
    |--------------------------------------------------------------------------
    |
    | Esta opción le permite especificar fácilmente que todos los datos de sesión
    | deben cifrarse antes de almacenarse. Todo el cifrado se realiza
    | automáticamente por Laravel y puede usar la sesión con normalidad.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false),

    /*
    |--------------------------------------------------------------------------
    | Ubicación de Archivos de Sesión
    |--------------------------------------------------------------------------
    |
    | Al utilizar el controlador de sesión "file", los archivos de sesión se guardan
    | en disco. La ubicación de almacenamiento predeterminada se define aquí; sin embargo,
    | puede proporcionar otra ubicación donde deberían almacenarse.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Conexión de Base de Datos para Sesión
    |--------------------------------------------------------------------------
    |
    | Al usar los controladores de sesión "database" o "redis", puede especificar una
    | conexión que se usará para gestionar estas sesiones. Esto debe
    | corresponder a una conexión en las opciones de configuración de su base de datos.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Tabla de Base de Datos para Sesión
    |--------------------------------------------------------------------------
    |
    | Al usar el controlador de sesión "database", puede especificar la tabla
    | que se usará para almacenar las sesiones. Por supuesto, se define un valor
    | predeterminado razonable; sin embargo, puede cambiarlo a otra tabla.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Almacén de Caché para Sesión
    |--------------------------------------------------------------------------
    |
    | Al usar uno de los backends de sesión basados en caché del framework, puede
    | definir el almacén de caché que se usará para guardar los datos de sesión
    | entre solicitudes. Debe coincidir con uno de los almacenes de caché definidos.
    |
    | Afecta: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Lotería de Limpieza de Sesión
    |--------------------------------------------------------------------------
    |
    | Algunos controladores de sesión deben limpiar manualmente su almacenamiento
    | para eliminar sesiones antiguas. Aquí se definen las probabilidades de que esto
    | ocurra en una solicitud determinada. Por defecto, son 2 de cada 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Nombre de la Cookie de Sesión
    |--------------------------------------------------------------------------
    |
    | Aquí puede cambiar el nombre de la cookie de sesión que crea el framework.
    | Normalmente no necesita cambiar este valor, ya que hacerlo no otorga
    | una mejora de seguridad significativa.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Ruta de la Cookie de Sesión
    |--------------------------------------------------------------------------
    |
    | La ruta de la cookie de sesión determina la ruta para la cual la cookie
    | estará disponible. Normalmente será la ruta raíz de su aplicación,
    | pero puede cambiarla cuando sea necesario.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Dominio de la Cookie de Sesión
    |--------------------------------------------------------------------------
    |
    | Este valor determina el dominio y subdominios en los que la cookie de sesión
    | estará disponible. Por defecto, la cookie estará disponible para el dominio raíz
    | y todos los subdominios. Normalmente, esto no debería cambiarse.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Cookies Solo por HTTPS
    |--------------------------------------------------------------------------
    |
    | Al establecer esta opción en true, las cookies de sesión solo se enviarán
    | al servidor si el navegador tiene una conexión HTTPS. Esto evitará que
    | la cookie se envíe cuando no pueda hacerse de forma segura.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | Solo Acceso HTTP
    |--------------------------------------------------------------------------
    |
    | Establecer este valor en true impedirá que JavaScript acceda al valor
    | de la cookie, y solo será accesible mediante el protocolo HTTP.
    | Es poco probable que necesite deshabilitar esta opción.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Cookies Same-Site
    |--------------------------------------------------------------------------
    |
    | Esta opción determina cómo se comportan sus cookies en solicitudes entre sitios,
    | y puede usarse para mitigar ataques CSRF. Por defecto, se establece
    | en "lax" para permitir solicitudes seguras entre sitios.
    |
    | Ver: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Compatibles: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Cookies Particionadas
    |--------------------------------------------------------------------------
    |
    | Establecer este valor en true vinculará la cookie al sitio de nivel superior
    | en un contexto entre sitios. El navegador acepta las cookies particionadas
    | cuando están marcadas como "secure" y el atributo Same-Site es "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];

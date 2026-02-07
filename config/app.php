<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    |Este valor es el nombre de su aplicación, que se utilizará cuando el
    | marco necesite colocar el nombre de la aplicación en una notificación u
    | otros elementos de la interfaz de usuario donde sea necesario mostrar el nombre de la aplicación.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    | Este valor determina el «entorno» en el que se está ejecutando actualmente su aplicación.
    | Esto puede determinar cómo prefiere configurar los distintos
    | servicios que utiliza la aplicación. Configúrelo en su archivo «.env».
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | Cuando su aplicación está en modo de depuración, se mostrarán mensajes de error detallados con
    | trazas de pila en cada error que se produzca dentro de su
    | aplicación. Si se desactiva, se muestra una página de error genérica simple.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | La consola utiliza esta URL para generar correctamente las URL cuando se utiliza
    | la herramienta de línea de comandos Artisan. Debe establecerla en la raíz de
    | la aplicación para que esté disponible en los comandos de Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Aquí puede especificar la zona horaria predeterminada para su aplicación, que
    | será utilizada por las funciones de fecha y hora de PHP. La zona horaria
    | está configurada en «UTC» de forma predeterminada, ya que es adecuada para la mayoría de los casos de uso.
    |
    */

    'timezone' => 'America/Bogota', // para colombia

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | La configuración regional de la aplicación determina la configuración regional predeterminada que utilizarán
    | los métodos de traducción/localización de Laravel. Esta opción se puede
    | establecer en cualquier configuración regional para la que se prevea disponer de cadenas de traducción.
    |
    */

    'locale' => env('APP_LOCALE', 'es'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'es'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'es_CO'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Esta clave es utilizada por los servicios de cifrado de Laravel y debe establecerse
    | en una cadena aleatoria de 32 caracteres para garantizar que todos los valores cifrados
    | sean seguros. Debe hacerlo antes de implementar la aplicación.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | Estas opciones de configuración determinan el controlador utilizado para determinar y
    | gestionar el estado del «modo de mantenimiento» de Laravel. El controlador «cache»
    | permitirá controlar el modo de mantenimiento en varias máquinas.
    |
    | Controladores compatibles: «file», «cache»
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];

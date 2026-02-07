<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Esta opción define la autenticación predeterminada «guard» y el restablecimiento de contraseña «broker»
    | para su aplicación. Puede cambiar estos valores
    | según sea necesario, pero son un punto de partida perfecto para la mayoría de las aplicaciones.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | A continuación, puede definir cada guardia de autenticación para su aplicación.
    | Por supuesto, se ha definido una excelente configuración predeterminada para usted,
    | que utiliza el almacenamiento de sesiones y el proveedor de usuarios Eloquent.
    |
    | Todos los guardias de autenticación tienen un proveedor de usuarios, que define cómo se
    | recuperan realmente los usuarios de su base de datos u otro sistema de almacenamiento
    | utilizado por la aplicación. Normalmente, se utiliza Eloquent.
    |
    | Compatible: «sesión»
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Todos los guardias de autenticación tienen un proveedor de usuarios, que define cómo se
    | recuperan realmente los usuarios de su base de datos u otro sistema de almacenamiento
    | utilizado por la aplicación. Normalmente, se utiliza Eloquent.
    |
    | Si tienes varias tablas o modelos de usuarios, puedes configurar varios
    | proveedores para representar el modelo/tabla. Estos proveedores pueden
    | asignarse a cualquier guardia de autenticación adicional que hayas definido.
    |
    | Compatible: «database», «eloquent»
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\Usuario::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Estas opciones de configuración especifican el comportamiento de la funcionalidad de restablecimiento de contraseña de Laravel,
    | incluyendo la tabla utilizada para el almacenamiento de tokens
    | y el proveedor de usuarios que se invoca para recuperar realmente a los usuarios.
    |
    | El tiempo de caducidad es el número de minutos durante los que cada token de restablecimiento se
    | considerará válido. Esta característica de seguridad mantiene los tokens con una vida útil corta para que
    | tengan menos tiempo para ser adivinados. Puede cambiar esto según sea necesario.
    |
    | La configuración de limitación es el número de segundos que un usuario debe esperar antes de
    | generar más tokens de restablecimiento de contraseña. Esto evita que el usuario
    | genere rápidamente una gran cantidad de tokens de restablecimiento de contraseña.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Aquí puede definir el número de segundos antes de que expire la ventana de confirmación de contraseña
    | y se pida a los usuarios que vuelvan a introducir su contraseña a través de la
    | pantalla de confirmación. Por defecto, el tiempo de espera es de tres horas.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];

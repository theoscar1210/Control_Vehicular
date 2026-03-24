<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nombre de Conexión de Cola Predeterminada
    |--------------------------------------------------------------------------
    |
    | Las colas de Laravel admiten una variedad de backends mediante una única
    | API unificada, proporcionando acceso conveniente a cada backend usando
    | la misma sintaxis. La conexión de cola predeterminada se define a continuación.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Conexiones de Cola
    |--------------------------------------------------------------------------
    |
    | Aquí puede configurar las opciones de conexión para cada backend de cola
    | utilizado por su aplicación. Se proporciona una configuración de ejemplo para
    | cada backend compatible con Laravel. También puede agregar más según necesite.
    |
    | Controladores: "sync", "database", "beanstalkd", "sqs", "redis", "failover", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],

        'failover' => [
            'driver' => 'failover',
            'connections' => [
                'database',
                'sync',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Procesamiento por Lotes de Trabajos
    |--------------------------------------------------------------------------
    |
    | Las siguientes opciones configuran la base de datos y la tabla que almacenan
    | la información de procesamiento por lotes de trabajos. Estas opciones pueden
    | actualizarse a cualquier conexión de base de datos y tabla definida por su aplicación.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Trabajos de Cola Fallidos
    |--------------------------------------------------------------------------
    |
    | Estas opciones configuran el comportamiento del registro de trabajos de cola fallidos,
    | para controlar cómo y dónde se almacenan los trabajos fallidos. Laravel incluye
    | soporte para almacenar trabajos fallidos en un archivo simple o en una base de datos.
    |
    | Controladores compatibles: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];

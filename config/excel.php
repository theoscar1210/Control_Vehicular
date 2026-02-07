<?php

use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

return [
    'exports' => [

        /*
        |--------------------------------------------------------------------------
        | Chunk size
        |--------------------------------------------------------------------------
        |
        | Al utilizar FromQuery, la consulta se divide automáticamente en fragmentos.
        | Aquí puede especificar el tamaño que deben tener los fragmentos.
        |
        */
        'chunk_size'             => 1000,

        /*
        |--------------------------------------------------------------------------
        | Precalcular fórmulas durante la exportación
        |--------------------------------------------------------------------------
        */
        'pre_calculate_formulas' => false,

        /*
        |--------------------------------------------------------------------------
        | Habilitar comparación estricta de valores nulos
        |--------------------------------------------------------------------------
        |
        | Al habilitar la comparación estricta de valores nulos, se añadirán celdas vacías (“”)
        | a la hoja.
        */
        'strict_null_comparison' => false,

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        |
        | Configure, por ejemplo, el delimitador, el recinto y el final de línea para las exportaciones CSV.
        |
        */
        'csv'                    => [
            'delimiter'              => ',',
            'enclosure'              => '"',
            'line_ending'            => PHP_EOL,
            'use_bom'                => false,
            'include_separator_line' => false,
            'excel_compatibility'    => false,
            'output_encoding'        => '',
            'test_auto_detect'       => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Worksheet properties
        |--------------------------------------------------------------------------
        |
        | Configurar, por ejemplo, título predeterminado, creador, asunto.,...
        |
        */
        'properties'             => [
            'creator'        => '',
            'lastModifiedBy' => '',
            'title'          => '',
            'description'    => '',
            'subject'        => '',
            'keywords'       => '',
            'category'       => '',
            'manager'        => '',
            'company'        => '',
        ],
    ],

    'imports'            => [

        /*
        |--------------------------------------------------------------------------
        | Read Only
        |--------------------------------------------------------------------------
        |
        | Al trabajar con importaciones, es posible que solo le interesen los
        | datos que contiene la hoja. Por defecto, ignoramos todos los estilos,
        | pero si desea aplicar alguna lógica basada en los datos de estilo,
        | puede habilitarla estableciendo read_only en false.
        |
        */
        'read_only'    => true,

        /*
        |--------------------------------------------------------------------------
        | Ignore Empty
        |--------------------------------------------------------------------------
        |
        | Al trabajar con importaciones, es posible que le interese ignorar
        | las filas que contengan valores nulos o cadenas vacías. Por defecto, las filas
        | que contienen cadenas vacías o valores vacíos no se ignoran, pero se pueden
        | ignorar activando la configuración ignore_empty a true.
        |
        */
        'ignore_empty' => false,

        /*
        |--------------------------------------------------------------------------
        | Heading Row Formatter
        |--------------------------------------------------------------------------
        |
        | Configurar el formateador de la fila de encabezado.
        | Opciones disponibles: ninguna|slug|personalizada
        |
        */
        'heading_row'  => [
            'formatter' => 'slug',
        ],

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        |
        | Configure, por ejemplo, el delimitador, el recinto y el final de línea para las importaciones CSV.
        |
        */
        'csv'          => [
            'delimiter'        => null,
            'enclosure'        => '"',
            'escape_character' => '\\',
            'contiguous'       => false,
            'input_encoding'   => Csv::GUESS_ENCODING,
        ],

        /*
        |--------------------------------------------------------------------------
        | Worksheet properties
        |--------------------------------------------------------------------------
        |
        | Configure e.g. default title, creator, subject,...
        |
        */
        'properties'   => [
            'creator'        => '',
            'lastModifiedBy' => '',
            'title'          => '',
            'description'    => '',
            'subject'        => '',
            'keywords'       => '',
            'category'       => '',
            'manager'        => '',
            'company'        => '',
        ],

        /*
       |--------------------------------------------------------------------------
       | Cell Middleware
       |--------------------------------------------------------------------------
       |Configurar el middleware que se ejecuta al obtener el valor de una celda.
       | 
       |
       */
        'cells'        => [
            'middleware' => [
                //\Maatwebsite\Excel\Middleware\TrimCellValue::class,
                //\Maatwebsite\Excel\Middleware\ConvertEmptyCellValuesToNull::class,
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Extension detector
    |--------------------------------------------------------------------------
    |
    | Configure aquí qué tipo de escritor/lector debe utilizarse cuando el paquete
    | necesita adivinar el tipo correcto basándose únicamente en la extensión.
    |
    */
    'extension_detector' => [
        'xlsx'     => Excel::XLSX,
        'xlsm'     => Excel::XLSX,
        'xltx'     => Excel::XLSX,
        'xltm'     => Excel::XLSX,
        'xls'      => Excel::XLS,
        'xlt'      => Excel::XLS,
        'ods'      => Excel::ODS,
        'ots'      => Excel::ODS,
        'slk'      => Excel::SLK,
        'xml'      => Excel::XML,
        'gnumeric' => Excel::GNUMERIC,
        'htm'      => Excel::HTML,
        'html'     => Excel::HTML,
        'csv'      => Excel::CSV,
        'tsv'      => Excel::TSV,

        /*
        |--------------------------------------------------------------------------
        | PDF Extension
        |--------------------------------------------------------------------------
        |
        | Configure here which Pdf driver should be used by default.
        | Available options: Excel::MPDF | Excel::TCPDF | Excel::DOMPDF
        |
        */
        'pdf'      => Excel::DOMPDF,
    ],

    /*
    |--------------------------------------------------------------------------
    | Value Binder
    |--------------------------------------------------------------------------
    |
    | PhpSpreadsheet ofrece una forma de conectarse al proceso de escritura de un valor
    | en una celda. En él se hacen algunas suposiciones sobre cómo
    | debe formatearse el valor. Si desea cambiar esos valores predeterminados,
    | puede implementar su propio enlazador de valores predeterminados.
    |
    | Possible value binders:
    |
    | [x] Maatwebsite\Excel\DefaultValueBinder::class
    | [x] PhpOffice\PhpSpreadsheet\Cell\StringValueBinder::class
    | [x] PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder::class
    |
    */
    'value_binder'       => [
        'default' => Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    'cache'        => [
        /*
        |--------------------------------------------------------------------------
        | Default cell caching driver
        |--------------------------------------------------------------------------
        |
        | Por defecto, PhpSpreadsheet guarda todos los valores de las celdas en la memoria, sin embargo, cuando
        | se trata de archivos grandes, esto puede provocar problemas de memoria. Si
        | desea mitigar esto, puede configurar aquí un controlador de almacenamiento en caché de celdas.
        | Al utilizar el controlador illuminate, este almacenará cada valor en el
        | almacén de caché. Esto puede ralentizar el proceso, ya que es necesario
        | almacenar cada valor. Puede utilizar el almacén «batch» si desea
        | conservar solo el almacén cuando se alcance el límite de memoria.
        |
        | Controladores: memory|illuminate|batch
        |
        */
        'driver'      => 'memory',

        /*
        |--------------------------------------------------------------------------
        | Batch memory caching
        |--------------------------------------------------------------------------
        |
        | Cuando se trata del controlador de almacenamiento en caché «por lotes», solo
        | persistirá en el almacén cuando se alcance el límite de memoria.
        | Aquí puede ajustar el límite de memoria a su gusto.
        |
        */
        'batch'       => [
            'memory_limit' => 60000,
        ],

        /*
        |--------------------------------------------------------------------------
        | Illuminate cache
        |--------------------------------------------------------------------------
        |
        | Al utilizar el controlador de caché «illuminate», se utilizará automáticamente
        | su almacén de caché predeterminado. Sin embargo, si prefiere tener la caché de celdas
        | en un almacén independiente, puede configurar aquí el nombre del almacén.
        | Puede utilizar cualquier almacén definido en su configuración de caché. Si lo deja
        | en «null», se utilizará el almacén predeterminado.
        |
        */
        'illuminate'  => [
            'store' => null,
        ],

        /*
        |--------------------------------------------------------------------------
        | Cache Time-to-live (TTL)
        |--------------------------------------------------------------------------
        |
        | El TTL de los elementos escritos en la caché. Si desea mantener los elementos almacenados en caché
        | indefinidamente, establezca este valor en nulo.  De lo contrario, establezca un número de segundos,
        | un \DateInterval o un callable.
        |
        | Tipos permitidos: callable|\DateInterval|int|null
        |
         */
        'default_ttl' => 10800,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gestor de transacciones
    |--------------------------------------------------------------------------
    |
    | De forma predeterminada, la importación se incluye en una transacción. Esto resulta útil
    | cuando una importación puede fallar y se desea volver a intentarla. Con las
    | transacciones, la importación anterior se revierte.
    |
    | Puede desactivar el controlador de transacciones estableciendo este valor en nulo.
    | O bien, puede elegir aquí un controlador de transacciones personalizado.
    |
    | Supported handlers: null|db
    |
    */
    'transactions' => [
        'handler' => 'db',
        'db'      => [
            'connection' => null,
        ],
    ],

    'temporary_files' => [

        /*
        |--------------------------------------------------------------------------
        | Ruta temporal local
        |--------------------------------------------------------------------------
        |
        | Al exportar e importar archivos, utilizamos un archivo temporal, antes de
        | almacenar, leer o descargar. Aquí puede personalizar esa ruta.
        | permissions es una matriz con los indicadores de permiso para el directorio (dir)
        | y el archivo creado (file).
        |
        */
        'local_path'          => storage_path('framework/cache/laravel-excel'),

        /*
        |--------------------------------------------------------------------------
        | Permisos de ruta temporal local
        |--------------------------------------------------------------------------
        |
        | Permisos es una matriz con los indicadores de permiso para el directorio (dir)
        | y el archivo creado (file).
        | Si se omite, se utilizarán los permisos predeterminados del sistema de archivos.
        |
        */
        'local_permissions'   => [
            // 'dir'  => 0755,
            // 'file' => 0644,
        ],

        /*
        |--------------------------------------------------------------------------
        | Disco temporal remoto
        |--------------------------------------------------------------------------
        |
        | Cuando se trata de una configuración de varios servidores con colas en la que
        | no se puede confiar en tener una ruta temporal local compartida, es posible que
        | desee almacenar el archivo temporal en un disco compartido. Durante la
        | ejecución de la cola, recuperaremos el archivo temporal de esa
        | ubicación. Si se deja en nulo, siempre se utilizará
        | la ruta local. Esta configuración solo tiene efecto cuando se utiliza
        | junto con importaciones y exportaciones en cola.
        |
        */
        'remote_disk'         => null,
        'remote_prefix'       => null,

        /*
        |--------------------------------------------------------------------------
        | Forzar resincronización
        |--------------------------------------------------------------------------
        |
        | Cuando se trata de una configuración con varios servidores como la anterior, es posible
        | que la limpieza que se produce después de que se haya ejecutado toda la cola solo
        | limpie el servidor en el que se ejecuta el último AfterImportJob. El resto de los servidores
        | seguirían teniendo el archivo temporal local almacenado en ellos. En este caso, se podrían
        | superar los límites de almacenamiento local y no se procesarían las importaciones futuras.
        | Para mitigar esto, puede establecer este valor de configuración en verdadero, de modo que después de que se procese cada
        | fragmento en cola, el archivo temporal local se elimine del servidor que
        | lo procesó.
        |
        */
        'force_resync_remote' => null,
    ],
];

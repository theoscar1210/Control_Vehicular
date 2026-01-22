<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes de la Aplicación
    |--------------------------------------------------------------------------
    */

    // Mensajes de éxito
    'success' => [
        'created' => ':resource creado correctamente.',
        'updated' => ':resource actualizado correctamente.',
        'deleted' => ':resource eliminado correctamente.',
        'saved' => 'Cambios guardados correctamente.',
        'restored' => ':resource restaurado correctamente.',
    ],

    // Mensajes de error
    'error' => [
        'general' => 'Ha ocurrido un error. Por favor, intente de nuevo.',
        'not_found' => ':resource no encontrado.',
        'unauthorized' => 'No tiene permisos para realizar esta acción.',
        'delete_failed' => 'No se pudo eliminar :resource.',
        'save_failed' => 'No se pudieron guardar los cambios.',
    ],

    // Mensajes de confirmación
    'confirm' => [
        'delete' => '¿Está seguro de que desea eliminar este registro?',
        'action' => '¿Está seguro de que desea realizar esta acción?',
    ],

    // Recursos
    'resources' => [
        'vehiculo' => 'Vehículo',
        'propietario' => 'Propietario',
        'conductor' => 'Conductor',
        'documento' => 'Documento',
        'usuario' => 'Usuario',
        'alerta' => 'Alerta',
    ],

];

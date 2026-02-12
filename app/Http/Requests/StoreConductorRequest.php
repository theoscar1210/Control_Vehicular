<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConductorRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'tipo_doc' => ['required', Rule::in(['CC', 'CE'])],
            'identificacion' => 'required|string|max:50|unique:conductores,identificacion',
            'telefono' => 'nullable|string|max:30',
            'telefono_emergencia' => 'nullable|string|max:30',
            'activo' => 'nullable|boolean',
            'clasificacion' => 'nullable|in:EMPLEADO,EXTERNO,CONTRATISTA',
            'observaciones' => 'nullable|string|max:1000',
            'id_vehiculo' => 'nullable|integer|exists:vehiculos,id_vehiculo',

            'documento_tipo' => 'nullable|string|in:Licencia Conducción,Certificado Médico,ARL,EPS,Otro',
            'documento_numero' => 'nullable|string|max:50',
            'documento_fecha_emision' => 'nullable|date',
            'documento_fecha_vencimiento' => 'nullable|date|after_or_equal:documento_fecha_emision',
            'entidad_emisora' => 'nullable|string|max:100',

            'categoria_licencia' => 'nullable|string|in:A1,A2,B1,B2,B3,C1,C2,C3',
            'categorias_adicionales' => 'nullable|array',
            'categorias_adicionales.*' => 'string|in:A1,A2,B1,B2,B3,C1,C2,C3',

            'fechas_categoria' => 'nullable|array',
            'fechas_categoria.*.fecha_vencimiento' => 'nullable|date',

            'categorias_monitoreadas' => 'nullable|array',
            'categorias_monitoreadas.*' => 'string|in:A1,A2,B1,B2,B3,C1,C2,C3',
        ];
    }
}

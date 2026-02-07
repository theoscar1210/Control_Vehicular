<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConductorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $conductor = $this->route('conductor');

        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'tipo_doc' => ['required', Rule::in(['CC', 'CE'])],
            'identificacion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('conductores', 'identificacion')->ignore($conductor->id_conductor, 'id_conductor'),
            ],
            'telefono' => 'nullable|string|max:30',
            'telefono_emergencia' => 'nullable|string|max:30',
            'activo' => 'nullable|boolean',
            'id_vehiculo' => 'nullable|integer|exists:vehiculos,id_vehiculo',

            'documento_action' => ['nullable', Rule::in(['none', 'update_existing', 'create_version'])],
            'documento_id' => 'nullable|integer|exists:documentos_conductor,id_doc_conductor',
            'documento_tipo' => 'nullable|string|max:100',
            'documento_numero' => 'nullable|string|max:100',
            'documento_fecha_emision' => 'nullable|date',
            'documento_fecha_vencimiento' => 'nullable|date|after_or_equal:documento_fecha_emision',
            'categoria_licencia' => 'nullable|string|in:A1,A2,B1,B2,B3,C1,C2,C3',

            'categorias_monitoreadas' => 'nullable|array',
            'categorias_monitoreadas.*' => 'string|in:A1,A2,B1,B2,B3,C1,C2,C3',
        ];
    }
}

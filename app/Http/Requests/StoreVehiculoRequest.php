<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehiculoRequest extends FormRequest
{
    /**
     * Solo usuarios autenticados con rol ADMIN o SST pueden registrar vehículos.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->rol, ['ADMIN', 'SST']);
    }

    /**
     * Obtener las reglas de validación que aplican a la solicitud.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'placa' => 'required|string|max:10|unique:vehiculos,placa',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'color' => 'required|string|max:30',
            'tipo' => 'required|in:CARRO,MOTO',
            'id_propietario' => 'required|exists:propietarios,id_propietario',
            'clasificacion' => 'nullable|in:EMPLEADO,EXTERNO,CONTRATISTA',
            'observaciones' => 'nullable|string|max:1000',
        ];
    }
}

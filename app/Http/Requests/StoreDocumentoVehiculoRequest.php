<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoVehiculoRequest extends FormRequest
{
    private array $documentosConVencimiento = [
        'SOAT',
        'TECNOMECANICA',
        'POLIZA'
    ];

    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool true si el usuario está autorizado, false en caso contrario.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Devuelve un array con las reglas de validación para la solicitud.
     * 
     * Las reglas de validación son las siguientes:
     * 
     * - tipo_documento: required, in:SOAT,TECNOMECANICA,TARJETA PROPIEDAD,POLIZA
     * - numero_documento: required, string, max:50
     * - entidad_emisora: nullable, string, max:100
     * - nota: nullable, string, max:255
     * 
     * Si el tipo de documento es alguno de los que necesitan vencimiento,
     * se agrega la regla de fecha_emision como required|date.
     * 
     * Si el tipo de documento es TARJETA PROPIEDAD, se agrega la regla de
     * fecha_matricula como required|date|before_or_equal:today.
     * 
     * @return array Las reglas de validación.
     */
    public function rules(): array
    {
        $rules = [
            'tipo_documento' => 'required|in:SOAT,TECNOMECANICA,TARJETA PROPIEDAD,POLIZA',
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora' => 'nullable|string|max:100',
            'nota' => 'nullable|string|max:255',
        ];

        if (in_array($this->tipo_documento, $this->documentosConVencimiento)) {
            $rules['fecha_emision'] = 'required|date';
        } else {
            $rules['fecha_emision'] = 'nullable|date';
        }

        if ($this->tipo_documento === 'TARJETA PROPIEDAD') {
            $rules['fecha_matricula'] = 'required|date|before_or_equal:today';
        }

        return $rules;
    }
}

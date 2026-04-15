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
     * Solo usuarios autenticados con rol ADMIN o SST pueden registrar documentos de vehículo.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->rol, ['ADMIN', 'SST']);
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

        // Validar archivo: solo formatos seguros, sin tipos ejecutables ni documentos con macros
        $rules['archivo'] = 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'archivo.mimes' => 'Solo se permiten archivos PDF, JPG o PNG.',
            'archivo.max'   => 'El archivo no puede superar los 10 MB.',
        ];
    }
}

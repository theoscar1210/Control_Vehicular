<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoVehiculoRequest extends FormRequest
{
    private array $documentosConVencimiento = [
        'SOAT',
        'Tecnomecanica',
        'Poliza_Seguro'
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'tipo_documento' => 'required|in:SOAT,Tecnomecanica,Tarjeta Propiedad,Poliza_Seguro',
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora' => 'nullable|string|max:100',
            'nota' => 'nullable|string|max:255',
        ];

        if (in_array($this->tipo_documento, $this->documentosConVencimiento)) {
            $rules['fecha_emision'] = 'required|date';
        } else {
            $rules['fecha_emision'] = 'nullable|date';
        }

        if ($this->tipo_documento === 'Tarjeta Propiedad') {
            $rules['fecha_matricula'] = 'required|date|before_or_equal:today';
        }

        return $rules;
    }
}

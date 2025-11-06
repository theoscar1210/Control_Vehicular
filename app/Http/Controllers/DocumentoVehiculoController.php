<?php

namespace App\Http\Controllers;

use App\Models\DocumentoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentoVehiculoController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_vehiculo' => 'required|exists:vehiculos,id_vehiculo',
                'tipo_documento' => 'required|in:SOAT,Tecnomecanica,Tarjeta Propiedad,Póliza,Otro',
                'numero_documento' => 'required|string|max:50',
                'entidad_emisora' => 'nullable|string|max:100',
                'fecha_emision' => 'nullable|date',
                'fecha_vencimiento' => 'nullable|date',
                'estado' => 'nullable|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            ]);

            DocumentoVehiculo::create($validated);

            return redirect()->back()->with('success', 'Documento guardado correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al guardar documento: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar documento.');
        }
    }
}

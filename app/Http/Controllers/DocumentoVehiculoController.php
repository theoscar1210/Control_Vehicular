<?php

namespace App\Http\Controllers;

use App\Models\DocumentoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


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

    /**
     * Mostrar formulario de actualización de documentos
     */
    public function edit($id)
    {
        $vehiculo = Vehiculo::with(['documentosConductor', 'conductor.documentosConductor', 'propietario'])->findOrFail($id);
        return view('vehiculos.edit', compact('vehiculo'));
    }

    /**
     * Actualizar los datos de un documento existente
     */
    public function update(Request $request, $id)
    {
        try {
            $doc = DocumentoVehiculo::findOrFail($id);

            $validated = $request->validate([
                'numero_documento' => 'required|string|max:50',
                'entidad_emisora' => 'nullable|string|max:100',
                'fecha_emision' => 'nullable|date',
                'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            ]);

            $doc->update($validated);
            return redirect()->back()->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al actualizar documento: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar documento.');
        }
    }
}

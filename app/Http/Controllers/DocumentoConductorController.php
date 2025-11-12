<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conductor;
use App\Models\DocumentoConductor;
use Carbon\Carbon;

class DocumentoConductorController extends Controller
{
    /**
     * Mostrar formulario para crear documento de un conductor.
     * Route-model binding inyecta el Conductor.
     */
    public function create(Conductor $conductor)
    {
        return view('documentos_conductor.create', compact('conductor'));
    }

    /**
     * Guardar documento asociado al conductor (sin archivos).
     */
    public function store(Request $request, Conductor $conductor)
    {
        $data = $request->validate([
            'tipo_documento'    => 'required|string|max:100',
            'numero_documento'  => 'required|string|max:100',
            'entidad_emisora'   => 'nullable|string|max:150',
            'fecha_emision'     => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        // Asignar id_conductor explícitamente
        $data['id_conductor'] = $conductor->id_conductor;

        // Determinar estado según fecha_vencimiento
        if (!empty($data['fecha_vencimiento'])) {
            $vto = Carbon::parse($data['fecha_vencimiento']);
            if ($vto->isPast()) {
                $data['estado'] = 'VENCIDO';
            } elseif ($vto->diffInDays(Carbon::today()) <= 30) {
                $data['estado'] = 'POR_VENCER';
            } else {
                $data['estado'] = 'VIGENTE';
            }
        } else {
            $data['estado'] = 'VIGENTE';
        }

        DocumentoConductor::create($data);

        return redirect()->route('conductores.show', $conductor->id_conductor)
            ->with('success', 'Documento guardado correctamente.');
    }

    /**
     * Mostrar formulario de edición de documento (opcional).
     */
    public function edit($id)
    {
        $doc = DocumentoConductor::findOrFail($id);
        // Pasamos también el conductor por conveniencia en la vista de edición
        $conductor = $doc->conductor;
        return view('documentos_conductor.edit', compact('doc', 'conductor'));
    }

    /**
     * Actualizar documento.
     */
    public function update(Request $request, $id)
    {
        $doc = DocumentoConductor::findOrFail($id);

        $data = $request->validate([
            'tipo_documento'    => 'required|string|max:100',
            'numero_documento'  => 'required|string|max:100',
            'entidad_emisora'   => 'nullable|string|max:150',
            'fecha_emision'     => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        if (!empty($data['fecha_vencimiento'])) {
            $vto = Carbon::parse($data['fecha_vencimiento']);
            if ($vto->isPast()) {
                $data['estado'] = 'VENCIDO';
            } elseif ($vto->diffInDays(Carbon::today()) <= 30) {
                $data['estado'] = 'POR_VENCER';
            } else {
                $data['estado'] = 'VIGENTE';
            }
        } else {
            $data['estado'] = 'VIGENTE';
        }

        $doc->update($data);

        return redirect()->route('conductores.show', $doc->id_conductor)
            ->with('success', 'Documento actualizado correctamente.');
    }

    /**
     * Eliminar documento.
     */
    public function destroy($id)
    {
        $doc = DocumentoConductor::findOrFail($id);
        $conductorId = $doc->id_conductor;
        $doc->delete();

        return redirect()->route('conductores.show', $conductorId)
            ->with('success', 'Documento eliminado correctamente.');
    }
}

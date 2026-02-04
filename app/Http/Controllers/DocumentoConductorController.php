<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conductor;
use App\Models\DocumentoConductor;
use App\Models\Alerta;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DocumentoConductorController extends Controller
{
    /**
     * Mostrar historial completo de documentos del conductor.
     */
    public function historial(Conductor $conductor)
    {
        $historial = DocumentoConductor::where('id_conductor', $conductor->id_conductor)
            ->orderBy('tipo_documento')
            ->orderByDesc('version')
            ->get();

        return view('conductores.documentos.historial', compact('conductor', 'historial'));
    }

    /**
     * Renovar documento de licencia del conductor (crear nueva versión).
     */
    public function renovar(Request $request, Conductor $conductor)
    {
        $data = $request->validate([
            'documento_id' => 'required|integer|exists:documentos_conductor,id_doc_conductor',
            'numero_documento' => 'required|string|max:100',
            'entidad_emisora' => 'nullable|string|max:150',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_emision',
            'categoria_licencia' => 'nullable|string|max:10',
        ]);

        // Obtener documento actual
        $docActual = DocumentoConductor::findOrFail($data['documento_id']);

        // Verificar que pertenece al conductor
        if ($docActual->id_conductor !== $conductor->id_conductor) {
            return back()->with('error', 'El documento no pertenece a este conductor.');
        }

        // Calcular estado
        $fechaVenc = Carbon::parse($data['fecha_vencimiento']);
        $hoy = Carbon::today();
        $dias = $hoy->diffInDays($fechaVenc, false);

        if ($dias < 0) {
            $estado = 'VENCIDO';
        } elseif ($dias <= 20) {
            $estado = 'POR_VENCER';
        } else {
            $estado = 'VIGENTE';
        }

        // Crear nueva versión del documento
        $newVersion = $docActual->version + 1;

        // Preparar fechas por categoría (mantener estructura existente)
        $fechasPorCategoria = $docActual->fechas_por_categoria ?? [];
        $categoria = $data['categoria_licencia'] ?? $docActual->categoria_licencia;

        if ($categoria) {
            $fechasPorCategoria[$categoria] = [
                'fecha_vencimiento' => $data['fecha_vencimiento'],
            ];
        }

        $newDoc = DocumentoConductor::create([
            'id_conductor' => $conductor->id_conductor,
            'tipo_documento' => $docActual->tipo_documento,
            'categoria_licencia' => $categoria ?? $docActual->categoria_licencia,
            'categorias_adicionales' => $docActual->categorias_adicionales,
            'fechas_por_categoria' => $fechasPorCategoria,
            'categorias_monitoreadas' => $docActual->categorias_monitoreadas,
            'numero_documento' => $data['numero_documento'],
            'entidad_emisora' => $data['entidad_emisora'],
            'fecha_emision' => $data['fecha_emision'],
            'fecha_vencimiento' => $data['fecha_vencimiento'],
            'estado' => $estado,
            'activo' => 1,
            'creado_por' => Auth::id(),
            'version' => $newVersion,
            'fecha_registro' => now(),
        ]);

        // Marcar documento anterior como reemplazado
        $docActual->update([
            'activo' => 0,
            'reemplazado_por' => $newDoc->id_doc_conductor,
        ]);

        // Marcar alertas del documento anterior como solucionadas
        Alerta::solucionarPorDocumentoConductor($docActual->id_doc_conductor, 'DOCUMENTO_RENOVADO');

        // Generar nuevas alertas si el nuevo documento está próximo a vencer
        $newDoc->load('conductor');
        Alerta::generarAlertasDocumentoConductor($newDoc);

        return redirect()->route('conductores.documentos.historial', $conductor->id_conductor)
            ->with('success', 'Licencia renovada correctamente. Versión ' . $newVersion);
    }

    /**
     * Renovar solo una categoría específica de la licencia.
     * Crea una nueva versión del documento para mantener trazabilidad.
     */
    public function renovarCategoria(Request $request, Conductor $conductor)
    {
        $data = $request->validate([
            'documento_id' => 'required|integer|exists:documentos_conductor,id_doc_conductor',
            'categoria' => 'required|string|max:10',
            'fecha_vencimiento' => 'required|date|after:today',
            'agregar_monitoreo' => 'nullable',
        ]);

        // Obtener documento actual
        $docActual = DocumentoConductor::findOrFail($data['documento_id']);

        // Verificar que pertenece al conductor
        if ($docActual->id_conductor !== $conductor->id_conductor) {
            return back()->with('error', 'El documento no pertenece a este conductor.');
        }

        // Verificar que la categoría existe en el documento
        $todasCategorias = $docActual->todas_categorias;
        if (!in_array($data['categoria'], $todasCategorias)) {
            return back()->with('error', 'La categoría no existe en este documento.');
        }

        // Preparar fechas por categoría (copiar existentes y actualizar la renovada)
        $fechasPorCategoria = $docActual->fechas_por_categoria ?? [];
        $fechasPorCategoria[$data['categoria']] = [
            'fecha_vencimiento' => $data['fecha_vencimiento'],
        ];

        // Preparar categorías monitoreadas
        $categoriasMonitoreadas = $docActual->categorias_monitoreadas ?? [];
        if ($request->has('agregar_monitoreo')) {
            if (!in_array($data['categoria'], $categoriasMonitoreadas)) {
                $categoriasMonitoreadas[] = $data['categoria'];
            }
        } else {
            $categoriasMonitoreadas = array_filter($categoriasMonitoreadas, fn($c) => $c !== $data['categoria']);
        }
        $categoriasMonitoreadas = array_values($categoriasMonitoreadas);

        // Calcular nueva versión
        $newVersion = $docActual->version + 1;

        // Calcular fecha de vencimiento general (la más próxima de todas las categorías)
        $fechaVencimientoGeneral = null;
        foreach ($fechasPorCategoria as $cat => $info) {
            if (!empty($info['fecha_vencimiento'])) {
                $fecha = Carbon::parse($info['fecha_vencimiento']);
                if ($fechaVencimientoGeneral === null || $fecha->lt($fechaVencimientoGeneral)) {
                    $fechaVencimientoGeneral = $fecha;
                }
            }
        }

        // Crear nueva versión del documento
        $newDoc = DocumentoConductor::create([
            'id_conductor' => $conductor->id_conductor,
            'tipo_documento' => $docActual->tipo_documento,
            'categoria_licencia' => $docActual->categoria_licencia,
            'categorias_adicionales' => $docActual->categorias_adicionales,
            'fechas_por_categoria' => $fechasPorCategoria,
            'categorias_monitoreadas' => $categoriasMonitoreadas,
            'numero_documento' => $docActual->numero_documento,
            'entidad_emisora' => $docActual->entidad_emisora,
            'fecha_emision' => $docActual->fecha_emision,
            'fecha_vencimiento' => $fechaVencimientoGeneral,
            'activo' => 1,
            'creado_por' => Auth::id(),
            'version' => $newVersion,
            'nota' => 'Refrendación categoría ' . $data['categoria'],
            'fecha_registro' => now(),
        ]);

        // Marcar documento anterior como reemplazado
        $docActual->update([
            'activo' => 0,
            'reemplazado_por' => $newDoc->id_doc_conductor,
        ]);

        // Solucionar alertas del documento anterior
        Alerta::solucionarPorDocumentoConductor($docActual->id_doc_conductor, 'Categoría ' . $data['categoria'] . ' refrendada');

        // Generar nuevas alertas si el nuevo documento tiene categorías próximas a vencer
        $newDoc->load('conductor');
        Alerta::generarAlertasDocumentoConductor($newDoc);

        return redirect()->route('conductores.documentos.historial', $conductor->id_conductor)
            ->with('success', 'Categoría ' . $data['categoria'] . ' refrendada correctamente. Versión ' . $newVersion);
    }

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
            } elseif ($vto->diffInDays(Carbon::today()) <= 20) {
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
            } elseif ($vto->diffInDays(Carbon::today()) <= 20) {
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

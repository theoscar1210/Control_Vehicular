<?php

namespace App\Http\Controllers;

use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DocumentoVehiculoController extends Controller
{
    /**
     * Tipos de documentos que requieren fecha de vencimiento
     */
    private array $documentosConVencimiento = [
        'SOAT',
        'Tecnomecanica',
        'Poliza_Seguro'
    ];

    /**
     * ============================================
     * GUARDAR DOCUMENTO DE VEHÍCULO
     * ============================================
     */
    public function store(Request $request, $idVehiculo)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN DINÁMICA
        |--------------------------------------------------------------------------
        | Si el documento tiene vencimiento, fecha_emision es obligatoria
        | Si es Tarjeta Propiedad, fecha_matricula es obligatoria
        */
        $rules = [
            'tipo_documento'   => 'required|string',
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora'  => 'nullable|string|max:100',
            'nota'             => 'nullable|string|max:255',
        ];

        if (in_array($request->tipo_documento, $this->documentosConVencimiento)) {
            $rules['fecha_emision'] = 'required|date';
        } else {
            $rules['fecha_emision'] = 'nullable|date';
        }

        // Tarjeta de Propiedad requiere fecha de matrícula
        if ($request->tipo_documento === 'Tarjeta Propiedad') {
            $rules['fecha_matricula'] = 'required|date|before_or_equal:today';
        }

        $validated = $request->validate($rules);

        $vehiculo = Vehiculo::with(['propietario'])->findOrFail($idVehiculo);
        $tipo = $validated['tipo_documento'];

        try {
            $result = DB::transaction(function () use ($validated, $vehiculo, $tipo, $request) {

                /*
                |--------------------------------------------------------------------------
                | GUARDAR FECHA DE MATRÍCULA (Si es Tarjeta de Propiedad)
                |--------------------------------------------------------------------------
                */
                if ($tipo === 'Tarjeta Propiedad' && !empty($validated['fecha_matricula'])) {
                    $vehiculo->update([
                        'fecha_matricula' => Carbon::parse($validated['fecha_matricula'])->startOfDay()
                    ]);
                    // Refrescar el modelo para obtener la fecha actualizada
                    $vehiculo->refresh();
                }

                /*
                |--------------------------------------------------------------------------
                | FECHAS Y ESTADO
                |--------------------------------------------------------------------------
                */
                $fechaEmision = !empty($validated['fecha_emision'])
                    ? Carbon::parse($validated['fecha_emision'])->startOfDay()
                    : null;

                $fechaVencimiento = null;
                $estado = 'VIGENTE';

                // Solo calcular vencimiento para documentos que lo requieren
                if (in_array($tipo, $this->documentosConVencimiento) && $fechaEmision) {
                    /*
                    |--------------------------------------------------------------------------
                    | CÁLCULO ESPECIAL PARA TECNOMECÁNICA
                    |--------------------------------------------------------------------------
                    | - Vehículos nuevos (Carro): Primera revisión a los 5 años
                    | - Motos/Motocarros: Primera revisión a los 2 años
                    | - Después de la primera revisión: Renovación anual
                    */
                    if ($tipo === 'Tecnomecanica') {
                        $fechaVencimiento = $vehiculo->calcularVencimientoTecnomecanica($fechaEmision);
                    } else {
                        // SOAT y otros: vencimiento a 1 año desde emisión
                        $fechaVencimiento = $fechaEmision->copy()->addYear();
                    }

                    if ($fechaVencimiento) {
                        $dias = Carbon::today()->diffInDays($fechaVencimiento, false);
                        if ($dias < 0) {
                            $estado = 'VENCIDO';
                        } elseif ($dias <= 20) {
                            $estado = 'POR_VENCER';
                        } else {
                            $estado = 'VIGENTE';
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | OBTENER ÚLTIMA VERSIÓN DEL DOCUMENTO
                |--------------------------------------------------------------------------
                */
                $ultimoDocumento = DocumentoVehiculo::where('id_vehiculo', $vehiculo->id_vehiculo)
                    ->where('tipo_documento', $tipo)
                    ->where('estado', '!=', 'REEMPLAZADO')
                    ->orderByDesc('version')
                    ->first();

                $version = $ultimoDocumento ? $ultimoDocumento->version + 1 : 1;

                /*
                |--------------------------------------------------------------------------
                | CREAR DOCUMENTO
                |--------------------------------------------------------------------------
                */
                $nuevoDocumento = DocumentoVehiculo::create([
                    'id_vehiculo'       => $vehiculo->id_vehiculo,
                    'tipo_documento'    => $tipo,
                    'numero_documento'  => $validated['numero_documento'],
                    'entidad_emisora'   => $validated['entidad_emisora'] ?? null,
                    'fecha_emision'     => $fechaEmision,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado'            => $estado,
                    'activo'            => 1,
                    'version'           => $version,
                    'nota'              => $validated['nota'] ?? null,
                    'creado_por'        => auth()->id() ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | MARCAR DOCUMENTO ANTERIOR COMO REEMPLAZADO
                |--------------------------------------------------------------------------
                */
                if ($ultimoDocumento) {
                    $ultimoDocumento->update([
                        'estado'           => 'REEMPLAZADO',
                        'reemplazado_por'  => $nuevoDocumento->id_doc_vehiculo,
                        'activo'           => 0,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | CREAR ALERTA SI APLICA
                |--------------------------------------------------------------------------
                */
                if (in_array($estado, ['VENCIDO', 'POR_VENCER']) && $fechaVencimiento) {
                    Alerta::create([
                        'tipo_alerta'      => 'VEHICULO',
                        'id_doc_vehiculo'  => $nuevoDocumento->id_doc_vehiculo,
                        'tipo_vencimiento' => $estado === 'VENCIDO' ? 'VENCIDO' : 'PROXIMO_VENCER',
                        'mensaje'          => "Documento {$tipo} ({$nuevoDocumento->numero_documento}) vence el {$fechaVencimiento->format('d/m/Y')}",
                        'fecha_alerta'     => now()->toDateString(),
                        'leida'            => 0,
                        'visible_para'     => 'TODOS',
                        'creado_por'       => auth()->id() ?? null,
                    ]);
                }

                return [
                    'tipo' => $tipo,
                    'vehiculo_id' => $vehiculo->id_vehiculo
                ];
            });

            // Verificar si existe la ruta vehiculos.create o usar index
            if (\Route::has('vehiculos.create')) {
                return redirect()
                    ->route('vehiculos.create', ['vehiculo' => $result['vehiculo_id']])
                    ->with('success', "Documento {$result['tipo']} guardado correctamente.");
            } else {
                return redirect()
                    ->route('vehiculos.index')
                    ->with('success', "Documento {$result['tipo']} guardado correctamente.");
            }
        } catch (\Exception $e) {
            Log::error('Error al guardar documento', [
                'vehiculo' => $idVehiculo,
                'tipo_documento' => $tipo ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al guardar el documento: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * RENOVAR DOCUMENTO (CREA NUEVA VERSIÓN)
     * ============================================
     */
    public function update(Request $request, $idVehiculo, $idDocumento)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);
        $documentoAnterior = DocumentoVehiculo::where('id_doc_vehiculo', $idDocumento)
            ->where('id_vehiculo', $vehiculo->id_vehiculo)
            ->firstOrFail();

        // Validar que el documento requiera renovación
        if (!in_array($documentoAnterior->tipo_documento, $this->documentosConVencimiento)) {
            return back()->with(
                'error',
                'Este tipo de documento no requiere renovación.'
            );
        }

        if (!in_array($documentoAnterior->estado, ['VENCIDO', 'POR_VENCER'])) {
            return back()->with(
                'error',
                'Solo se pueden renovar documentos vencidos o próximos a vencer.'
            );
        }

        $validated = $request->validate([
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora'  => 'nullable|string|max:100',
            'fecha_emision'    => 'required|date',
            'nota'             => 'nullable|string|max:255',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $documentoAnterior, $vehiculo) {

                /*
                |--------------------------------------------------------------------------
                | CALCULAR FECHAS Y ESTADO
                |--------------------------------------------------------------------------
                */
                $fechaEmision = Carbon::parse($validated['fecha_emision'])->startOfDay();
                $fechaVencimiento = null;
                $estado = 'VIGENTE';

                if (in_array($documentoAnterior->tipo_documento, $this->documentosConVencimiento)) {
                    /*
                    |--------------------------------------------------------------------------
                    | CÁLCULO ESPECIAL PARA TECNOMECÁNICA (Renovación)
                    |--------------------------------------------------------------------------
                    | En renovaciones siempre es anual porque ya pasó la primera revisión
                    */
                    if ($documentoAnterior->tipo_documento === 'Tecnomecanica') {
                        // Para renovaciones, usar el método del vehículo que considera la lógica
                        $fechaVencimiento = $vehiculo->calcularVencimientoTecnomecanica($fechaEmision);
                    } else {
                        $fechaVencimiento = $fechaEmision->copy()->addYear();
                    }

                    if ($fechaVencimiento) {
                        $dias = Carbon::today()->diffInDays($fechaVencimiento, false);

                        if ($dias < 0) {
                            $estado = 'VENCIDO';
                        } elseif ($dias <= 20) {
                            $estado = 'POR_VENCER';
                        } else {
                            $estado = 'VIGENTE';
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | CREAR NUEVA VERSIÓN
                |--------------------------------------------------------------------------
                */
                $nuevoDocumento = DocumentoVehiculo::create([
                    'id_vehiculo'       => $vehiculo->id_vehiculo,
                    'tipo_documento'    => $documentoAnterior->tipo_documento,
                    'numero_documento'  => $validated['numero_documento'],
                    'entidad_emisora'   => $validated['entidad_emisora'] ?? null,
                    'fecha_emision'     => $fechaEmision,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado'            => $estado,
                    'activo'            => 1,
                    'version'           => $documentoAnterior->version + 1,
                    'nota'              => $validated['nota'] ?? null,
                    'creado_por'        => auth()->id() ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | MARCAR DOCUMENTO ANTERIOR COMO REEMPLAZADO
                |--------------------------------------------------------------------------
                */
                $documentoAnterior->update([
                    'estado' => 'REEMPLAZADO',
                    'reemplazado_por' => $nuevoDocumento->id_doc_vehiculo,
                    'activo' => 0
                ]);

                /*
                |--------------------------------------------------------------------------
                | MARCAR ALERTAS ANTERIORES COMO LEÍDAS (SIN updated_at)
                |--------------------------------------------------------------------------
                */
                // CORRECCIÓN: No intentar actualizar updated_at
                Alerta::where('id_doc_vehiculo', $documentoAnterior->id_doc_vehiculo)
                    ->where('leida', 0)
                    ->update(['leida' => 1]); // ✅ Sin updated_at

                /*
                |--------------------------------------------------------------------------
                | CREAR NUEVA ALERTA SI APLICA
                |--------------------------------------------------------------------------
                */
                if (in_array($estado, ['VENCIDO', 'POR_VENCER']) && $fechaVencimiento) {
                    Alerta::create([
                        'tipo_alerta'      => 'VEHICULO',
                        'id_doc_vehiculo'  => $nuevoDocumento->id_doc_vehiculo,
                        'tipo_vencimiento' => $estado === 'VENCIDO' ? 'VENCIDO' : 'PROXIMO_VENCER',
                        'mensaje'          => "Documento {$nuevoDocumento->tipo_documento} vence el {$fechaVencimiento->format('d/m/Y')}",
                        'fecha_alerta'     => now()->toDateString(),
                        'leida'            => 0,
                        'visible_para'     => 'TODOS',
                        'creado_por'       => auth()->id() ?? null,
                    ]);
                }

                return [
                    'tipo' => $nuevoDocumento->tipo_documento,
                    'vehiculo_id' => $vehiculo->id_vehiculo
                ];
            });

            // Redirigir al historial del vehículo
            if (\Route::has('vehiculos.documentos.historial.completo')) {
                return redirect()
                    ->route('vehiculos.documentos.historial.completo', $result['vehiculo_id'])
                    ->with('success', "¡Documento {$result['tipo']} renovado correctamente!");
            } else {
                return redirect()
                    ->route('vehiculos.index')
                    ->with('success', "¡Documento {$result['tipo']} renovado correctamente!");
            }
        } catch (\Exception $e) {
            Log::error('Error al renovar documento', [
                'documento' => $idDocumento,
                'vehiculo' => $idVehiculo,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al renovar el documento: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * FORMULARIO DE EDICIÓN/RENOVACIÓN
     * ============================================
     */
    public function edit($idVehiculo, $idDocumento)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);

        $documento = DocumentoVehiculo::where('id_doc_vehiculo', $idDocumento)
            ->where('id_vehiculo', $vehiculo->id_vehiculo)
            ->firstOrFail();

        $nuevaVersion = $documento->version + 1;

        return view('vehiculos.documentos.edit', compact('vehiculo', 'documento', 'nuevaVersion'));
    }

    /**
     * ============================================
     * HISTORIAL COMPLETO DE DOCUMENTOS DEL VEHÍCULO
     * ============================================
     */
    public function historial($idVehiculo, $tipoDocumento = null)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);

        // Si se especifica un tipo, filtrar por ese tipo
        // Si no, traer todos los documentos
        $query = DocumentoVehiculo::where('id_vehiculo', $vehiculo->id_vehiculo)
            ->with('creador');

        if ($tipoDocumento) {
            $query->where('tipo_documento', $tipoDocumento);
        }

        $historial = $query->orderBy('tipo_documento')
            ->orderByDesc('version')
            ->get();

        return view('vehiculos.documentos.historial', compact('vehiculo', 'historial', 'tipoDocumento'));
    }

    /**
     * ============================================
     * HISTORIAL COMPLETO (ALIAS)
     * ============================================
     */
    public function historialCompleto($idVehiculo)
    {
        return $this->historial($idVehiculo);
    }
}

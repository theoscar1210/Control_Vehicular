<?php

namespace App\Http\Controllers;

use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use App\Models\Alerta;
use App\Helpers\DocumentoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DocumentoVehiculoController extends Controller
{
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_documento'   => 'required|string',
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora'  => 'nullable|string|max:100',
            'fecha_emision'    => 'required|date',
            'fecha_vencimiento' => 'nullable|date',
            'nota'             => 'nullable|string|max:255',
        ]);

        $vehiculo = Vehiculo::findOrFail($id);

        // Validar tipo de documento ANTES de la transacción
        $tipo = $validated['tipo_documento'];

        if ($tipo === 'Tecnomecanica' && method_exists($vehiculo, 'requiereTecnomecanica') && !$vehiculo->requiereTecnomecanica()) {
            return back()->withErrors([
                'tipo_documento' => 'Este vehículo solo requiere revisión técnicomecánica a partir del'
                    . optional($vehiculo->fechaPrimeraTecnomecanica())->format('d/m/Y')
            ])->withInput();
        }

        try {

            $result = DB::transaction(function () use ($validated, $vehiculo, $tipo) {

                $vehiculoId = $vehiculo->id_vehiculo;

                // ============================================
                // CALCULAR FECHA DE VENCIMIENTO (+1 año)
                // ============================================
                $fechaEmision = Carbon::parse($validated['fecha_emision'])->startOfDay();
                $fechaVenc = $fechaEmision->copy()->addYear()->toDateString();

                // ============================================
                // CALCULAR ESTADO
                // ============================================
                $hoy = Carbon::today();
                $dias = $hoy->diffInDays(Carbon::parse($fechaVenc), false);

                if ($dias < 0) {
                    $estado = 'VENCIDO';
                } elseif ($dias <= 30) {
                    $estado = 'POR_VENCER';
                } else {
                    $estado = 'VIGENTE';
                }

                // ============================================
                // OBTENER ÚLTIMA VERSIÓN DEL MISMO DOCUMENTO
                // ============================================
                $last = DocumentoVehiculo::where('id_vehiculo', $vehiculoId)
                    ->where('tipo_documento', $tipo)
                    ->where('estado', '!=', 'REEMPLAZADO')
                    ->orderByDesc('version')
                    ->first();

                $newVersion = $last ? $last->version + 1 : 1;

                // ============================================
                // GUARDAR DOCUMENTO NUEVO
                // ============================================
                $new = DocumentoVehiculo::create([
                    'id_vehiculo'       => $vehiculoId,
                    'tipo_documento'    => $tipo,
                    'numero_documento'  => $validated['numero_documento'],
                    'entidad_emisora'   => $validated['entidad_emisora'] ?? null,
                    'fecha_emision'     => $fechaEmision->toDateString(),
                    'fecha_vencimiento' => $fechaVenc,
                    'estado'            => $estado,
                    'activo'            => 1,
                    'creado_por'        => auth()->id() ?? null,
                    'version'           => $newVersion,
                    'nota'              => $validated['nota'] ?? null,
                ]);

                // ============================================
                // MARCAR DOCUMENTO ANTERIOR COMO REEMPLAZADO
                // ============================================
                if ($last) {
                    $last->estado = 'REEMPLAZADO';
                    $last->reemplazado_por = $new->id_doc_vehiculo;
                    $last->activo = 0;
                    $last->save();
                }

                // ============================================
                // CREAR ALERTA SI APLICA
                // ============================================
                if (in_array($estado, ['VENCIDO', 'POR_VENCER'])) {
                    Alerta::create([
                        'tipo_alerta'      => 'VEHICULO',
                        'id_doc_vehiculo'  => $new->id_doc_vehiculo,
                        'tipo_vencimiento' => $estado === 'VENCIDO' ? 'VENCIDO' : 'PROXIMO_VENCER',
                        'mensaje'          => "Documento {$new->tipo_documento} ({$new->numero_documento}) - vence: {$new->fecha_vencimiento}",
                        'fecha_alerta'     => now()->toDateString(),
                        'leida'            => 0,
                        'visible_para'     => 'TODOS',
                        'creado_por'       => auth()->id() ?? null,
                    ]);
                }

                return [
                    'fecha_vencimiento' => $fechaVenc,
                    'tipo' => $tipo
                ];
            });

            // ============================================
            // REDIRIGIR CON MENSAJE DE ÉXITO
            // ============================================

            // Verificar si existe la ruta vehiculos.show
            if (\Route::has('vehiculos.show')) {
                return redirect()
                    ->route('vehiculos.show', $vehiculo->id_vehiculo)
                    ->with('success', "Documento {$result['tipo']} guardado correctamente.");
            } else {
                // Si no existe, redirigir al index
                return redirect()
                    ->route('vehiculos.index')
                    ->with('success', "Documento {$result['tipo']} guardado correctamente.");
            }
        } catch (\Exception $e) {

            Log::error("Error al guardar documento vehículo: " . $e->getMessage(), [
                'vehiculo_id' => $id,
                'tipo_documento' => $validated['tipo_documento'] ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar el documento: ' . $e->getMessage());
        }
    }

    /**
     * Renovar/Actualizar un documento de vehículo
     * Crea una nueva versión del documento y marca el anterior como REEMPLAZADO
     */
    public function update(Request $request, $idVehiculo, $idDocumento)
    {
        $validated = $request->validate([
            'numero_documento'  => 'required|string|max:50',
            'entidad_emisora'   => 'nullable|string|max:100',
            'fecha_emision'     => 'required|date',
            'fecha_vencimiento' => 'nullable|date',
            'nota'              => 'nullable|string|max:255',
        ]);

        $vehiculo = Vehiculo::findOrFail($idVehiculo);
        $documentoAnterior = DocumentoVehiculo::where('id_doc_vehiculo', $idDocumento)
            ->where('id_vehiculo', $vehiculo->id_vehiculo)
            ->firstOrFail();

        try {

            $result = DB::transaction(function () use ($validated, $documentoAnterior) {

                // ============================================
                // CALCULAR FECHA DE VENCIMIENTO (+1 año)
                // ============================================
                $fechaEmision = Carbon::parse($validated['fecha_emision'])->startOfDay();
                $fechaVenc = $fechaEmision->copy()->addYear()->toDateString();

                // ============================================
                // CALCULAR ESTADO
                // ============================================
                $hoy = Carbon::today();
                $dias = $hoy->diffInDays(Carbon::parse($fechaVenc), false);

                if ($dias < 0) {
                    $estado = 'VENCIDO';
                } elseif ($dias <= 30) {
                    $estado = 'POR_VENCER';
                } else {
                    $estado = 'VIGENTE';
                }

                // ============================================
                // CREAR NUEVA VERSIÓN DEL DOCUMENTO
                // ============================================
                $newVersion = $documentoAnterior->version + 1;

                $nuevoDocumento = DocumentoVehiculo::create([
                    'id_vehiculo'       => $documentoAnterior->id_vehiculo,
                    'tipo_documento'    => $documentoAnterior->tipo_documento,
                    'numero_documento'  => $validated['numero_documento'],
                    'entidad_emisora'   => $validated['entidad_emisora'] ?? null,
                    'fecha_emision'     => $fechaEmision->toDateString(),
                    'fecha_vencimiento' => $fechaVenc,
                    'estado'            => $estado,
                    'activo'            => 1,
                    'creado_por'        => auth()->id() ?? null,
                    'version'           => $newVersion,
                    'nota'              => $validated['nota'] ?? null,
                ]);

                // ============================================
                // MARCAR DOCUMENTO ANTERIOR COMO REEMPLAZADO
                // ============================================
                $documentoAnterior->estado = 'REEMPLAZADO';
                $documentoAnterior->reemplazado_por = $nuevoDocumento->id_doc_vehiculo;
                $documentoAnterior->activo = 0;
                $documentoAnterior->save();

                // ============================================
                // MARCAR ALERTAS ANTERIORES COMO LEÍDAS
                // ============================================
                Alerta::where('id_doc_vehiculo', $documentoAnterior->id_doc_vehiculo)
                    ->where('leida', 0)
                    ->update(['leida' => 1]);

                // ============================================
                // CREAR NUEVA ALERTA SI APLICA
                // ============================================
                if (in_array($estado, ['VENCIDO', 'POR_VENCER'])) {
                    Alerta::create([
                        'tipo_alerta'      => 'VEHICULO',
                        'id_doc_vehiculo'  => $nuevoDocumento->id_doc_vehiculo,
                        'tipo_vencimiento' => $estado === 'VENCIDO' ? 'VENCIDO' : 'PROXIMO_VENCER',
                        'mensaje'          => "Documento {$nuevoDocumento->tipo_documento} ({$nuevoDocumento->numero_documento}) - vence: {$nuevoDocumento->fecha_vencimiento}",
                        'fecha_alerta'     => now()->toDateString(),
                        'leida'            => 0,
                        'visible_para'     => 'TODOS',
                        'creado_por'       => auth()->id() ?? null,
                    ]);
                }

                return [
                    'documento' => $nuevoDocumento,
                    'fecha_vencimiento' => $fechaVenc,
                    'tipo' => $documentoAnterior->tipo_documento
                ];
            });

            // ============================================
            // REDIRIGIR CON MENSAJE DE ÉXITO
            // ============================================

            // Verificar si existe la ruta vehiculos.show
            if (\Route::has('vehiculos.show')) {
                return redirect()
                    ->route('vehiculos.show', $idVehiculo)
                    ->with('success', "{$result['tipo']} renovado correctamente.");
            } else {
                // Si no existe, redirigir al index
                return redirect()
                    ->route('vehiculos.index')
                    ->with('success', "{$result['tipo']} renovado correctamente.");
            }
        } catch (\Exception $e) {

            Log::error("Error al renovar documento vehículo: " . $e->getMessage(), [
                'id_documento' => $idDocumento,
                'id_vehiculo' => $idVehiculo,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al renovar el documento: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar el formulario de edición/renovación
     */
    public function edit($idVehiculo, $idDocumento)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);
        $documento = DocumentoVehiculo::where('id_doc_vehiculo', $idDocumento)
            ->where('id_vehiculo', $vehiculo->id_vehiculo)
            ->firstOrFail();

        return view('vehiculos.documentos.edit', compact('vehiculo', 'documento'));
    }

    /**
     * Obtener historial de versiones de un tipo de documento
     */
    public function historial($idVehiculo, $tipoDocumento)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);

        $historial = DocumentoVehiculo::where('id_vehiculo', $vehiculo->id_vehiculo)
            ->where('tipo_documento', $tipoDocumento)
            ->orderByDesc('version')
            ->get();

        return view('vehiculos.documentos.historial', compact('vehiculo', 'historial', 'tipoDocumento'));
    }
}

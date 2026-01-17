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
        'Póliza'
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

        $validated = $request->validate($rules);

        $vehiculo = Vehiculo::with(['propietario'])->find($idVehiculo);
        $tipo = $validated['tipo_documento'];

        try {
            return DB::transaction(function () use ($validated, $vehiculo, $tipo) {

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

                if (in_array($tipo, $this->documentosConVencimiento)) {
                    $fechaVencimiento = $fechaEmision->copy()->addYear();

                    $hoy = Carbon::today();

                    if ($fechaVencimiento->isPast()) {
                        $estado = 'VENCIDO';
                    } elseif ($fechaVencimiento->diffInDays($hoy) <= 30) {
                        $estado = 'POR_VENCER';
                    } else {
                        $estado = 'VIGENTE';
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
                    'creado_por'        => auth()->user()->id_usuario ?? null,
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
                if (in_array($estado, ['VENCIDO', 'POR_VENCER'])) {
                    Alerta::create([
                        'tipo_alerta'      => 'VEHICULO',
                        'id_doc_vehiculo'  => $nuevoDocumento->id_doc_vehiculo,
                        'tipo_vencimiento' => $estado === 'VENCIDO' ? 'VENCIDO' : 'PROXIMO_VENCER',
                        'mensaje'          => "Documento {$tipo} ({$nuevoDocumento->numero_documento}) vence el {$fechaVencimiento->format('d/m/Y')}",
                        'fecha_alerta'     => now()->toDateString(),
                        'leida'            => 0,
                        'visible_para'     => 'TODOS',
                        'creado_por'       => auth()->user()->id_usuario ?? null,
                    ]);
                }

                return redirect()
                    ->route('vehiculos.create', ['vehiculo' => $vehiculo->id_vehiculo])
                    ->with('success', "Documento {$tipo} guardado correctamente.");
            });
        } catch (\Exception $e) {
            Log::error('Error al guardar documento', [
                'vehiculo' => $idVehiculo,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al guardar el documento.');
        }
    }

    /**
     * ============================================
     * RENOVAR DOCUMENTO (CREA NUEVA VERSIÓN)
     * ============================================
     */
    public function update(Request $request, $idVehiculo, $idDocumento)
    {
        $validated = $request->validate([
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora'  => 'nullable|string|max:100',
            'fecha_emision'    => 'required|date',
            'nota'             => 'nullable|string|max:255',
        ]);

        $vehiculo = Vehiculo::findOrFail($idVehiculo);
        $documentoAnterior = DocumentoVehiculo::where('id_doc_vehiculo', $idDocumento)
            ->where('id_vehiculo', $vehiculo->id_vehiculo)
            ->firstOrFail();

        try {
            return DB::transaction(function () use ($validated, $documentoAnterior, $vehiculo) {

                $fechaEmision = Carbon::parse($validated['fecha_emision'])->startOfDay();
                $fechaVencimiento = null;
                $estado = 'VIGENTE';

                if (in_array($documentoAnterior->tipo_documento, $this->documentosConVencimiento)) {
                    $fechaVencimiento = $fechaEmision->copy()->addYear();
                    $hoy = Carbon::today();

                    if ($fechaVencimiento->isPast()) {
                        $estado = 'VENCIDO';
                    } elseif ($fechaVencimiento->diffInDays($hoy) <= 30) {
                        $estado = 'POR_VENCER';
                    } else {
                        $estado = 'VIGENTE';
                    }
                }

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
                    'creado_por'        => auth()->user()->id_usuario ?? null,
                ]);

                $documentoAnterior->update([
                    'estado' => 'REEMPLAZADO',
                    'reemplazado_por' => $nuevoDocumento->id_doc_vehiculo,
                    'activo' => 0
                ]);

                Alerta::where('id_doc_vehiculo', $documentoAnterior->id_doc_vehiculo)
                    ->update(['leida' => 1]);

                if (in_array($estado, ['VENCIDO', 'POR_VENCER'])) {
                    Alerta::create([
                        'tipo_alerta'      => 'VEHICULO',
                        'id_doc_vehiculo'  => $nuevoDocumento->id_doc_vehiculo,
                        'tipo_vencimiento' => $estado === 'VENCIDO' ? 'VENCIDO' : 'PROXIMO_VENCER',
                        'mensaje'          => "Documento {$nuevoDocumento->tipo_documento} vence el {$fechaVencimiento->format('d/m/Y')}",
                        'fecha_alerta'     => now()->toDateString(),
                        'leida'            => 0,
                        'visible_para'     => 'TODOS',
                        'creado_por'       => auth()->user()->id_usuario ?? null,
                    ]);
                }

                return redirect()
                    ->route('vehiculos.show', $vehiculo->id_vehiculo)
                    ->with('success', 'Documento renovado correctamente.');
            });
        } catch (\Exception $e) {
            Log::error('Error al renovar documento', [
                'documento' => $idDocumento,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al renovar el documento.');
        }
    }

    /**
     * FORMULARIO DE EDICIÓN
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
     * HISTORIAL COMPLETO DE DOCUMENTOS
     */
    public function historialCompleto($idVehiculo)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);

        $historial = DocumentoVehiculo::where('id_vehiculo', $vehiculo->id_vehiculo)
            ->with('creador')
            ->orderBy('tipo_documento')
            ->orderByDesc('version')
            ->get();

        return view('vehiculos.documentos.historial', compact('vehiculo', 'historial'));
    }
}

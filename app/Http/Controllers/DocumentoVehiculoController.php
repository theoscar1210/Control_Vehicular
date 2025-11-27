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

        try {

            $result = DB::transaction(function () use ($validated, $vehiculo) {

                $vehiculoId = $vehiculo->id_vehiculo;
                $tipo = $validated['tipo_documento'];

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

                return $fechaVenc;
            });

            // ============================================
            // RESPUESTA ESPECÍFICA SEGÚN DOCUMENTO
            // ============================================

            // TECNOMECÁNICA
            if ($validated['tipo_documento'] === 'Tecnomecanica') {
                return back()->with([
                    'success' => 'Documento Tecnomecánica guardado correctamente.',
                    'fecha_venc_tecnomecanica' => $result
                ])->withInput();
            }

            // SOAT
            if ($validated['tipo_documento'] === 'SOAT') {
                return back()->with([
                    'success' => 'SOAT guardado correctamente.',
                    'fecha_venc_soat' => $result
                ])->withInput();
            }

            // OTROS
            return back()->with([
                'success' => "{$validated['tipo_documento']} guardado correctamente.",
                'fecha_venc_generada' => $result
            ])->withInput();
        } catch (\Exception $e) {

            Log::error("Error al guardar documento vehículo: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar el documento.');
        }
    }
}

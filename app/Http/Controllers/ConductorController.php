<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use App\Models\Vehiculo;
use App\Models\DocumentoConductor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ConductorController extends Controller
{
    /**
     * Mostrar formulario de creación de conductor.
     */
    public function create()
    {
        // Trae todos los vehículos disponibles
        $vehiculos = Vehiculo::orderBy('placa')->get();

        return view('conductores.create', compact('vehiculos'));
    }

    /**
     * Guardar un nuevo conductor.
     */
    public function store(Request $request)
    {
        // Validaciones de campos del formulario
        $rules = [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'tipo_doc' => ['required', Rule::in(['CC', 'CE'])],
            'identificacion' => 'required|string|max:50|unique:conductores,identificacion',
            'telefono' => 'nullable|string|max:30',
            'telefono_emergencia' => 'nullable|string|max:30',
            'activo' => 'nullable|boolean',
            'id_vehiculo' => 'nullable|integer|exists:vehiculos,id_vehiculo',

            // Datos del documento sin archivo
            'documento_tipo' => 'nullable|string|in:Licencia Conducción,Certificado Médico,ARL,EPS,Otro',
            'documento_numero' => 'nullable|string|max:50',
            'documento_fecha_emision' => 'nullable|date',
            'documento_fecha_vencimiento' => 'nullable|date|after_or_equal:documento_fecha_emision',
            'entidad_emisora' => 'nullable|string|max:100',

            // Categorías de licencia
            'categoria_licencia' => 'nullable|string|in:A1,A2,B1,B2,B3,C1,C2,C3',
            'categorias_adicionales' => 'nullable|array',
            'categorias_adicionales.*' => 'string|in:A1,A2,B1,B2,B3,C1,C2,C3',
        ];

        $validated = $request->validate($rules);

        // Crear el conductor
        $conductor = Conductor::create([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'tipo_doc' => $validated['tipo_doc'],
            'identificacion' => $validated['identificacion'],
            'telefono' => $validated['telefono'] ?? null,
            'telefono_emergencia' => $validated['telefono_emergencia'] ?? null,
            'activo' => $request->has('activo') ? boolval($request->input('activo')) : true,
            'creado_por' => Auth::id(),
        ]);

        // Asignar vehículo (si se seleccionó)
        if (!empty($validated['id_vehiculo'])) {
            $vehiculo = Vehiculo::find($validated['id_vehiculo']);
            if ($vehiculo) {
                $vehiculo->id_conductor = $conductor->id_conductor;
                $vehiculo->save();
            }
        }

        // Guardar datos del documento (sin archivo)
        if (!empty($validated['documento_numero'])) {
            // Procesar categorías adicionales
            $categoriasAdicionales = null;
            if (!empty($validated['categorias_adicionales'])) {
                // Filtrar para no incluir la categoría principal
                $adicionales = array_filter($validated['categorias_adicionales'], function($cat) use ($validated) {
                    return $cat !== ($validated['categoria_licencia'] ?? '');
                });
                if (!empty($adicionales)) {
                    $categoriasAdicionales = implode(',', $adicionales);
                }
            }

            // Calcular estado basado en fecha de vencimiento
            $estado = 'VIGENTE';
            if (!empty($validated['documento_fecha_vencimiento'])) {
                $fechaVenc = \Carbon\Carbon::parse($validated['documento_fecha_vencimiento']);
                if (now()->greaterThan($fechaVenc)) {
                    $estado = 'VENCIDO';
                } elseif (now()->diffInDays($fechaVenc, false) <= 30) {
                    $estado = 'POR_VENCER';
                }
            }

            DocumentoConductor::create([
                'id_conductor' => $conductor->id_conductor,
                'tipo_documento' => $validated['documento_tipo'] ?? 'Licencia Conducción',
                'categoria_licencia' => $validated['categoria_licencia'] ?? null,
                'categorias_adicionales' => $categoriasAdicionales,
                'numero_documento' => $validated['documento_numero'],
                'entidad_emisora' => $validated['entidad_emisora'] ?? null,
                'fecha_emision' => $validated['documento_fecha_emision'] ?? null,
                'fecha_vencimiento' => $validated['documento_fecha_vencimiento'] ?? null,
                'estado' => $estado,
                'activo' => 1,
                'creado_por' => Auth::id(),
                'version' => 1,
                'fecha_registro' => now(),
            ]);
        }

        return redirect()->route('conductores.create')->with('success', 'Conductor creado correctamente.');
    }

    /**
     * Mostrar formulario de edición del conductor.
     */
    public function edit(Conductor $conductor)
    {
        // Lista de vehículos disponibles
        $vehiculos = Vehiculo::orderBy('placa')->get();

        // Historial de documentos del conductor
        $documentos = $conductor->documentosConductor()
            ->orderBy('tipo_documento')
            ->get();

        return view('conductores.edit', compact('conductor', 'vehiculos', 'documentos'));
    }

    /**
     * Actualizar conductor existente.
     */
    public function update(Request $request, Conductor $conductor)
    {
        $rules = [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'tipo_doc' => ['required', Rule::in(['CC', 'CE'])],
            'identificacion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('conductores', 'identificacion')->ignore($conductor->id_conductor, 'id_conductor'),
            ],
            'telefono' => 'nullable|string|max:30',
            'telefono_emergencia' => 'nullable|string|max:30',
            'activo' => 'nullable|boolean',
            'id_vehiculo' => 'nullable|integer|exists:vehiculos,id_vehiculo',

            // Datos de documentos
            'documento_action' => ['nullable', Rule::in(['none', 'update_existing', 'create_version'])],
            'documento_id' => 'nullable|integer|exists:documentos_conductor,id_doc_conductor',
            'documento_tipo' => 'nullable|string|max:100',
            'documento_numero' => 'nullable|string|max:100',
            'documento_fecha_emision' => 'nullable|date',
            'documento_fecha_vencimiento' => 'nullable|date|after_or_equal:documento_fecha_emision',
        ];

        $validated = $request->validate($rules);

        // Transacción para consistencia
        DB::transaction(function () use ($validated, $request, $conductor) {
            // 1) Actualizar datos básicos
            $conductor->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'tipo_doc' => $validated['tipo_doc'],
                'identificacion' => $validated['identificacion'],
                'telefono' => $validated['telefono'] ?? null,
                'telefono_emergencia' => $validated['telefono_emergencia'] ?? null,
                'activo' => $request->has('activo') ? boolval($request->input('activo')) : $conductor->activo,
            ]);

            // 2) Manejo del vehículo asignado
            if (array_key_exists('id_vehiculo', $validated)) {
                $newVehiculoId = $validated['id_vehiculo'] ?: null;

                // Quitar vehículo anterior si cambió
                $oldVeh = Vehiculo::where('id_conductor', $conductor->id_conductor)->first();
                if ($oldVeh && (!$newVehiculoId || $oldVeh->id_vehiculo != $newVehiculoId)) {
                    $oldVeh->id_conductor = null;
                    $oldVeh->save();
                }

                // Asignar nuevo vehículo
                if ($newVehiculoId) {
                    $veh = Vehiculo::find($newVehiculoId);
                    if ($veh->id_conductor && $veh->id_conductor != $conductor->id_conductor) {
                        throw new \Exception('El vehículo seleccionado ya está asignado a otro conductor.');
                    }
                    $veh->id_conductor = $conductor->id_conductor;
                    $veh->save();
                }
            }

            // 3) Manejo de documentos (solo metadata)
            $action = $validated['documento_action'] ?? 'none';

            if ($action === 'update_existing' && !empty($validated['documento_id'])) {
                // Actualizar documento existente
                $doc = DocumentoConductor::find($validated['documento_id']);
                if ($doc && $doc->id_conductor == $conductor->id_conductor) {
                    $doc->update([
                        'tipo_documento' => $validated['documento_tipo'] ?? $doc->tipo_documento,
                        'numero_documento' => $validated['documento_numero'] ?? $doc->numero_documento,
                        'fecha_emision' => $validated['documento_fecha_emision'] ?? $doc->fecha_emision,
                        'fecha_vencimiento' => $validated['documento_fecha_vencimiento'] ?? $doc->fecha_vencimiento,
                        'estado' => ($doc->fecha_vencimiento && now()->greaterThan($doc->fecha_vencimiento))
                            ? 'VENCIDO'
                            : 'VIGENTE',
                    ]);
                }
            } elseif ($action === 'create_version') {
                // Crear nueva versión del documento (sin archivo)
                $tipo = $validated['documento_tipo'] ?? 'Licencia Conducción';

                $last = DocumentoConductor::where('id_conductor', $conductor->id_conductor)
                    ->where('tipo_documento', $tipo)
                    ->orderByDesc('version')
                    ->first();

                $newVersion = $last ? $last->version + 1 : 1;

                $newDoc = DocumentoConductor::create([
                    'id_conductor' => $conductor->id_conductor,
                    'tipo_documento' => $tipo,
                    'numero_documento' => $validated['documento_numero'] ?? null,
                    'fecha_emision' => $validated['documento_fecha_emision'] ?? null,
                    'fecha_vencimiento' => $validated['documento_fecha_vencimiento'] ?? null,
                    'estado' => (!empty($validated['documento_fecha_vencimiento']) && now()->greaterThan($validated['documento_fecha_vencimiento']))
                        ? 'VENCIDO'
                        : 'VIGENTE',
                    'activo' => 1,
                    'creado_por' => Auth::id(),
                    'fecha_registro' => now(),
                    'version' => $newVersion,
                ]);

                // Marcar anterior como histórico
                if ($last) {
                    $last->update([
                        'activo' => 0,
                        'reemplazado_por' => $newDoc->id_doc_conductor,
                    ]);
                }
            }
        });

        return redirect()->route('conductores.edit', $conductor)->with('success', 'Conductor actualizado correctamente.');
    }
}

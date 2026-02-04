<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use App\Models\Vehiculo;
use App\Models\DocumentoConductor;
use App\Models\Alerta;
use App\Traits\SanitizesSearchInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ConductorController extends Controller
{
    use SanitizesSearchInput;
    /**
     * Mostrar listado de conductores.
     */
    public function index(Request $request)
    {
        $query = Conductor::with(['vehiculos', 'documentosConductor'])
            ->orderBy('nombre');

        // Búsqueda (sanitizada contra caracteres especiales LIKE)
        if ($request->filled('search')) {
            $searchSanitized = $this->sanitizeForLike($request->search);
            $search = '%' . $searchSanitized . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', $search)
                    ->orWhere('apellido', 'like', $search)
                    ->orWhere('identificacion', 'like', $search)
                    ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", [$search]);
            });
        }

        $conductores = $query->paginate(15)->withQueryString();

        // Contar conductores eliminados para mostrar badge
        $eliminadosCount = Conductor::onlyTrashed()->count();

        return view('conductores.index', compact('conductores', 'eliminadosCount'));
    }

    /**
     * Mostrar conductores eliminados (papelera)
     */
    public function trashed(Request $request)
    {
        $query = Conductor::onlyTrashed();

        // Búsqueda (sanitizada)
        if ($request->filled('search')) {
            $search = $this->prepareLikeSearch($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', $search)
                    ->orWhere('apellido', 'like', $search)
                    ->orWhere('identificacion', 'like', $search);
            });
        }

        $conductores = $query->orderBy('deleted_at', 'desc')->paginate(15);

        return view('conductores.trashed', compact('conductores'));
    }

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

            // Vencimiento por categoría adicional (solo fecha de vencimiento)
            'fechas_categoria' => 'nullable|array',
            'fechas_categoria.*.fecha_vencimiento' => 'nullable|date',

            // Categorías a monitorear para alertas (por defecto solo la principal)
            'categorias_monitoreadas' => 'nullable|array',
            'categorias_monitoreadas.*' => 'string|in:A1,A2,B1,B2,B3,C1,C2,C3',
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

        // Asignar vehículo (si se seleccionó) usando tabla pivote
        if (!empty($validated['id_vehiculo'])) {
            $vehiculo = Vehiculo::find($validated['id_vehiculo']);
            if ($vehiculo) {
                // Mantener compatibilidad con campo legacy
                $vehiculo->id_conductor = $conductor->id_conductor;
                $vehiculo->save();

                // Usar tabla pivote para relación muchos a muchos
                $conductor->vehiculosAsignados()->syncWithoutDetaching([
                    $validated['id_vehiculo'] => [
                        'es_principal' => true,
                        'fecha_asignacion' => now(),
                        'activo' => true,
                    ]
                ]);
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

            // Procesar vencimientos por categoría
            // En Colombia, la licencia tiene una sola fecha de expedición pero cada categoría tiene su propio vencimiento
            $fechasPorCategoria = [];

            // Agregar vencimiento de la categoría principal
            if (!empty($validated['categoria_licencia'])) {
                $fechasPorCategoria[$validated['categoria_licencia']] = [
                    'fecha_vencimiento' => $validated['documento_fecha_vencimiento'] ?? null,
                ];
            }

            // Agregar vencimientos de categorías adicionales
            if (!empty($validated['fechas_categoria'])) {
                foreach ($validated['fechas_categoria'] as $cat => $fechas) {
                    if (!empty($fechas['fecha_vencimiento'])) {
                        $fechasPorCategoria[$cat] = [
                            'fecha_vencimiento' => $fechas['fecha_vencimiento'],
                        ];
                    }
                }
            }

            // Calcular la fecha de vencimiento más próxima de todas las categorías
            $fechaVencimientoFinal = $validated['documento_fecha_vencimiento'] ?? null;

            if (!empty($fechasPorCategoria)) {
                $fechaMinima = null;
                foreach ($fechasPorCategoria as $cat => $fechas) {
                    if (!empty($fechas['fecha_vencimiento'])) {
                        $fecha = \Carbon\Carbon::parse($fechas['fecha_vencimiento']);
                        if ($fechaMinima === null || $fecha->lt($fechaMinima)) {
                            $fechaMinima = $fecha;
                        }
                    }
                }
                if ($fechaMinima) {
                    $fechaVencimientoFinal = $fechaMinima->format('Y-m-d');
                }
            }

            // Calcular estado basado en fecha de vencimiento más próxima
            $estado = 'VIGENTE';
            if (!empty($fechaVencimientoFinal)) {
                $fechaVenc = \Carbon\Carbon::parse($fechaVencimientoFinal);
                if (now()->greaterThan($fechaVenc)) {
                    $estado = 'VENCIDO';
                } elseif (now()->diffInDays($fechaVenc, false) <= 20) {
                    $estado = 'POR_VENCER';
                }
            }

            // Determinar categorías a monitorear (por defecto solo la principal)
            $categoriasMonitoreadas = null;
            if (!empty($validated['categorias_monitoreadas'])) {
                $categoriasMonitoreadas = $validated['categorias_monitoreadas'];
            } elseif (!empty($validated['categoria_licencia'])) {
                // Si no se especifica, monitorear solo la categoría principal
                $categoriasMonitoreadas = [$validated['categoria_licencia']];
            }

            $documento = DocumentoConductor::create([
                'id_conductor' => $conductor->id_conductor,
                'tipo_documento' => $validated['documento_tipo'] ?? 'Licencia Conducción',
                'categoria_licencia' => $validated['categoria_licencia'] ?? null,
                'categorias_adicionales' => $categoriasAdicionales,
                'fechas_por_categoria' => !empty($fechasPorCategoria) ? $fechasPorCategoria : null,
                'categorias_monitoreadas' => $categoriasMonitoreadas,
                'numero_documento' => $validated['documento_numero'],
                'entidad_emisora' => $validated['entidad_emisora'] ?? null,
                'fecha_emision' => $validated['documento_fecha_emision'] ?? null,
                'fecha_vencimiento' => $fechaVencimientoFinal,
                'estado' => $estado,
                'activo' => 1,
                'creado_por' => Auth::id(),
                'version' => 1,
                'fecha_registro' => now(),
            ]);

            // Generar alertas si el documento está vencido o próximo a vencer
            $documento->load('conductor');
            Alerta::generarAlertasDocumentoConductor($documento);
        }

        return redirect()->route('conductores.index')->with('success', 'Conductor creado correctamente.');
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
            'categoria_licencia' => 'nullable|string|in:A1,A2,B1,B2,B3,C1,C2,C3',

            // Categorías a monitorear para alertas
            'categorias_monitoreadas' => 'nullable|array',
            'categorias_monitoreadas.*' => 'string|in:A1,A2,B1,B2,B3,C1,C2,C3',
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

            // 2) Manejo del vehículo asignado (usando tabla pivote)
            if (array_key_exists('id_vehiculo', $validated)) {
                $newVehiculoId = $validated['id_vehiculo'] ?: null;

                // Desactivar asignación anterior si cambió de vehículo
                if ($newVehiculoId) {
                    // Desmarcar otros vehículos como principal para este conductor
                    DB::table('conductor_vehiculo')
                        ->where('id_conductor', $conductor->id_conductor)
                        ->where('id_vehiculo', '!=', $newVehiculoId)
                        ->where('es_principal', true)
                        ->update(['es_principal' => false]);

                    // Asignar nuevo vehículo en tabla pivote
                    $existeRelacion = DB::table('conductor_vehiculo')
                        ->where('id_conductor', $conductor->id_conductor)
                        ->where('id_vehiculo', $newVehiculoId)
                        ->first();

                    if ($existeRelacion) {
                        // Actualizar relación existente
                        DB::table('conductor_vehiculo')
                            ->where('id_conductor', $conductor->id_conductor)
                            ->where('id_vehiculo', $newVehiculoId)
                            ->update([
                                'es_principal' => true,
                                'activo' => true,
                                'fecha_desasignacion' => null,
                            ]);
                    } else {
                        // Crear nueva relación
                        DB::table('conductor_vehiculo')->insert([
                            'id_conductor' => $conductor->id_conductor,
                            'id_vehiculo' => $newVehiculoId,
                            'es_principal' => true,
                            'fecha_asignacion' => now(),
                            'activo' => true,
                        ]);
                    }

                    // Mantener compatibilidad con campo legacy id_conductor en vehiculos
                    $veh = Vehiculo::find($newVehiculoId);
                    if ($veh) {
                        $veh->id_conductor = $conductor->id_conductor;
                        $veh->save();
                    }
                } else {
                    // Desasignar todos los vehículos del conductor
                    DB::table('conductor_vehiculo')
                        ->where('id_conductor', $conductor->id_conductor)
                        ->where('activo', true)
                        ->update([
                            'activo' => false,
                            'fecha_desasignacion' => now(),
                        ]);

                    // Limpiar campo legacy
                    Vehiculo::where('id_conductor', $conductor->id_conductor)
                        ->update(['id_conductor' => null]);
                }
            }

            // 3) Actualizar categorías monitoreadas (se puede hacer sin crear nueva versión)
            if (array_key_exists('categorias_monitoreadas', $validated)) {
                // Buscar el documento de licencia activo del conductor
                $licencia = DocumentoConductor::where('id_conductor', $conductor->id_conductor)
                    ->where('tipo_documento', 'Licencia Conducción')
                    ->where('activo', 1)
                    ->first();

                if ($licencia) {
                    $licencia->update([
                        'categorias_monitoreadas' => $validated['categorias_monitoreadas'],
                    ]);
                }
            }

            // 4) Manejo de documentos (solo metadata)
            $action = $validated['documento_action'] ?? 'none';

            if ($action === 'update_existing' && !empty($validated['documento_id'])) {
                // Actualizar documento existente
                $doc = DocumentoConductor::find($validated['documento_id']);
                if ($doc && $doc->id_conductor == $conductor->id_conductor) {
                    $updateData = [
                        'tipo_documento' => $validated['documento_tipo'] ?? $doc->tipo_documento,
                        'numero_documento' => $validated['documento_numero'] ?? $doc->numero_documento,
                        'fecha_emision' => $validated['documento_fecha_emision'] ?? $doc->fecha_emision,
                        'fecha_vencimiento' => $validated['documento_fecha_vencimiento'] ?? $doc->fecha_vencimiento,
                        'estado' => ($doc->fecha_vencimiento && now()->greaterThan($doc->fecha_vencimiento))
                            ? 'VENCIDO'
                            : 'VIGENTE',
                    ];
                    // Add category if document is a license
                    if (($validated['documento_tipo'] ?? $doc->tipo_documento) === 'Licencia Conducción' && !empty($validated['categoria_licencia'])) {
                        $updateData['categoria_licencia'] = $validated['categoria_licencia'];
                    }
                    // Actualizar categorías monitoreadas si se proporcionaron
                    if (array_key_exists('categorias_monitoreadas', $validated)) {
                        $updateData['categorias_monitoreadas'] = $validated['categorias_monitoreadas'];
                    }
                    $doc->update($updateData);
                }
            } elseif ($action === 'create_version') {
                // Crear nueva versión del documento (sin archivo)
                $tipo = $validated['documento_tipo'] ?? 'Licencia Conducción';

                $last = DocumentoConductor::where('id_conductor', $conductor->id_conductor)
                    ->where('tipo_documento', $tipo)
                    ->orderByDesc('version')
                    ->first();

                $newVersion = $last ? $last->version + 1 : 1;

                $newDocData = [
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
                ];
                // Add category if document is a license
                if ($tipo === 'Licencia Conducción' && !empty($validated['categoria_licencia'])) {
                    $newDocData['categoria_licencia'] = $validated['categoria_licencia'];
                }
                $newDoc = DocumentoConductor::create($newDocData);

                // Marcar anterior como histórico
                if ($last) {
                    $last->update([
                        'activo' => 0,
                        'reemplazado_por' => $newDoc->id_doc_conductor,
                    ]);

                    // Marcar alertas del documento anterior como solucionadas
                    Alerta::solucionarPorDocumentoConductor(
                        $last->id_doc_conductor,
                        'DOCUMENTO_RENOVADO'
                    );
                }

                // Generar alertas si el nuevo documento está vencido o próximo a vencer
                $newDoc->load('conductor');
                Alerta::generarAlertasDocumentoConductor($newDoc);
            }
        });

        return redirect()->route('conductores.edit', $conductor)->with('success', 'Conductor actualizado correctamente.');
    }

    /**
     * Eliminar conductor (soft delete).
     * El conductor y sus documentos permanecen 6 meses antes de ser eliminados definitivamente.
     */
    public function destroy(Conductor $conductor)
    {
        try {
            DB::transaction(function () use ($conductor) {
                // 1) Desasignar vehículos en tabla pivote
                DB::table('conductor_vehiculo')
                    ->where('id_conductor', $conductor->id_conductor)
                    ->where('activo', true)
                    ->update([
                        'activo' => false,
                        'fecha_desasignacion' => now(),
                    ]);

                // 2) Limpiar campo legacy id_conductor en vehículos
                Vehiculo::where('id_conductor', $conductor->id_conductor)
                    ->update(['id_conductor' => null]);

                // 3) Soft delete de documentos del conductor
                $conductor->documentosConductor()->delete();

                // 4) Soft delete del conductor
                $conductor->delete();
            });

            return redirect()->route('conductores.index')
                ->with('success', 'Conductor eliminado correctamente. Permanecerá en el sistema por 6 meses antes de ser eliminado definitivamente.');
        } catch (\Exception $e) {
            return redirect()->route('conductores.index')
                ->with('error', 'Error al eliminar el conductor: ' . $e->getMessage());
        }
    }

    /**
     * Restaurar conductor eliminado.
     */
    public function restore($id)
    {
        try {
            $conductor = Conductor::onlyTrashed()->findOrFail($id);

            DB::transaction(function () use ($conductor) {
                // 1) Restaurar conductor
                $conductor->restore();

                // 2) Restaurar documentos del conductor
                $conductor->documentosConductor()->onlyTrashed()->restore();
            });

            return redirect()->route('conductores.index')
                ->with('success', 'Conductor restaurado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('conductores.index')
                ->with('error', 'Error al restaurar el conductor: ' . $e->getMessage());
        }
    }
}

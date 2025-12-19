<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Propietario;
use App\Models\DocumentoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehiculoController extends Controller
{
    /**
     * LISTADO DE VEHÍCULOS
     * Se agrega ViewModel para estados de documentos
     */
    public function index()
    {
        $vehiculos = Vehiculo::with([
            'propietario',
            'conductor',
            'documentosVehiculo' => function ($q) {
                $q->where('activo', 1);
            }
        ])
            ->orderBy('id_vehiculo', 'desc')
            ->paginate(15);

        /**
         * ================================
         * VIEWMODEL: estados de documentos
         * ================================
         */
        $vehiculos->getCollection()->transform(function ($vehiculo) {

            $soat = $vehiculo->documentosVehiculo
                ->where('tipo_documento', 'SOAT')
                ->first();

            $tecno = $vehiculo->documentosVehiculo
                ->where('tipo_documento', 'Tecnomecanica')
                ->first();

            // Se agregan propiedades calculadas al modelo
            $vehiculo->estado_soat  = $this->calcularEstadoDocumento($soat);
            $vehiculo->estado_tecno = $this->calcularEstadoDocumento($tecno);

            return $vehiculo;
        });

        return view('vehiculos.index', compact('vehiculos'));
    }

    /**
     * Mostrar la vista de create.
     * Si viene ?propietario=ID, cargamos ese propietario
     */
    public function create(Request $request)
    {
        $propietario = null;
        $propId = $request->query('propietario') ?? session('created_propietario_id');

        if ($propId) {
            $propietario = Propietario::find($propId);
        }

        return view('vehiculos.create', compact('propietario'));
    }

    /**
     * Guardar vehículo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:vehiculos,placa',
            'marca' => 'required|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'fecha_matricula' => 'required|date|before_or_equal:today',
            'color' => 'nullable|string|max:50',
            'tipo' => 'required|in:Carro,Moto,Camion,Otro',
            'id_propietario' => 'required|integer|exists:propietarios,id_propietario',
        ], [
            'id_propietario.required' => 'Debe existir un propietario asociado. Crea primero el propietario.',
            'id_propietario.exists' => 'Propietario no válido.',
        ]);

        DB::beginTransaction();
        try {
            $veh = Vehiculo::create([
                'placa' => strtoupper($validated['placa']),
                'marca' => $validated['marca'],
                'modelo' => $validated['modelo'] ?? null,
                'fecha_matricula' => $validated['fecha_matricula'],
                'color' => $validated['color'] ?? null,
                'tipo' => $validated['tipo'],
                'id_propietario' => $validated['id_propietario'],
                'id_conductor' => null,
                'estado' => 'Activo',
                'creado_por' => auth()->id() ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('vehiculos.create', [
                    'propietario' => $veh->id_propietario,
                    'vehiculo' => $veh->id_vehiculo
                ])
                ->with('success', 'Vehículo creado. Ahora puede agregar los documentos.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error creando vehículo: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Error al crear vehículo.']);
        }
    }

    /**
     * Editar vehículo
     */
    public function edit(Vehiculo $vehiculo)
    {
        return view('vehiculos.edit', compact('vehiculo'));
    }

    /**
     * Eliminar vehículo y documentos
     */
    public function destroy(Vehiculo $vehiculo)
    {
        DB::beginTransaction();
        try {
            DocumentoVehiculo::where('id_vehiculo', $vehiculo->id_vehiculo)->delete();
            $vehiculo->delete();

            DB::commit();
            return redirect()
                ->route('vehiculos.index')
                ->with('success', 'Vehículo y documentos asociados eliminados correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error eliminando vehículo: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Error al eliminar vehículo.']);
        }
    }

    /**
     * =================================================
     * MÉTODO PRIVADO VIEWMODEL (NO AFECTA RUTAS)
     * =================================================
     */
    private function calcularEstadoDocumento($documento): array
    {
        if (!$documento) {
            return [
                'estado' => 'SIN_REGISTRO',
                'dias'   => null,
                'clase'  => 'secondary',
                'fecha'  => null,
                'id'     => null,
            ];
        }

        $hoy = Carbon::today();
        $vencimiento = Carbon::parse($documento->fecha_vencimiento);
        $dias = $hoy->diffInDays($vencimiento, false);

        if ($dias < 0) {
            return [
                'estado' => 'VENCIDO',
                'dias'   => $dias,
                'clase'  => 'danger',
                'fecha'  => $vencimiento,
                'id'     => $documento->id_doc_vehiculo,
            ];
        }

        if ($dias <= 30) {
            return [
                'estado' => 'POR_VENCER',
                'dias'   => $dias,
                'clase'  => 'warning',
                'fecha'  => $vencimiento,
                'id'     => $documento->id_doc_vehiculo,
            ];
        }

        return [
            'estado' => 'VIGENTE',
            'dias'   => $dias,
            'clase'  => 'success',
            'fecha'  => $vencimiento,
            'id'     => $documento->id_doc_vehiculo,
        ];
    }
}

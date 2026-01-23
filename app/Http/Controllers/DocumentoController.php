<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Conductor;
use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Models\Propietario;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\DocumentosCollectionExport;
use Carbon\Carbon;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        // -----------------------------
        //  VALIDACIÓN OPTIMIZADA
        // -----------------------------
        $request->validate([
            'documentos'   => 'nullable|array',
            'documentos.*' => 'string|max:100',
            'estado'       => 'nullable|string|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            'conductor'    => 'nullable|string|max:100',
            'placa'        => 'nullable|string|max:20',
            'propietario'  => 'nullable|string|max:100',
            'fecha_from'   => 'nullable|date',
            'fecha_to'     => 'nullable|date|after_or_equal:fecha_from',
            'per_page'     => 'nullable|integer|min:5|max:200',
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $page    = (int) $request->input('page', 1);

        // -----------------------------------------
        // OBTENER DOCUMENTOS FILTRADOS (TUS MÉTODOS)
        // -----------------------------------------
        $docsConductor = $this->getDocumentoConductorCollection($request);
        $docsVehiculo  = $this->getDocumentoVehiculoCollection($request);

        // Pre-cargar relaciones para evitar N+1
        $conductorIds = $docsConductor->pluck('id_conductor')->unique()->filter();
        $vehiculoIds  = $docsVehiculo->pluck('id_vehiculo')->unique()->filter();

        $conductores = Conductor::whereIn('id_conductor', $conductorIds)->get()->keyBy('id_conductor');
        $vehiculos   = Vehiculo::whereIn('id_vehiculo', $vehiculoIds)->get()->keyBy('id_vehiculo');

        // Propietarios de ambos tipos de documentos
        $propietarioIds = $vehiculos->pluck('id_propietario')->unique()->filter();
        $propietarios   = Propietario::whereIn('id_propietario', $propietarioIds)->get()->keyBy('id_propietario');

        // -----------------------------------------
        // NORMALIZACIÓN REUTILIZANDO UN SOLO MÉTODO
        // -----------------------------------------
        $normalized = collect()
            ->merge(
                $docsConductor->map(function ($d) use ($conductores, $vehiculos, $propietarios) {
                    $conductor = $conductores[$d->id_conductor] ?? null;
                    $vehiculo  = $conductor
                        ? $vehiculos->firstWhere('id_conductor', $conductor->id_conductor)
                        : null;
                    $prop      = $vehiculo
                        ? ($propietarios[$vehiculo->id_propietario] ?? null)
                        : null;

                    return $this->formatDocumento($d, 'CONDUCTOR', $conductor, $vehiculo, $prop);
                })
            )
            ->merge(
                $docsVehiculo->map(function ($d) use ($conductores, $vehiculos, $propietarios) {
                    $vehiculo = $vehiculos[$d->id_vehiculo] ?? null;
                    $conductor = $vehiculo
                        ? ($conductores[$vehiculo->id_conductor] ?? null)
                        : null;
                    $prop = $vehiculo
                        ? ($propietarios[$vehiculo->id_propietario] ?? null)
                        : null;

                    return $this->formatDocumento($d, 'VEHICULO', $conductor, $vehiculo, $prop);
                })
            );

        // -----------------------------------------
        // ORDENAR POR FECHA (DESC)
        // -----------------------------------------
        $sorted = $normalized->sortByDesc(function ($item) {
            return strtotime($item->fecha_registro ?? '1970-01-01');
        })->values();

        // -----------------------------------------
        // PAGINACIÓN MANUAL
        // -----------------------------------------
        $paginated = $this->paginateCollection(
            $sorted,
            $perPage,
            $page,
            $request->url(),
            $request->query()
        );

        // -----------------------------------------
        // DATOS AUXILIARES (OPTIMIZADOS)
        // -----------------------------------------
        $tipos = DocumentoConductor::select('tipo_documento')
            ->union(DocumentoVehiculo::select('tipo_documento'))
            ->distinct()
            ->pluck('tipo_documento');

        return view('reportes.index', [
            'documentos'    => $paginated,
            'request'       => $request,
            'tipos'         => $tipos,
            'propietarios'  => Propietario::orderBy('nombre')->orderBy('apellido')->get(),
            'conductores'   => Conductor::orderBy('nombre')->orderBy('apellido')->get(),
            'vehiculos'     => Vehiculo::orderBy('placa')->get(),
        ]);
    }

    // renovar documento de conductor
    public function renewDocumentoConductor(Request $request)
    {
        $data = $request->validate([
            'id_conductor' => 'required|integer|exists:conductores,id_conductor',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora' => 'nullable|string|max:100',
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'ruta_archivo' => 'nullable|string',
            'nota' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $last = DocumentoConductor::where('id_conductor', $data['id_conductor'])
                ->where('tipo_documento', $data['tipo_documento'])
                ->orderByDesc('version')->first();

            $newVersion = $last ? $last->version + 1 : 1;

            $new = DocumentoConductor::create([
                'id_conductor' => $data['id_conductor'],
                'tipo_documento' => $data['tipo_documento'],
                'numero_documento' => $data['numero_documento'],
                'entidad_emisora' => $data['entidad_emisora'] ?? null,
                'fecha_emision' => $data['fecha_emision'] ?? null,
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
                'estado' => 'VIGENTE',
                'activo' => 1,
                'ruta_archivo' => $data['ruta_archivo'] ?? null,
                'creado_por' => auth()->id() ?? $request->input('creado_por', null),
                'version' => $newVersion,
                'nota' => $data['nota'] ?? null,
            ]);

            if ($last) {
                $last->estado = 'REEMPLAZADO';
                $last->reemplazado_por = $new->id_doc_conductor;
                $last->save();
            }

            return redirect()->back()->with('success', 'Documento renovado correctamente.');
        });
    }
    /**
     * Normaliza un documento para unir ambos tipos en un solo formato.
     */
    private function formatDocumento($d, $source, $conductor, $vehiculo, $propietario)
    {
        return (object) [
            'id'               => $d->id_doc_conductor ?? $d->id_doc_vehiculo,
            'source'           => $source,
            'tipo_documento'   => $d->tipo_documento,
            'numero_documento' => $d->numero_documento,
            'version'          => $d->version,
            'fecha_emision'   => $d->fecha_emision,
            'fecha_vencimiento' => $d->fecha_vencimiento,
            'estado'           => $d->estado,
            'activo'           => $d->activo,
            'conductor'        => $conductor,
            'vehiculo'         => $vehiculo,
            'propietario'      => $propietario,
            'raw'              => $d,
        ];
    }



    /**
     * Filtra los documentos de conductores según los par metros de entrada.
     * Soporta los siguientes par metros de filtrado:
     * - documentType: tipo de documento (transformaci n optimizada)
     * - estado: estado del documento (incluye caso especial "REEMPLAZADO")
     * - conductor: nombre, apellido o identificaci n del conductor
     * - placa: placa del veh culo asociado al documento
     * - propietario: nombre, apellido o identificaci n del propietario asociado al veh culo
     * - fecha_from: fecha de inicio para filtrar por fecha de registro
     * - fecha_to: fecha de fin para filtrar por fecha de registro
     * El m todo devuelve un objeto con los documentos filtrados.
     */
    private function getDocumentoConductorCollection(Request $request)
    {
        $q = DocumentoConductor::query()
            ->with('conductor'); // eager loading ok

        // -------------------------------------------------------
        // FILTRO: TIPOS DE DOCUMENTO (transformación optimizada)
        // -------------------------------------------------------
        if ($docs = $request->input('documentos')) {
            $upperDocs = collect($docs)
                ->map(fn($d) => mb_strtoupper(trim($d)))
                ->toArray();

            $q->whereIn(DB::raw("UPPER(TRIM(tipo_documento))"), $upperDocs);
        }

        // -------------------------------------------------------
        // FILTRO: ESTADO (incluye caso especial "REEMPLAZADO")
        // -------------------------------------------------------
        if ($estado = $request->input('estado')) {

            $hoy = Carbon::now()->startOfDay();
            $limite = Carbon::now()->addDays(20)->endOfDay(); // Cambio de 30 a 20 días

            match ($estado) {
                'REEMPLAZADO' => $q->where('activo', false),

                'VENCIDO' => $q->whereNotNull('fecha_vencimiento')
                    ->whereDate('fecha_vencimiento', '<', $hoy)
                    ->where('activo', true),

                'POR_VENCER' => $q->whereBetween(
                    'fecha_vencimiento',
                    [$hoy, $limite]
                )->where('activo', true),

                'VIGENTE' => $q->where(function ($w) use ($limite) {
                    $w->whereNull('fecha_vencimiento')
                        ->orWhereDate('fecha_vencimiento', '>', $limite);
                })->where('activo', true),
            };
        }


        // -------------------------------------------------------
        // FILTRO: CONDUCTOR (nombre, apellido, identificación)
        // -------------------------------------------------------
        if ($texto = $request->input('conductor')) {
            $q->whereHas('conductor', function ($qr) use ($texto) {
                $qr->where(function ($w) use ($texto) {
                    $w->where('nombre', 'LIKE', "%{$texto}%")
                        ->orWhere('apellido', 'LIKE', "%{$texto}%")
                        ->orWhere('identificacion', 'LIKE', "%{$texto}%");
                });
            });
        }

        // -------------------------------------------------------
        // FILTRO: POR PLACA (optimización → 1 sola consulta)
        // -------------------------------------------------------
        if ($placa = $request->input('placa')) {
            $conductorId = Vehiculo::where('placa', $placa)
                ->value('id_conductor'); // devuelve null si no existe

            if (!$conductorId) {
                return collect();
            }

            $q->where('id_conductor', $conductorId);
        }

        // -------------------------------------------------------
        // FILTRO: PROPIETARIO (con join optimizado)
        // -------------------------------------------------------
        if ($textoProp = $request->input('propietario')) {

            // obtener IDs de propietarios filtrados
            $propIds = Propietario::where(function ($qr) use ($textoProp) {
                $qr->where('nombre', 'LIKE', "%{$textoProp}%")
                    ->orWhere('apellido', 'LIKE', "%{$textoProp}%")
                    ->orWhere('identificacion', 'LIKE', "%{$textoProp}%");
            })->pluck('id_propietario');

            if ($propIds->isEmpty()) {
                return collect();
            }

            // obtener IDs de conductores asociados a los propietarios
            $conductorIds = Vehiculo::whereIn('id_propietario', $propIds)
                ->pluck('id_conductor')
                ->filter()
                ->unique();

            if ($conductorIds->isEmpty()) {
                return collect();
            }

            $q->whereIn('id_conductor', $conductorIds);
        }

        // -------------------------------------------------------
        // FILTRO: FECHAS
        // -------------------------------------------------------
        if ($from = $request->input('fecha_from')) {
            $q->whereDate('fecha_registro', '>=', $from);
        }
        if ($to = $request->input('fecha_to')) {
            $q->whereDate('fecha_registro', '<=', $to);
        }

        // -------------------------------------------------------
        // RETORNO FINAL
        // -------------------------------------------------------
        return $q->get();
    }

    /**
     * Obtiene una colecci n de documentos vehiculos filtrados por tipos de documentos, estado, conductor, placa, propietario, fecha de registro y fecha de vencimiento.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getDocumentoVehiculoCollection(Request $request)
    {
        $q = DocumentoVehiculo::query()
            ->with('vehiculo'); // eager loading correcto

        // -------------------------------------------------------
        // FILTRO: TIPOS DE DOCUMENTO (normalización optimizada)
        // -------------------------------------------------------
        if ($docs = $request->input('documentos')) {
            $upperDocs = collect($docs)
                ->map(fn($d) => mb_strtoupper(trim($d)))
                ->toArray();

            $q->whereIn(DB::raw("UPPER(TRIM(tipo_documento))"), $upperDocs);
        }

        // -------------------------------------------------------
        // FILTRO: ESTADO (incluye caso especial "REEMPLAZADO")
        // -------------------------------------------------------
        if ($estado = $request->input('estado')) {
            if ($estado === 'REEMPLAZADO') {
                $q->whereNotNull('reemplazado_por');
            } else {
                $q->where('estado', $estado);
            }
        }

        // -------------------------------------------------------
        // FILTRO: POR CONDUCTOR (nombre/apellido/ID)
        // -------------------------------------------------------
        if ($texto = $request->input('conductor')) {
            $conductorIds = Conductor::where(function ($qr) use ($texto) {
                $qr->where('nombre', 'LIKE', "%{$texto}%")
                    ->orWhere('apellido', 'LIKE', "%{$texto}%")
                    ->orWhere('identificacion', 'LIKE', "%{$texto}%");
            })->pluck('id_conductor');

            if ($conductorIds->isEmpty()) {
                return collect();
            }

            $vehiculoIds = Vehiculo::whereIn('id_conductor', $conductorIds)
                ->pluck('id_vehiculo');

            if ($vehiculoIds->isEmpty()) {
                return collect();
            }

            $q->whereIn('id_vehiculo', $vehiculoIds);
        }

        // -------------------------------------------------------
        // FILTRO: POR PLACA (optimización → 1 consulta)
        // -------------------------------------------------------
        if ($placa = $request->input('placa')) {
            $vehiculoId = Vehiculo::where('placa', $placa)
                ->value('id_vehiculo'); // más eficiente que ->first()

            if (!$vehiculoId) {
                return collect();
            }

            $q->where('id_vehiculo', $vehiculoId);
        }

        // -------------------------------------------------------
        // FILTRO: POR PROPIETARIO (menos consultas)
        // -------------------------------------------------------
        if ($textoProp = $request->input('propietario')) {

            $propIds = Propietario::where(function ($qr) use ($textoProp) {
                $qr->where('nombre', 'LIKE', "%{$textoProp}%")
                    ->orWhere('apellido', 'LIKE', "%{$textoProp}%")
                    ->orWhere('identificacion', 'LIKE', "%{$textoProp}%");
            })->pluck('id_propietario');

            if ($propIds->isEmpty()) {
                return collect();
            }

            $vehiculoIds = Vehiculo::whereIn('id_propietario', $propIds)
                ->pluck('id_vehiculo');

            if ($vehiculoIds->isEmpty()) {
                return collect();
            }

            $q->whereIn('id_vehiculo', $vehiculoIds);
        }

        // -------------------------------------------------------
        // FILTROS DE FECHAS
        // -------------------------------------------------------
        if ($from = $request->input('fecha_from')) {
            $q->whereDate('fecha_registro', '>=', $from);
        }
        if ($to = $request->input('fecha_to')) {
            $q->whereDate('fecha_registro', '<=', $to);
        }

        // -------------------------------------------------------
        // RETORNO FINAL
        // -------------------------------------------------------
        return $q->get();
    }



    private function getUsuarioNombreById($id)
    {
        if (empty($id)) return '';
        $u = Usuario::find($id);
        return $u ? trim(($u->nombre ?? '') . ' ' . ($u->apellido ?? '')) : '';
    }


    private function paginateCollection(Collection $collection, $perPage = 15, $page = 1, $path = null, $query = [])
    {
        return new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => $path ?: url()->current(),
                'query' => $query ?: request()->query(),
            ]
        );
    }

    /**
     * Mapea un documento para la exportaci n en formato PDF.
     * @param DocumentoConductor|DocumentoVehiculo $d
     * @param string $tipo
     * @param Collection $vehiculos
     * @param Collection $conductores
     * @param Collection $propietarios
     * @return array
     */
    private function mapDocumentoForExport($d, $tipo, $vehiculos, $conductores, $propietarios)
    {
        $veh = $vehiculos->get($d->id_vehiculo ?? ($d->conductor->id_conductor ?? null));

        $conductor = $veh && $veh->id_conductor
            ? $conductores->get($veh->id_conductor)
            : ($d->conductor ?? null);

        $prop = $veh && $veh->id_propietario
            ? $propietarios->get($veh->id_propietario)
            : null;

        $creadoPor = $veh ? $this->getUsuarioNombreById($veh->creado_por ?? null) : '';

        return [
            'origen' => $tipo,
            'tipo_documento' => $d->tipo_documento,
            'numero_documento' => $d->numero_documento,
            'conductor' => $conductor ? "{$conductor->nombre} {$conductor->apellido}" : '',
            'version' => $d->version ?? '',
            'fecha_emision' => optional($d->fecha_emision)->format('Y-m-d'),
            'fecha_vencimiento' => optional($d->fecha_vencimiento)->format('Y-m-d'),
            'estado' => $d->estado,
            'propietario' => $prop ? "{$prop->nombre} {$prop->apellido}" : '',
            'placa' => $veh ? $veh->placa : '',
            'creado_por' => $creadoPor
        ];
    }



    /**
     * Exporta una colecci n de documentos en formato XLS.
     *
     * Filtra por tipo de documento, estado, conductor, placa, propietario, fecha de registro y fecha de vencimiento.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'documentos' => 'nullable|array',
            'documentos.*' => 'string|max:100',
            'estado' => 'nullable|string|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            'conductor' => 'nullable|string|max:100',
            'placa' => 'nullable|string|max:20',
            'propietario' => 'nullable|string|max:100',
            'fecha_from' => 'nullable|date',
            'fecha_to' => 'nullable|date|after_or_equal:fecha_from',
        ]);

        $docsConductor = $this->getDocumentoConductorCollection($request);
        $docsVehiculo  = $this->getDocumentoVehiculoCollection($request);

        // -----------------------------------------
        // Pre-cargar relaciones para evitar N+1
        // -----------------------------------------
        $vehiculoIds = $docsVehiculo->pluck('id_vehiculo')
            ->concat($docsConductor->pluck('conductor.*.id_conductor')->flatten())
            ->filter()
            ->unique();

        $vehiculos = Vehiculo::whereIn('id_vehiculo', $vehiculoIds)->get()->keyBy('id_vehiculo');
        $conductores = Conductor::whereIn('id_conductor', $vehiculos->pluck('id_conductor'))->get()->keyBy('id_conductor');
        $propietarios = Propietario::whereIn('id_propietario', $vehiculos->pluck('id_propietario'))->get()->keyBy('id_propietario');

        $normalized = collect();

        foreach ($docsConductor as $d) {
            $normalized->push($this->mapDocumentoForExport($d, 'CONDUCTOR', $vehiculos, $conductores, $propietarios));
        }
        foreach ($docsVehiculo as $d) {
            $normalized->push($this->mapDocumentoForExport($d, 'VEHICULO', $vehiculos, $conductores, $propietarios));
        }

        $collection = $normalized->sortByDesc('Fecha registro')->values();

        return Excel::download(new DocumentosCollectionExport($collection), 'documentos_report.xlsx');
    }

    /**
     * Exporta un reporte en formato PDF de todos los documentos de los vehículos y conductores.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'documentos' => 'nullable|array',
            'documentos.*' => 'string|max:100',
            'estado' => 'nullable|string|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            'conductor' => 'nullable|string|max:100',
            'placa' => 'nullable|string|max:20',
            'propietario' => 'nullable|string|max:100',
            'fecha_from' => 'nullable|date',
            'fecha_to' => 'nullable|date|after_or_equal:fecha_from',
        ]);
        // obtener documentos filtrados
        $docsConductor = $this->getDocumentoConductorCollection($request);
        $docsVehiculo  = $this->getDocumentoVehiculoCollection($request);

        // IDs correctos
        $vehiculoIds = $docsVehiculo->pluck('id_vehiculo')->filter()->unique();

        $conductorIds = collect()
            ->merge($docsConductor->pluck('id_conductor'))
            ->merge($docsVehiculo->pluck('id_conductor'))
            ->filter()
            ->unique();

        // Propietarios (dependen del vehículo)
        $propietarioIds = Vehiculo::whereIn('id_vehiculo', $vehiculoIds)
            ->pluck('id_propietario')
            ->filter()
            ->unique();

        // Consultas reales
        $vehiculos = Vehiculo::whereIn('id_vehiculo', $vehiculoIds)->get()->keyBy('id_vehiculo');
        $conductores = Conductor::whereIn('id_conductor', $conductorIds)->get()->keyBy('id_conductor');
        $propietarios = Propietario::whereIn('id_propietario', $propietarioIds)->get()->keyBy('id_propietario');

        // Normalizar datos
        $normalized = collect();

        foreach ($docsConductor as $d) {
            $normalized->push((object) $this->mapDocumentoForExport($d, 'CONDUCTOR', $vehiculos, $conductores, $propietarios));
        }

        foreach ($docsVehiculo as $d) {
            $normalized->push((object) $this->mapDocumentoForExport($d, 'VEHICULO', $vehiculos, $conductores, $propietarios));
        }

        $documentos = $normalized->sortByDesc('Fecha registro')->values();

        $pdf = PDF::loadView('reportes.pdf.documentos', compact('documentos', 'request'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('documentos_report.pdf');
    }
}

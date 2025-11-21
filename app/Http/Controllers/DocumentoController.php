<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Conductor;
use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Models\Propietario;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\DocumentosCollectionExport;

use function Symfony\Component\String\u;

class DocumentoController extends Controller
{
    public function index(Request $request)
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
            'per_page' => 'nullable|integer|min:5|max:200',
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $page = (int) ($request->input('page', 1));

        // obtener collections filtradas
        $docsConductor = $this->getDocumentoConductorCollection($request);
        $docsVehiculo  = $this->getDocumentoVehiculoCollection($request);

        // normalizar y unir
        $normalizedConductor = $docsConductor->map(function ($d) {
            $conductor = $d->conductor ?? null;
            $veh = null;
            if ($conductor) {
                $veh = Vehiculo::where('id_conductor', $conductor->id_conductor)->first();
            }
            $propietario = $veh && $veh->id_propietario ? Propietario::find($veh->id_propietario) : null;

            return (object) [
                'id' => $d->id_doc_conductor ?? null,
                'source' => 'CONDUCTOR',
                'tipo_documento' => $d->tipo_documento,
                'numero_documento' => $d->numero_documento,
                'version' => $d->version ?? null,
                'fecha_registro' => $d->fecha_registro,
                'fecha_vencimiento' => $d->fecha_vencimiento,
                'estado' => $d->estado,
                'activo' => $d->activo ?? null,
                'conductor' => $conductor,
                'vehiculo' => $veh,
                'propietario' => $propietario,
                'raw' => $d,
            ];
        });

        $normalizedVehiculo = $docsVehiculo->map(function ($d) {
            $veh = $d->vehiculo ?? null;
            $conductor = $veh && $veh->id_conductor ? Conductor::find($veh->id_conductor) : null;
            $propietario = $veh && $veh->id_propietario ? Propietario::find($veh->id_propietario) : null;

            return (object) [
                'id' => $d->id_doc_vehiculo ?? null,
                'source' => 'VEHICULO',
                'tipo_documento' => $d->tipo_documento,
                'numero_documento' => $d->numero_documento,
                'version' => $d->version ?? null,
                'fecha_registro' => $d->fecha_registro,
                'fecha_vencimiento' => $d->fecha_vencimiento,
                'estado' => $d->estado,
                'activo' => $d->activo ?? null,
                'conductor' => $conductor,
                'vehiculo' => $veh,
                'propietario' => $propietario,
                'raw' => $d,
            ];
        });

        $all = $normalizedConductor->concat($normalizedVehiculo);

        $sorted = $all->sortByDesc(function ($item) {
            return $item->fecha_registro ? (is_object($item->fecha_registro) ? $item->fecha_registro->getTimestamp() : strtotime($item->fecha_registro)) : 0;
        })->values();

        $paginated = $this->paginateCollection($sorted, $perPage, $page, $request->url(), $request->query());

        // datos auxiliares
        $tipos = collect(array_merge(
            DocumentoConductor::select('tipo_documento')->distinct()->pluck('tipo_documento')->toArray(),
            DocumentoVehiculo::select('tipo_documento')->distinct()->pluck('tipo_documento')->toArray()
        ))->unique()->values();

        $propietarios = Propietario::orderBy('nombre')->orderBy('apellido')->get(['id_propietario', 'nombre', 'apellido']);
        $conductores = Conductor::orderBy('nombre')->orderBy('apellido')->get(['id_conductor', 'nombre', 'apellido']);
        $vehiculos = Vehiculo::orderBy('placa')->get(['id_vehiculo', 'placa', 'marca', 'modelo']);

        return view('reportes.index', [
            'documentos' => $paginated,
            'request' => $request,
            'tipos' => $tipos,
            'propietarios' => $propietarios,
            'conductores' => $conductores,
            'vehiculos' => $vehiculos,
        ]);
    }

    private function getDocumentoConductorCollection(Request $request)
    {
        $q = DocumentoConductor::query()->with('conductor');

        if ($docs = $request->input('documentos')) {
            // Normalizar: quitar espacios, pasar a mayúsculas
            $upperedDocs = array_map(function ($v) {
                return mb_strtoupper(trim($v));
            }, (array)$docs);
            // Comparación insensible a mayúsculas/espacios usando UPPER en la columna
            $q->whereIn(DB::raw('UPPER(TRIM(tipo_documento))'), $upperedDocs);
        }

        if ($estado = $request->input('estado')) {
            if ($estado === 'REEMPLAZADO') {
                $q->whereNotNull('reemplazado_por');
            } else {
                $q->where('estado', $estado);
            }
        }

        if ($texto = $request->input('conductor')) {
            $q->whereHas('conductor', function ($qr) use ($texto) {
                $qr->where('nombre', 'LIKE', "%{$texto}%")
                    ->orWhere('apellido', 'LIKE', "%{$texto}%")
                    ->orWhere('identificacion', 'LIKE', "%{$texto}%");
            });
        }

        if ($placa = $request->input('placa')) {
            $veh = Vehiculo::where('placa', $placa)->first();
            if ($veh && $veh->id_conductor) {
                $q->where('id_conductor', $veh->id_conductor);
            } else {
                return collect();
            }
        }

        if ($textoProp = $request->input('propietario')) {
            $propIds = Propietario::where('nombre', 'LIKE', "%{$textoProp}%")
                ->orWhere('apellido', 'LIKE', "%{$textoProp}%")
                ->orWhere('identificacion', 'LIKE', "%{$textoProp}%")
                ->pluck('id_propietario');

            if ($propIds->isEmpty()) return collect();

            $conductorIds = Vehiculo::whereIn('id_propietario', $propIds)
                ->pluck('id_conductor')->filter()->unique();

            if ($conductorIds->isEmpty()) return collect();

            $q->whereIn('id_conductor', $conductorIds);
        }

        if ($from = $request->input('fecha_from')) {
            $q->whereDate('fecha_registro', '>=', $from);
        }
        if ($to = $request->input('fecha_to')) {
            $q->whereDate('fecha_registro', '<=', $to);
        }

        return $q->get();
    }

    private function getDocumentoVehiculoCollection(Request $request)
    {
        $q = DocumentoVehiculo::query()->with('vehiculo');

        if ($docs = $request->input('documentos')) {
            // Normalizar: quitar espacios, pasar a mayúsculas
            $upperedDocs = array_map(function ($v) {
                return mb_strtoupper(trim($v));
            }, (array)$docs);
            // Comparación insensible a mayúsculas/espacios usando UPPER en la columna
            $q->whereIn(DB::raw('UPPER(TRIM(tipo_documento))'), $upperedDocs);
        }

        if ($estado = $request->input('estado')) {
            if ($estado === 'REEMPLAZADO') {
                $q->whereNotNull('reemplazado_por');
            } else {
                $q->where('estado', $estado);
            }
        }

        if ($texto = $request->input('conductor')) {
            $conductorIds = Conductor::where('nombre', 'LIKE', "%{$texto}%")
                ->orWhere('apellido', 'LIKE', "%{$texto}%")
                ->orWhere('identificacion', 'LIKE', "%{$texto}%")
                ->pluck('id_conductor');

            if ($conductorIds->isEmpty()) return collect();

            $vehIds = Vehiculo::whereIn('id_conductor', $conductorIds)->pluck('id_vehiculo');
            if ($vehIds->isEmpty()) return collect();

            $q->whereIn('id_vehiculo', $vehIds);
        }

        if ($placa = $request->input('placa')) {
            $veh = Vehiculo::where('placa', $placa)->first();
            if ($veh) {
                $q->where('id_vehiculo', $veh->id_vehiculo);
            } else {
                return collect();
            }
        }

        if ($textoProp = $request->input('propietario')) {
            $propIds = Propietario::where('nombre', 'LIKE', "%{$textoProp}%")
                ->orWhere('apellido', 'LIKE', "%{$textoProp}%")
                ->orWhere('identificacion', 'LIKE', "%{$textoProp}%")
                ->pluck('id_propietario');

            if ($propIds->isEmpty()) return collect();

            $vehIds = Vehiculo::whereIn('id_propietario', $propIds)->pluck('id_vehiculo');
            if ($vehIds->isEmpty()) return collect();

            $q->whereIn('id_vehiculo', $vehIds);
        }

        if ($from = $request->input('fecha_from')) {
            $q->whereDate('fecha_registro', '>=', $from);
        }
        if ($to = $request->input('fecha_to')) {
            $q->whereDate('fecha_registro', '<=', $to);
        }

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
        $total = $collection->count();
        $results = $collection->forPage($page, $perPage)->values();

        $paginator = new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => $path ?: url()->current(),
            'query' => $query ?: request()->query(),
        ]);

        return $paginator;
    }

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

        $normalized = collect();

        foreach ($docsConductor as $d) {
            $conductor = $d->conductor ?? null;
            $veh = $conductor ? Vehiculo::where('id_conductor', $conductor->id_conductor)->first() : null;
            $prop = $veh && $veh->id_propietario ? Propietario::find($veh->id_propietario) : null;
            $creadoPor = $veh ?  $this->getUsuarioNombreById($veh->creado_por ?? null) : '';


            $normalized->push([
                'Origen' => 'CONDUCTOR',
                'Tipo' => $d->tipo_documento,
                'Numero' => $d->numero_documento,
                'Conductor' => $conductor ? "{$conductor->nombre} {$conductor->apellido}" : '',
                'Version' => $d->version ?? '',
                'Fecha registro' => optional($d->fecha_registro)->format('Y-m-d H:i'),
                'Fecha vencimiento' => optional($d->fecha_vencimiento)->format('Y-m-d'),
                'Estado' => $d->estado,
                'Propietario' => $prop ? "{$prop->nombre} {$prop->apellido}" : '',
                'Placa' => $veh ? $veh->placa : '',
                'Creado por' => $creadoPor
            ]);
        }

        foreach ($docsVehiculo as $d) {
            $veh = $d->vehiculo ?? null;
            $conductor = $veh && $veh->id_conductor ? Conductor::find($veh->id_conductor) : null;
            $prop = $veh && $veh->id_propietario ? Propietario::find($veh->id_propietario) : null;
            $creadoPor = $veh ?  $this->getUsuarioNombreById($veh->creado_por ?? null) : '';

            $normalized->push([
                'Origen' => 'VEHICULO',
                'Tipo' => $d->tipo_documento,
                'Numero' => $d->numero_documento,
                'Conductor' => $conductor ? "{$conductor->nombre} {$conductor->apellido}" : '',
                'Version' => $d->version ?? '',
                'Fecha registro' => optional($d->fecha_registro)->format('Y-m-d H:i'),
                'Fecha vencimiento' => optional($d->fecha_vencimiento)->format('Y-m-d'),
                'Estado' => $d->estado,
                'Propietario' => $prop ? "{$prop->nombre} {$prop->apellido}" : '',
                'Placa' => $veh ? $veh->placa : '',
                'Creado por' => $creadoPor
            ]);
        }

        $collection = $normalized->sortByDesc(function ($row) {
            return $row['Fecha registro'] ? strtotime($row['Fecha registro']) : 0;
        })->values();

        return Excel::download(new DocumentosCollectionExport($collection), 'documentos_report.xlsx');
    }

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

        $docsConductor = $this->getDocumentoConductorCollection($request);
        $docsVehiculo  = $this->getDocumentoVehiculoCollection($request);

        $normalized = collect();

        foreach ($docsConductor as $d) {
            $conductor = $d->conductor ?? null;
            $veh = $conductor ? Vehiculo::where('id_conductor', $conductor->id_conductor)->first() : null;
            $prop = $veh && $veh->id_propietario ? Propietario::find($veh->id_propietario) : null;
            $creadoPor = $veh ? $this->getUsuarioNombreById($veh->creado_por ?? null) : '';

            // construimos un stdClass con propiedades tanto en lowercase (vista espera) como en TitleCase (compatibilidad)
            $obj = new \stdClass();
            // lowercase/unificado (vista PDF debe usar estas)
            $obj->tipo_documento = $d->tipo_documento;
            $obj->numero_documento = $d->numero_documento;
            $obj->version = $d->version ?? '';
            $obj->fecha_registro = optional($d->fecha_registro)->format('Y-m-d H:i');
            $obj->fecha_vencimiento = optional($d->fecha_vencimiento)->format('Y-m-d');
            $obj->estado = $d->estado;
            $obj->propietario = $prop ? "{$prop->nombre} {$prop->apellido}" : '';
            $obj->placa = $veh ? $veh->placa : '';
            $obj->creado_por = $creadoPor;
            $obj->origen = 'CONDUCTOR';
            $obj->conductor = $conductor ? "{$conductor->nombre} {$conductor->apellido}" : '';

            // también dejamos las claves tipo "Tipo"/"Numero" por si alguna otra parte las usa
            $obj->Tipo = $obj->tipo_documento;
            $obj->Numero = $obj->numero_documento;
            $obj->Placa = $obj->placa;
            $obj->{"Fecha registro"} = $obj->fecha_registro;
            $obj->{"Fecha vencimiento"} = $obj->fecha_vencimiento;
            $obj->Estado = $obj->estado;
            $obj->{"Placa registrada por"} = $obj->creado_por;

            $normalized->push($obj);
        }

        foreach ($docsVehiculo as $d) {
            $veh = $d->vehiculo ?? null;
            $conductor = $veh && $veh->id_conductor ? Conductor::find($veh->id_conductor) : null;
            $prop = $veh && $veh->id_propietario ? Propietario::find($veh->id_propietario) : null;
            $creadoPor = $veh ? $this->getUsuarioNombreById($veh->creado_por ?? null) : '';

            $obj = new \stdClass();
            $obj->tipo_documento = $d->tipo_documento;
            $obj->numero_documento = $d->numero_documento;
            $obj->version = $d->version ?? '';
            $obj->fecha_registro = optional($d->fecha_registro)->format('Y-m-d H:i');
            $obj->fecha_vencimiento = optional($d->fecha_vencimiento)->format('Y-m-d');
            $obj->estado = $d->estado;
            $obj->propietario = $prop ? "{$prop->nombre} {$prop->apellido}" : '';
            $obj->placa = $veh ? $veh->placa : '';
            $obj->creado_por = $creadoPor;
            $obj->origen = 'VEHICULO';
            $obj->conductor = $conductor ? "{$conductor->nombre} {$conductor->apellido}" : '';

            $obj->Tipo = $obj->tipo_documento;
            $obj->Numero = $obj->numero_documento;
            $obj->Placa = $obj->placa;
            $obj->{"Fecha registro"} = $obj->fecha_registro;
            $obj->{"Fecha vencimiento"} = $obj->fecha_vencimiento;
            $obj->Estado = $obj->estado;
            $obj->{"Placa registrada por"} = $obj->creado_por;

            $normalized->push($obj);
        }

        $documentos = $normalized->sortByDesc('fecha_registro')->values();

        $pdf = PDF::loadView('reportes.pdf.documentos', compact('documentos', 'request'));
        return $pdf->download('documentos_report.pdf');
    }


    public function download($documentoId)
    {
        $docC = DocumentoConductor::find($documentoId);
        if ($docC && !empty($docC->ruta_archivo) && Storage::disk('public')->exists($docC->ruta_archivo)) {
            return Storage::disk('public')->download($docC->ruta_archivo);
        }

        $docV = DocumentoVehiculo::find($documentoId);
        if ($docV && !empty($docV->ruta_archivo) && Storage::disk('public')->exists($docV->ruta_archivo)) {
            return Storage::disk('public')->download($docV->ruta_archivo);
        }

        return redirect()->back()->with('error', 'Archivo no disponible para este documento.');
    }
}

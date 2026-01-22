<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Propietario;
use App\Models\Conductor;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Vista principal del módulo de reportes
     */
    public function index()
    {
        $navbarEspecial = true;

        $stats = [
            'total_vehiculos' => Vehiculo::where('estado', 'Activo')->count(),
            'total_propietarios' => Propietario::count(),
            'total_conductores' => Conductor::where('activo', 1)->count(),
            'docs_vigentes' => DocumentoVehiculo::where('activo', 1)->where('estado', 'VIGENTE')->count(),
            'docs_por_vencer' => DocumentoVehiculo::where('activo', 1)->where('estado', 'POR_VENCER')->count(),
            'docs_vencidos' => DocumentoVehiculo::where('activo', 1)->where('estado', 'VENCIDO')->count(),
        ];

        return view('reportes.centro', compact('stats', 'navbarEspecial'));
    }

    /**
     * 1. REPORTE GENERAL DE VEHÍCULOS
     */
    public function vehiculos(Request $request)
    {
        $navbarEspecial = true;

        $query = Vehiculo::with(['propietario', 'conductor', 'documentos' => function($q) {
            $q->where('activo', 1);
        }])->where('estado', 'Activo');

        $estadoFiltro = $request->input('estado_docs');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('propietario')) {
            $query->where('id_propietario', $request->propietario);
        }

        if ($request->filled('placa')) {
            $query->where('placa', 'LIKE', '%' . strtoupper($request->placa) . '%');
        }

        $vehiculos = $query->orderBy('placa')->get();

        $vehiculos = $vehiculos->map(function($vehiculo) {
            $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
            return $vehiculo;
        });

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            $vehiculos = $vehiculos->filter(function($v) use ($estadoFiltro) {
                return $v->estado_general['estado'] === $estadoFiltro;
            });
        }

        $estadisticas = [
            'total' => $vehiculos->count(),
            'vigentes' => $vehiculos->where('estado_general.estado', 'VIGENTE')->count(),
            'por_vencer' => $vehiculos->where('estado_general.estado', 'POR_VENCER')->count(),
            'vencidos' => $vehiculos->where('estado_general.estado', 'VENCIDO')->count(),
            'sin_docs' => $vehiculos->where('estado_general.estado', 'SIN_DOCUMENTOS')->count(),
        ];

        $propietarios = Propietario::orderBy('nombre')->get();

        return view('reportes.vehiculos', compact('vehiculos', 'estadisticas', 'propietarios', 'navbarEspecial'));
    }

    /**
     * 2. REPORTE DE DOCUMENTACIÓN POR VEHÍCULO (Ficha)
     */
    public function fichaVehiculo($id)
    {
        $navbarEspecial = true;

        $vehiculo = Vehiculo::with([
            'propietario',
            'conductor.documentosConductor' => function($q) {
                $q->where('activo', 1);
            },
            'documentos' => function($q) {
                $q->where('activo', 1)->orderBy('tipo_documento');
            }
        ])->findOrFail($id);

        $estadosDocumentos = $this->calcularEstadosDocumentosDetallado($vehiculo);
        $historialReciente = DocumentoVehiculo::where('id_vehiculo', $id)
            ->where('activo', 0)
            ->orderByDesc('fecha_registro')
            ->take(10)
            ->get();

        return view('reportes.ficha-vehiculo', compact('vehiculo', 'estadosDocumentos', 'historialReciente', 'navbarEspecial'));
    }

    /**
     * Exportar ficha de vehículo a PDF
     */
    public function fichaVehiculoPdf($id)
    {
        $vehiculo = Vehiculo::with([
            'propietario',
            'conductor.documentosConductor' => function($q) {
                $q->where('activo', 1);
            },
            'documentos' => function($q) {
                $q->where('activo', 1)->orderBy('tipo_documento');
            }
        ])->findOrFail($id);

        $estadosDocumentos = $this->calcularEstadosDocumentosDetallado($vehiculo);
        $historialReciente = DocumentoVehiculo::where('id_vehiculo', $id)
            ->where('activo', 0)
            ->orderByDesc('fecha_registro')
            ->take(10)
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.ficha-vehiculo', compact('vehiculo', 'estadosDocumentos', 'historialReciente'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('ficha_vehiculo_' . $vehiculo->placa . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * 3. REPORTE DE ALERTAS
     */
    public function alertas(Request $request)
    {
        $navbarEspecial = true;

        $diasProximoVencer = $request->input('dias', 30);
        $tipoFiltro = $request->input('tipo_documento');
        $estadoFiltro = $request->input('estado_alerta');

        $queryVehiculos = DocumentoVehiculo::with(['vehiculo.propietario', 'vehiculo.conductor'])
            ->where('activo', 1)
            ->whereIn('estado', ['POR_VENCER', 'VENCIDO']);

        if ($tipoFiltro) {
            $queryVehiculos->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            $queryVehiculos->where('estado', $estadoFiltro);
        }

        $documentosVehiculos = $queryVehiculos->orderBy('fecha_vencimiento')->get();

        $queryConductores = DocumentoConductor::with(['conductor'])
            ->where('activo', 1)
            ->whereIn('estado', ['POR_VENCER', 'VENCIDO']);

        if ($tipoFiltro) {
            $queryConductores->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            $queryConductores->where('estado', $estadoFiltro);
        }

        $documentosConductores = $queryConductores->orderBy('fecha_vencimiento')->get();

        $estadisticas = [
            'vehiculos_por_vencer' => $documentosVehiculos->where('estado', 'POR_VENCER')->count(),
            'vehiculos_vencidos' => $documentosVehiculos->where('estado', 'VENCIDO')->count(),
            'conductores_por_vencer' => $documentosConductores->where('estado', 'POR_VENCER')->count(),
            'conductores_vencidos' => $documentosConductores->where('estado', 'VENCIDO')->count(),
        ];

        $lineaTiempo = $this->generarLineaTiempo($documentosVehiculos, $documentosConductores);

        $tiposDocumentoVehiculo = ['SOAT', 'Tecnomecánica', 'Tarjeta Propiedad', 'Póliza', 'Otro'];
        $tiposDocumentoConductor = ['Licencia Conducción', 'EPS', 'ARL', 'Certificado Médico', 'Otro'];

        return view('reportes.alertas', compact(
            'documentosVehiculos',
            'documentosConductores',
            'estadisticas',
            'lineaTiempo',
            'tiposDocumentoVehiculo',
            'tiposDocumentoConductor',
            'diasProximoVencer',
            'navbarEspecial'
        ));
    }

    /**
     * 4. REPORTE POR PROPIETARIO
     */
    public function propietarios(Request $request)
    {
        $navbarEspecial = true;

        $query = Propietario::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo')->with(['documentos' => function($q2) {
                $q2->where('activo', 1);
            }, 'conductor']);
        }]);

        if ($request->filled('propietario')) {
            $query->where('id_propietario', $request->propietario);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('identificacion', 'LIKE', "%{$buscar}%");
            });
        }

        $propietarios = $query->orderBy('nombre')->get();

        $propietarios = $propietarios->map(function($propietario) {
            $propietario->vehiculos = $propietario->vehiculos->map(function($vehiculo) {
                $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
                return $vehiculo;
            });

            $propietario->stats = [
                'total_vehiculos' => $propietario->vehiculos->count(),
                'vigentes' => $propietario->vehiculos->where('estado_general.estado', 'VIGENTE')->count(),
                'por_vencer' => $propietario->vehiculos->where('estado_general.estado', 'POR_VENCER')->count(),
                'vencidos' => $propietario->vehiculos->where('estado_general.estado', 'VENCIDO')->count(),
            ];

            return $propietario;
        });

        $estadisticas = [
            'total_propietarios' => $propietarios->count(),
            'total_vehiculos' => $propietarios->sum('stats.total_vehiculos'),
            'vehiculos_vigentes' => $propietarios->sum('stats.vigentes'),
            'vehiculos_por_vencer' => $propietarios->sum('stats.por_vencer'),
            'vehiculos_vencidos' => $propietarios->sum('stats.vencidos'),
        ];

        return view('reportes.propietarios', compact('propietarios', 'estadisticas', 'navbarEspecial'));
    }

    /**
     * 5. REPORTE HISTÓRICO
     */
    public function historico(Request $request)
    {
        $navbarEspecial = true;

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        $tipoDocumento = $request->input('tipo_documento');
        $placa = $request->input('placa');

        $queryVehiculos = DocumentoVehiculo::with(['vehiculo.propietario'])
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin . ' 23:59:59']);

        if ($tipoDocumento) {
            $queryVehiculos->where('tipo_documento', $tipoDocumento);
        }

        if ($placa) {
            $queryVehiculos->whereHas('vehiculo', function($q) use ($placa) {
                $q->where('placa', 'LIKE', '%' . strtoupper($placa) . '%');
            });
        }

        $historialVehiculos = $queryVehiculos->orderByDesc('fecha_registro')->get();

        $queryConductores = DocumentoConductor::with(['conductor'])
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin . ' 23:59:59']);

        if ($tipoDocumento) {
            $queryConductores->where('tipo_documento', $tipoDocumento);
        }

        $historialConductores = $queryConductores->orderByDesc('fecha_registro')->get();

        $estadisticas = [
            'renovaciones_vehiculos' => $historialVehiculos->where('version', '>', 1)->count(),
            'nuevos_vehiculos' => $historialVehiculos->where('version', 1)->count(),
            'renovaciones_conductores' => $historialConductores->where('version', '>', 1)->count(),
            'nuevos_conductores' => $historialConductores->where('version', 1)->count(),
            'documentos_vencidos' => $historialVehiculos->where('estado', 'VENCIDO')->count()
                                   + $historialConductores->where('estado', 'VENCIDO')->count(),
        ];

        $cronologia = $this->generarCronologia($historialVehiculos, $historialConductores);

        $tiposDocumento = [
            'vehiculo' => ['SOAT', 'Tecnomecánica', 'Tarjeta Propiedad', 'Póliza', 'Otro'],
            'conductor' => ['Licencia Conducción', 'EPS', 'ARL', 'Certificado Médico', 'Otro']
        ];

        return view('reportes.historico', compact(
            'historialVehiculos',
            'historialConductores',
            'estadisticas',
            'cronologia',
            'tiposDocumento',
            'fechaInicio',
            'fechaFin',
            'navbarEspecial'
        ));
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportPdf(Request $request, $tipo)
    {
        switch ($tipo) {
            case 'vehiculos':
                return $this->exportVehiculosPdf($request);
            case 'alertas':
                return $this->exportAlertasPdf($request);
            case 'propietarios':
                return $this->exportPropietariosPdf($request);
            case 'historico':
                return $this->exportHistoricoPdf($request);
            default:
                return back()->with('error', 'Tipo de reporte no válido');
        }
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportExcel(Request $request, $tipo)
    {
        switch ($tipo) {
            case 'vehiculos':
                return $this->exportVehiculosExcel($request);
            case 'alertas':
                return $this->exportAlertasExcel($request);
            case 'propietarios':
                return $this->exportPropietariosExcel($request);
            case 'historico':
                return $this->exportHistoricoExcel($request);
            default:
                return back()->with('error', 'Tipo de reporte no válido');
        }
    }

    // ==================== EXPORTACIONES PDF ====================

    private function exportVehiculosPdf(Request $request)
    {
        $query = Vehiculo::with(['propietario', 'conductor', 'documentos' => function($q) {
            $q->where('activo', 1);
        }])->where('estado', 'Activo');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('propietario')) {
            $query->where('id_propietario', $request->propietario);
        }

        if ($request->filled('placa')) {
            $query->where('placa', 'LIKE', '%' . strtoupper($request->placa) . '%');
        }

        $vehiculos = $query->orderBy('placa')->get();

        $vehiculos = $vehiculos->map(function($vehiculo) {
            $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
            return $vehiculo;
        });

        $estadoFiltro = $request->input('estado_docs');
        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            $vehiculos = $vehiculos->filter(function($v) use ($estadoFiltro) {
                return $v->estado_general['estado'] === $estadoFiltro;
            });
        }

        $pdf = Pdf::loadView('reportes.pdf.vehiculos', compact('vehiculos'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('reporte_vehiculos_' . date('Y-m-d') . '.pdf');
    }

    private function exportAlertasPdf(Request $request)
    {
        $tipoFiltro = $request->input('tipo_documento');
        $estadoFiltro = $request->input('estado_alerta');

        $queryVehiculos = DocumentoVehiculo::with(['vehiculo.propietario'])
            ->where('activo', 1)
            ->whereIn('estado', ['POR_VENCER', 'VENCIDO']);

        if ($tipoFiltro) {
            $queryVehiculos->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            $queryVehiculos->where('estado', $estadoFiltro);
        }

        $documentosVehiculos = $queryVehiculos->orderBy('fecha_vencimiento')->get();

        $queryConductores = DocumentoConductor::with(['conductor'])
            ->where('activo', 1)
            ->whereIn('estado', ['POR_VENCER', 'VENCIDO']);

        if ($tipoFiltro) {
            $queryConductores->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            $queryConductores->where('estado', $estadoFiltro);
        }

        $documentosConductores = $queryConductores->orderBy('fecha_vencimiento')->get();

        $pdf = Pdf::loadView('reportes.pdf.alertas', compact('documentosVehiculos', 'documentosConductores'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('reporte_alertas_' . date('Y-m-d') . '.pdf');
    }

    private function exportPropietariosPdf(Request $request)
    {
        $query = Propietario::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo')->with(['documentos' => function($q2) {
                $q2->where('activo', 1);
            }]);
        }]);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('identificacion', 'LIKE', "%{$buscar}%");
            });
        }

        $propietarios = $query->orderBy('nombre')->get();

        $propietarios = $propietarios->map(function($propietario) {
            $propietario->vehiculos = $propietario->vehiculos->map(function($vehiculo) {
                $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
                return $vehiculo;
            });
            return $propietario;
        });

        $pdf = Pdf::loadView('reportes.pdf.propietarios', compact('propietarios'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('reporte_propietarios_' . date('Y-m-d') . '.pdf');
    }

    private function exportHistoricoPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $historialVehiculos = DocumentoVehiculo::with(['vehiculo'])
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderByDesc('fecha_registro')
            ->get();

        $historialConductores = DocumentoConductor::with(['conductor'])
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderByDesc('fecha_registro')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.historico', compact('historialVehiculos', 'historialConductores', 'fechaInicio', 'fechaFin'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('reporte_historico_' . date('Y-m-d') . '.pdf');
    }

    // ==================== EXPORTACIONES EXCEL (CSV) ====================

    private function exportVehiculosExcel(Request $request)
    {
        $query = Vehiculo::with(['propietario', 'conductor', 'documentos' => function($q) {
            $q->where('activo', 1);
        }])->where('estado', 'Activo');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $vehiculos = $query->orderBy('placa')->get();

        $vehiculos = $vehiculos->map(function($vehiculo) {
            $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
            return $vehiculo;
        });

        $filename = 'reporte_vehiculos_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($vehiculos) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, ['Placa', 'Tipo', 'Marca', 'Modelo', 'Color', 'Propietario', 'Conductor', 'Estado Documental']);

            foreach ($vehiculos as $v) {
                fputcsv($file, [
                    $v->placa,
                    $v->tipo,
                    $v->marca,
                    $v->modelo,
                    $v->color,
                    $v->propietario ? $v->propietario->nombre . ' ' . $v->propietario->apellido : 'Sin propietario',
                    $v->conductor ? $v->conductor->nombre . ' ' . $v->conductor->apellido : 'Sin conductor',
                    $v->estado_general['texto']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAlertasExcel(Request $request)
    {
        $documentosVehiculos = DocumentoVehiculo::with(['vehiculo'])
            ->where('activo', 1)
            ->whereIn('estado', ['POR_VENCER', 'VENCIDO'])
            ->orderBy('fecha_vencimiento')
            ->get();

        $documentosConductores = DocumentoConductor::with(['conductor'])
            ->where('activo', 1)
            ->whereIn('estado', ['POR_VENCER', 'VENCIDO'])
            ->orderBy('fecha_vencimiento')
            ->get();

        $filename = 'reporte_alertas_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($documentosVehiculos, $documentosConductores) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Tipo', 'Referencia', 'Documento', 'Vencimiento', 'Estado', 'Días']);

            foreach ($documentosVehiculos as $doc) {
                $dias = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
                fputcsv($file, [
                    'Vehículo',
                    $doc->vehiculo->placa ?? 'N/A',
                    $doc->tipo_documento,
                    Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y'),
                    $doc->estado,
                    $dias
                ]);
            }

            foreach ($documentosConductores as $doc) {
                $dias = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
                fputcsv($file, [
                    'Conductor',
                    $doc->conductor ? $doc->conductor->nombre . ' ' . $doc->conductor->apellido : 'N/A',
                    $doc->tipo_documento,
                    Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y'),
                    $doc->estado,
                    $dias
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPropietariosExcel(Request $request)
    {
        $propietarios = Propietario::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo');
        }])->orderBy('nombre')->get();

        $filename = 'reporte_propietarios_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($propietarios) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Propietario', 'Documento', 'Identificación', 'Vehículos', 'Placas']);

            foreach ($propietarios as $p) {
                $placas = $p->vehiculos->pluck('placa')->implode(', ');
                fputcsv($file, [
                    $p->nombre . ' ' . $p->apellido,
                    $p->tipo_doc,
                    $p->identificacion,
                    $p->vehiculos->count(),
                    $placas
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportHistoricoExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $historialVehiculos = DocumentoVehiculo::with(['vehiculo'])
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderByDesc('fecha_registro')
            ->get();

        $historialConductores = DocumentoConductor::with(['conductor'])
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderByDesc('fecha_registro')
            ->get();

        $filename = 'reporte_historico_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($historialVehiculos, $historialConductores) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Fecha', 'Tipo', 'Referencia', 'Documento', 'Acción', 'Estado', 'Versión']);

            foreach ($historialVehiculos as $doc) {
                fputcsv($file, [
                    Carbon::parse($doc->fecha_registro)->format('d/m/Y H:i'),
                    'Vehículo',
                    $doc->vehiculo->placa ?? 'N/A',
                    $doc->tipo_documento,
                    $doc->version > 1 ? 'Renovación' : 'Nuevo',
                    $doc->estado,
                    'v' . $doc->version
                ]);
            }

            foreach ($historialConductores as $doc) {
                fputcsv($file, [
                    Carbon::parse($doc->fecha_registro)->format('d/m/Y H:i'),
                    'Conductor',
                    $doc->conductor ? $doc->conductor->nombre . ' ' . $doc->conductor->apellido : 'N/A',
                    $doc->tipo_documento,
                    $doc->version > 1 ? 'Renovación' : 'Nuevo',
                    $doc->estado,
                    'v' . $doc->version
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== MÉTODOS AUXILIARES ====================

    private function calcularEstadoGeneral($vehiculo)
    {
        $documentos = $vehiculo->documentos;

        if ($documentos->isEmpty()) {
            return [
                'estado' => 'SIN_DOCUMENTOS',
                'clase' => 'secondary',
                'icono' => 'fas fa-question-circle',
                'texto' => 'Sin documentos'
            ];
        }

        $tieneVencido = $documentos->where('estado', 'VENCIDO')->count() > 0;
        $tienePorVencer = $documentos->where('estado', 'POR_VENCER')->count() > 0;

        if ($tieneVencido) {
            return [
                'estado' => 'VENCIDO',
                'clase' => 'danger',
                'icono' => 'fas fa-times-circle',
                'texto' => 'Documentos vencidos'
            ];
        }

        if ($tienePorVencer) {
            return [
                'estado' => 'POR_VENCER',
                'clase' => 'warning',
                'icono' => 'fas fa-exclamation-triangle',
                'texto' => 'Próximo a vencer'
            ];
        }

        return [
            'estado' => 'VIGENTE',
            'clase' => 'success',
            'icono' => 'fas fa-check-circle',
            'texto' => 'Documentos vigentes'
        ];
    }

    private function calcularEstadosDocumentosDetallado($vehiculo)
    {
        $estados = [];
        $tiposVehiculo = ['SOAT', 'Tecnomecánica', 'Tarjeta Propiedad', 'Póliza'];

        foreach ($tiposVehiculo as $tipo) {
            $doc = $vehiculo->documentos->where('tipo_documento', $tipo)->first();

            if (!$doc) {
                $estados[$tipo] = [
                    'estado' => 'SIN_REGISTRO',
                    'clase' => 'secondary',
                    'documento' => null,
                    'dias_restantes' => null,
                    'mensaje' => 'No registrado'
                ];
            } else {
                $diasRestantes = $doc->diasRestantes();
                $estados[$tipo] = [
                    'estado' => $doc->estado,
                    'clase' => $this->getClaseEstado($doc->estado),
                    'documento' => $doc,
                    'dias_restantes' => $diasRestantes,
                    'mensaje' => $this->getMensajeEstado($doc->estado, $diasRestantes)
                ];
            }
        }

        if ($vehiculo->conductor) {
            $tiposConductor = ['Licencia Conducción'];
            foreach ($tiposConductor as $tipo) {
                $doc = $vehiculo->conductor->documentosConductor->where('tipo_documento', $tipo)->first();

                if (!$doc) {
                    $estados['conductor_' . $tipo] = [
                        'estado' => 'SIN_REGISTRO',
                        'clase' => 'secondary',
                        'documento' => null,
                        'dias_restantes' => null,
                        'mensaje' => 'No registrado'
                    ];
                } else {
                    $diasRestantes = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
                    $estados['conductor_' . $tipo] = [
                        'estado' => $doc->estado,
                        'clase' => $this->getClaseEstado($doc->estado),
                        'documento' => $doc,
                        'dias_restantes' => $diasRestantes,
                        'mensaje' => $this->getMensajeEstado($doc->estado, $diasRestantes)
                    ];
                }
            }
        }

        return $estados;
    }

    private function generarLineaTiempo($docsVehiculos, $docsConductores)
    {
        $hoy = Carbon::now();
        $limite = Carbon::now()->addDays(90);

        $eventos = collect();

        foreach ($docsVehiculos as $doc) {
            if ($doc->fecha_vencimiento && Carbon::parse($doc->fecha_vencimiento)->between($hoy, $limite)) {
                $eventos->push([
                    'fecha' => $doc->fecha_vencimiento,
                    'tipo' => 'vehiculo',
                    'documento' => $doc->tipo_documento,
                    'referencia' => $doc->vehiculo->placa ?? 'N/A',
                    'estado' => $doc->estado,
                    'dias' => Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false)
                ]);
            }
        }

        foreach ($docsConductores as $doc) {
            if ($doc->fecha_vencimiento && Carbon::parse($doc->fecha_vencimiento)->between($hoy, $limite)) {
                $eventos->push([
                    'fecha' => $doc->fecha_vencimiento,
                    'tipo' => 'conductor',
                    'documento' => $doc->tipo_documento,
                    'referencia' => $doc->conductor->nombre . ' ' . $doc->conductor->apellido ?? 'N/A',
                    'estado' => $doc->estado,
                    'dias' => Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false)
                ]);
            }
        }

        return $eventos->sortBy('fecha')->groupBy(function($item) {
            return Carbon::parse($item['fecha'])->format('Y-m');
        });
    }

    private function generarCronologia($historialVehiculos, $historialConductores)
    {
        $cronologia = collect();

        foreach ($historialVehiculos as $doc) {
            $cronologia->push([
                'fecha' => $doc->fecha_registro,
                'tipo' => 'vehiculo',
                'accion' => $doc->version > 1 ? 'Renovación' : 'Registro inicial',
                'documento' => $doc->tipo_documento,
                'referencia' => $doc->vehiculo->placa ?? 'N/A',
                'estado' => $doc->estado,
                'version' => $doc->version
            ]);
        }

        foreach ($historialConductores as $doc) {
            $cronologia->push([
                'fecha' => $doc->fecha_registro,
                'tipo' => 'conductor',
                'accion' => $doc->version > 1 ? 'Renovación' : 'Registro inicial',
                'documento' => $doc->tipo_documento,
                'referencia' => $doc->conductor->nombre . ' ' . $doc->conductor->apellido ?? 'N/A',
                'estado' => $doc->estado,
                'version' => $doc->version
            ]);
        }

        return $cronologia->sortByDesc('fecha')->groupBy(function($item) {
            return Carbon::parse($item['fecha'])->format('Y-m');
        });
    }

    private function getClaseEstado($estado)
    {
        return match($estado) {
            'VIGENTE' => 'success',
            'POR_VENCER' => 'warning',
            'VENCIDO' => 'danger',
            default => 'secondary'
        };
    }

    private function getMensajeEstado($estado, $dias)
    {
        return match($estado) {
            'VIGENTE' => "Vigente ({$dias} días restantes)",
            'POR_VENCER' => "Vence en {$dias} días",
            'VENCIDO' => "Vencido hace " . abs($dias) . " días",
            default => 'Estado desconocido'
        };
    }
}

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

        $hoy = Carbon::today();
        $limite20Dias = Carbon::today()->addDays(20); // Cambio de 30 a 20 días

        $stats = [
            'total_vehiculos' => Vehiculo::where('estado', 'Activo')->count(),
            'total_propietarios' => Propietario::count(),
            'total_conductores' => Conductor::where('activo', 1)->count(),
            'docs_vigentes' => DocumentoVehiculo::where('activo', 1)
                ->where('fecha_vencimiento', '>', $limite20Dias)
                ->count(),
            'docs_por_vencer' => DocumentoVehiculo::where('activo', 1)
                ->where('fecha_vencimiento', '>=', $hoy)
                ->where('fecha_vencimiento', '<=', $limite20Dias)
                ->count(),
            'docs_vencidos' => DocumentoVehiculo::where('activo', 1)
                ->where('fecha_vencimiento', '<', $hoy)
                ->count(),
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

        $diasProximoVencer = (int) $request->input('dias', 30);
        $tipoFiltro = $request->input('tipo_documento');
        $estadoFiltro = $request->input('estado_alerta');

        $hoy = Carbon::today();
        $limiteDias = Carbon::today()->addDays($diasProximoVencer);

        // Query para documentos de vehículos (por vencer o vencidos)
        $queryVehiculos = DocumentoVehiculo::with(['vehiculo.propietario', 'vehiculo.conductor'])
            ->where('activo', 1)
            ->where('fecha_vencimiento', '<=', $limiteDias);

        if ($tipoFiltro) {
            $queryVehiculos->where('tipo_documento', $tipoFiltro);
        }

        // Filtrar por estado basado en fecha
        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            if ($estadoFiltro === 'POR_VENCER') {
                $queryVehiculos->where('fecha_vencimiento', '>=', $hoy)
                               ->where('fecha_vencimiento', '<=', $limiteDias);
            } elseif ($estadoFiltro === 'VENCIDO') {
                $queryVehiculos->where('fecha_vencimiento', '<', $hoy);
            }
        }

        $documentosVehiculos = $queryVehiculos->orderBy('fecha_vencimiento')->get();

        // Query para documentos de conductores (por vencer o vencidos)
        $queryConductores = DocumentoConductor::with(['conductor'])
            ->where('activo', 1)
            ->where('fecha_vencimiento', '<=', $limiteDias);

        if ($tipoFiltro) {
            $queryConductores->where('tipo_documento', $tipoFiltro);
        }

        // Filtrar por estado basado en fecha
        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            if ($estadoFiltro === 'POR_VENCER') {
                $queryConductores->where('fecha_vencimiento', '>=', $hoy)
                                 ->where('fecha_vencimiento', '<=', $limiteDias);
            } elseif ($estadoFiltro === 'VENCIDO') {
                $queryConductores->where('fecha_vencimiento', '<', $hoy);
            }
        }

        $documentosConductores = $queryConductores->orderBy('fecha_vencimiento')->get();

        // Estadísticas usando el accessor 'estado' para consistencia
        $estadisticas = [
            'vehiculos_por_vencer' => $documentosVehiculos->filter(fn($d) => $d->estado === 'POR_VENCER')->count(),
            'vehiculos_vencidos' => $documentosVehiculos->filter(fn($d) => $d->estado === 'VENCIDO')->count(),
            'conductores_por_vencer' => $documentosConductores->filter(fn($d) => $d->estado === 'POR_VENCER')->count(),
            'conductores_vencidos' => $documentosConductores->filter(fn($d) => $d->estado === 'VENCIDO')->count(),
        ];

        $lineaTiempo = $this->generarLineaTiempo($documentosVehiculos, $documentosConductores);

        $tiposDocumentoVehiculo = ['SOAT', 'Tecnomecanica', 'Tarjeta Propiedad', 'Póliza', 'Otro'];
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
     * 5. REPORTE POR CONDUCTOR
     */
    public function conductores(Request $request)
    {
        $navbarEspecial = true;

        $query = Conductor::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo')->with(['documentos' => function($q2) {
                $q2->where('activo', 1);
            }, 'propietario']);
        }, 'documentosConductor' => function($q) {
            $q->where('activo', 1);
        }])->where('activo', 1);

        if ($request->filled('conductor')) {
            $query->where('id_conductor', $request->conductor);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('identificacion', 'LIKE', "%{$buscar}%");
            });
        }

        $conductores = $query->orderBy('nombre')->get();

        // Calcular estado documental de cada conductor
        $conductores = $conductores->map(function($conductor) {
            // Estado de documentos del conductor (licencia)
            $conductor->estado_documentos = $this->calcularEstadoDocumentosConductor($conductor);

            // Calcular estado de cada vehículo asignado
            $conductor->vehiculos = $conductor->vehiculos->map(function($vehiculo) {
                $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
                return $vehiculo;
            });

            // Estadísticas del conductor
            $conductor->stats = [
                'total_vehiculos' => $conductor->vehiculos->count(),
                'vehiculos_vigentes' => $conductor->vehiculos->where('estado_general.estado', 'VIGENTE')->count(),
                'vehiculos_por_vencer' => $conductor->vehiculos->where('estado_general.estado', 'POR_VENCER')->count(),
                'vehiculos_vencidos' => $conductor->vehiculos->where('estado_general.estado', 'VENCIDO')->count(),
            ];

            return $conductor;
        });

        // Estadísticas generales
        $estadisticas = [
            'total_conductores' => $conductores->count(),
            'total_vehiculos' => $conductores->sum('stats.total_vehiculos'),
            'vehiculos_vigentes' => $conductores->sum('stats.vehiculos_vigentes'),
            'vehiculos_por_vencer' => $conductores->sum('stats.vehiculos_por_vencer'),
            'vehiculos_vencidos' => $conductores->sum('stats.vehiculos_vencidos'),
            'licencias_vigentes' => $conductores->where('estado_documentos.estado', 'VIGENTE')->count(),
            'licencias_por_vencer' => $conductores->where('estado_documentos.estado', 'POR_VENCER')->count(),
            'licencias_vencidas' => $conductores->where('estado_documentos.estado', 'VENCIDO')->count(),
        ];

        // Lista de todos los conductores para el filtro
        $listaConductores = Conductor::where('activo', 1)->orderBy('nombre')->get();

        return view('reportes.conductores', compact('conductores', 'estadisticas', 'listaConductores', 'navbarEspecial'));
    }

    /**
     * Calcular estado documental del conductor (principalmente licencia)
     */
    private function calcularEstadoDocumentosConductor($conductor)
    {
        $documentos = $conductor->documentosConductor;

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
                'texto' => 'Licencia vencida'
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

    /**
     * 6. REPORTE HISTÓRICO
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
            'vehiculo' => ['SOAT', 'Tecnomecanica', 'Tarjeta Propiedad', 'Póliza', 'Otro'],
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
            case 'conductores':
                return $this->exportConductoresPdf($request);
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
            case 'conductores':
                return $this->exportConductoresExcel($request);
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

        $hoy = Carbon::today();
        $limiteDias = Carbon::today()->addDays(20); // Cambio de 30 a 20 días

        // Query base: documentos activos con vencimiento dentro de 20 días o ya vencidos
        $queryVehiculos = DocumentoVehiculo::with(['vehiculo.propietario'])
            ->where('activo', 1)
            ->where('fecha_vencimiento', '<=', $limiteDias);

        if ($tipoFiltro) {
            $queryVehiculos->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            if ($estadoFiltro === 'POR_VENCER') {
                $queryVehiculos->where('fecha_vencimiento', '>=', $hoy)
                               ->where('fecha_vencimiento', '<=', $limiteDias);
            } elseif ($estadoFiltro === 'VENCIDO') {
                $queryVehiculos->where('fecha_vencimiento', '<', $hoy);
            }
        }

        $documentosVehiculos = $queryVehiculos->orderBy('fecha_vencimiento')->get();

        $queryConductores = DocumentoConductor::with(['conductor'])
            ->where('activo', 1)
            ->where('fecha_vencimiento', '<=', $limiteDias);

        if ($tipoFiltro) {
            $queryConductores->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            if ($estadoFiltro === 'POR_VENCER') {
                $queryConductores->where('fecha_vencimiento', '>=', $hoy)
                                 ->where('fecha_vencimiento', '<=', $limiteDias);
            } elseif ($estadoFiltro === 'VENCIDO') {
                $queryConductores->where('fecha_vencimiento', '<', $hoy);
            }
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

    private function exportConductoresPdf(Request $request)
    {
        $query = Conductor::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo')->with(['documentos' => function($q2) {
                $q2->where('activo', 1);
            }]);
        }, 'documentosConductor' => function($q) {
            $q->where('activo', 1);
        }])->where('activo', 1);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('identificacion', 'LIKE', "%{$buscar}%");
            });
        }

        $conductores = $query->orderBy('nombre')->get();

        $conductores = $conductores->map(function($conductor) {
            $conductor->estado_documentos = $this->calcularEstadoDocumentosConductor($conductor);
            $conductor->vehiculos = $conductor->vehiculos->map(function($vehiculo) {
                $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
                return $vehiculo;
            });
            return $conductor;
        });

        $pdf = Pdf::loadView('reportes.pdf.conductores', compact('conductores'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('reporte_conductores_' . date('Y-m-d') . '.pdf');
    }

    private function exportConductoresExcel(Request $request)
    {
        $query = Conductor::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo');
        }, 'documentosConductor' => function($q) {
            $q->where('activo', 1);
        }])->where('activo', 1);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('identificacion', 'LIKE', "%{$buscar}%");
            });
        }

        $conductores = $query->orderBy('nombre')->get();

        $filename = 'reporte_conductores_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($conductores) {
            $file = fopen('php://output', 'w');

            $limpiar = function($texto) {
                if (is_numeric($texto)) return $texto;
                return mb_convert_encoding((string)$texto, 'Windows-1252', 'UTF-8');
            };

            fputcsv($file, array_map($limpiar, [
                'Nombre', 'Apellido', 'Tipo Doc', 'Identificacion', 'Telefono',
                'Tel. Emergencia', 'Activo', 'Num. Licencia', 'Categoria',
                'Categorias Adicionales', 'Vencimiento Licencia', 'Estado Licencia',
                'Vehiculos Asignados'
            ]), ';');

            foreach ($conductores as $c) {
                $licencia = $c->documentosConductor->where('tipo_documento', 'Licencia Conducción')->first();
                $placas = $c->vehiculos->pluck('placa')->implode(' | ');

                fputcsv($file, array_map($limpiar, [
                    $c->nombre,
                    $c->apellido,
                    $c->tipo_doc,
                    $c->identificacion,
                    $c->telefono ?? '-',
                    $c->telefono_emergencia ?? '-',
                    $c->activo ? 'Si' : 'No',
                    $licencia->numero_documento ?? '-',
                    $licencia->categoria_licencia ?? '-',
                    $licencia->categorias_adicionales ?? '-',
                    $licencia && $licencia->fecha_vencimiento ? Carbon::parse($licencia->fecha_vencimiento)->format('d/m/Y') : '-',
                    $licencia ? $licencia->estado : 'SIN LICENCIA',
                    $placas ?: 'Sin vehiculos'
                ]), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        $filename = 'reporte_vehiculos_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($vehiculos) {
            $file = fopen('php://output', 'w');

            // Función para limpiar caracteres especiales para Excel
            $limpiar = function($texto) {
                return mb_convert_encoding($texto, 'Windows-1252', 'UTF-8');
            };

            // Encabezados
            fputcsv($file, array_map($limpiar, ['Placa', 'Tipo', 'Marca', 'Modelo', 'Color', 'Año', 'Propietario', 'Documento Propietario', 'Conductor', 'Estado Documental', 'SOAT Vence', 'Tecno Vence']), ';');

            foreach ($vehiculos as $v) {
                $soatVence = $v->documentos->where('tipo_documento', 'SOAT')->first();
                $tecnoVence = $v->documentos->where('tipo_documento', 'Tecnomecanica')->first();

                fputcsv($file, array_map($limpiar, [
                    $v->placa,
                    $v->tipo,
                    $v->marca,
                    $v->modelo,
                    $v->color,
                    $v->anio ?? '-',
                    $v->propietario ? $v->propietario->nombre . ' ' . $v->propietario->apellido : 'Sin propietario',
                    $v->propietario ? $v->propietario->tipo_doc . ' ' . $v->propietario->identificacion : '-',
                    $v->conductor ? $v->conductor->nombre . ' ' . $v->conductor->apellido : 'Sin conductor',
                    $v->estado_general['texto'],
                    $soatVence ? Carbon::parse($soatVence->fecha_vencimiento)->format('d/m/Y') : 'Sin registro',
                    $tecnoVence ? Carbon::parse($tecnoVence->fecha_vencimiento)->format('d/m/Y') : 'Sin registro',
                ]), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAlertasExcel(Request $request)
    {
        $tipoFiltro = $request->input('tipo_documento');
        $estadoFiltro = $request->input('estado_alerta');

        $hoy = Carbon::today();
        $limiteDias = Carbon::today()->addDays(20); // Cambio de 30 a 20 días

        $queryVehiculos = DocumentoVehiculo::with(['vehiculo.propietario', 'vehiculo.conductor'])
            ->where('activo', 1)
            ->where('fecha_vencimiento', '<=', $limiteDias);

        if ($tipoFiltro) {
            $queryVehiculos->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            if ($estadoFiltro === 'POR_VENCER') {
                $queryVehiculos->where('fecha_vencimiento', '>=', $hoy)
                               ->where('fecha_vencimiento', '<=', $limiteDias);
            } elseif ($estadoFiltro === 'VENCIDO') {
                $queryVehiculos->where('fecha_vencimiento', '<', $hoy);
            }
        }

        $documentosVehiculos = $queryVehiculos->orderBy('fecha_vencimiento')->get();

        $queryConductores = DocumentoConductor::with(['conductor'])
            ->where('activo', 1)
            ->where('fecha_vencimiento', '<=', $limiteDias);

        if ($tipoFiltro) {
            $queryConductores->where('tipo_documento', $tipoFiltro);
        }

        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            if ($estadoFiltro === 'POR_VENCER') {
                $queryConductores->where('fecha_vencimiento', '>=', $hoy)
                                 ->where('fecha_vencimiento', '<=', $limiteDias);
            } elseif ($estadoFiltro === 'VENCIDO') {
                $queryConductores->where('fecha_vencimiento', '<', $hoy);
            }
        }

        $documentosConductores = $queryConductores->orderBy('fecha_vencimiento')->get();

        $filename = 'reporte_alertas_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($documentosVehiculos, $documentosConductores) {
            $file = fopen('php://output', 'w');

            // Función para limpiar caracteres especiales para Excel
            $limpiar = function($texto) {
                if (is_numeric($texto)) return $texto;
                return mb_convert_encoding((string)$texto, 'Windows-1252', 'UTF-8');
            };

            // Encabezados
            fputcsv($file, array_map($limpiar, ['Tipo Entidad', 'Placa/Nombre', 'Propietario', 'Tipo Documento', 'Numero Documento', 'Fecha Vencimiento', 'Estado', 'Dias Restantes', 'Urgencia']), ';');

            foreach ($documentosVehiculos as $doc) {
                $dias = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
                $urgencia = $dias < 0 ? 'VENCIDO' : ($dias <= 7 ? 'URGENTE' : ($dias <= 15 ? 'PRONTO' : 'NORMAL'));

                fputcsv($file, array_map($limpiar, [
                    'Vehiculo',
                    $doc->vehiculo->placa ?? 'N/A',
                    $doc->vehiculo && $doc->vehiculo->propietario ? $doc->vehiculo->propietario->nombre . ' ' . $doc->vehiculo->propietario->apellido : '-',
                    $doc->tipo_documento,
                    $doc->numero_documento ?? '-',
                    Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y'),
                    $doc->estado,
                    $dias,
                    $urgencia
                ]), ';');
            }

            foreach ($documentosConductores as $doc) {
                $dias = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
                $urgencia = $dias < 0 ? 'VENCIDO' : ($dias <= 7 ? 'URGENTE' : ($dias <= 15 ? 'PRONTO' : 'NORMAL'));

                fputcsv($file, array_map($limpiar, [
                    'Conductor',
                    $doc->conductor ? $doc->conductor->nombre . ' ' . $doc->conductor->apellido : 'N/A',
                    '-',
                    $doc->tipo_documento,
                    $doc->numero_documento ?? '-',
                    Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y'),
                    $doc->estado,
                    $dias,
                    $urgencia
                ]), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPropietariosExcel(Request $request)
    {
        $query = Propietario::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo')->with(['documentos' => function($q2) {
                $q2->where('activo', 1);
            }, 'conductor']);
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

        $filename = 'reporte_propietarios_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($propietarios) {
            $file = fopen('php://output', 'w');

            // Función para limpiar caracteres especiales para Excel
            $limpiar = function($texto) {
                if (is_numeric($texto)) return $texto;
                return mb_convert_encoding((string)$texto, 'Windows-1252', 'UTF-8');
            };

            // Encabezados
            fputcsv($file, array_map($limpiar, ['Nombre', 'Apellido', 'Tipo Documento', 'Identificacion', 'Telefono', 'Email', 'Direccion', 'Total Vehiculos', 'Vehiculos Vigentes', 'Vehiculos Por Vencer', 'Vehiculos Vencidos', 'Placas']), ';');

            foreach ($propietarios as $p) {
                $placas = $p->vehiculos->pluck('placa')->implode(' | ');
                $vigentes = $p->vehiculos->where('estado_general.estado', 'VIGENTE')->count();
                $porVencer = $p->vehiculos->where('estado_general.estado', 'POR_VENCER')->count();
                $vencidos = $p->vehiculos->where('estado_general.estado', 'VENCIDO')->count();

                fputcsv($file, array_map($limpiar, [
                    $p->nombre,
                    $p->apellido,
                    $p->tipo_doc,
                    $p->identificacion,
                    $p->telefono ?? '-',
                    $p->email ?? '-',
                    $p->direccion ?? '-',
                    $p->vehiculos->count(),
                    $vigentes,
                    $porVencer,
                    $vencidos,
                    $placas ?: 'Sin vehiculos'
                ]), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportHistoricoExcel(Request $request)
    {
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

        $filename = 'reporte_historico_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($historialVehiculos, $historialConductores, $fechaInicio, $fechaFin) {
            $file = fopen('php://output', 'w');

            // Función para limpiar caracteres especiales para Excel
            $limpiar = function($texto) {
                if (is_numeric($texto)) return $texto;
                return mb_convert_encoding((string)$texto, 'Windows-1252', 'UTF-8');
            };

            // Encabezados
            fputcsv($file, array_map($limpiar, ['Fecha Registro', 'Hora', 'Tipo Entidad', 'Placa/Nombre', 'Propietario', 'Tipo Documento', 'Numero Documento', 'Fecha Expedicion', 'Fecha Vencimiento', 'Estado', 'Accion', 'Version']), ';');

            foreach ($historialVehiculos as $doc) {
                fputcsv($file, array_map($limpiar, [
                    Carbon::parse($doc->fecha_registro)->format('d/m/Y'),
                    Carbon::parse($doc->fecha_registro)->format('H:i'),
                    'Vehiculo',
                    $doc->vehiculo->placa ?? 'N/A',
                    $doc->vehiculo && $doc->vehiculo->propietario ? $doc->vehiculo->propietario->nombre . ' ' . $doc->vehiculo->propietario->apellido : '-',
                    $doc->tipo_documento,
                    $doc->numero_documento ?? '-',
                    $doc->fecha_expedicion ? Carbon::parse($doc->fecha_expedicion)->format('d/m/Y') : '-',
                    $doc->fecha_vencimiento ? Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') : '-',
                    $doc->estado,
                    $doc->version > 1 ? 'Renovacion' : 'Registro inicial',
                    'v' . $doc->version
                ]), ';');
            }

            foreach ($historialConductores as $doc) {
                fputcsv($file, array_map($limpiar, [
                    Carbon::parse($doc->fecha_registro)->format('d/m/Y'),
                    Carbon::parse($doc->fecha_registro)->format('H:i'),
                    'Conductor',
                    $doc->conductor ? $doc->conductor->nombre . ' ' . $doc->conductor->apellido : 'N/A',
                    '-',
                    $doc->tipo_documento,
                    $doc->numero_documento ?? '-',
                    $doc->fecha_expedicion ? Carbon::parse($doc->fecha_expedicion)->format('d/m/Y') : '-',
                    $doc->fecha_vencimiento ? Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') : '-',
                    $doc->estado,
                    $doc->version > 1 ? 'Renovacion' : 'Registro inicial',
                    'v' . $doc->version
                ]), ';');
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
        $tiposVehiculo = ['SOAT', 'Tecnomecanica', 'Tarjeta Propiedad', 'Póliza'];

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

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

class ReporteController extends Controller
{
    /**
     * Vista principal del módulo de reportes
     */
    public function index()
    {
        // Estadísticas rápidas para el dashboard de reportes
        $stats = [
            'total_vehiculos' => Vehiculo::where('estado', 'Activo')->count(),
            'total_propietarios' => Propietario::count(),
            'total_conductores' => Conductor::where('activo', 1)->count(),
            'docs_vigentes' => DocumentoVehiculo::where('activo', 1)->where('estado', 'VIGENTE')->count(),
            'docs_por_vencer' => DocumentoVehiculo::where('activo', 1)->where('estado', 'POR_VENCER')->count(),
            'docs_vencidos' => DocumentoVehiculo::where('activo', 1)->where('estado', 'VENCIDO')->count(),
        ];

        return view('reportes.centro', compact('stats'));
    }

    /**
     * 1. REPORTE GENERAL DE VEHÍCULOS
     * Lista todos los vehículos con estado de documentación
     */
    public function vehiculos(Request $request)
    {
        $query = Vehiculo::with(['propietario', 'conductor', 'documentos' => function($q) {
            $q->where('activo', 1);
        }])->where('estado', 'Activo');

        // Filtro por estado de documentación
        $estadoFiltro = $request->input('estado_docs');

        // Filtro por tipo de vehículo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por propietario
        if ($request->filled('propietario')) {
            $query->where('id_propietario', $request->propietario);
        }

        // Búsqueda por placa
        if ($request->filled('placa')) {
            $query->where('placa', 'LIKE', '%' . strtoupper($request->placa) . '%');
        }

        $vehiculos = $query->orderBy('placa')->get();

        // Calcular estado general de cada vehículo
        $vehiculos = $vehiculos->map(function($vehiculo) {
            $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
            return $vehiculo;
        });

        // Filtrar por estado si se especificó
        if ($estadoFiltro && $estadoFiltro !== 'TODOS') {
            $vehiculos = $vehiculos->filter(function($v) use ($estadoFiltro) {
                return $v->estado_general['estado'] === $estadoFiltro;
            });
        }

        // Estadísticas del reporte
        $estadisticas = [
            'total' => $vehiculos->count(),
            'vigentes' => $vehiculos->where('estado_general.estado', 'VIGENTE')->count(),
            'por_vencer' => $vehiculos->where('estado_general.estado', 'POR_VENCER')->count(),
            'vencidos' => $vehiculos->where('estado_general.estado', 'VENCIDO')->count(),
            'sin_docs' => $vehiculos->where('estado_general.estado', 'SIN_DOCUMENTOS')->count(),
        ];

        $propietarios = Propietario::orderBy('nombre')->get();

        return view('reportes.vehiculos', compact('vehiculos', 'estadisticas', 'propietarios'));
    }

    /**
     * 2. REPORTE DE DOCUMENTACIÓN POR VEHÍCULO (Ficha)
     */
    public function fichaVehiculo($id)
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

        return view('reportes.ficha-vehiculo', compact('vehiculo', 'estadosDocumentos', 'historialReciente'));
    }

    /**
     * 3. REPORTE DE ALERTAS
     * Documentos próximos a vencer y vencidos con semáforo
     */
    public function alertas(Request $request)
    {
        $diasProximoVencer = $request->input('dias', 30);
        $tipoFiltro = $request->input('tipo_documento');
        $estadoFiltro = $request->input('estado_alerta');

        // Documentos de vehículos
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

        // Documentos de conductores
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

        // Agrupar por estado para estadísticas
        $estadisticas = [
            'vehiculos_por_vencer' => $documentosVehiculos->where('estado', 'POR_VENCER')->count(),
            'vehiculos_vencidos' => $documentosVehiculos->where('estado', 'VENCIDO')->count(),
            'conductores_por_vencer' => $documentosConductores->where('estado', 'POR_VENCER')->count(),
            'conductores_vencidos' => $documentosConductores->where('estado', 'VENCIDO')->count(),
        ];

        // Línea de tiempo de próximos vencimientos (próximos 90 días)
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
            'diasProximoVencer'
        ));
    }

    /**
     * 4. REPORTE POR PROPIETARIO
     */
    public function propietarios(Request $request)
    {
        $query = Propietario::with(['vehiculos' => function($q) {
            $q->where('estado', 'Activo')->with(['documentos' => function($q2) {
                $q2->where('activo', 1);
            }, 'conductor']);
        }]);

        // Filtro por propietario específico
        if ($request->filled('propietario')) {
            $query->where('id_propietario', $request->propietario);
        }

        // Búsqueda por nombre o identificación
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('identificacion', 'LIKE', "%{$buscar}%");
            });
        }

        $propietarios = $query->orderBy('nombre')->get();

        // Calcular estado documental de cada vehículo
        $propietarios = $propietarios->map(function($propietario) {
            $propietario->vehiculos = $propietario->vehiculos->map(function($vehiculo) {
                $vehiculo->estado_general = $this->calcularEstadoGeneral($vehiculo);
                return $vehiculo;
            });

            // Estadísticas del propietario
            $propietario->stats = [
                'total_vehiculos' => $propietario->vehiculos->count(),
                'vigentes' => $propietario->vehiculos->where('estado_general.estado', 'VIGENTE')->count(),
                'por_vencer' => $propietario->vehiculos->where('estado_general.estado', 'POR_VENCER')->count(),
                'vencidos' => $propietario->vehiculos->where('estado_general.estado', 'VENCIDO')->count(),
            ];

            return $propietario;
        });

        // Estadísticas generales
        $estadisticas = [
            'total_propietarios' => $propietarios->count(),
            'total_vehiculos' => $propietarios->sum('stats.total_vehiculos'),
            'vehiculos_vigentes' => $propietarios->sum('stats.vigentes'),
            'vehiculos_por_vencer' => $propietarios->sum('stats.por_vencer'),
            'vehiculos_vencidos' => $propietarios->sum('stats.vencidos'),
        ];

        return view('reportes.propietarios', compact('propietarios', 'estadisticas'));
    }

    /**
     * 5. REPORTE HISTÓRICO
     * Renovaciones y cronología de documentos
     */
    public function historico(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        $tipoDocumento = $request->input('tipo_documento');
        $placa = $request->input('placa');

        // Historial de documentos de vehículos
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

        // Historial de documentos de conductores
        $queryConductores = DocumentoConductor::with(['conductor'])
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin . ' 23:59:59']);

        if ($tipoDocumento) {
            $queryConductores->where('tipo_documento', $tipoDocumento);
        }

        $historialConductores = $queryConductores->orderByDesc('fecha_registro')->get();

        // Estadísticas del período
        $estadisticas = [
            'renovaciones_vehiculos' => $historialVehiculos->where('version', '>', 1)->count(),
            'nuevos_vehiculos' => $historialVehiculos->where('version', 1)->count(),
            'renovaciones_conductores' => $historialConductores->where('version', '>', 1)->count(),
            'nuevos_conductores' => $historialConductores->where('version', 1)->count(),
            'documentos_vencidos' => $historialVehiculos->where('estado', 'VENCIDO')->count()
                                   + $historialConductores->where('estado', 'VENCIDO')->count(),
        ];

        // Cronología agrupada por mes
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
            'fechaFin'
        ));
    }

    /**
     * Calcular estado general de un vehículo basado en sus documentos
     */
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

    /**
     * Calcular estados detallados de documentos para ficha
     */
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

        // Documentos del conductor
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

    /**
     * Generar línea de tiempo de vencimientos
     */
    private function generarLineaTiempo($docsVehiculos, $docsConductores)
    {
        $hoy = Carbon::now();
        $limite = Carbon::now()->addDays(90);

        $eventos = collect();

        // Documentos de vehículos
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

        // Documentos de conductores
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

    /**
     * Generar cronología histórica
     */
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

    /**
     * Exportar reporte a Excel
     */
    public function exportExcel(Request $request, $tipo)
    {
        // TODO: Implementar exportación Excel con Laravel Excel
        // Por ahora redirige con mensaje
        return back()->with('info', 'Exportación a Excel en desarrollo');
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportPdf(Request $request, $tipo)
    {
        // TODO: Implementar exportación PDF con DomPDF o Snappy
        // Por ahora redirige con mensaje
        return back()->with('info', 'Exportación a PDF en desarrollo');
    }
}

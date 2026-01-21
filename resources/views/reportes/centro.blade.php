@extends('layouts.app')

@section('title', 'Centro de Reportes')

@section('content')
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-chart-bar me-2" style="color: #5B8238;"></i>Centro de Reportes</h2>
            <p class="text-muted mb-0">Genera reportes detallados para trazabilidad completa del sistema</p>
        </div>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="row mb-4">
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-car fa-2x mb-2" style="color: #5B8238;"></i>
                    <h3 class="mb-0">{{ $stats['total_vehiculos'] }}</h3>
                    <small class="text-muted">Vehículos Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2 text-primary"></i>
                    <h3 class="mb-0">{{ $stats['total_propietarios'] }}</h3>
                    <small class="text-muted">Propietarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-id-card fa-2x mb-2 text-info"></i>
                    <h3 class="mb-0">{{ $stats['total_conductores'] }}</h3>
                    <small class="text-muted">Conductores</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-3">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                    <h3 class="mb-0">{{ $stats['docs_vigentes'] }}</h3>
                    <small class="text-muted">Docs. Vigentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-3">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                    <h3 class="mb-0">{{ $stats['docs_por_vencer'] }}</h3>
                    <small class="text-muted">Por Vencer</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-3">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x mb-2 text-danger"></i>
                    <h3 class="mb-0">{{ $stats['docs_vencidos'] }}</h3>
                    <small class="text-muted">Vencidos</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tipos de Reportes --}}
    <div class="row">
        {{-- Reporte General de Vehículos --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 reporte-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-car-side fa-lg text-success"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Reporte de Vehículos</h5>
                            <small class="text-muted">Vista general de flota</small>
                        </div>
                    </div>
                    <p class="card-text text-muted small">
                        Lista completa de vehículos con estado de documentación, propietario y conductor asignado.
                        Incluye semáforo de estados y filtros dinámicos.
                    </p>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Placa, marca, modelo</li>
                        <li><i class="fas fa-check text-success me-2"></i>Estado documental (semáforo)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Filtros por estado y tipo</li>
                        <li><i class="fas fa-check text-success me-2"></i>Exportación PDF/Excel</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('reportes.vehiculos') }}" class="btn btn-success w-100">
                        <i class="fas fa-file-alt me-2"></i>Generar Reporte
                    </a>
                </div>
            </div>
        </div>

        {{-- Reporte de Alertas --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 reporte-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-bell fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Reporte de Alertas</h5>
                            <small class="text-muted">Vencimientos y alertas</small>
                        </div>
                    </div>
                    <p class="card-text text-muted small">
                        Documentos próximos a vencer y vencidos con código de colores.
                        Incluye línea de tiempo de vencimientos y estadísticas.
                    </p>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Semáforo de estados</li>
                        <li><i class="fas fa-check text-success me-2"></i>Línea de tiempo (90 días)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Filtro por días y tipo</li>
                        <li><i class="fas fa-check text-success me-2"></i>Vehículos afectados</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('reportes.alertas') }}" class="btn btn-warning w-100">
                        <i class="fas fa-exclamation-triangle me-2"></i>Ver Alertas
                    </a>
                </div>
            </div>
        </div>

        {{-- Reporte por Propietario --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 reporte-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-user-tie fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Reporte por Propietario</h5>
                            <small class="text-muted">Agrupado por dueño</small>
                        </div>
                    </div>
                    <p class="card-text text-muted small">
                        Vehículos agrupados por propietario con resumen de estado documental.
                        Ideal para notificar a propietarios sobre vencimientos.
                    </p>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Datos del propietario</li>
                        <li><i class="fas fa-check text-success me-2"></i>Vehículos asociados</li>
                        <li><i class="fas fa-check text-success me-2"></i>Estado por vehículo</li>
                        <li><i class="fas fa-check text-success me-2"></i>Resumen consolidado</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('reportes.propietarios') }}" class="btn btn-primary w-100">
                        <i class="fas fa-users me-2"></i>Ver Propietarios
                    </a>
                </div>
            </div>
        </div>

        {{-- Reporte Histórico --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 reporte-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-history fa-lg text-info"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Reporte Histórico</h5>
                            <small class="text-muted">Cronología y auditoría</small>
                        </div>
                    </div>
                    <p class="card-text text-muted small">
                        Historial de renovaciones, cambios y vencimientos.
                        Cronología exportable para auditorías y trazabilidad completa.
                    </p>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Renovaciones realizadas</li>
                        <li><i class="fas fa-check text-success me-2"></i>Filtro por rango de fechas</li>
                        <li><i class="fas fa-check text-success me-2"></i>Cronología por mes</li>
                        <li><i class="fas fa-check text-success me-2"></i>Exportable para auditoría</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('reportes.historico') }}" class="btn btn-info w-100 text-white">
                        <i class="fas fa-clock me-2"></i>Ver Historial
                    </a>
                </div>
            </div>
        </div>

        {{-- Ficha por Vehículo --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 reporte-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-id-badge fa-lg text-secondary"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Ficha de Vehículo</h5>
                            <small class="text-muted">Detalle individual</small>
                        </div>
                    </div>
                    <p class="card-text text-muted small">
                        Ficha detallada de un vehículo específico con toda su documentación.
                        Formato listo para imprimir o enviar por correo.
                    </p>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li><i class="fas fa-check text-success me-2"></i>Datos completos del vehículo</li>
                        <li><i class="fas fa-check text-success me-2"></i>Todos los documentos</li>
                        <li><i class="fas fa-check text-success me-2"></i>Historial reciente</li>
                        <li><i class="fas fa-check text-success me-2"></i>Formato imprimible</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('reportes.vehiculos') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-search me-2"></i>Buscar Vehículo
                    </a>
                </div>
            </div>
        </div>

        {{-- Gráficos y Estadísticas --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 reporte-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box rounded-circle p-3 me-3" style="background-color: rgba(91, 130, 56, 0.1);">
                            <i class="fas fa-chart-pie fa-lg" style="color: #5B8238;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Estadísticas</h5>
                            <small class="text-muted">Gráficos visuales</small>
                        </div>
                    </div>
                    <p class="card-text text-muted small">
                        Visualización gráfica del estado de la flota vehicular.
                    </p>

                    {{-- Mini gráfico de barras con Bootstrap --}}
                    @php
                        $totalDocs = $stats['docs_vigentes'] + $stats['docs_por_vencer'] + $stats['docs_vencidos'];
                        $pctVigentes = $totalDocs > 0 ? round(($stats['docs_vigentes'] / $totalDocs) * 100) : 0;
                        $pctPorVencer = $totalDocs > 0 ? round(($stats['docs_por_vencer'] / $totalDocs) * 100) : 0;
                        $pctVencidos = $totalDocs > 0 ? round(($stats['docs_vencidos'] / $totalDocs) * 100) : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Vigentes</span>
                            <span class="text-success">{{ $pctVigentes }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $pctVigentes }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Por vencer</span>
                            <span class="text-warning">{{ $pctPorVencer }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $pctPorVencer }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Vencidos</span>
                            <span class="text-danger">{{ $pctVencidos }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $pctVencidos }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('reportes.alertas') }}" class="btn w-100" style="background-color: #5B8238; color: white;">
                        <i class="fas fa-chart-bar me-2"></i>Ver Detalles
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Acciones Rápidas</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-3">
                    <a href="{{ route('reportes.alertas') }}?estado_alerta=VENCIDO" class="btn btn-outline-danger w-100 py-3">
                        <i class="fas fa-exclamation-circle fa-lg d-block mb-2"></i>
                        Ver Documentos Vencidos
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <a href="{{ route('reportes.alertas') }}?dias=15" class="btn btn-outline-warning w-100 py-3">
                        <i class="fas fa-clock fa-lg d-block mb-2"></i>
                        Vencen en 15 días
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <a href="{{ route('reportes.vehiculos') }}?estado_docs=SIN_DOCUMENTOS" class="btn btn-outline-secondary w-100 py-3">
                        <i class="fas fa-question-circle fa-lg d-block mb-2"></i>
                        Sin Documentación
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <a href="{{ route('reportes.historico') }}?fecha_inicio={{ now()->subDays(30)->format('Y-m-d') }}" class="btn btn-outline-info w-100 py-3">
                        <i class="fas fa-sync fa-lg d-block mb-2"></i>
                        Renovaciones (30 días)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .reporte-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .reporte-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Acerca del Sistema')

@section('content')
<div class="container py-4" style="max-width: 800px;">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-info-circle me-2" style="color:#5B8238;"></i>Acerca del Sistema
            </h3>
            <p class="text-muted small mb-0">Información técnica del sistema — solo visible para ADMIN</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Card versión --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3 text-white" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
            <h5 class="mb-0"><i class="fas fa-code-branch me-2"></i>Versión Actual</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-4 text-center">
                    <div class="display-6 fw-bold" style="color:#5B8238;">v1.1.1</div>
                    <small class="text-muted">Versión estable</small>
                </div>
                <div class="col-sm-8">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Lanzamiento:</td>
                            <td class="fw-medium">Mayo 2026</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Entorno:</td>
                            <td><span class="badge bg-{{ config('app.env') === 'production' ? 'success' : 'warning' }}">{{ ucfirst(config('app.env')) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">PHP:</td>
                            <td>{{ PHP_VERSION }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Laravel:</td>
                            <td>{{ app()->version() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted" colspan="2">
                                Último commit:
                                <div class="font-monospace mt-1 p-2 bg-light rounded" style="font-size:0.8rem; word-break:break-word; white-space:pre-wrap;">{{ $lastCommit }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de versiones --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-history me-2" style="color:#5B8238;"></i>Historial de Versiones</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge me-2" style="background-color:#5B8238;">v1.1.1</span>
                            <strong>Correcciones de responsividad mobile</strong>
                            <ul class="mt-2 mb-0 small text-muted">
                                <li>Eliminado <code>white-space: nowrap !important</code> global que impedía wrap en tablas en móvil</li>
                                <li>Corregido selector CSS roto <code>F .btn-outline-primary</code></li>
                                <li>Acotada regla <code>.btn { width:100% }</code> y <code>display:flex</code> en botones — ya no afecta btn-close ni botones de tabla</li>
                                <li>Eliminado espaciado <code>&lt;br&gt;&lt;br&gt;&lt;br&gt;</code> en 16 vistas, reemplazado por clase CSS responsiva</li>
                                <li>Corregido desbordamiento del dropdown de notificaciones en pantallas &lt; 360px</li>
                                <li>Corregido bug: <code>fecha_matricula</code> no se guardaba al crear vehículo</li>
                            </ul>
                        </div>
                        <small class="text-muted text-nowrap ms-3">May 2026</small>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge me-2" style="background-color:#6c757d;">v1.1.0</span>
                            <strong>Alertas para vehículos sin Tecnomecánica al vencer exención</strong>
                            <ul class="mt-2 mb-0 small text-muted">
                                <li>Detección automática (batch diario) de vehículos exentos que superaron su fecha de primera revisión</li>
                                <li>Genera alerta PROXIMO_VENCER a 15 días antes y VENCIDO al vencer</li>
                                <li>La alerta se cierra automáticamente al registrar el documento de Tecnomecánica</li>
                            </ul>
                        </div>
                        <small class="text-muted text-nowrap ms-3">May 2026</small>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge me-2" style="background-color:#6c757d;">v1.0.0</span>
                            <strong>Release inicial estable</strong>
                            <ul class="mt-2 mb-0 small text-muted">
                                <li>Gestión de vehículos, conductores y propietarios</li>
                                <li>Documentos con versionamiento (SOAT, Tecnomecánica, Licencias)</li>
                                <li>Alertas automáticas con transición Próximo a Vencer → Vencido</li>
                                <li>Reportes y fichas con soporte multi-conductor y multi-vehículo</li>
                                <li>Clasificación por Empleado / Contratista / Externo</li>
                                <li>Módulo de portería con búsqueda global</li>
                                <li>Backup automático y tareas programadas (cron)</li>
                                <li>Integración Google Drive para documentos</li>
                                <li>Corrección cálculo fecha vencimiento SOAT (emisión + 1 año − 1 día)</li>
                            </ul>
                        </div>
                        <small class="text-muted text-nowrap ms-3">Abr 2026</small>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    {{-- Commits recientes --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-code-commit me-2" style="color:#5B8238;"></i>Commits Recientes</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @foreach($commits as $commit)
                <li class="list-group-item py-2">
                    <div class="d-flex align-items-start gap-2">
                        <span class="font-monospace text-muted" style="font-size:0.75rem; min-width:60px;">{{ $commit['hash'] }}</span>
                        <span class="small">{{ $commit['mensaje'] }}</span>
                        <span class="text-muted small text-nowrap ms-auto">{{ $commit['fecha'] }}</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

</div>
@endsection

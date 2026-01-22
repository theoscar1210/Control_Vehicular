@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp


@extends('layouts.app')

@section('title', 'Reporte Histórico')

@section('content')
<br><br><br>
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item active">Histórico</li>
                </ol>
            </nav>
            <h2 class="mb-0"><i class="fas fa-history me-2" style="color: #5B8238;"></i>Reporte Histórico</h2>
            <small class="text-muted">Cronología de documentos para auditoría y trazabilidad</small>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="exportarReporte('excel')">
                <i class="fas fa-file-excel me-1"></i> Excel
            </button>
            <button type="button" class="btn btn-danger" onclick="exportarReporte('pdf')">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
        </div>
    </div>

    {{-- Estadísticas del Período --}}
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-success border-3">
                <div class="card-body py-3">
                    <i class="fas fa-sync text-success fa-lg mb-1"></i>
                    <h4 class="mb-0 text-success">{{ $estadisticas['renovaciones_vehiculos'] }}</h4>
                    <small class="text-muted">Renovaciones Vehículos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-primary border-3">
                <div class="card-body py-3">
                    <i class="fas fa-plus-circle text-primary fa-lg mb-1"></i>
                    <h4 class="mb-0 text-primary">{{ $estadisticas['nuevos_vehiculos'] }}</h4>
                    <small class="text-muted">Nuevos Documentos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-info border-3">
                <div class="card-body py-3">
                    <i class="fas fa-sync text-info fa-lg mb-1"></i>
                    <h4 class="mb-0 text-info">{{ $estadisticas['renovaciones_conductores'] }}</h4>
                    <small class="text-muted">Renovaciones Conductores</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-secondary border-3">
                <div class="card-body py-3">
                    <i class="fas fa-plus-circle text-secondary fa-lg mb-1"></i>
                    <h4 class="mb-0 text-secondary">{{ $estadisticas['nuevos_conductores'] }}</h4>
                    <small class="text-muted">Nuevos Conductores</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-danger border-3">
                <div class="card-body py-3">
                    <i class="fas fa-times-circle text-danger fa-lg mb-1"></i>
                    <h4 class="mb-0 text-danger">{{ $estadisticas['documentos_vencidos'] }}</h4>
                    <small class="text-muted">Docs. Vencidos</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
            <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.historico') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-calendar me-1"></i> Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-calendar me-1"></i> Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-file me-1"></i> Tipo Documento</label>
                        <select name="tipo_documento" class="form-select">
                            <option value="">Todos los tipos</option>
                            <optgroup label="Vehículos">
                                @foreach($tiposDocumento['vehiculo'] as $tipo)
                                <option value="{{ $tipo }}" {{ request('tipo_documento') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Conductores">
                                @foreach($tiposDocumento['conductor'] as $tipo)
                                <option value="{{ $tipo }}" {{ request('tipo_documento') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-car me-1"></i> Placa</label>
                        <input type="text" name="placa" class="form-control text-uppercase"
                            placeholder="ABC123" value="{{ request('placa') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn px-4" style="background-color: #5B8238; color: white;">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <a href="{{ route('reportes.historico') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Limpiar
                        </a>
                        {{-- Accesos rápidos --}}
                        <div class="btn-group ms-2">
                            <a href="{{ route('reportes.historico') }}?fecha_inicio={{ now()->subDays(7)->format('Y-m-d') }}&fecha_fin={{ now()->format('Y-m-d') }}"
                                class="btn btn-outline-info btn-sm">Última semana</a>
                            <a href="{{ route('reportes.historico') }}?fecha_inicio={{ now()->subMonth()->format('Y-m-d') }}&fecha_fin={{ now()->format('Y-m-d') }}"
                                class="btn btn-outline-info btn-sm">Último mes</a>
                            <a href="{{ route('reportes.historico') }}?fecha_inicio={{ now()->subMonths(3)->format('Y-m-d') }}&fecha_fin={{ now()->format('Y-m-d') }}"
                                class="btn btn-outline-info btn-sm">Últimos 3 meses</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Cronología por Mes --}}
    @if($cronologia->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-stream me-2" style="color: #5B8238;"></i>Cronología de Documentos</h5>
        </div>
        <div class="card-body">
            @foreach($cronologia as $mes => $eventos)
            <div class="cronologia-mes mb-4">
                <div class="cronologia-header d-flex align-items-center mb-3">
                    <div class="cronologia-punto"></div>
                    <h5 class="mb-0 ms-3 text-uppercase">
                        {{ \Carbon\Carbon::parse($mes . '-01')->translatedFormat('F Y') }}
                        <span class="badge bg-secondary ms-2">{{ $eventos->count() }} registros</span>
                    </h5>
                </div>
                <div class="cronologia-contenido ms-4 ps-3 border-start border-2" style="border-color: #5B8238 !important;">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                    <th>Tipo</th>
                                    <th>Documento</th>
                                    <th>Referencia</th>
                                    <th>Estado</th>
                                    <th>Versión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($eventos as $evento)
                                <tr>
                                    <td class="small">{{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $evento['accion'] == 'Renovación' ? 'success' : 'primary' }}">
                                            <i class="fas fa-{{ $evento['accion'] == 'Renovación' ? 'sync' : 'plus' }} me-1"></i>
                                            {{ $evento['accion'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $evento['tipo'] == 'vehiculo' ? 'dark' : 'info' }}">
                                            <i class="fas fa-{{ $evento['tipo'] == 'vehiculo' ? 'car' : 'user' }} me-1"></i>
                                            {{ ucfirst($evento['tipo']) }}
                                        </span>
                                    </td>
                                    <td>{{ $evento['documento'] }}</td>
                                    <td class="fw-medium" style="color: #5B8238;">{{ $evento['referencia'] }}</td>
                                    <td>
                                        @php
                                        $estadoClase = match($evento['estado']) {
                                        'VIGENTE' => 'success',
                                        'POR_VENCER' => 'warning',
                                        'VENCIDO' => 'danger',
                                        'REEMPLAZADO' => 'secondary',
                                        default => 'secondary'
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $estadoClase }}">{{ $evento['estado'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">v{{ $evento['version'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tablas detalladas --}}
    <div class="row">
        {{-- Historial Vehículos --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-car me-2" style="color: #5B8238;"></i>
                        Documentos de Vehículos
                        <span class="badge bg-secondary ms-2">{{ $historialVehiculos->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead style="background-color: #f8f9fa; position: sticky; top: 0;">
                                <tr>
                                    <th class="px-3">Fecha</th>
                                    <th>Placa</th>
                                    <th>Tipo Doc.</th>
                                    <th>Acción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historialVehiculos as $doc)
                                <tr>
                                    <td class="px-3 text-muted">{{ \Carbon\Carbon::parse($doc->fecha_registro)->format('d/m/Y') }}</td>
                                    <td class="fw-medium" style="color: #5B8238;">{{ $doc->vehiculo->placa ?? 'N/A' }}</td>
                                    <td>{{ $doc->tipo_documento }}</td>
                                    <td>
                                        <span class="badge bg-{{ $doc->version > 1 ? 'success' : 'primary' }}">
                                            {{ $doc->version > 1 ? 'Renovación' : 'Nuevo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $doc->activo ? ($doc->estado == 'VIGENTE' ? 'success' : ($doc->estado == 'POR_VENCER' ? 'warning' : 'danger')) : 'secondary' }}">
                                            {{ $doc->estado }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay registros en este período
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial Conductores --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2" style="color: #5B8238;"></i>
                        Documentos de Conductores
                        <span class="badge bg-secondary ms-2">{{ $historialConductores->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead style="background-color: #f8f9fa; position: sticky; top: 0;">
                                <tr>
                                    <th class="px-3">Fecha</th>
                                    <th>Conductor</th>
                                    <th>Tipo Doc.</th>
                                    <th>Acción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historialConductores as $doc)
                                <tr>
                                    <td class="px-3 text-muted">{{ \Carbon\Carbon::parse($doc->fecha_registro)->format('d/m/Y') }}</td>
                                    <td class="fw-medium">{{ $doc->conductor->nombre ?? '' }} {{ $doc->conductor->apellido ?? '' }}</td>
                                    <td>{{ $doc->tipo_documento }}</td>
                                    <td>
                                        <span class="badge bg-{{ $doc->version > 1 ? 'success' : 'primary' }}">
                                            {{ $doc->version > 1 ? 'Renovación' : 'Nuevo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $doc->activo ? ($doc->estado == 'VIGENTE' ? 'success' : ($doc->estado == 'POR_VENCER' ? 'warning' : 'danger')) : 'secondary' }}">
                                            {{ $doc->estado }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay registros en este período
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cronologia-punto {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #5B8238;
        box-shadow: 0 0 0 4px rgba(91, 130, 56, 0.2);
    }
</style>

<script>
    function exportarReporte(formato) {
        const params = new URLSearchParams(window.location.search);
        let url = formato === 'excel' ?
            '{{ route("reportes.export.excel", ["tipo" => "historico"]) }}' :
            '{{ route("reportes.export.pdf", ["tipo" => "historico"]) }}';
        window.open(url + '?' + params.toString(), '_blank');
    }
</script>
@endsection
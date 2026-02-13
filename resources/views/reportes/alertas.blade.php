@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp


@extends('layouts.app')

@section('title', 'Reporte de Alertas')

@section('content')
<br><br><br>
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item active">Alertas</li>
                </ol>
            </nav>
            <h2 class="mb-0"><i class="fas fa-bell me-2" style="color: #5B8238;"></i>Reporte de Alertas y Vencimientos</h2>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reportes.centro') }}" class="btn btn-secondary px-3 py-2" style="border-radius:12px;">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>



            <div class="btn-group">
                <button type="button" class="btn btn-success" onclick="exportarReporte('excel')">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </button>
                <button type="button" class="btn btn-danger" onclick="exportarReporte('pdf')">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
            </div>
        </div>
    </div>

    {{-- Estadísticas con Semáforo --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Vehículos por Vencer</h6>
                            <h2 class="mb-0 text-warning">{{ $estadisticas['vehiculos_por_vencer'] }}</h2>
                        </div>
                        <div class="semaforo-grande bg-warning"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Vehículos Vencidos</h6>
                            <h2 class="mb-0 text-danger">{{ $estadisticas['vehiculos_vencidos'] }}</h2>
                        </div>
                        <div class="semaforo-grande bg-danger"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Conductores por Vencer</h6>
                            <h2 class="mb-0 text-warning">{{ $estadisticas['conductores_por_vencer'] }}</h2>
                        </div>
                        <div class="semaforo-grande bg-warning"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Conductores Vencidos</h6>
                            <h2 class="mb-0 text-danger">{{ $estadisticas['conductores_vencidos'] }}</h2>
                        </div>
                        <div class="semaforo-grande bg-danger"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
            <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.alertas') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Próximos a vencer en (días)</label>
                        <select name="dias" class="form-select">
                            <option value="15" {{ request('dias', 30) == 15 ? 'selected' : '' }}>15 días</option>
                            <option value="30" {{ request('dias', 30) == 30 ? 'selected' : '' }}>30 días</option>
                            <option value="60" {{ request('dias', 30) == 60 ? 'selected' : '' }}>60 días</option>
                            <option value="90" {{ request('dias', 30) == 90 ? 'selected' : '' }}>90 días</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Documento</label>
                        <select name="tipo_documento" class="form-select">
                            <option value="">Todos</option>
                            <optgroup label="Vehiculos">
                                <option value="SOAT" {{ request('tipo_documento') == 'SOAT' ? 'selected' : '' }}>SOAT</option>
                                <option value="TECNOMECANICA" {{ request('tipo_documento') == 'TECNOMECANICA' ? 'selected' : '' }}>Tecnomecánica</option>
                                <option value="TARJETA PROPIEDAD" {{ request('tipo_documento') == 'TARJETA PROPIEDAD' ? 'selected' : '' }}>Tarjeta Propiedad</option>
                                {{-- Comentados para futuras actualizaciones
                                <option value="Poliza" {{ request('tipo_documento') == 'Poliza' ? 'selected' : '' }}>Poliza</option>
                                <option value="Otro" {{ request('tipo_documento') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                --}}
                            </optgroup>
                            <optgroup label="Conductores">
                                <option value="LICENCIA CONDUCCION" {{ request('tipo_documento') == 'LICENCIA CONDUCCION' ? 'selected' : '' }}>Licencia Conducción</option>
                                {{-- Comentados para futuras actualizaciones
                                <option value="EPS" {{ request('tipo_documento') == 'EPS' ? 'selected' : '' }}>EPS</option>
                                <option value="ARL" {{ request('tipo_documento') == 'ARL' ? 'selected' : '' }}>ARL</option>
                                <option value="Certificado Medico" {{ request('tipo_documento') == 'Certificado Medico' ? 'selected' : '' }}>Certificado Medico</option>
                                <option value="Otro" {{ request('tipo_documento') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                --}}
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado_alerta" class="form-select">
                            <option value="TODOS">Todos</option>
                            <option value="POR_VENCER" {{ request('estado_alerta') == 'POR_VENCER' ? 'selected' : '' }}>Por Vencer</option>
                            <option value="VENCIDO" {{ request('estado_alerta') == 'VENCIDO' ? 'selected' : '' }}>Vencidos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Clasificación</label>
                        <select name="clasificacion" class="form-select">
                            <option value="">Todas</option>
                            <option value="EMPLEADO" {{ request('clasificacion') == 'EMPLEADO' ? 'selected' : '' }}>Empleado</option>
                            <option value="CONTRATISTA" {{ request('clasificacion') == 'CONTRATISTA' ? 'selected' : '' }}>Contratista</option>
                            <option value="EXTERNO" {{ request('clasificacion') == 'EXTERNO' ? 'selected' : '' }}>Externo</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn w-100" style="background-color: #5B8238; color: white;">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        {{-- Documentos de Vehículos --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-car me-2" style="color: #5B8238;"></i>
                        Documentos de Vehículos
                        <span class="badge bg-secondary ms-2">{{ $documentosVehiculos->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #f8f9fa; position: sticky; top: 0;">
                                <tr>
                                    <th class="px-3">Placa</th>
                                    <th>Documento</th>
                                    <th>Vencimiento</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentosVehiculos as $doc)
                                @php
                                $diasRestantes = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($doc->fecha_vencimiento)->startOfDay(), false);
                                @endphp
                                <tr>
                                    <td class="px-3">
                                        <a href="{{ route('reportes.ficha', $doc->vehiculo->id_vehiculo ?? 0) }}"
                                            class="fw-bold text-decoration-none" style="color: #5B8238;">
                                            {{ $doc->vehiculo->placa ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $doc->tipo_documento }}</div>
                                        <small class="text-muted">{{ $doc->numero_documento }}</small>
                                    </td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</div>
                                        <small class="text-{{ $diasRestantes < 0 || $diasRestantes <= 5 ? 'danger' : 'warning' }} fw-bold">
                                            @if($diasRestantes < 0)
                                                Vencido hace {{ abs($diasRestantes) }} días
                                                @else
                                                Vence en {{ $diasRestantes }} días
                                                @endif
                                                </small>
                                    </td>
                                    {{-- Semáforo: Rojo (0-5 días o vencido), Amarillo (6-20 días), Verde (> 20 días) --}}
                                    <td class="text-center">
                                        <span class="semaforo semaforo-{{ $diasRestantes < 0 || $diasRestantes <= 5 ? 'danger' : ($diasRestantes <= 20 ? 'warning' : 'success') }}"></span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                        <p class="mb-0">No hay documentos por vencer o vencidos</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documentos de Conductores --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2" style="color: #5B8238;"></i>
                        Documentos de Conductores
                        <span class="badge bg-secondary ms-2">{{ $documentosConductores->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #f8f9fa; position: sticky; top: 0;">
                                <tr>
                                    <th class="px-3">Conductor</th>
                                    <th>Documento</th>
                                    <th>Vencimiento</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentosConductores as $doc)
                                @php
                                $diasRestantes = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($doc->fecha_vencimiento)->startOfDay(), false);
                                @endphp
                                <tr>
                                    <td class="px-3">
                                        <div class="fw-medium">{{ $doc->conductor->nombre ?? '' }} {{ $doc->conductor->apellido ?? '' }}</div>
                                        <small class="text-muted">{{ $doc->conductor->identificacion ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $doc->tipo_documento }}</div>
                                        <small class="text-muted">{{ $doc->numero_documento }}</small>
                                    </td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</div>
                                        <small class="text-{{ $diasRestantes < 0 || $diasRestantes <= 5 ? 'danger' : 'warning' }} fw-bold">
                                            @if($diasRestantes < 0)
                                                Vencido hace {{ abs($diasRestantes) }} días
                                                @else
                                                Vence en {{ $diasRestantes }} días
                                                @endif
                                                </small>
                                    </td>
                                    {{-- Semáforo: Rojo (0-5 días o vencido), Amarillo (6-20 días), Verde (> 20 días) --}}
                                    <td class="text-center">
                                        <span class="semaforo semaforo-{{ $diasRestantes < 0 || $diasRestantes <= 5 ? 'danger' : ($diasRestantes <= 20 ? 'warning' : 'success') }}"></span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                        <p class="mb-0">No hay documentos por vencer o vencidos</p>
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

    {{-- Línea de Tiempo --}}
    @if($lineaTiempo->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2" style="color: #5B8238;"></i>Línea de Tiempo - Próximos Vencimientos (90 días)</h5>
        </div>
        <div class="card-body">
            @foreach($lineaTiempo as $mes => $eventos)
            <div class="timeline-mes mb-4">
                <h6 class="text-uppercase text-muted mb-3">
                    <i class="fas fa-calendar me-1"></i>
                    {{ \Carbon\Carbon::parse($mes . '-01')->translatedFormat('F Y') }}
                </h6>
                <div class="row">
                    @foreach($eventos as $evento)
                    <div class="col-md-4 col-lg-3 mb-3">
                        <div class="card border-{{ $evento['estado'] == 'VENCIDO' ? 'danger' : 'warning' }} border-2 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-{{ $evento['tipo'] == 'vehiculo' ? 'dark' : 'info' }}">
                                        <i class="fas fa-{{ $evento['tipo'] == 'vehiculo' ? 'car' : 'user' }} me-1"></i>
                                        {{ $evento['referencia'] }}
                                    </span>

                                </div>
                                <small class="text-muted d-block">{{ $evento['documento'] }}</small>
                                <small class="fw-medium">{{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Leyenda de Semáforo --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Leyenda de Estados</h6>
            <div class="d-flex flex-wrap gap-4">
                <div class="d-flex align-items-center">
                    <span class="semaforo semaforo-success me-2"></span>
                    <span>Vigente (más de 20 días)</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="semaforo semaforo-warning me-2"></span>
                    <span>Por vencer (6-20 días)</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="semaforo semaforo-danger me-2"></span>
                    <span>Crítico/Vencido (0-5 días o vencido)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function exportarReporte(formato) {
        const params = new URLSearchParams(window.location.search);
        let url = formato === 'excel' ?
            '{{ route("reportes.export.excel", ["tipo" => "alertas"]) }}' :
            '{{ route("reportes.export.pdf", ["tipo" => "alertas"]) }}';
        window.open(url + '?' + params.toString(), '_blank');
    }
</script>
@endsection
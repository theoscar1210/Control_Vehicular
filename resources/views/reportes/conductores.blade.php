@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Reporte por Conductor')

@section('content')
<br><br><br>
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item active">Por Conductor</li>
                </ol>
            </nav>
            <h2 class="mb-0"><i class="fas fa-id-card me-2" style="color: #5B8238;"></i>Reporte por Conductor</h2>
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

    {{-- Estadísticas Generales --}}
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-id-card fa-lg mb-2" style="color: #5B8238;"></i>
                    <h4 class="mb-0">{{ $estadisticas['total_conductores'] }}</h4>
                    <small class="text-muted">Conductores</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-car fa-lg mb-2 text-primary"></i>
                    <h4 class="mb-0">{{ $estadisticas['total_vehiculos'] }}</h4>
                    <small class="text-muted">Vehículos Asignados</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-success border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-success">{{ $estadisticas['licencias_vigentes'] }}</h4>
                    <small class="text-muted">Licencias Vigentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-warning border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-warning">{{ $estadisticas['licencias_por_vencer'] }}</h4>
                    <small class="text-muted">Por Vencer</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-danger border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-danger">{{ $estadisticas['licencias_vencidas'] }}</h4>
                    <small class="text-muted">Vencidas</small>
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
            <form method="GET" action="{{ route('reportes.conductores') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> Buscar Conductor</label>
                        <input type="text" name="buscar" class="form-control"
                            placeholder="Nombre, apellido o identificación..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="fas fa-user me-1"></i> Conductor Específico</label>
                        <select name="conductor" class="form-select">
                            <option value="">Todos los conductores</option>
                            @foreach($listaConductores as $cond)
                            <option value="{{ $cond->id_conductor }}" {{ request('conductor') == $cond->id_conductor ? 'selected' : '' }}>
                                {{ $cond->nombre }} {{ $cond->apellido }} ({{ $cond->identificacion }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn w-100" style="background-color: #5B8238; color: white;">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de Conductores con Vehículos --}}
    @forelse($conductores as $conductor)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3" style="background-color: #5B8238;">
                            {{ strtoupper(substr($conductor->nombre, 0, 1)) }}{{ strtoupper(substr($conductor->apellido, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $conductor->nombre }} {{ $conductor->apellido }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-id-card me-1"></i>{{ $conductor->tipo_doc }} {{ $conductor->identificacion }}
                                @if($conductor->telefono)
                                <span class="ms-2"><i class="fas fa-phone me-1"></i>{{ $conductor->telefono }}</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    {{-- Resumen del conductor --}}
                    <div class="d-flex gap-2 flex-wrap">
                        {{-- Estado de licencia --}}
                        <span class="badge bg-{{ $conductor->estado_documentos['clase'] }} px-3 py-2">
                            <i class="{{ $conductor->estado_documentos['icono'] }} me-1"></i>
                            {{ $conductor->estado_documentos['texto'] }}
                        </span>
                        @if($conductor->stats['total_vehiculos'] > 0)
                        <span class="badge bg-primary px-3 py-2">
                            <i class="fas fa-car me-1"></i>{{ $conductor->stats['total_vehiculos'] }} vehículo(s)
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            {{-- Información de Licencia --}}
            @php
                $licencia = $conductor->documentosConductor->where('tipo_documento', 'Licencia Conducción')->first();
            @endphp
            @if($licencia)
            <div class="px-4 py-3 bg-light border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Licencia de Conducción</small>
                        <strong>{{ $licencia->numero_documento ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Categoría(s)</small>
                        <span class="badge bg-info">{{ $licencia->categoria_licencia ?? 'N/A' }}</span>
                        @if($licencia->categorias_adicionales)
                        <small class="text-muted ms-1">+{{ $licencia->categorias_adicionales }}</small>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Vencimiento</small>
                        @if($licencia->fecha_vencimiento)
                        <strong class="text-{{ $licencia->clase_badge }}">
                            {{ $licencia->fecha_vencimiento->format('d/m/Y') }}
                        </strong>
                        <small class="text-muted">({{ $licencia->diasRestantes() }} días)</small>
                        @else
                        <span class="text-muted">Sin fecha</span>
                        @endif
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-{{ $licencia->clase_badge }} px-3 py-2">
                            {{ $licencia->estado_legible }}
                        </span>
                    </div>
                </div>
            </div>
            @else
            <div class="px-4 py-3 bg-light border-bottom">
                <div class="text-center text-muted">
                    <i class="fas fa-id-card me-2"></i>Sin licencia de conducción registrada
                </div>
            </div>
            @endif

            {{-- Vehículos asignados --}}
            @if($conductor->vehiculos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4">Placa</th>
                            <th>Tipo</th>
                            <th>Marca / Modelo</th>
                            <th>Propietario</th>
                            <th class="text-center">Estado Documental</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conductor->vehiculos as $vehiculo)
                        <tr>
                            <td class="px-4">
                                <strong style="color: #5B8238;">{{ $vehiculo->placa }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $vehiculo->tipo == 'Carro' ? 'primary' : 'info' }}">
                                    {{ $vehiculo->tipo }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $vehiculo->marca }}</div>
                                <small class="text-muted">{{ $vehiculo->modelo }}</small>
                            </td>
                            <td>
                                @if($vehiculo->propietario)
                                {{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}
                                @else
                                <span class="text-muted">Sin propietario</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $vehiculo->estado_general['clase'] }} px-3 py-2">
                                    <i class="{{ $vehiculo->estado_general['icono'] }} me-1"></i>
                                    {{ $vehiculo->estado_general['texto'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('reportes.ficha', $vehiculo->id_vehiculo) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-id-badge"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-car fa-2x mb-2"></i>
                <p class="mb-0">Este conductor no tiene vehículos asignados</p>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No se encontraron conductores</h5>
            <p class="text-muted mb-0">Ajusta los filtros de búsqueda</p>
        </div>
    </div>
    @endforelse
</div>

<style>
    .avatar-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
    }
</style>

<script>
    function exportarReporte(formato) {
        const params = new URLSearchParams(window.location.search);
        let url = formato === 'excel' ?
            '{{ route("reportes.export.excel", ["tipo" => "conductores"]) }}' :
            '{{ route("reportes.export.pdf", ["tipo" => "conductores"]) }}';
        window.open(url + '?' + params.toString(), '_blank');
    }
</script>
@endsection

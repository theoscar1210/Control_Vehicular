@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Reporte de Conductores')

@section('content')
<br><br><br>
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item active">Conductores</li>
                </ol>
            </nav>
            <h2 class="mb-0"><i class="fas fa-id-card me-2" style="color: #5B8238;"></i>Reporte General de Conductores</h2>
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

    {{-- Estadisticas del Reporte --}}
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <h4 class="mb-0" style="color: #5B8238;">{{ $estadisticas['total_conductores'] }}</h4>
                    <small class="text-muted">Total Conductores</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-success border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-success">{{ $estadisticas['licencias_vigentes'] }}</h4>
                    <small class="text-muted">Licencias Vigentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-warning border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-warning">{{ $estadisticas['licencias_por_vencer'] }}</h4>
                    <small class="text-muted">Por Vencer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
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
            <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filtros de Busqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.conductores') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> Buscar</label>
                        <input type="text" name="buscar" class="form-control"
                            placeholder="Nombre, apellido o identificación..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-user me-1"></i> Conductor</label>
                        <select name="conductor" class="form-select">
                            <option value="">Todos los conductores</option>
                            @foreach($listaConductores as $cond)
                            <option value="{{ $cond->id_conductor }}" {{ request('conductor') == $cond->id_conductor ? 'selected' : '' }}>
                                {{ $cond->nombre }} {{ $cond->apellido }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-traffic-light me-1"></i> Estado Licencia</label>
                        <select name="estado_licencia" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="VIGENTE" {{ request('estado_licencia') == 'VIGENTE' ? 'selected' : '' }}>Vigentes</option>
                            <option value="POR_VENCER" {{ request('estado_licencia') == 'POR_VENCER' ? 'selected' : '' }}>Por Vencer</option>
                            <option value="VENCIDO" {{ request('estado_licencia') == 'VENCIDO' ? 'selected' : '' }}>Vencidas</option>
                            <option value="SIN_DOCUMENTOS" {{ request('estado_licencia') == 'SIN_DOCUMENTOS' ? 'selected' : '' }}>Sin Licencia</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-tags me-1"></i> Clasificación</label>
                        <select name="clasificacion" class="form-select">
                            <option value="">Todas</option>
                            <option value="EMPLEADO" {{ request('clasificacion') == 'EMPLEADO' ? 'selected' : '' }}>Empleado</option>
                            <option value="CONTRATISTA" {{ request('clasificacion') == 'CONTRATISTA' ? 'selected' : '' }}>Contratista</option>
                            <option value="EXTERNO" {{ request('clasificacion') == 'EXTERNO' ? 'selected' : '' }}>Externo</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn px-4" style="background-color: #5B8238; color: white;">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <a href="{{ route('reportes.conductores') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Resultados --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Listado de Conductores</h5>
            <span class="badge px-3 py-2" style="background-color: #5B8238;">
                {{ $conductores->count() }} conductor(es)
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4 py-3">Conductor</th>
                            <th class="py-3">Identificacion</th>
                            <th class="py-3">Telefono</th>
                            <th class="py-3">Licencia</th>
                            <th class="py-3">Categoria(s)</th>
                            <th class="py-3">Vencimiento</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="py-3 text-center">Vehiculo</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conductores as $conductor)
                        @php
                            $licencia = $conductor->documentosConductor->where('tipo_documento', 'Licencia Conducción')->first();
                        @endphp
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="background-color: #5B8238; width: 35px; height: 35px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($conductor->nombre, 0, 1)) }}{{ strtoupper(substr($conductor->apellido, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $conductor->nombre }} {{ $conductor->apellido }}</strong>
                                        @if($conductor->clasificacion && $conductor->clasificacion !== 'EMPLEADO')
                                        @php
                                        $badgeClas = match($conductor->clasificacion) {
                                            'CONTRATISTA' => 'warning',
                                            'EXTERNO' => 'info',
                                            default => 'secondary',
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $badgeClas }} ms-1" style="font-size: 0.65rem;">{{ $conductor->clasificacion }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $conductor->tipo_doc }}</small><br>
                                {{ $conductor->identificacion }}
                            </td>
                            <td>
                                @if($conductor->telefono)
                                {{ $conductor->telefono }}
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($licencia && $licencia->numero_documento)
                                {{ $licencia->numero_documento }}
                                @else
                                <span class="text-muted">Sin registro</span>
                                @endif
                            </td>
                            <td>
                                @if($licencia)
                                <span class="badge bg-info">{{ $licencia->categoria_licencia ?? 'N/A' }}</span>
                                @if($licencia->categorias_adicionales)
                                <small class="text-muted">+ {{ $licencia->categorias_adicionales }}</small>
                                @endif
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($licencia && $licencia->fecha_vencimiento)
                                <span class="text-{{ $licencia->clase_badge }}">
                                    {{ $licencia->fecha_vencimiento->format('d/m/Y') }}
                                </span>
                                <br>
                                <small class="text-muted">({{ $licencia->diasRestantes() }} dias)</small>
                                @else
                                <span class="text-muted">Sin fecha</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $conductor->estado_documentos['clase'] }} px-3 py-2">
                                    <i class="{{ $conductor->estado_documentos['icono'] }} me-1"></i>
                                    {{ $conductor->estado_documentos['texto'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($conductor->vehiculos->count() > 0)
                                <span class="badge bg-primary">
                                    {{ $conductor->vehiculos->first()->placa }}
                                </span>
                                @if($conductor->vehiculos->count() > 1)
                                <small class="text-muted d-block">+{{ $conductor->vehiculos->count() - 1 }} mas</small>
                                @endif
                                @else
                                <span class="text-muted">Sin vehiculo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('reportes.ficha.conductor', $conductor->id_conductor) }}"
                                    class="btn btn-sm btn-outline-primary" title="Ver Ficha">
                                    <i class="fas fa-id-badge me-1"></i> Ficha
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No se encontraron conductores con los filtros seleccionados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function exportarReporte(formato) {
        const form = document.getElementById('filtrosForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        let url = formato === 'excel' ?
            '{{ route("reportes.export.excel", ["tipo" => "conductores"]) }}' :
            '{{ route("reportes.export.pdf", ["tipo" => "conductores"]) }}';

        if (params) {
            url += '?' + params;
        }

        window.open(url, '_blank');
    }
</script>
@endsection

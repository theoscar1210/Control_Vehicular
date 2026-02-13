@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp



@extends('layouts.app')

@section('title', 'Reporte por Propietario')

@section('content')
<br><br><br>
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item active">Por Propietario</li>
                </ol>
            </nav>
            <h2 class="mb-0"><i class="fas fa-user-tie me-2" style="color: #5B8238;"></i>Reporte por Propietario</h2>
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

    {{-- Estadísticas Generales --}}
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-users fa-lg mb-2" style="color: #5B8238;"></i>
                    <h4 class="mb-0">{{ $estadisticas['total_propietarios'] }}</h4>
                    <small class="text-muted">Propietarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-car fa-lg mb-2 text-primary"></i>
                    <h4 class="mb-0">{{ $estadisticas['total_vehiculos'] }}</h4>
                    <small class="text-muted">Vehículos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-success border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-success">{{ $estadisticas['vehiculos_vigentes'] }}</h4>
                    <small class="text-muted">Vigentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-warning border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-warning">{{ $estadisticas['vehiculos_por_vencer'] }}</h4>
                    <small class="text-muted">Por Vencer</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-danger border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-danger">{{ $estadisticas['vehiculos_vencidos'] }}</h4>
                    <small class="text-muted">Vencidos</small>
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
            <form method="GET" action="{{ route('reportes.propietarios') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> Buscar Propietario</label>
                        <input type="text" name="buscar" class="form-control"
                            placeholder="Nombre, apellido o identificación..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="fas fa-user me-1"></i> Propietario Específico</label>
                        <select name="propietario" class="form-select">
                            <option value="">Todos los propietarios</option>
                            @foreach($propietarios as $prop)
                            <option value="{{ $prop->id_propietario }}" {{ request('propietario') == $prop->id_propietario ? 'selected' : '' }}>
                                {{ $prop->nombre }} {{ $prop->apellido }} ({{ $prop->identificacion }})
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

    {{-- Lista de Propietarios con Vehículos --}}
    @forelse($propietarios as $propietario)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3" style="background-color: #5B8238;">
                            {{ strtoupper(substr($propietario->nombre, 0, 1)) }}{{ strtoupper(substr($propietario->apellido, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $propietario->nombre }} {{ $propietario->apellido }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-id-card me-1"></i>{{ $propietario->tipo_doc }} {{ $propietario->identificacion }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    {{-- Resumen del propietario --}}
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="fas fa-car me-1"></i>{{ $propietario->stats['total_vehiculos'] }} vehículo(s)
                        </span>
                        @if($propietario->stats['vigentes'] > 0)
                        <span class="badge bg-success px-2 py-2">{{ $propietario->stats['vigentes'] }} OK</span>
                        @endif
                        @if($propietario->stats['por_vencer'] > 0)
                        <span class="badge bg-warning text-dark px-2 py-2">{{ $propietario->stats['por_vencer'] }} Por vencer</span>
                        @endif
                        @if($propietario->stats['vencidos'] > 0)
                        <span class="badge bg-danger px-2 py-2">{{ $propietario->stats['vencidos'] }} Vencidos</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($propietario->vehiculos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4">Placa</th>
                            <th>Tipo</th>
                            <th>Marca / Modelo</th>
                            <th>Conductor</th>
                            <th class="text-center">Estado Documental</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($propietario->vehiculos as $vehiculo)
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
                                @if($vehiculo->conductor)
                                {{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}
                                @else
                                <span class="text-muted">Sin asignar</span>
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
                <p class="mb-0">Este propietario no tiene vehículos registrados</p>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No se encontraron propietarios</h5>
            <p class="text-muted mb-0">Ajusta los filtros de búsqueda</p>
        </div>
    </div>
    @endforelse
</div>

<script>
    function exportarReporte(formato) {
        const params = new URLSearchParams(window.location.search);
        let url = formato === 'excel' ?
            '{{ route("reportes.export.excel", ["tipo" => "propietarios"]) }}' :
            '{{ route("reportes.export.pdf", ["tipo" => "propietarios"]) }}';
        window.open(url + '?' + params.toString(), '_blank');
    }
</script>
@endsection
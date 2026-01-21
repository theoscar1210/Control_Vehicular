@extends('layouts.app')

@section('title', 'Reporte de Vehículos')

@section('content')
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item active">Vehículos</li>
                </ol>
            </nav>
            <h2 class="mb-0"><i class="fas fa-car-side me-2" style="color: #5B8238;"></i>Reporte General de Vehículos</h2>
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

    {{-- Estadísticas del Reporte --}}
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <h4 class="mb-0" style="color: #5B8238;">{{ $estadisticas['total'] }}</h4>
                    <small class="text-muted">Total Vehículos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-success border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-success">{{ $estadisticas['vigentes'] }}</h4>
                    <small class="text-muted">Docs. Vigentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-warning border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-warning">{{ $estadisticas['por_vencer'] }}</h4>
                    <small class="text-muted">Por Vencer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100 border-start border-danger border-3">
                <div class="card-body py-3">
                    <h4 class="mb-0 text-danger">{{ $estadisticas['vencidos'] }}</h4>
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
            <form method="GET" action="{{ route('reportes.vehiculos') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-car me-1"></i> Placa</label>
                        <input type="text" name="placa" class="form-control text-uppercase"
                               placeholder="ABC123" value="{{ request('placa') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-truck me-1"></i> Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="Carro" {{ request('tipo') == 'Carro' ? 'selected' : '' }}>Carro</option>
                            <option value="Moto" {{ request('tipo') == 'Moto' ? 'selected' : '' }}>Moto</option>
                            <option value="Camión" {{ request('tipo') == 'Camión' ? 'selected' : '' }}>Camión</option>
                            <option value="Otro" {{ request('tipo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-user-tie me-1"></i> Propietario</label>
                        <select name="propietario" class="form-select">
                            <option value="">Todos</option>
                            @foreach($propietarios as $prop)
                                <option value="{{ $prop->id_propietario }}" {{ request('propietario') == $prop->id_propietario ? 'selected' : '' }}>
                                    {{ $prop->nombre }} {{ $prop->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-traffic-light me-1"></i> Estado Docs.</label>
                        <select name="estado_docs" class="form-select">
                            <option value="TODOS">Todos los estados</option>
                            <option value="VIGENTE" {{ request('estado_docs') == 'VIGENTE' ? 'selected' : '' }}>
                                Vigentes
                            </option>
                            <option value="POR_VENCER" {{ request('estado_docs') == 'POR_VENCER' ? 'selected' : '' }}>
                                Por Vencer
                            </option>
                            <option value="VENCIDO" {{ request('estado_docs') == 'VENCIDO' ? 'selected' : '' }}>
                                Vencidos
                            </option>
                            <option value="SIN_DOCUMENTOS" {{ request('estado_docs') == 'SIN_DOCUMENTOS' ? 'selected' : '' }}>
                                Sin Documentos
                            </option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn px-4" style="background-color: #5B8238; color: white;">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <a href="{{ route('reportes.vehiculos') }}" class="btn btn-outline-secondary">
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
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Listado de Vehículos</h5>
            <span class="badge px-3 py-2" style="background-color: #5B8238;">
                {{ $vehiculos->count() }} vehículo(s)
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4 py-3">Placa</th>
                            <th class="py-3">Tipo</th>
                            <th class="py-3">Marca / Modelo</th>
                            <th class="py-3">Propietario</th>
                            <th class="py-3">Conductor</th>
                            <th class="py-3 text-center">Estado Documental</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiculos as $vehiculo)
                        <tr>
                            <td class="px-4">
                                <strong style="color: #5B8238;">{{ $vehiculo->placa }}</strong>
                            </td>
                            <td>
                                <span class="badge {{ $vehiculo->tipo == 'Carro' ? 'bg-primary' : ($vehiculo->tipo == 'Moto' ? 'bg-info' : 'bg-secondary') }}">
                                    <i class="fas fa-{{ $vehiculo->tipo == 'Carro' ? 'car' : ($vehiculo->tipo == 'Moto' ? 'motorcycle' : 'truck') }} me-1"></i>
                                    {{ $vehiculo->tipo }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $vehiculo->marca }}</div>
                                <small class="text-muted">{{ $vehiculo->modelo }}</small>
                            </td>
                            <td>
                                @if($vehiculo->propietario)
                                    <div>{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}</div>
                                    <small class="text-muted">{{ $vehiculo->propietario->identificacion }}</small>
                                @else
                                    <span class="text-muted">Sin propietario</span>
                                @endif
                            </td>
                            <td>
                                @if($vehiculo->conductor)
                                    {{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}
                                @else
                                    <span class="text-muted">Sin conductor</span>
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
                                   class="btn btn-sm btn-outline-primary" title="Ver Ficha">
                                    <i class="fas fa-id-badge me-1"></i> Ficha
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No se encontraron vehículos con los filtros seleccionados</p>
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

    // Crear URL de exportación
    let url = '{{ route("reportes.export", ["tipo" => "vehiculos"]) }}?formato=' + formato;
    if (params) {
        url += '&' + params;
    }

    window.open(url, '_blank');
}
</script>
@endsection

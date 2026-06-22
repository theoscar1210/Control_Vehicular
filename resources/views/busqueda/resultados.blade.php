@extends('layouts.app')

@section('title', 'Búsqueda: ' . $q)

@section('content')
<div class="container py-4">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
        <div>
            <h3 class="mb-0"><i class="fas fa-search me-2 text-success"></i>Resultados de búsqueda</h3>
            @if($q)
            <p class="text-muted mb-0 small">
                Mostrando resultados para <strong>"{{ $q }}"</strong>
                — {{ $vehiculos->count() + $conductores->count() + $propietarios->count() }} encontrado(s)
            </p>
            @endif
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    {{-- Buscador en página --}}
    <form action="{{ route('busqueda.resultados') }}" method="GET" class="mb-4">
        <div class="input-group input-group-lg shadow-sm">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" name="q" value="{{ $q }}" class="form-control border-start-0 ps-0"
                placeholder="Buscar vehículo, conductor o propietario..." autofocus>
            <button type="submit" class="btn text-white px-4" style="background-color: #5B8238;">
                Buscar
            </button>
        </div>
    </form>

    @if(mb_strlen($q) < 2)
    <div class="text-center py-5 text-muted">
        <i class="fas fa-search fa-3x mb-3 d-block"></i>
        Escribe al menos 2 caracteres para buscar.
    </div>
    @elseif($vehiculos->isEmpty() && $conductores->isEmpty() && $propietarios->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
        <h5>Sin resultados para "{{ $q }}"</h5>
        <p class="mb-0">Intenta con la placa, nombre, identificación u otro término.</p>
    </div>
    @else

    <div class="row g-4">

        {{-- VEHÍCULOS --}}
        @if($vehiculos->isNotEmpty())
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #5B8238, #7da956);">
                    <h6 class="mb-0 text-white">
                        <i class="fas fa-car me-2"></i>Vehículos
                        <span class="badge bg-white text-success ms-2">{{ $vehiculos->count() }}</span>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Placa</th>
                                <th>Marca / Modelo</th>
                                <th class="d-none d-md-table-cell">Color</th>
                                <th class="d-none d-md-table-cell">Tipo</th>
                                <th class="d-none d-sm-table-cell">Propietario</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehiculos as $v)
                            <tr>
                                <td>
                                    <strong class="text-success">{{ $v->placa }}</strong>
                                </td>
                                <td>
                                    {{ $v->marca }}
                                    <small class="text-muted d-block">{{ $v->modelo }}</small>
                                </td>
                                <td class="d-none d-md-table-cell text-muted small">{{ $v->color ?? '—' }}</td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-secondary">{{ $v->tipo ?? '—' }}</span>
                                </td>
                                <td class="d-none d-sm-table-cell small text-muted">
                                    {{ $v->propietario ? $v->propietario->nombre . ' ' . $v->propietario->apellido : '—' }}
                                </td>
                                <td class="text-end">
                                    @if($esGestor)
                                    <a href="{{ route('reportes.ficha', $v->id_vehiculo) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-eye me-1"></i>Ver ficha
                                    </a>
                                    @else
                                    <a href="{{ route('porteria.index', ['busqueda' => $v->placa, 'tipo_busqueda' => 'placa']) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- CONDUCTORES --}}
        @if($conductores->isNotEmpty())
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #1a6fa8, #3a8fd0);">
                    <h6 class="mb-0 text-white">
                        <i class="fas fa-id-card me-2"></i>Conductores
                        <span class="badge bg-white text-primary ms-2">{{ $conductores->count() }}</span>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Identificación</th>
                                <th class="d-none d-md-table-cell">Teléfono</th>
                                <th class="d-none d-sm-table-cell">Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conductores as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c->nombre }} {{ $c->apellido }}</strong>
                                    <small class="text-muted d-block d-sm-none">{{ $c->identificacion }}</small>
                                </td>
                                <td class="small text-muted">{{ $c->identificacion }}</td>
                                <td class="d-none d-md-table-cell small text-muted">{{ $c->telefono ?? '—' }}</td>
                                <td class="d-none d-sm-table-cell">
                                    @if($c->activo)
                                    <span class="badge bg-success">Activo</span>
                                    @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('reportes.ficha.conductor', $c->id_conductor) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Ver ficha
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- PROPIETARIOS (solo ADMIN/SST) --}}
        @if($propietarios->isNotEmpty())
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #6f42c1, #9b72e0);">
                    <h6 class="mb-0 text-white">
                        <i class="fas fa-user-tie me-2"></i>Propietarios
                        <span class="badge bg-white text-purple ms-2">{{ $propietarios->count() }}</span>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Identificación</th>
                                <th class="d-none d-md-table-cell">Teléfono</th>
                                <th class="d-none d-sm-table-cell">Vehículos</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($propietarios as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->nombre }} {{ $p->apellido }}</strong>
                                </td>
                                <td class="small text-muted">{{ $p->identificacion }}</td>
                                <td class="d-none d-md-table-cell small text-muted">{{ $p->telefono ?? '—' }}</td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge bg-primary">{{ $p->vehiculos()->count() }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('reportes.propietarios', ['propietario' => $p->id_propietario]) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye me-1"></i>Ver reporte
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
    @endif

</div>
@endsection

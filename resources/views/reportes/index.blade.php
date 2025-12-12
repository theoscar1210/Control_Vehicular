@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp


@extends('layouts.app')

@section('title','Consultas y Reportes')

@section('content')
<div class="container mt-4">
    <h2>Consultas y Reportes</h2>

    {{-- Formulario de filtro --}}
    <form method="GET" action="{{ route('documentos.consultar') }}" class="card card-body mb-3 shadow-lg">
        <h5 class="fw-bold text-primary mb-3">Filtros de búsqueda</h5>

        <div class="row g-3">

            {{-- Documentos --}}
            <div class="col-lg-4 col-md-6">
                <label class="fw-semibold text-primary">Documentos a buscar</label>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="documentos[]" value="SOAT"
                        {{ in_array('SOAT', (array)request('documentos')) ? 'checked' : '' }}>
                    <label class="form-check-label">SOAT</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="documentos[]" value="Licencia Conduccion"
                        {{ in_array('LICENCIA_TRANSITO', (array)request('documentos')) ? 'checked' : '' }}>
                    <label class="form-check-label">Licencia de Conduccion</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="documentos[]" value="Tarjeta Propiedad"
                        {{ in_array('TARJETA_PROPIEDAD', (array)request('documentos')) ? 'checked' : '' }}>
                    <label class="form-check-label">Licencia de Transito</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="documentos[]" value="Tecnomecanica"
                        {{ in_array('REVISION_TECNICOMECANICA', (array)request('documentos')) ? 'checked' : '' }}>
                    <label class="form-check-label">Revisión tecnicomecánica</label>
                </div>
            </div>

            {{-- Estado --}}
            <div class="col-lg-2 col-md-6">
                <label class="fw-semibold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">-- Todos --</option>
                    <option value="VIGENTE" {{ request('estado')=='VIGENTE' ? 'selected' : '' }}>VIGENTE</option>
                    <option value="POR_VENCER" {{ request('estado')=='POR_VENCER' ? 'selected' : '' }}>PRÓXIMO</option>
                    <option value="VENCIDO" {{ request('estado')=='VENCIDO' ? 'selected' : '' }}>VENCIDO</option>
                    <option value="REEMPLAZADO" {{ request('estado')=='REEMPLAZADO' ? 'selected' : '' }}>REEMPLAZADO</option>
                </select>
            </div>

            {{-- Conductor --}}
            <div class="col-lg-3 col-md-6">
                <label class="fw-semibold">Conductor</label>
                <input type="text" name="conductor" value="{{ request('conductor') }}" class="form-control" placeholder="Nombre o Documento">
            </div>

            {{-- Placa --}}
            <div class="col-lg-2 col-md-6">
                <label class="fw-semibold">Placa</label>
                <input name="placa" value="{{ request('placa') }}" class="form-control" placeholder="ABC123">
            </div>

            {{-- Propietario --}}
            <div class="col-lg-3 col-md-6">
                <label class="fw-semibold">Propietario</label>
                <input type="text" name="propietario" value="{{ request('propietario') }}" class="form-control" placeholder="Nombre o Documento">
            </div>

            {{-- Desde --}}
            <div class="col-lg-2 col-md-6">
                <label class="fw-semibold">Desde</label>
                <input type="date" name="fecha_from" value="{{ request('fecha_from') }}" class="form-control">
            </div>

            {{-- Hasta --}}
            <div class="col-lg-2 col-md-6">
                <label class="fw-semibold">Hasta</label>
                <input type="date" name="fecha_to" value="{{ request('fecha_to') }}" class="form-control">
            </div>

            {{-- Botones --}}
            <div class="col-lg-3 col-md-6 d-flex gap-2 align-items-end">
                <button class="btn btn-primary w-100">Filtrar</button>
                <a href="{{ route('documentos.consultar') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
            </div>

        </div>

    </form>

    {{-- Botones export --}}
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('documentos.consultar.export.excel', request()->all()) }}" class="btn btn-success">Exportar Excel</a>
        <a href="{{ route('documentos.consultar.export.pdf', request()->all()) }}" class="btn btn-danger">Exportar PDF</a>
    </div>

    {{-- Resultados --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tipo</th>
                        <th>Número</th>
                        <th>Conductor</th>
                        <th>Fecha registro</th>
                        <th>Vencimiento</th>
                        <th>Placa</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $doc)
                    <tr class="{{ $doc->activo ? '' : 'text-muted' }}">
                        <td>{{ $doc->tipo_documento }}</td>
                        <td>{{ $doc->numero_documento }}</td>
                        <td>
                            @if($doc->conductor)
                            <a href="{{ route('conductores.edit', $doc->conductor->id_conductor) }}">
                                {{ $doc->conductor->nombre }} {{ $doc->conductor->apellido }}
                            </a>
                            @else
                            —
                            @endif
                        </td>
                        <td>{{ $doc->fecha_registro }}</td>
                        <td>{{ $doc->fecha_vencimiento }}</td>
                        <td>{{ $doc->vehiculo->placa ?? '—' }}</td>
                        <td>{{ $doc->estado }}</td>
                        <td>
                            @php
                            // Intentamos obtener el id del conductor desde diferentes fuentes
                            $conductorId = null;

                            // Si $doc->conductor ya existe y tiene id
                            if (isset($doc->conductor) && !empty($doc->conductor->id_conductor)) {
                            $conductorId = $doc->conductor->id_conductor;
                            }
                            // Si no, si el documento tiene vehiculo y ese vehiculo tiene id_conductor
                            elseif (isset($doc->vehiculo) && !empty($doc->vehiculo->id_conductor)) {
                            $conductorId = $doc->vehiculo->id_conductor;
                            }
                            @endphp

                            @if($conductorId)
                            <a href="{{ route('conductores.edit', $conductorId) }}" class="btn btn-sm btn-outline-secondary">
                                Ver historial
                            </a>
                            @else
                            <button class="btn btn-sm btn-outline-secondary" disabled title="No hay conductor asignado">
                                Ver historial
                            </button>
                            @endif

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay resultados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $documentos->links() }}
        </div>
    </div>
</div>
@endsection
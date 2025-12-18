@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title','Consultas y Reportes')

@section('content')
<br><br><br>
<div class="container-fluid px-4 py-4">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                Consultas y Reportes
            </h2>
            <p class="text-muted mb-0">Filtra y visualiza la información </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('documentos.consultar.export.excel', request()->all()) }}"
                class="btn btn-excel d-flex align-items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span class="d-none d-md-inline">Excel</span>
            </a>
            <a href="{{ route('documentos.consultar.export.pdf', request()->all()) }}"
                class="btn btn-pdf d-flex align-items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                <span class="d-none d-md-inline">PDF</span>
            </a>
        </div>
    </div>

    {{-- Formulario de Filtros --}}
    <form method="GET" action="{{ route('documentos.consultar') }}">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-gradient border-0 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-black fw-semibold">
                        <i class="fas fa-filter me-2 icono-filtro"></i>Filtros de Búsqueda
                    </h5>
                    <button type="button" class="btn btn-sm btn-light " data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
                        <i class="fas fa-chevron-down icono-filtro"></i>
                    </button>
                </div>
            </div>

            <div class="collapse show" id="filtrosCollapse">
                <div class="card-body p-4">
                    <div class="row g-4">

                        {{-- Documentos --}}
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold text-dark mb-3">
                                <i class="fas fa-file-alt text-primary me-2 "></i>Tipo de Documento
                            </label>

                            <div class="border rounded p-3 bg-light">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="documentos[]" value="SOAT" id="docSOAT"
                                        {{ in_array('SOAT', (array)request('documentos')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="docSOAT">
                                        <i class="fas fa-shield-alt text-success me-1"></i>SOAT
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="documentos[]" value="Licencia Conduccion" id="docLicencia"
                                        {{ in_array('LICENCIA_TRANSITO', (array)request('documentos')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="docLicencia">
                                        <i class="fas fa-id-card text-info me-1"></i>Licencia de Conducción
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="documentos[]" value="Tarjeta Propiedad" id="docTarjeta"
                                        {{ in_array('TARJETA_PROPIEDAD', (array)request('documentos')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="docTarjeta">
                                        <i class="fas fa-credit-card text-warning me-1"></i>Tarjeta de Propiedad
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="documentos[]" value="Tecnomecanica" id="docTecno"
                                        {{ in_array('REVISION_TECNICOMECANICA', (array)request('documentos')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="docTecno">
                                        <i class="fas fa-wrench text-danger me-1"></i>Revisión Tecnicomecánica
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-traffic-light text-primary me-2"></i>Estado
                            </label>
                            <select name="estado" class="form-select form-select-lg">
                                <option value="">Todos</option>
                                <option value="VIGENTE" {{ request('estado')=='VIGENTE' ? 'selected' : '' }}>
                                    ✓ Vigente
                                </option>
                                <option value="POR_VENCER" {{ request('estado')=='POR_VENCER' ? 'selected' : '' }}>
                                    ⚠ Próximo a vencer
                                </option>
                                <option value="VENCIDO" {{ request('estado')=='VENCIDO' ? 'selected' : '' }}>
                                    ✗ Vencido
                                </option>
                                <option value="REEMPLAZADO" {{ request('estado')=='REEMPLAZADO' ? 'selected' : '' }}>
                                    ↻ Reemplazado
                                </option>
                            </select>
                        </div>

                        {{-- Conductor --}}
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-user text-primary me-2"></i>Conductor
                            </label>
                            <input type="text" name="conductor" value="{{ request('conductor') }}"
                                class="form-control form-control-lg"
                                placeholder="Nombre o documento">
                        </div>

                        {{-- Placa --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-car text-primary me-2"></i>Placa
                            </label>
                            <input type="text" name="placa" value="{{ request('placa') }}"
                                class="form-control form-control-lg text-uppercase"
                                placeholder="ABC123" maxlength="6">
                        </div>

                        {{-- Propietario --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-user-tie text-primary me-2"></i>Propietario
                            </label>
                            <input type="text" name="propietario" value="{{ request('propietario') }}"
                                class="form-control form-control-lg"
                                placeholder="Nombre o documento">
                        </div>

                        {{-- Rango de fechas --}}
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Rango de Fechas
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="date" name="fecha_from" value="{{ request('fecha_from') }}"
                                    class="form-control" placeholder="Desde">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                                <input type="date" name="fecha_to" value="{{ request('fecha_to') }}"
                                    class="form-control" placeholder="Hasta">
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label fw-semibold text-dark opacity-0">Acciones</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-universal btn-lg w-50 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-search"></i>
                                    Buscar
                                </button>
                                <a href="{{ route('documentos.consultar') }}"
                                    class="btn btn-limpiar btn-lg w-50 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-eraser"></i>
                                    Limpiar
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Resultados --}}

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark">
                    Resultados de la Búsqueda
                </h5>
                <span class="badge bg-primary rounded-pill px-3 py-2 ms-4">
                    {{ $documentos->total() }} documento(s) encontrado(s)
                </span>
            </div>
        </div>

        {{-- Tabla de Resultados --}}

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 fw-semibold">Tipo</th>
                            <th class="px-3 py-3 fw-semibold">Número</th>
                            <th class="px-3 py-3 fw-semibold">Conductor</th>
                            <th class="px-3 py-3 fw-semibold">F. Emision</th>
                            <th class="px-3 py-3 fw-semibold">Vencimiento</th>
                            <th class="px-3 py-3 fw-semibold">Placa</th>
                            <th class="px-3 py-3 fw-semibold text-center">Estado</th>
                            <th class="px-3 py-3 fw-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr class="{{ $doc->activo ? '' : 'table-secondary' }}">
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    @php
                                    $iconos = [
                                    'SOAT' => ['icon' => 'fa-shield-alt', 'color' => 'success'],
                                    'Licencia Conduccion' => ['icon' => 'fa-id-card', 'color' => 'info'],
                                    'Tarjeta Propiedad' => ['icon' => 'fa-credit-card', 'color' => 'warning'],
                                    'Tecnomecanica' => ['icon' => 'fa-wrench', 'color' => 'danger']
                                    ];
                                    $config = $iconos[$doc->tipo_documento] ?? ['icon' => 'fa-file', 'color' => 'secondary'];
                                    @endphp
                                    <i class="fas {{ $config['icon'] }} text-{{ $config['color'] }} me-2"></i>
                                    <span class="fw-medium">{{ $doc->tipo_documento }}</span>
                                </div>
                            </td>
                            <td class="px-3">
                                <code class="bg-light px-2 py-1 rounded">{{ $doc->numero_documento }}</code>
                            </td>
                            <td class="px-3">
                                @if($doc->conductor)
                                <a href="{{ route('conductores.edit', $doc->conductor->id_conductor) }}"
                                    class="text-decoration-none fw-medium text-primary">

                                    {{ $doc->conductor->nombre }} {{ $doc->conductor->apellido }}
                                </a>
                                @else
                                <span class="text-muted fst-italic">Sin asignar</span>
                                @endif
                            </td>
                            <td class="px-3">
                                <small class="text-muted">
                                    {{ $doc->fecha_emision }}
                                </small>
                            </td>
                            <td class="px-3">
                                <small class="text-muted">
                                    {{ $doc->fecha_vencimiento }}
                                </small>
                            </td>
                            <td class="px-3">
                                @if($doc->vehiculo)
                                <span class="badge bg-dark">{{ $doc->vehiculo->placa }}</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-3 text-center">
                                @php
                                $estadoBadge = [
                                'VIGENTE' => ['class' => 'success', 'icon' => 'check-circle'],
                                'POR_VENCER' => ['class' => 'warning', 'icon' => 'exclamation-triangle'],
                                'VENCIDO' => ['class' => 'danger', 'icon' => 'times-circle'],
                                'REEMPLAZADO' => ['class' => 'secondary', 'icon' => 'sync-alt']
                                ];
                                $badge = $estadoBadge[$doc->estado] ?? ['class' => 'secondary', 'icon' => 'info-circle'];
                                @endphp
                                <span class="badge bg-{{ $badge['class'] }} d-inline-flex align-items-center gap-1">
                                    <i class="fas fa-{{ $badge['icon'] }}"></i>
                                    {{ $doc->estado }}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                @php
                                $conductorId = null;
                                if (isset($doc->conductor) && !empty($doc->conductor->id_conductor)) {
                                $conductorId = $doc->conductor->id_conductor;
                                }
                                elseif (isset($doc->vehiculo) && !empty($doc->vehiculo->id_conductor)) {
                                $conductorId = $doc->vehiculo->id_conductor;
                                }
                                @endphp

                                @if($conductorId)
                                <a href="{{ route('conductores.edit', $conductorId) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-history me-1"></i>
                                    <span class="d-none d-lg-inline">Historial</span>
                                </a>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" disabled
                                    title="No hay conductor asignado">
                                    <i class="fas fa-ban me-1"></i>
                                    <span class="d-none d-lg-inline">Sin historial</span>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-medium">No se encontraron resultados</p>
                                    <small>Intenta ajustar los filtros de búsqueda</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($documentos->hasPages())
        <div class="card-footer bg-white border-top py-3">

            <div class="text-muted small mb-2 text-center">
                Mostrando {{ $documentos->firstItem() }} - {{ $documentos->lastItem() }}
                de {{ $documentos->total() }} resultados
            </div>

            <div class="d-flex justify-content-center">
                {{ $documentos->links('pagination::bootstrap-5') }}
            </div>

        </div>
        @endif


    </div>

</div>


@endsection
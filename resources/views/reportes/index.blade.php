@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title','Consultas y Reportes')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection

@section('content')
<br><br><br>
<div class="container-fluid px-4 py-4">

    {{-- Header Section --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-bar-graph me-2" style="color:#5B8238;"></i>
                Consultas y Reportes
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-funnel me-1"></i>
                Filtra, visualiza y exporta información del sistema
            </p>
        </div>
    </div>

    {{-- Pestañas de Tipos de Reporte --}}
    <ul class="nav nav-pills mb-4 shadow-sm bg-white rounded p-2" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="documentos-tab" data-bs-toggle="tab"
                data-bs-target="#documentos-panel" type="button" role="tab">
                <i class="bi bi-file-text-fill me-2"></i>Documentos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vehiculos-tab" data-bs-toggle="tab"
                data-bs-target="#vehiculos-panel" type="button" role="tab">
                <i class="bi bi-car-front-fill me-2"></i>Vehículos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="conductores-tab" data-bs-toggle="tab"
                data-bs-target="#conductores-panel" type="button" role="tab">
                <i class="bi bi-person-badge-fill me-2"></i>Conductores
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="propietarios-tab" data-bs-toggle="tab"
                data-bs-target="#propietarios-panel" type="button" role="tab">
                <i class="bi bi-person-circle-fill me-2"></i>Propietarios
            </button>
        </li>
    </ul>

    {{-- Contenido de las Pestañas --}}
    <div class="tab-content" id="reportTabsContent">

        {{-- ==================== PANEL DOCUMENTOS ==================== --}}
        <div class="tab-pane fade show active" id="documentos-panel" role="tabpanel">

            {{-- Formulario de Filtros --}}
            <form method="GET" action="{{ route('documentos.consultar') }}" id="form-documentos">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white fw-semibold">
                                Filtros de Búsqueda
                            </h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm bg-transparent border-0" data-bs-toggle="collapse"
                                    data-bs-target="#filtrosCollapse">
                                    <i class="bi bi-caret-down-fill text-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="collapse show" id="filtrosCollapse">
                        <div class="card-body p-4">
                            <div class="row g-4">

                                {{-- Búsqueda General --}}
                                <div class="col-lg-12">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-search text-primary me-2"></i>Búsqueda General
                                    </label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control form-control-lg"
                                        placeholder="Buscar por número de documento, conductor, placa, propietario...">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Busca en todos los campos disponibles
                                    </small>
                                </div>

                                {{-- Tipo de Documento --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-3">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Tipo de Documento
                                    </label>
                                    <div class="border rounded p-3 bg-light">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="documentos[]"
                                                value="SOAT" id="docSOAT"
                                                {{ in_array('SOAT', (array)request('documentos')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="docSOAT">
                                                <i class="bi bi-shield-check text-success me-1"></i>SOAT
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="documentos[]"
                                                value="Licencia Conduccion" id="docLicencia"
                                                {{ in_array('Licencia Conduccion', (array)request('documentos')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="docLicencia">
                                                <i class="bi bi-person-vcard text-info me-1"></i>Licencia de Conducción
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="documentos[]"
                                                value="Tarjeta Propiedad" id="docTarjeta"
                                                {{ in_array('Tarjeta Propiedad', (array)request('documentos')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="docTarjeta">
                                                <i class="bi bi-credit-card text-warning me-1"></i>Tarjeta de Propiedad
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="documentos[]"
                                                value="Tecnomecanica" id="docTecno"
                                                {{ in_array('Tecnomecanica', (array)request('documentos')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="docTecno">
                                                <i class="bi bi-tools text-danger me-1"></i>Tecnomecánica
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Estado del Documento --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-stoplights text-primary me-2"></i>Estado
                                    </label>
                                    <select name="estado" class="form-select form-select-lg">
                                        <option value="">Todos los estados</option>
                                        <option value="VIGENTE" {{ request('estado')=='VIGENTE' ? 'selected' : '' }}>
                                            <i class="bi bi-check-circle"></i> Vigente
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
                                        <i class="bi bi-person text-primary me-2"></i>Conductor
                                    </label>
                                    <input type="text" name="conductor" value="{{ request('conductor') }}"
                                        class="form-control form-control-lg"
                                        placeholder="Nombre o documento">
                                </div>

                                {{-- Placa --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-car-front text-primary me-2"></i>Placa
                                    </label>
                                    <input type="text" name="placa" value="{{ request('placa') }}"
                                        class="form-control form-control-lg text-uppercase"
                                        placeholder="ABC123" maxlength="10">
                                </div>

                                {{-- Propietario --}}
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-person-circle text-primary me-2"></i>Propietario
                                    </label>
                                    <input type="text" name="propietario" value="{{ request('propietario') }}"
                                        class="form-control form-control-lg"
                                        placeholder="Nombre o documento">
                                </div>

                                {{-- Rango de Fechas de Vencimiento --}}
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-calendar-range text-primary me-2"></i>Rango de Vencimiento
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <input type="date" name="fecha_from" value="{{ request('fecha_from') }}"
                                            class="form-control" placeholder="Desde">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-arrow-right"></i>
                                        </span>
                                        <input type="date" name="fecha_to" value="{{ request('fecha_to') }}"
                                            class="form-control" placeholder="Hasta">
                                    </div>
                                </div>

                                {{-- Ordenar por --}}
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-sort-down text-primary me-2"></i>Ordenar por
                                    </label>
                                    <select name="orden" class="form-select form-select-lg">
                                        <option value="fecha_vencimiento_asc" {{ request('orden')=='fecha_vencimiento_asc' ? 'selected' : '' }}>
                                            Vencimiento (más próximo)
                                        </option>
                                        <option value="fecha_vencimiento_desc" {{ request('orden')=='fecha_vencimiento_desc' ? 'selected' : '' }}>
                                            Vencimiento (más lejano)
                                        </option>
                                        <option value="tipo_asc" {{ request('orden')=='tipo_asc' ? 'selected' : '' }}>
                                            Tipo (A-Z)
                                        </option>
                                        <option value="estado_asc" {{ request('orden')=='estado_asc' ? 'selected' : '' }}>
                                            Estado
                                        </option>
                                    </select>
                                </div>

                                {{-- Botones de Acción --}}
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button type="submit" class="btn btn-lg px-4"
                                                style="background-color:#5B8238; color:white;">
                                                <i class="bi bi-search me-2"></i>Buscar
                                            </button>
                                            <a href="{{ route('documentos.consultar') }}"
                                                class="btn btn-outline-secondary btn-lg px-4">
                                                <i class="bi bi-x-circle me-2"></i>Limpiar
                                            </a>
                                        </div>

                                        <div class="d-flex gap-2 flex-wrap">
                                            <button type="button" class="btn btn-success btn-lg"
                                                onclick="exportarReporte('excel')">
                                                <i class="bi bi-file-earmark-excel-fill me-2"></i>Excel
                                            </button>
                                            <button type="button" class="btn btn-danger btn-lg"
                                                onclick="exportarReporte('pdf')">
                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>PDF
                                            </button>
                                        </div>
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-semibold text-dark">
                            Resultados de la Búsqueda
                        </h5>
                        <span class="badge px-3 py-2" style="background-color:#5B8238; color:white; font-size:1rem;">

                            {{ $documentos->total() }} documento(s) encontrado(s)
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color:#f8f9fa; border-bottom: 2px solid #5B8238;">
                                <tr>
                                    <th class="px-4 py-3 fw-semibold">Tipo</th>
                                    <th class="px-3 py-3 fw-semibold">Número</th>
                                    <th class="px-3 py-3 fw-semibold">Conductor</th>
                                    <th class="px-3 py-3 fw-semibold">F. Emisión</th>
                                    <th class="px-3 py-3 fw-semibold">Vencimiento</th>
                                    <th class="px-3 py-3 fw-semibold">Placa</th>
                                    <th class="px-3 py-3 fw-semibold text-center">Estado</th>
                                    <th class="px-3 py-3 fw-semibold text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentos as $doc)
                                <tr class="documento-row {{ $doc->activo ? '' : 'table-secondary' }}">
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            @php
                                            $iconos = [
                                            'SOAT' => ['icon' => 'bi-shield-check', 'color' => 'success'],
                                            'Licencia Conduccion' => ['icon' => 'bi-person-vcard', 'color' => 'info'],
                                            'Tarjeta Propiedad' => ['icon' => 'bi-credit-card', 'color' => 'warning'],
                                            'Tecnomecanica' => ['icon' => 'bi-tools', 'color' => 'danger']
                                            ];
                                            $config = $iconos[$doc->tipo_documento] ?? ['icon' => 'bi-file-text', 'color' => 'secondary'];
                                            @endphp
                                            <i class="bi {{ $config['icon'] }} text-{{ $config['color'] }} me-2 fs-5"></i>
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
                                            <i class="bi bi-person me-1"></i>
                                            {{ $doc->conductor->nombre }} {{ $doc->conductor->apellido }}
                                        </a>
                                        @else
                                        <span class="text-muted fst-italic">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td class="px-3">
                                        @php
                                        $vencimiento = \Carbon\Carbon::parse($doc->fecha_vencimiento);
                                        $hoy = \Carbon\Carbon::now();
                                        $diasRestantes = (int) $hoy->diffInDays($vencimiento, false);
                                        @endphp
                                        <div class="d-flex flex-column">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-x me-1"></i>
                                                {{ $vencimiento->format('d/m/Y') }}
                                            </small>
                                            @if($doc->estado !== 'REEMPLAZADO')
                                            @if($diasRestantes < 0)
                                                <small class="text-danger fw-bold">Vencido hace {{ abs($diasRestantes) }} días</small>
                                                @elseif($diasRestantes <= 30)
                                                    <small class="text-warning fw-bold">Vence en {{ $diasRestantes }} días</small>
                                                    @endif
                                                    @endif
                                        </div>
                                    </td>
                                    <td class="px-3">
                                        @if($doc->vehiculo)
                                        <span class="badge bg-dark px-3 py-2">
                                            <i class="bi bi-car-front me-1"></i>
                                            {{ $doc->vehiculo->placa }}
                                        </span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-center">
                                        @php
                                        $estadoBadge = [
                                        'VIGENTE' => ['class' => 'success', 'icon' => 'bi-check-circle'],
                                        'POR_VENCER' => ['class' => 'warning', 'icon' => 'bi-exclamation-triangle'],
                                        'VENCIDO' => ['class' => 'danger', 'icon' => 'bi-x-circle'],
                                        'REEMPLAZADO' => ['class' => 'secondary', 'icon' => 'bi-arrow-repeat']
                                        ];
                                        $badge = $estadoBadge[$doc->estado] ?? ['class' => 'secondary', 'icon' => 'bi-info-circle'];
                                        @endphp
                                        <span class="badge bg-{{ $badge['class'] }} d-inline-flex align-items-center gap-1 px-3 py-2">
                                            <i class="bi {{ $badge['icon'] }}"></i>
                                            {{ $doc->estado }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        @php
                                        $conductorId = null;
                                        if (isset($doc->conductor) && !empty($doc->conductor->id_conductor)) {
                                        $conductorId = $doc->conductor->id_conductor;
                                        } elseif (isset($doc->vehiculo) && !empty($doc->vehiculo->id_conductor)) {
                                        $conductorId = $doc->vehiculo->id_conductor;
                                        }
                                        @endphp

                                        @if($conductorId)
                                        <a href="{{ route('conductores.edit', $conductorId) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-clock-history me-1"></i>
                                            <span class="d-none d-lg-inline">Historial</span>
                                        </a>
                                        @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled
                                            title="No hay conductor asignado">
                                            <i class="bi bi-slash-circle me-1"></i>
                                            <span class="d-none d-lg-inline">Sin historial</span>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 mb-3 d-block" style="opacity:0.3;"></i>
                                            <h5>No se encontraron resultados</h5>
                                            <p class="mb-0">Intenta ajustar los filtros de búsqueda</p>
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Mostrando {{ $documentos->firstItem() }} - {{ $documentos->lastItem() }}
                            de {{ $documentos->total() }} resultados
                        </div>
                        <div>
                            {{ $documentos->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>

        {{-- ==================== PANEL VEHÍCULOS ==================== --}}
        <div class="tab-pane fade" id="vehiculos-panel" role="tabpanel">

            {{-- Filtros de Vehículos --}}
            <form method="GET" action="{{ route('vehiculos.index') }}" id="form-vehiculos">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white fw-semibold">
                                <i class="bi bi-funnel-fill me-2"></i>Filtros de Vehículos
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-search me-2"></i>Búsqueda
                                </label>
                                <input type="text" name="search_vehiculo" class="form-control form-control-lg"
                                    placeholder="Placa, marca, modelo...">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-truck me-2"></i>Tipo
                                </label>
                                <select name="tipo" class="form-select form-select-lg">
                                    <option value="">Todos</option>
                                    <option value="Carro">Carro</option>
                                    <option value="Moto">Moto</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-2"></i>Estado
                                </label>
                                <select name="estado_vehiculo" class="form-select form-select-lg">
                                    <option value="">Todos</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold opacity-0">Acciones</label>
                                <div class="d-flex gap-2">
                                    <button type="button" onclick="buscarVehiculos()" class="btn btn-lg w-50"
                                        style="background-color:#5B8238; color:white;">
                                        <i class="bi bi-search me-1"></i>Buscar
                                    </button>
                                    <button type="button" onclick="exportarVehiculos('excel')" class="btn btn-success btn-lg w-25">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </button>
                                    <button type="button" onclick="exportarVehiculos('pdf')" class="btn btn-danger btn-lg w-25">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Tabla de Vehículos --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-table me-2"></i>Listado de Vehículos
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color:#f8f9fa; border-bottom: 2px solid #5B8238;">
                                <tr>
                                    <th class="px-4 py-3">Placa</th>
                                    <th class="px-3 py-3">Tipo</th>
                                    <th class="px-3 py-3">Marca/Modelo</th>
                                    <th class="px-3 py-3">Color</th>
                                    <th class="px-3 py-3">Propietario</th>
                                    <th class="px-3 py-3">Conductor</th>
                                    <th class="px-3 py-3 text-center">Estado</th>
                                    <th class="px-3 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $vehiculos = \App\Models\Vehiculo::with(['propietario', 'conductor'])->paginate(10);
                                @endphp
                                @forelse($vehiculos as $vehiculo)
                                <tr class="documento-row">
                                    <td class="px-4">
                                        <strong style="color:#5B8238;">{{ $vehiculo->placa }}</strong>
                                    </td>
                                    <td class="px-3">
                                        <span class="badge {{ $vehiculo->tipo == 'Carro' ? 'bg-primary' : 'bg-info' }}">
                                            <i class="bi bi-{{ $vehiculo->tipo == 'Carro' ? 'car-front-fill' : 'scooter' }} me-1"></i>
                                            {{ $vehiculo->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-3">
                                        <div class="fw-medium">{{ $vehiculo->marca }}</div>
                                        <small class="text-muted">{{ $vehiculo->modelo }}</small>
                                    </td>
                                    <td class="px-3">{{ $vehiculo->color }}</td>
                                    <td class="px-3">
                                        @if($vehiculo->propietario)
                                        <div>{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}</div>
                                        <small class="text-muted">{{ $vehiculo->propietario->documento }}</small>
                                        @else
                                        <span class="text-muted">Sin propietario</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        @if($vehiculo->conductor)
                                        {{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}
                                        @else
                                        <span class="text-muted">Sin conductor</span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-{{ $vehiculo->estado == 'Activo' ? 'success' : 'secondary' }} px-3 py-2">
                                            {{ $vehiculo->estado }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        <a href="{{ route('vehiculos.edit', $vehiculo->id_vehiculo) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye-fill me-1"></i>Ver
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 mb-3 d-block" style="opacity:0.3;"></i>
                                        <h5>No hay vehículos registrados</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($vehiculos->hasPages())
                <div class="card-footer bg-white">
                    {{ $vehiculos->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

        {{-- ==================== PANEL CONDUCTORES ==================== --}}
        <div class="tab-pane fade" id="conductores-panel" role="tabpanel">

            {{-- Filtros de Conductores --}}
            <form method="GET" action="#" id="form-conductores">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white fw-semibold">
                                <i class="bi bi-funnel-fill me-2"></i>Filtros de Conductores
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-search me-2"></i>Búsqueda
                                </label>
                                <input type="text" name="search_conductor" class="form-control form-control-lg"
                                    placeholder="Nombre, apellido, documento...">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-2"></i>Estado
                                </label>
                                <select name="activo" class="form-select form-select-lg">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-12">
                                <label class="form-label fw-semibold opacity-0">Acciones</label>
                                <div class="d-flex gap-2">
                                    <button type="button" onclick="buscarConductores()" class="btn btn-lg flex-grow-1"
                                        style="background-color:#5B8238; color:white;">
                                        <i class="bi bi-search me-1"></i>Buscar
                                    </button>
                                    <button type="button" onclick="exportarConductores('excel')" class="btn btn-success btn-lg">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </button>
                                    <button type="button" onclick="exportarConductores('pdf')" class="btn btn-danger btn-lg">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Tabla de Conductores --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-table me-2"></i>Listado de Conductores
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color:#f8f9fa; border-bottom: 2px solid #5B8238;">
                                <tr>
                                    <th class="px-4 py-3">Nombre</th>
                                    <th class="px-3 py-3">Documento</th>
                                    <th class="px-3 py-3">Teléfono</th>
                                    <th class="px-3 py-3">Vehículo Asignado</th>
                                    <th class="px-3 py-3">Licencia</th>
                                    <th class="px-3 py-3 text-center">Estado</th>
                                    <th class="px-3 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $conductores = \App\Models\Conductor::with(['vehiculos', 'documentosConductor'])->paginate(10);
                                @endphp
                                @forelse($conductores as $conductor)
                                <tr class="documento-row">
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle-fill fs-4 me-2 text-primary"></i>
                                            <div>
                                                <div class="fw-medium">{{ $conductor->nombre }} {{ $conductor->apellido }}</div>
                                                <small class="text-muted">{{ $conductor->tipo_doc }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3">
                                        <code class="bg-light px-2 py-1 rounded">{{ $conductor->identificacion }}</code>
                                    </td>
                                    <td class="px-3">
                                        <i class="bi bi-telephone-fill me-1"></i>{{ $conductor->telefono ?? 'N/A' }}
                                    </td>
                                    <td class="px-3">
                                        @if($conductor->vehiculos && $conductor->vehiculos->count() > 0)
                                        <span class="badge bg-dark px-3 py-2">
                                            <i class="bi bi-car-front-fill me-1"></i>
                                            {{ $conductor->vehiculos->first()->placa }}
                                        </span>
                                        @if($conductor->vehiculos->count() > 1)
                                        <small class="text-muted d-block mt-1">+{{ $conductor->vehiculos->count() - 1 }} más</small>
                                        @endif
                                        @else
                                        <span class="text-muted">Sin vehículo</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        @php
                                        $licencia = $conductor->documentosConductor()
                                        ->where('tipo_documento', 'Licencia Conducción')
                                        ->where('activo', 1)
                                        ->first();
                                        @endphp
                                        @if($licencia)
                                        @php
                                        $vencimiento = \Carbon\Carbon::parse($licencia->fecha_vencimiento);
                                        $dias = (int) now()->diffInDays($vencimiento, false);
                                        @endphp
                                        <div>
                                            <small class="text-muted">{{ $vencimiento->format('d/m/Y') }}</small>
                                            @if($dias < 0)
                                                <br><small class="text-danger">Vencida</small>
                                                @elseif($dias <= 30)
                                                    <br><small class="text-warning">Por vencer</small>
                                                    @endif
                                        </div>
                                        @else
                                        <span class="text-muted">Sin licencia</span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-{{ $conductor->activo ? 'success' : 'secondary' }} px-3 py-2">
                                            {{ $conductor->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        <a href="{{ route('conductores.edit', $conductor->id_conductor) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye-fill me-1"></i>Ver
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 mb-3 d-block" style="opacity:0.3;"></i>
                                        <h5>No hay conductores registrados</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($conductores->hasPages())
                <div class="card-footer bg-white">
                    {{ $conductores->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

        {{-- ==================== PANEL PROPIETARIOS ==================== --}}
        <div class="tab-pane fade" id="propietarios-panel" role="tabpanel">

            {{-- Filtros de Propietarios --}}
            <form method="GET" action="#" id="form-propietarios">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white fw-semibold">
                                <i class="bi bi-funnel-fill me-2"></i>Filtros de Propietarios
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-search me-2"></i>Búsqueda
                                </label>
                                <input type="text" name="search_propietario" class="form-control form-control-lg"
                                    placeholder="Nombre, apellido, documento...">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-card-text me-2"></i>Tipo Documento
                                </label>
                                <select name="tipo_doc" class="form-select form-select-lg">
                                    <option value="">Todos</option>
                                    <option value="CC">CC</option>
                                    <option value="NIT">NIT</option>
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-12">
                                <label class="form-label fw-semibold opacity-0">Acciones</label>
                                <div class="d-flex gap-2">
                                    <button type="button" onclick="buscarPropietarios()" class="btn btn-lg flex-grow-1"
                                        style="background-color:#5B8238; color:white;">
                                        <i class="bi bi-search me-1"></i>Buscar
                                    </button>
                                    <button type="button" onclick="exportarPropietarios('excel')" class="btn btn-success btn-lg">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </button>
                                    <button type="button" onclick="exportarPropietarios('pdf')" class="btn btn-danger btn-lg">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Tabla de Propietarios --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-table me-2"></i>Listado de Propietarios
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color:#f8f9fa; border-bottom: 2px solid #5B8238;">
                                <tr>
                                    <th class="px-4 py-3">Nombre</th>
                                    <th class="px-3 py-3">Documento</th>
                                    <th class="px-3 py-3">Teléfono</th>
                                    <th class="px-3 py-3">Email</th>
                                    <th class="px-3 py-3">Vehículos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $propietarios = \App\Models\Propietario::withCount('vehiculos')->paginate(10);
                                @endphp
                                @forelse($propietarios as $propietario)
                                <tr class="documento-row">
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-badge-fill fs-4 me-2 text-success"></i>
                                            <div>
                                                <div class="fw-medium">{{ $propietario->nombre }} {{ $propietario->apellido }}</div>
                                                <small class="text-muted">{{ $propietario->tipo_doc }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3">
                                        <code class="bg-light px-2 py-1 rounded">{{ $propietario->identificacion }}</code>
                                    </td>
                                    <td class="px-3">
                                        <i class="bi bi-telephone-fill me-1"></i>{{ $propietario->telefono ?? 'N/A' }}
                                    </td>
                                    <td class="px-3">
                                        @if($propietario->email)
                                        <i class="bi bi-envelope-fill me-1"></i>{{ $propietario->email }}
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        <span class="badge bg-primary px-3 py-2">
                                            <i class="bi bi-car-front-fill me-1"></i>
                                            {{ $propietario->vehiculos_count }} vehículo(s)
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 mb-3 d-block" style="opacity:0.3;"></i>
                                        <h5>No hay propietarios registrados</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($propietarios->hasPages())
                <div class="card-footer bg-white">
                    {{ $propietarios->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

    </div>

</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Función para exportar reportes
    function exportarReporte(formato) {
        const form = document.getElementById('form-documentos');
        const url = formato === 'excel' ?
            '{{ route("documentos.consultar.export.excel") }}' :
            '{{ route("documentos.consultar.export.pdf") }}';

        // Crear formulario temporal para exportación
        const exportForm = document.createElement('form');
        exportForm.method = 'GET';
        exportForm.action = url;

        // Copiar todos los inputs del formulario original
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            exportForm.appendChild(input);
        }

        document.body.appendChild(exportForm);
        exportForm.submit();
        document.body.removeChild(exportForm);
    }

    // Auto-uppercase para placa
    document.querySelectorAll('input[name="placa"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    // Animación de hover en filas
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.documento-row');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.002)';
                this.style.boxShadow = '0 2px 8px rgba(91, 130, 56, 0.15)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            });
        });
    });

    // ========== FUNCIONES PARA VEHÍCULOS ==========
    function buscarVehiculos() {
        const form = document.getElementById('form-vehiculos');
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        window.location.href = '{{ route("vehiculos.index") }}?' + params.toString();
    }

    function exportarVehiculos(formato) {
        // TODO: Implementar rutas backend para exportación
        alert('Funcionalidad de exportación a ' + formato.toUpperCase() + ' próximamente disponible.');
    }

    // ========== FUNCIONES PARA CONDUCTORES ==========
    function buscarConductores() {
        // Obtener datos del formulario
        const searchValue = document.querySelector('input[name="search_conductor"]').value;
        const activoValue = document.querySelector('select[name="activo"]').value;

        // Recargar la página actual (los datos se filtran en el servidor)
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('tab', 'conductores');

        if (searchValue) currentUrl.searchParams.set('search_conductor', searchValue);
        if (activoValue) currentUrl.searchParams.set('activo', activoValue);

        window.location.href = currentUrl.toString();
    }

    function exportarConductores(formato) {
        // TODO: Implementar rutas backend para exportación
        alert('Funcionalidad de exportación a ' + formato.toUpperCase() + ' próximamente disponible.');
    }

    // ========== FUNCIONES PARA PROPIETARIOS ==========
    function buscarPropietarios() {
        // Obtener datos del formulario
        const searchValue = document.querySelector('input[name="search_propietario"]').value;
        const tipoDocValue = document.querySelector('select[name="tipo_doc"]').value;

        // Recargar la página actual
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('tab', 'propietarios');

        if (searchValue) currentUrl.searchParams.set('search_propietario', searchValue);
        if (tipoDocValue) currentUrl.searchParams.set('tipo_doc', tipoDocValue);

        window.location.href = currentUrl.toString();
    }

    function exportarPropietarios(formato) {
        // TODO: Implementar rutas backend para exportación
        alert('Funcionalidad de exportación a ' + formato.toUpperCase() + ' próximamente disponible.');
    }

    function verVehiculosPropietario(id) {
        window.location.href = '{{ route("vehiculos.index") }}?propietario=' + id;
    }

    // Activar pestaña según parámetro URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');

        if (tab === 'vehiculos') {
            document.getElementById('vehiculos-tab').click();
        } else if (tab === 'conductores') {
            document.getElementById('conductores-tab').click();
        } else if (tab === 'propietarios') {
            document.getElementById('propietarios-tab').click();
        }
    });
</script>

<style>
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(91, 130, 56, 0.1);
        color: #5B8238;
    }

    .nav-pills .nav-link.active {
        background-color: #5B8238;
        color: white;
    }

    .documento-row {
        transition: all 0.2s ease-in-out;
    }

    .form-check-input:checked {
        background-color: #5B8238;
        border-color: #5B8238;
    }

    .btn-icon {
        background-color: transparent;
        /* fondo transparente */
        border: none;
        /* sin borde */
        color: white;
        /* ícono en blanco */
        padding: 0.25rem 0.5rem;
        /* opcional: ajusta el espacio */
    }




    .btn:hover {
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
    }
</style>

@endsection
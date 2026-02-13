@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp


@extends('layouts.app')

@section('title', 'Portería - Control Vehicular')

@section('content')
<div class="container-fluid py-4">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="titulo-seccion mb-0">
            <i class="fas fa-door-open me-2 text-success"></i>Panel de Portería
        </h3>
        <span class="badge bg-success fs-6">
            <i class="fas fa-user me-1"></i>{{ auth()->user()->nombre ?? 'Usuario' }}
        </span>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Buscador Múltiple - Ancho Completo --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header" style="background-color: #5B8238; color: white;">
            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Consultar Vehículo</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('porteria.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">Buscar por:</label>
                        <select name="tipo_busqueda" class="form-select" id="tipoBusqueda">
                            <option value="todo" {{ ($tipoBusqueda ?? 'todo') === 'todo' ? 'selected' : '' }}>Todo (Global)</option>
                            <option value="placa" {{ ($tipoBusqueda ?? '') === 'placa' ? 'selected' : '' }}>Placa</option>
                            <option value="conductor" {{ ($tipoBusqueda ?? '') === 'conductor' ? 'selected' : '' }}>Conductor</option>
                            <option value="propietario" {{ ($tipoBusqueda ?? '') === 'propietario' ? 'selected' : '' }}>Propietario</option>
                            <option value="documento" {{ ($tipoBusqueda ?? '') === 'documento' ? 'selected' : '' }}>Documento Identidad</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-semibold small mb-1" id="labelBusqueda">Término de búsqueda:</label>
                        <input type="text"
                            name="busqueda"
                            class="form-control form-control-lg text-uppercase"
                            placeholder="Placa, nombre, cédula..."
                            value="{{ $busqueda ?? '' }}"
                            maxlength="50"
                            required
                            autofocus>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-universal btn-lg w-100">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1 text-warning"></i>
                        <strong>Tip:</strong> Seleccione "Todo" para buscar en placa, conductor, propietario o documento de identidad al mismo tiempo.
                    </small>
                    @if($vehiculo || (isset($vehiculos) && $vehiculos->count() > 0))
                    <a href="{{ route('porteria.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Limpiar búsqueda
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Mensaje de error si no se encuentra --}}
    @if($mensaje)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ $mensaje }}
    </div>
    @endif

    {{-- Si hay múltiples vehículos encontrados, mostrar lista para seleccionar --}}
    @if(isset($vehiculos) && $vehiculos->count() > 1)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header" style="background-color: #17a2b8; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Se encontraron {{ $vehiculos->count() }} resultados
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Placa</th>
                            <th>Vehículo</th>
                            <th>Conductor</th>
                            <th>Propietario</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehiculos as $veh)
                        <tr>
                            <td>
                                <span class="badge bg-dark fs-6">{{ $veh->placa }}</span>
                            </td>
                            <td>
                                {{ $veh->marca }} {{ $veh->modelo }}
                                <small class="text-muted d-block">{{ $veh->color ?? '' }}</small>
                            </td>
                            <td>
                                @if($veh->conductor)
                                <i class="fas fa-user text-success me-1"></i>
                                {{ $veh->conductor->nombre }} {{ $veh->conductor->apellido }}
                                <small class="text-muted d-block">{{ $veh->conductor->identificacion }}</small>
                                @else
                                <span class="text-muted">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($veh->propietario)
                                <i class="fas fa-user-tie text-primary me-1"></i>
                                {{ $veh->propietario->nombre }} {{ $veh->propietario->apellido }}
                                <small class="text-muted d-block">{{ $veh->propietario->identificacion }}</small>
                                @else
                                <span class="text-muted">Sin propietario</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('porteria.index', ['busqueda' => $veh->placa, 'tipo_busqueda' => 'placa']) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-eye me-1"></i>Ver detalles
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

    {{-- Si hay vehículo único encontrado, mostrar resultados a ancho completo --}}
    @if($vehiculo)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #5B8238; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-car me-2"></i>Información del Vehículo
            </h5>
            <span class="badge bg-light text-dark fs-6">{{ $vehiculo->placa }}</span>
        </div>
        <div class="card-body">

            {{-- Información básica del vehículo --}}
            <div class="row mb-4">
                <div class="col-md-6 col-lg-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-1"></i>Datos del Vehículo</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 120px;">Placa:</td>
                            <td><span class="badge bg-dark fs-6">{{ $vehiculo->placa }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Marca:</td>
                            <td>{{ $vehiculo->marca ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Modelo:</td>
                            <td>{{ $vehiculo->modelo ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Color:</td>
                            <td>{{ $vehiculo->color ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tipo:</td>
                            <td>
                                <span class="badge bg-info">{{ $vehiculo->tipo ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Clasificación:</td>
                            <td>
                                <span class="badge bg-{{ $vehiculo->clasificacion_badge }}">{{ $vehiculo->clasificacion_label }}</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6 col-lg-4">
                    {{-- Conductor --}}
                    <h6 class="text-muted mb-3"><i class="fas fa-user me-1"></i>Conductor Asignado</h6>
                    @if($vehiculo->conductor)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 120px;">Nombre:</td>
                            <td>{{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Identificación:</td>
                            <td>{{ $vehiculo->conductor->identificacion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Teléfono:</td>
                            <td>{{ $vehiculo->conductor->telefono ?? 'N/A' }}</td>
                        </tr>
                    </table>
                    @else
                    <div class="alert alert-secondary py-2 mb-0">
                        <i class="fas fa-user-slash me-1"></i>Sin conductor asignado
                    </div>
                    @endif
                </div>

                <div class="col-md-12 col-lg-4">
                    {{-- Propietario --}}
                    <h6 class="text-muted mb-3"><i class="fas fa-user-tie me-1"></i>Propietario</h6>
                    @if($vehiculo->propietario)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 120px;">Nombre:</td>
                            <td>{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Identificación:</td>
                            <td>{{ $vehiculo->propietario->identificacion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Teléfono:</td>
                            <td>{{ $vehiculo->propietario->telefono ?? 'N/A' }}</td>
                        </tr>
                    </table>
                    @else
                    <div class="alert alert-secondary py-2 mb-0">
                        <i class="fas fa-user-slash me-1"></i>Sin propietario registrado
                    </div>
                    @endif
                </div>
            </div>

            {{-- Estado de Documentos --}}
            <h6 class="text-muted mb-3"><i class="fas fa-file-alt me-1"></i>Estado de Documentos</h6>

            <div class="row">
                {{-- SOAT --}}
                <div class="col-6 col-md-3 mb-3">
                    <div class="card h-100 border-{{ $estadosDocumentos['vehiculo_SOAT']['clase'] ?? 'secondary' }}">
                        <div class="card-body text-center py-3">
                            <i class="fas fa-shield-alt fa-2x mb-2 text-{{ $estadosDocumentos['vehiculo_SOAT']['clase'] ?? 'secondary' }}"></i>
                            <h6 class="card-title mb-1">SOAT</h6>
                            <span class="badge bg-{{ $estadosDocumentos['vehiculo_SOAT']['clase'] ?? 'secondary' }}">
                                {{ $estadosDocumentos['vehiculo_SOAT']['mensaje'] ?? 'Sin registro' }}
                            </span>
                            @if(isset($estadosDocumentos['vehiculo_SOAT']['fecha']))
                            <p class="small text-muted mb-0 mt-1">
                                Vence: {{ $estadosDocumentos['vehiculo_SOAT']['fecha'] }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tecnomecánica --}}
                <div class="col-6 col-md-3 mb-3">
                    @php
                    $tecnoEstado = $estadosDocumentos['vehiculo_TECNOMECANICA'] ?? null;
                    $requiereTecnoPort = $vehiculo->requiereTecnomecanica();
                    $fechaPrimeraRevPort = $vehiculo->fechaPrimeraTecnomecanica();
                    $esVehiculoNuevoExento = $vehiculo->fecha_matricula && !$requiereTecnoPort && (!$tecnoEstado || ($tecnoEstado['mensaje'] ?? '') === 'Sin registro');
                    @endphp
                    <div class="card h-100 border-{{ $esVehiculoNuevoExento ? 'success' : ($tecnoEstado['clase'] ?? 'secondary') }}">
                        <div class="card-body text-center py-3">
                            <i class="fas fa-{{ $esVehiculoNuevoExento ? 'shield-alt' : 'tools' }} fa-2x mb-2 text-{{ $esVehiculoNuevoExento ? 'success' : ($tecnoEstado['clase'] ?? 'secondary') }}"></i>
                            <h6 class="card-title mb-1">Tecnomecánica</h6>
                            @if($esVehiculoNuevoExento)
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Vehículo "Nuevo"
                            </span>
                            <p class="small text-success mb-0 mt-1">
                                (Exención por tiempo)
                            </p>
                            <p class="small text-muted mb-0">
                                Hasta: {{ $fechaPrimeraRevPort?->format('d/m/Y') }}
                            </p>
                            @else
                            <span class="badge bg-{{ $tecnoEstado['clase'] ?? 'secondary' }}">
                                {{ $tecnoEstado['mensaje'] ?? 'Sin registro' }}
                            </span>
                            @if(isset($tecnoEstado['fecha']))
                            <p class="small text-muted mb-0 mt-1">
                                Vence: {{ $tecnoEstado['fecha'] }}
                            </p>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tarjeta de Propiedad (No tiene vencimiento) --}}
                @php
                $tarjetaPropiedad = $estadosDocumentos['vehiculo_TARJETA PROPIEDAD'] ?? null;
                $tieneTarjeta = $tarjetaPropiedad && ($tarjetaPropiedad['estado'] ?? 'SIN_REGISTRO') !== 'SIN_REGISTRO';
                @endphp
                <div class="col-6 col-md-3 mb-3">
                    <div class="card h-100 border-{{ $tieneTarjeta ? 'success' : 'secondary' }}">
                        <div class="card-body text-center py-3">
                            <i class="fas fa-credit-card fa-2x mb-2 text-{{ $tieneTarjeta ? 'success' : 'secondary' }}"></i>
                            <h6 class="card-title mb-1">Tarjeta Propiedad</h6>
                            <span class="badge bg-{{ $tieneTarjeta ? 'success' : 'secondary' }}">
                                {{ $tieneTarjeta ? 'Registrada' : 'Sin registro' }}
                            </span>
                            @if($tieneTarjeta)
                            <p class="small text-success mb-0 mt-1">
                                <i class="fas fa-infinity me-1"></i>Sin vencimiento
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Licencia del Conductor --}}
                <div class="col-6 col-md-3 mb-3">
                    <div class="card h-100 border-{{ $estadosDocumentos['conductor_LICENCIA CONDUCCION']['clase'] ?? 'secondary' }}">
                        <div class="card-body text-center py-3">
                            <i class="fas fa-id-card fa-2x mb-2 text-{{ $estadosDocumentos['conductor_LICENCIA CONDUCCION']['clase'] ?? 'secondary' }}"></i>
                            <h6 class="card-title mb-1">Licencia</h6>
                            <span class="badge bg-{{ $estadosDocumentos['conductor_LICENCIA CONDUCCION']['clase'] ?? 'secondary' }}">
                                {{ $estadosDocumentos['conductor_LICENCIA CONDUCCION']['mensaje'] ?? 'Sin registro' }}
                            </span>
                            @if(isset($estadosDocumentos['conductor_LICENCIA CONDUCCION']['fecha']))
                            <p class="small text-muted mb-0 mt-1">
                                Vence: {{ $estadosDocumentos['conductor_LICENCIA CONDUCCION']['fecha'] }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen de Estado General --}}
            @php
            $tieneVencidos = false;
            $tienePorVencer = false;
            foreach($estadosDocumentos as $estado) {
            if($estado['estado'] === 'VENCIDO') $tieneVencidos = true;
            if($estado['estado'] === 'POR_VENCER') $tienePorVencer = true;
            }
            @endphp

            <div class="mt-3 p-3 rounded @if($tieneVencidos) bg-danger bg-opacity-10 border border-danger @elseif($tienePorVencer) bg-warning bg-opacity-10 border border-warning @else bg-success bg-opacity-10 border border-success @endif">
                <div class="d-flex align-items-center">
                    @if($tieneVencidos)
                    <i class="fas fa-times-circle fa-2x text-danger me-3"></i>
                    <div>
                        <h6 class="mb-0 text-danger">ATENCIÓN: Documentos Vencidos</h6>
                        <small class="text-muted">Este vehículo tiene documentos vencidos. Se requiere actualización.</small>
                    </div>
                    @elseif($tienePorVencer)
                    <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                    <div>
                        <h6 class="mb-0 text-warning">PRECAUCIÓN: Documentos por Vencer</h6>
                        <small class="text-muted">Algunos documentos vencerán pronto. Notificar al conductor.</small>
                    </div>
                    @else
                    <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                    <div>
                        <h6 class="mb-0 text-success">TODO EN ORDEN</h6>
                        <small class="text-muted">Todos los documentos están vigentes.</small>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Observaciones --}}
            @if($vehiculo->observaciones || ($vehiculo->conductor && $vehiculo->conductor->observaciones))
            <div class="mt-4">
                <h6 class="text-muted mb-3"><i class="fas fa-sticky-note me-1"></i>Observaciones</h6>
                @if($vehiculo->observaciones)
                <div class="alert alert-light border mb-2">
                    <small class="fw-bold text-muted d-block mb-1"><i class="fas fa-car me-1"></i>Vehículo:</small>
                    {{ $vehiculo->observaciones }}
                </div>
                @endif
                @if($vehiculo->conductor && $vehiculo->conductor->observaciones)
                <div class="alert alert-light border mb-0">
                    <small class="fw-bold text-muted d-block mb-1"><i class="fas fa-user me-1"></i>Conductor:</small>
                    {{ $vehiculo->conductor->observaciones }}
                </div>
                @endif
            </div>
            @endif

            {{-- Detalle de Documentos del Vehículo --}}
            @php
            $docsActivos = $vehiculo->documentos->where('activo', 1);
            @endphp
            @if($docsActivos->isNotEmpty())
            <div class="mt-4">
                <h6 class="text-muted mb-3"><i class="fas fa-folder-open me-1"></i>Documentos del Vehículo</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Número</th>
                                <th>Entidad</th>
                                <th>Emisión</th>
                                <th>Vencimiento</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($docsActivos->sortBy('tipo_documento') as $doc)
                            @php
                            $claseBadge = match($doc->estado) {
                                'VIGENTE' => 'success',
                                'POR_VENCER' => 'warning',
                                'VENCIDO' => 'danger',
                                default => 'secondary'
                            };
                            @endphp
                            <tr>
                                <td class="fw-medium">{{ str_replace('_', ' ', $doc->tipo_documento) }}</td>
                                <td>{{ $doc->numero_documento ?? '-' }}</td>
                                <td>{{ $doc->entidad_emisora ?? '-' }}</td>
                                <td>{{ $doc->fecha_emision ? \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($doc->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                                    @else
                                    <span class="text-muted">Sin vencimiento</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $claseBadge }}">{{ $doc->estado }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Documentos del Conductor --}}
            @if($vehiculo->conductor)
            @php
            $docsConductor = $vehiculo->conductor->documentosConductor->where('activo', 1);
            @endphp
            @if($docsConductor->isNotEmpty())
            <div class="mt-4">
                <h6 class="text-muted mb-3"><i class="fas fa-id-card me-1"></i>Documentos del Conductor</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Número</th>
                                <th>Categoría</th>
                                <th>Emisión</th>
                                <th>Vencimiento</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($docsConductor->sortBy('tipo_documento') as $docC)
                            @php
                            $claseBadgeC = match($docC->estado) {
                                'VIGENTE' => 'success',
                                'POR_VENCER' => 'warning',
                                'VENCIDO' => 'danger',
                                default => 'secondary'
                            };
                            @endphp
                            <tr>
                                <td class="fw-medium">{{ str_replace('_', ' ', $docC->tipo_documento) }}</td>
                                <td>{{ $docC->numero_documento ?? '-' }}</td>
                                <td>{{ $docC->categoria_licencia ?? '-' }}</td>
                                <td>{{ $docC->fecha_emision ? \Carbon\Carbon::parse($docC->fecha_emision)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($docC->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($docC->fecha_vencimiento)->format('d/m/Y') }}
                                    @else
                                    <span class="text-muted">Sin vencimiento</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $claseBadgeC }}">{{ $docC->estado }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @endif

        </div>
    </div>
    @endif

    {{-- Alertas Pendientes --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #dc3545; color: white;">
            <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Alertas Pendientes</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark">{{ $alertas->total() }} alertas</span>
                @if($alertas->isNotEmpty())
                <form method="POST" action="{{ route('alertas.mark_all_read') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm">
                        <i class="fas fa-check-double me-1"></i>Marcar todas
                    </button>
                </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($alertas->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p class="text-muted mb-0">No hay alertas pendientes</p>
            </div>
            @else
            <div class="row g-3">
                @foreach($alertas as $alerta)
                @php
                $placaAlerta = null;
                $conductorAlerta = null;
                $conductorId = null;
                $tipoDocumento = null;
                $vehiculoInfo = null;
                $clasificacionAlerta = null;

                if ($alerta->documentoVehiculo) {
                $tipoDocumento = $alerta->documentoVehiculo->tipo_documento;
                if ($alerta->documentoVehiculo->vehiculo) {
                $placaAlerta = $alerta->documentoVehiculo->vehiculo->placa;
                $vehiculoInfo = $alerta->documentoVehiculo->vehiculo->marca . ' ' . $alerta->documentoVehiculo->vehiculo->modelo;
                $clasificacionAlerta = $alerta->documentoVehiculo->vehiculo->clasificacion;
                if ($alerta->documentoVehiculo->vehiculo->conductor) {
                $conductorAlerta = $alerta->documentoVehiculo->vehiculo->conductor->nombre . ' ' . $alerta->documentoVehiculo->vehiculo->conductor->apellido;
                $conductorId = $alerta->documentoVehiculo->vehiculo->conductor->id_conductor;
                }
                }
                }

                if ($alerta->documentoConductor) {
                $tipoDocumento = $alerta->documentoConductor->tipo_documento;
                if ($alerta->documentoConductor->conductor) {
                $conductorAlerta = $alerta->documentoConductor->conductor->nombre . ' ' . $alerta->documentoConductor->conductor->apellido;
                $conductorId = $alerta->documentoConductor->conductor->id_conductor;
                $clasificacionAlerta = $alerta->documentoConductor->conductor->clasificacion;
                }
                }

                $esVencido = $alerta->tipo_vencimiento === 'VENCIDO';
                $esAlertaVehiculo = $alerta->id_doc_vehiculo !== null;
                $colorBorde = $esVencido ? 'danger' : 'warning';
                $colorFondo = $esVencido ? 'rgba(220, 53, 69, 0.08)' : 'rgba(255, 193, 7, 0.08)';
                @endphp

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card alerta-card h-100 border-{{ $colorBorde }} border-start border-4" style="background: {{ $colorFondo }};">
                        {{-- Header de la tarjeta --}}
                        <div class="card-header bg-transparent border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                {{-- Tipo de alerta (Vehículo/Conductor) --}}
                                <div>
                                    @if($esAlertaVehiculo)
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <i class="fas fa-car me-1"></i>Vehículo
                                    </span>
                                    @else
                                    <span class="badge bg-info rounded-pill px-3 py-2">
                                        <i class="fas fa-user me-1"></i>Conductor
                                    </span>
                                    @endif
                                </div>
                                {{-- Estado (Vencido/Por vencer) --}}
                                <span class="badge bg-{{ $colorBorde }} {{ !$esVencido ? 'text-dark' : '' }} rounded-pill px-3 py-2">
                                    <i class="fas fa-{{ $esVencido ? 'times-circle' : 'exclamation-triangle' }} me-1"></i>
                                    {{ $esVencido ? 'VENCIDO' : 'POR VENCER' }}
                                </span>
                            </div>
                        </div>

                        {{-- Cuerpo de la tarjeta --}}
                        <div class="card-body pt-3">
                            {{-- Documento afectado --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-{{ $colorBorde }} bg-opacity-25 me-3">
                                        <i class="fas fa-file-alt text-{{ $colorBorde }}"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Documento</small>
                                        <span class="fw-bold">{{ $tipoDocumento ?? 'Sin especificar' }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Información del vehículo (si aplica) --}}
                            @if($placaAlerta)
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-dark bg-opacity-10 me-3">
                                        <i class="fas fa-car text-dark"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Vehículo</small>
                                        <span class="badge bg-dark fs-6 me-2">{{ $placaAlerta }}</span>
                                        @if($vehiculoInfo)
                                        <small class="text-muted">{{ $vehiculoInfo }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Conductor --}}
                            @if($conductorAlerta)
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-success bg-opacity-25 me-3">
                                        <i class="fas fa-user text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Conductor</small>
                                        <span class="fw-medium">{{ $conductorAlerta }}</span>
                                    </div>
                                    @if(isset($conductorId))
                                    <a href="{{ route('reportes.ficha.conductor', $conductorId) }}"
                                        class="btn btn-sm btn-outline-secondary rounded-circle" title="Ver ficha">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Clasificación --}}
                            @if($clasificacionAlerta)
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-secondary bg-opacity-10 me-3">
                                        <i class="fas fa-tags text-secondary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Clasificación</small>
                                        <span class="badge bg-{{ \App\Models\Vehiculo::CLASIFICACION_BADGES[$clasificacionAlerta] ?? 'secondary' }}">{{ ucfirst(strtolower($clasificacionAlerta)) }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Fecha de alerta --}}
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <span>Alerta generada: {{ optional($alerta->fecha_alerta)->format('d/m/Y') ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Footer con acciones --}}
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('alertas.read', $alerta->id_alerta) }}" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-check me-1"></i>Marcar leída
                                    </button>
                                </form>
                                @if($placaAlerta)
                                <a href="{{ route('porteria.index', ['busqueda' => $placaAlerta, 'tipo_busqueda' => 'placa']) }}"
                                    class="btn btn-outline-primary btn-sm" title="Ver vehículo">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            @if($alertas->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $alertas->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
            @endif
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <footer class="text-center mt-3 mb-3 text-muted small">
        &copy; 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoBusqueda = document.getElementById('tipoBusqueda');
        const inputBusqueda = document.querySelector('input[name="busqueda"]');
        const labelBusqueda = document.getElementById('labelBusqueda');

        const placeholders = {
            'todo': 'Placa, nombre, cédula...',
            'placa': 'Ej: ABC123',
            'conductor': 'Nombre o apellido del conductor',
            'propietario': 'Nombre o apellido del propietario',
            'documento': 'Número de cédula o documento'
        };

        const labels = {
            'todo': 'Término de búsqueda:',
            'placa': 'Placa del vehículo:',
            'conductor': 'Nombre del conductor:',
            'propietario': 'Nombre del propietario:',
            'documento': 'Número de documento:'
        };

        tipoBusqueda.addEventListener('change', function() {
            inputBusqueda.placeholder = placeholders[this.value] || placeholders['todo'];
            labelBusqueda.textContent = labels[this.value] || labels['todo'];
        });
    });
</script>
@endsection
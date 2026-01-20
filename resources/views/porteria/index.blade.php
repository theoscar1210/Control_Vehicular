@extends('layouts.app')

@section('title', 'Portería - Control Vehicular')

@section('content')
<div class="container mt-4">

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

    <div class="row">
        {{-- Columna Izquierda: Buscador de Placas --}}
        <div class="col-lg-7 mb-4">

            {{-- Buscador de Placas --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Consultar Vehículo por Placa</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('porteria.index') }}">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-car text-success"></i>
                            </span>
                            <input type="text"
                                   name="placa"
                                   class="form-control text-uppercase"
                                   placeholder="Ingrese la placa (Ej: ABC123)"
                                   value="{{ $placaBuscada ?? '' }}"
                                   maxlength="10"
                                   required
                                   autofocus>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
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

            {{-- Resultado de la búsqueda --}}
            @if($vehiculo)
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #5B8238; color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-car me-2"></i>Información del Vehículo
                    </h5>
                    <span class="badge bg-light text-dark fs-6">{{ $vehiculo->placa }}</span>
                </div>
                <div class="card-body">

                    {{-- Información básica del vehículo --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
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
                            </table>
                        </div>

                        <div class="col-md-6">
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
                    </div>

                    {{-- Propietario --}}
                    @if($vehiculo->propietario)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-user-tie me-1"></i>Propietario</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Nombre:</strong><br>
                                    {{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Identificación:</strong><br>
                                    {{ $vehiculo->propietario->identificacion ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Teléfono:</strong><br>
                                    {{ $vehiculo->propietario->telefono ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Estado de Documentos --}}
                    <h6 class="text-muted mb-3"><i class="fas fa-file-alt me-1"></i>Estado de Documentos</h6>

                    <div class="row">
                        {{-- SOAT --}}
                        <div class="col-md-6 col-lg-3 mb-3">
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
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 border-{{ $estadosDocumentos['vehiculo_Tecnomecanica']['clase'] ?? 'secondary' }}">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-tools fa-2x mb-2 text-{{ $estadosDocumentos['vehiculo_Tecnomecanica']['clase'] ?? 'secondary' }}"></i>
                                    <h6 class="card-title mb-1">Tecnomecánica</h6>
                                    <span class="badge bg-{{ $estadosDocumentos['vehiculo_Tecnomecanica']['clase'] ?? 'secondary' }}">
                                        {{ $estadosDocumentos['vehiculo_Tecnomecanica']['mensaje'] ?? 'Sin registro' }}
                                    </span>
                                    @if(isset($estadosDocumentos['vehiculo_Tecnomecanica']['fecha']))
                                    <p class="small text-muted mb-0 mt-1">
                                        Vence: {{ $estadosDocumentos['vehiculo_Tecnomecanica']['fecha'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tarjeta de Propiedad --}}
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 border-{{ $estadosDocumentos['vehiculo_Tarjeta Propiedad']['clase'] ?? 'secondary' }}">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-credit-card fa-2x mb-2 text-{{ $estadosDocumentos['vehiculo_Tarjeta Propiedad']['clase'] ?? 'secondary' }}"></i>
                                    <h6 class="card-title mb-1">Tarjeta Propiedad</h6>
                                    <span class="badge bg-{{ $estadosDocumentos['vehiculo_Tarjeta Propiedad']['clase'] ?? 'secondary' }}">
                                        {{ $estadosDocumentos['vehiculo_Tarjeta Propiedad']['mensaje'] ?? 'Sin registro' }}
                                    </span>
                                    @if(isset($estadosDocumentos['vehiculo_Tarjeta Propiedad']['fecha']))
                                    <p class="small text-muted mb-0 mt-1">
                                        Vence: {{ $estadosDocumentos['vehiculo_Tarjeta Propiedad']['fecha'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Licencia del Conductor --}}
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 border-{{ $estadosDocumentos['conductor_Licencia Conducción']['clase'] ?? 'secondary' }}">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-id-card fa-2x mb-2 text-{{ $estadosDocumentos['conductor_Licencia Conducción']['clase'] ?? 'secondary' }}"></i>
                                    <h6 class="card-title mb-1">Licencia</h6>
                                    <span class="badge bg-{{ $estadosDocumentos['conductor_Licencia Conducción']['clase'] ?? 'secondary' }}">
                                        {{ $estadosDocumentos['conductor_Licencia Conducción']['mensaje'] ?? 'Sin registro' }}
                                    </span>
                                    @if(isset($estadosDocumentos['conductor_Licencia Conducción']['fecha']))
                                    <p class="small text-muted mb-0 mt-1">
                                        Vence: {{ $estadosDocumentos['conductor_Licencia Conducción']['fecha'] }}
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

                </div>
            </div>
            @endif

        </div>

        {{-- Columna Derecha: Alertas --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #dc3545; color: white;">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Alertas Pendientes</h5>
                    <span class="badge bg-light text-dark">{{ $alertas->total() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($alertas->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted mb-0">No hay alertas pendientes</p>
                    </div>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($alertas as $alerta)
                        @php
                            $placaAlerta = null;
                            $conductorAlerta = null;
                            if ($alerta->documentoVehiculo && $alerta->documentoVehiculo->vehiculo) {
                                $placaAlerta = $alerta->documentoVehiculo->vehiculo->placa;
                                if ($alerta->documentoVehiculo->vehiculo->conductor) {
                                    $conductorAlerta = $alerta->documentoVehiculo->vehiculo->conductor->nombre . ' ' . $alerta->documentoVehiculo->vehiculo->conductor->apellido;
                                }
                            }
                            if ($alerta->documentoConductor && $alerta->documentoConductor->conductor) {
                                $conductorAlerta = $alerta->documentoConductor->conductor->nombre . ' ' . $alerta->documentoConductor->conductor->apellido;
                            }

                            $claseAlerta = $alerta->tipo_vencimiento === 'VENCIDO' ? 'danger' : 'warning';
                        @endphp
                        <div class="list-group-item border-start border-4 border-{{ $claseAlerta }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-{{ $claseAlerta }} me-2">
                                            {{ $alerta->tipo_vencimiento === 'VENCIDO' ? 'Vencido' : 'Por vencer' }}
                                        </span>
                                        <small class="text-muted">
                                            {{ optional($alerta->fecha_alerta)->format('d/m/Y') }}
                                        </small>
                                    </div>

                                    @if($placaAlerta || $conductorAlerta)
                                    <div class="mb-1">
                                        @if($placaAlerta)
                                        <span class="badge bg-dark me-1">
                                            <i class="fas fa-car me-1"></i>{{ $placaAlerta }}
                                        </span>
                                        @endif
                                        @if($conductorAlerta)
                                        <small class="text-primary">
                                            <i class="fas fa-user me-1"></i>{{ $conductorAlerta }}
                                        </small>
                                        @endif
                                    </div>
                                    @endif

                                    <p class="mb-1 small">{{ $alerta->mensaje }}</p>

                                    <form method="POST" action="{{ route('alertas.read', $alerta->id_alerta) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2">
                                            <i class="fas fa-check me-1"></i>Marcar leída
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Paginación de alertas --}}
                    <div class="card-footer bg-white">
                        {{ $alertas->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Acciones Rápidas --}}
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header" style="background-color: #5B8238; color: white;">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('alertas.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Ver Todas las Alertas
                        </a>
                        <form method="POST" action="{{ route('alertas.mark_all_read') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="fas fa-check-double me-2"></i>Marcar Todas como Leídas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        &copy; 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>
</div>
@endsection

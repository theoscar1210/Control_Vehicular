@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
$user = $user ?? auth()->user();
$rol = $user->rol ?? 'N/A';

@endphp


<div class="contentor mb-4">
    <h4 class="fw-bold">Bienvenido a la página principal</h4>
    <p class="text-muted mb-0">
        Resumen del estado del cumplimiento documental y actividad del sistema <br>
        <small>Última actualización: {{ $ultima_actualizacion ?? now()->translatedFormat('d F Y, H:i:s') }}</small>
    </p>
    <div class="mt-2">
        <span class="badge bg-secondary">Rol: {{ $rol }}</span>
    </div>
</div>

<!-- Tarjetas de resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Total Vehículos</h6>
                <h3 class="fw-bold text-success">{{ number_format($totalVehiculos) }}</h3>

            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Conductores Activos</h6>
                <h3 class="fw-bold text-success">{{ number_format($conductoresActivos) }}</h3>

            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Documentos por vencer</h6>
                <h3 class="fw-bold text-warning">{{ $porVencerCount }}</h3>
                <small class="text-warning">Próximos 20 días</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Vencidos</h6>
                <h3 class="fw-bold text-danger">{{ $vencidosCount }}</h3>
                <small class="text-danger">Comparativa mensual</small>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<div class="container mb-4">
    {{-- Mensajes de éxito/error --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="bi bi-bell-fill me-2" style="color:#5B8238;"></i>Alertas
        </h3>
        <form method="POST" action="{{ route('alertas.mark_all_read') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-check-all me-1"></i>Marcar todas como leídas
            </button>
        </form>
    </div>

    <div class="list-group mt-3">
        @forelse($alertas as $a)
        @php
        $iconos = [
        'SOAT' => ['icon' => 'bi-shield-check-fill', 'color' => 'success'],
        'Licencia Conducción' => ['icon' => 'bi-person-vcard-fill', 'color' => 'info'],
        'Tecnomecanica' => ['icon' => 'bi-tools', 'color' => 'danger'],
        'Tarjeta Propiedad' => ['icon' => 'bi-credit-card-fill', 'color' => 'warning']
        ];
        $config = $iconos[$a->tipo_vencimiento] ?? ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'warning'];

        // Obtener placa, conductor e IDs para enlaces
        $placaDash = null;
        $conductorDash = null;
        $vehiculoIdDash = null;
        $conductorIdDash = null;
        $esAlertaVehiculoDash = $a->id_doc_vehiculo !== null;

        if ($a->documentoVehiculo && $a->documentoVehiculo->vehiculo) {
            $placaDash = $a->documentoVehiculo->vehiculo->placa;
            $vehiculoIdDash = $a->documentoVehiculo->vehiculo->id_vehiculo;
            if ($a->documentoVehiculo->vehiculo->conductor) {
                $conductorDash = $a->documentoVehiculo->vehiculo->conductor->nombre . ' ' . $a->documentoVehiculo->vehiculo->conductor->apellido;
            }
        }
        if ($a->documentoConductor && $a->documentoConductor->conductor) {
            $conductorDash = $a->documentoConductor->conductor->nombre . ' ' . $a->documentoConductor->conductor->apellido;
            $conductorIdDash = $a->documentoConductor->conductor->id_conductor;
        }
        @endphp
        <div class="list-group-item {{ $a->leida ? 'bg-light text-muted' : 'border-start border-warning border-3' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi {{ $config['icon'] }} text-{{ $config['color'] }} me-2 fs-5"></i>
                        <strong class="{{ $a->leida ? '' : 'fw-bold' }}">{{ $a->tipo_vencimiento }}</strong>
                    </div>

                    {{-- Información de Placa y Conductor --}}
                    @if($placaDash || $conductorDash)
                    <div class="mb-2">
                        @if($placaDash)
                        <span class="badge bg-dark me-2">
                            <i class="bi bi-car-front-fill me-1"></i>{{ $placaDash }}
                        </span>
                        @endif
                        @if($conductorDash)
                        <span class="text-primary">
                            <i class="bi bi-person-fill me-1"></i>{{ $conductorDash }}
                        </span>
                        @endif
                    </div>
                    @endif

                    <p class="mb-1 {{ $a->leida ? 'text-muted' : '' }}">{{ $a->mensaje }}</p>
                    <small class="text-muted">
                        <i class="bi bi-calendar-event me-1"></i>
                        {{ optional($a->fecha_alerta)->format('d/m/Y H:i') ?? $a->fecha_alerta }}
                    </small>
                </div>
                <div class="d-flex gap-2 align-items-start ms-3">
                    {{-- Botón Ver: redirige al historial según tipo de alerta --}}
                    @if($esAlertaVehiculoDash && $vehiculoIdDash)
                    <a href="{{ route('vehiculos.documentos.historial.completo', $vehiculoIdDash) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye-fill me-1"></i>Ver
                    </a>
                    @elseif(!$esAlertaVehiculoDash && $conductorIdDash)
                    <a href="{{ route('conductores.documentos.historial', $conductorIdDash) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye-fill me-1"></i>Ver
                    </a>
                    @else
                    <a href="{{ route('alertas.show', $a->id_alerta) }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-eye-fill me-1"></i>Ver
                    </a>
                    @endif
                    @if(!$a->leida)
                    <form method="POST" action="{{ route('alertas.read', $a->id_alerta) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-check-circle me-1"></i>Marcar leída
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="list-group-item text-center text-muted py-4">
            <i class="bi bi-inbox fs-1 d-block mb-2" style="opacity:0.3;"></i>
            <p class="mb-0">No hay alertas pendientes</p>
        </div>
        @endforelse
    </div>

    @if(method_exists($alertas, 'links'))
    <div class="mt-3">{{ $alertas->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

{{-- Footer --}}
<footer class="text-center mt-5 mb-3 text-muted small">
    © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
</footer>

@endsection
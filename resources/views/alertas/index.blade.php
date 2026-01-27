@extends('layouts.app')

@section('title', 'Alertas - Control Vehicular')

@section('content')
<div class="container-fluid py-4">
    {{-- Mensajes de éxito/error --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #5B8238;">
            <h5 class="mb-0 text-white">
                <i class="fas fa-bell me-2"></i>Centro de Alertas
            </h5>
            @php
                // Contar alertas no leidas por el usuario actual
                $alertasNoLeidas = $alertas->filter(function($a) use ($userId) {
                    return !$a->leidaPorUsuario($userId);
                })->count();
            @endphp
            @if($alertasNoLeidas > 0)
            <form method="POST" action="{{ route('alertas.mark_all_read') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light btn-sm">
                    <i class="fas fa-check-double me-1"></i>Marcar todas como leídas
                </button>
            </form>
            @endif
        </div>

        <div class="card-body p-0">
            @if($alertas->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No hay alertas para mostrar.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($alertas as $alerta)
                        @php
                            // Iconos por tipo de documento
                            $iconos = [
                                'SOAT' => ['icon' => 'fas fa-shield-alt', 'color' => 'success'],
                                'Licencia Conducción' => ['icon' => 'fas fa-id-card', 'color' => 'info'],
                                'Tecnomecanica' => ['icon' => 'fas fa-tools', 'color' => 'primary'],
                                'Tarjeta Propiedad' => ['icon' => 'fas fa-credit-card', 'color' => 'secondary'],
                                'EPS' => ['icon' => 'fas fa-hospital', 'color' => 'info'],
                                'ARL' => ['icon' => 'fas fa-hard-hat', 'color' => 'warning'],
                                'Certificado Médico' => ['icon' => 'fas fa-notes-medical', 'color' => 'success'],
                            ];

                            // Obtener tipo de documento del documento relacionado
                            $tipoDocumento = null;
                            $placaAlerta = null;
                            $conductorAlerta = null;

                            if ($alerta->documentoVehiculo) {
                                $tipoDocumento = $alerta->documentoVehiculo->tipo_documento;
                                if ($alerta->documentoVehiculo->vehiculo) {
                                    $placaAlerta = $alerta->documentoVehiculo->vehiculo->placa;
                                    if ($alerta->documentoVehiculo->vehiculo->conductor) {
                                        $conductorAlerta = $alerta->documentoVehiculo->vehiculo->conductor->nombre . ' ' . $alerta->documentoVehiculo->vehiculo->conductor->apellido;
                                    }
                                }
                            }

                            if ($alerta->documentoConductor) {
                                $tipoDocumento = $alerta->documentoConductor->tipo_documento;
                                if ($alerta->documentoConductor->conductor) {
                                    $conductorAlerta = $alerta->documentoConductor->conductor->nombre . ' ' . $alerta->documentoConductor->conductor->apellido;
                                }
                            }

                            $config = $iconos[$tipoDocumento] ?? ['icon' => 'fas fa-file-alt', 'color' => 'secondary'];
                            $esNoLeida = !$alerta->leidaPorUsuario($userId);
                        @endphp
                        <div class="list-group-item {{ $esNoLeida ? 'bg-light border-start border-4 border-warning' : '' }}">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: var(--bs-{{ $config['color'] }}-bg-subtle, #f8f9fa);">
                                        <i class="{{ $config['icon'] }} text-{{ $config['color'] }} fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 {{ $esNoLeida ? 'fw-bold' : 'text-muted' }}">
                                            {{ $tipoDocumento ?? 'Documento' }}
                                            @if($esNoLeida)
                                                <span class="badge bg-warning text-dark ms-2">Nueva</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ optional($alerta->fecha_alerta)->format('d/m/Y') ?? 'Sin fecha' }}
                                        </small>
                                    </div>

                                    {{-- Información de Placa y Conductor --}}
                                    @if($placaAlerta || $conductorAlerta)
                                    <div class="mb-2">
                                        @if($placaAlerta)
                                        <span class="badge bg-dark me-2">
                                            <i class="fas fa-car me-1"></i>{{ $placaAlerta }}
                                        </span>
                                        @endif
                                        @if($conductorAlerta)
                                        <span class="text-primary">
                                            <i class="fas fa-user me-1"></i>{{ $conductorAlerta }}
                                        </span>
                                        @endif
                                    </div>
                                    @endif

                                    <p class="mb-2 {{ $esNoLeida ? '' : 'text-muted' }}">{{ $alerta->mensaje }}</p>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @if($alerta->id_doc_vehiculo)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-file-alt me-1"></i>Doc. Vehículo
                                            </span>
                                        @endif
                                        @if($alerta->id_doc_conductor)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-file-alt me-1"></i>Doc. Conductor
                                            </span>
                                        @endif
                                        @php
                                            $esVencido = $alerta->tipo_vencimiento === 'VENCIDO';
                                        @endphp
                                        <span class="badge bg-{{ $esVencido ? 'danger' : 'warning' }}">
                                            {{ $esVencido ? 'Vencido' : 'Próximo a vencer' }}
                                        </span>

                                        @if($esNoLeida)
                                            <form method="POST" action="{{ route('alertas.read', $alerta->id_alerta) }}" class="ms-auto">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-check me-1"></i>Marcar leída
                                                </button>
                                            </form>
                                        @else
                                            <span class="ms-auto text-success">
                                                <i class="fas fa-check-circle me-1"></i>Leída
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="d-flex justify-content-center py-3">
                    {{ $alertas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

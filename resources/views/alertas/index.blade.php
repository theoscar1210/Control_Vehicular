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
                $alertasNoLeidas = $alertas->where('leida', 0)->count();
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
                            $iconos = [
                                'SOAT' => ['icon' => 'fas fa-shield-alt', 'color' => 'success'],
                                'Licencia Conducción' => ['icon' => 'fas fa-id-card', 'color' => 'info'],
                                'Tecnomecánica' => ['icon' => 'fas fa-tools', 'color' => 'danger'],
                                'Tarjeta Propiedad' => ['icon' => 'fas fa-credit-card', 'color' => 'warning']
                            ];
                            $config = $iconos[$alerta->tipo_vencimiento] ?? ['icon' => 'fas fa-exclamation-triangle', 'color' => 'warning'];
                            $esNoLeida = !$alerta->leida;
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
                                            {{ $alerta->tipo_vencimiento }}
                                            @if($esNoLeida)
                                                <span class="badge bg-warning text-dark ms-2">Nueva</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ optional($alerta->fecha_alerta)->format('d/m/Y') ?? 'Sin fecha' }}
                                        </small>
                                    </div>
                                    <p class="mb-2 {{ $esNoLeida ? '' : 'text-muted' }}">{{ $alerta->mensaje }}</p>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($alerta->id_doc_vehiculo)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-car me-1"></i>Vehículo
                                            </span>
                                        @endif
                                        @if($alerta->id_doc_conductor)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-user me-1"></i>Conductor
                                            </span>
                                        @endif
                                        <span class="badge bg-{{ $alerta->tipo_alerta === 'VENCIDO' ? 'danger' : 'warning' }}">
                                            {{ $alerta->tipo_alerta === 'VENCIDO' ? 'Vencido' : 'Próximo a vencer' }}
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

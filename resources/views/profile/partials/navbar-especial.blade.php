{{-- Navbar principal --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="navbar-logo me-2">
            <div class="d-flex flex-column lh-sm">
                <span class="text-titulo">Control Vehicular</span>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEspecial" aria-controls="navbarEspecial" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarEspecial">
            <ul class="navbar-nav me-auto">


                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">Inicio</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('porteria.index') }}">Portería</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Gestión
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('vehiculos.index') }}"><i class="fas fa-list me-2"></i>Listado de Vehículos</a></li>
                        <li><a class="dropdown-item" href="{{ route('vehiculos.create') }}"><i class="fas fa-plus-circle me-2"></i>Nuevo Vehículo</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <h6 class="dropdown-header">Gestión Conductores</h6>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('conductores.index') }}"><i class="fas fa-list me-2"></i>Listado de Conductores</a></li>
                        <li><a class="dropdown-item" href="{{ route('conductores.create') }}"><i class="fas fa-plus-circle me-2"></i>Nuevo Conductor</a></li>
                    </ul>
                </li>




                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reportes.centro') }}">Centro de Reportes</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('alertas.index') }}">
                        Alertas
                    </a>
                </li>
            </ul>
        </div>

        @php
        $user = auth()->user();
        $alertasMenuEspecial = \App\Models\Alerta::with([
        'documentoVehiculo.vehiculo.conductor',
        'documentoConductor.conductor',
        'usuariosQueLeyeron'
        ])
        ->whereNull('deleted_at')
        ->activas() // Solo alertas no solucionadas
        ->conDocumentoVigente() // Solo alertas de documentos no reemplazados
        ->where(function($q) use ($user) {
        $q->where('visible_para','TODOS')->orWhere('visible_para', $user->rol);
        })
        ->noLeidasPor($user->id_usuario)
        ->orderByDesc('fecha_alerta')
        ->take(5)
        ->get();

        $totalAlertasNoLeidasEspecial = \App\Models\Alerta::whereNull('deleted_at')
        ->activas() // Solo alertas no solucionadas
        ->conDocumentoVigente() // Solo alertas de documentos no reemplazados
        ->where(function($q) use ($user) {
        $q->where('visible_para','TODOS')->orWhere('visible_para', $user->rol);
        })
        ->noLeidasPor($user->id_usuario)
        ->count();
        @endphp
        <ul class="navbar-nav ms-auto align-items-center">
            {{-- Notificaciones con Dropdown --}}
            <li class="nav-item dropdown me-3">
                <a class="nav-link text-dark position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg"></i>
                    @if($totalAlertasNoLeidasEspecial > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $totalAlertasNoLeidasEspecial > 99 ? '99+' : $totalAlertasNoLeidasEspecial }}
                    </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2" style="background-color: #5B8238;">
                        <span class="text-white fw-bold"><i class="fas fa-bell me-2"></i>Alertas</span>
                        @if($totalAlertasNoLeidasEspecial > 0)
                        <span class="badge bg-light text-dark">{{ $totalAlertasNoLeidasEspecial }} nuevas</span>
                        @endif
                    </li>
                    @if($alertasMenuEspecial->isEmpty())
                    <li class="text-center py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0 small">No hay alertas pendientes</p>
                    </li>
                    @else
                    @foreach($alertasMenuEspecial as $alerta)
                    @php
                    $iconosMenuE = [
                    'SOAT' => ['icon' => 'fas fa-shield-alt', 'color' => 'success'],
                    'Licencia Conducción' => ['icon' => 'fas fa-id-card', 'color' => 'info'],
                    'Tecnomecanica' => ['icon' => 'fas fa-tools', 'color' => 'danger'],
                    'Tarjeta Propiedad' => ['icon' => 'fas fa-credit-card', 'color' => 'warning']
                    ];
                    $configMenuE = $iconosMenuE[$alerta->tipo_vencimiento] ?? ['icon' => 'fas fa-exclamation-triangle', 'color' => 'warning'];

                    // Obtener placa y conductor
                    $placaMenuE = null;
                    $conductorMenuE = null;
                    if ($alerta->documentoVehiculo && $alerta->documentoVehiculo->vehiculo) {
                    $placaMenuE = $alerta->documentoVehiculo->vehiculo->placa;
                    if ($alerta->documentoVehiculo->vehiculo->conductor) {
                    $conductorMenuE = $alerta->documentoVehiculo->vehiculo->conductor->nombre . ' ' . $alerta->documentoVehiculo->vehiculo->conductor->apellido;
                    }
                    }
                    if ($alerta->documentoConductor && $alerta->documentoConductor->conductor) {
                    $conductorMenuE = $alerta->documentoConductor->conductor->nombre . ' ' . $alerta->documentoConductor->conductor->apellido;
                    }
                    @endphp
                    <li>
                        <div class="dropdown-item py-2 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="me-2">
                                    <i class="{{ $configMenuE['icon'] }} text-{{ $configMenuE['color'] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong class="small">{{ $alerta->tipo_vencimiento }}</strong>
                                        <small class="text-muted">{{ optional($alerta->fecha_alerta)->format('d/m') }}</small>
                                    </div>
                                    @if($placaMenuE || $conductorMenuE)
                                    <div class="small mb-1">
                                        @if($placaMenuE)
                                        <span class="badge bg-dark me-1"><i class="fas fa-car me-1"></i>{{ $placaMenuE }}</span>
                                        @endif
                                        @if($conductorMenuE)
                                        <span class="text-primary"><i class="fas fa-user me-1"></i>{{ Str::limit($conductorMenuE, 20) }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    <p class="mb-1 small text-muted text-truncate" style="max-width: 250px;">{{ $alerta->mensaje }}</p>
                                    <form method="POST" action="{{ route('alertas.read', $alerta->id_alerta) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-success p-0 small">
                                            <i class="fas fa-check me-1"></i>Marcar leída
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                    @endif
                    <li>
                        <hr class="dropdown-divider my-0">
                    </li>
                    <li class="text-center py-2">
                        <a href="{{ route('alertas.index') }}" class="text-decoration-none small" style="color: #5B8238;">
                            <i class="fas fa-list me-1"></i>Ver todas las alertas
                        </a>
                    </li>
                    @if($totalAlertasNoLeidasEspecial > 0)
                    <li class="px-3 pb-2">
                        <form method="POST" action="{{ route('alertas.mark_all_read') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm w-100" style="background-color: #5B8238; color: white;">
                                <i class="fas fa-check-double me-1"></i>Marcar todas como leídas
                            </button>
                        </form>
                    </li>
                    @endif
                </ul>
            </li>

            {{-- Usuario --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="circulo-redondo">
                        {{ strtoupper(substr(auth()->user()->nombre ?? 'U', 0, 2)) }}
                    </div>
                    {{ auth()->user()->nombre ?? 'Usuario' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
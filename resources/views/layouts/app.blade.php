<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Control Vehicular')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('imagenes/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('imagenes/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('imagenes/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('imagenes/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('imagenes/site.webmanifest') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

</head>

<body>

    {{-- Navbar Especial --}}
    @if(isset($navbarEspecial) && $navbarEspecial === true)
    @include('profile.partials.navbar-especial')
    <div class="{{ empty($sinPadding) ? 'content p-4 mt-5' : '' }}">
        @yield('content')
    </div>

    {{-- Layout Normal con Sidebar --}}
    @elseif(empty($ocultarNavbar))

    {{-- Navbar Principal --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container-fluid px-3 px-md-4">
            {{-- Boton hamburguesa - Solo visible en movil/tablet --}}
            <button class="btn-toggle-menu d-lg-none me-2" onclick="toggleSidebar()" type="button" aria-label="Abrir menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="navbar-logo me-2">
                <div class="d-flex flex-column lh-sm">
                    <span class="text-titulo">Control Vehicular</span>
                    <small class="text-muted d-none d-sm-block">Club Campestre Altos del Chicala</small>
                </div>
            </a>

            {{-- $alertasMenu y $totalAlertasNoLeidas son inyectados por AlertaComposer --}}
            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Notificaciones con Dropdown --}}
                <li class="nav-item dropdown me-3">
                    <a class="nav-link text-dark position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificacionesDropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        @if($totalAlertasNoLeidas > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $totalAlertasNoLeidas > 99 ? '99+' : $totalAlertasNoLeidas }}
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 350px; max-height: 400px; overflow-y: auto;" aria-labelledby="notificacionesDropdown">
                        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2" style="background-color: #5B8238;">
                            <span class="text-white fw-bold"><i class="fas fa-bell me-2"></i>Alertas</span>
                            @if($totalAlertasNoLeidas > 0)
                            <span class="badge bg-light text-dark">{{ $totalAlertasNoLeidas }} nuevas</span>
                            @endif
                        </li>
                        @if($alertasMenu->isEmpty())
                        <li class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0 small">No hay alertas pendientes</p>
                        </li>
                        @else
                        @foreach($alertasMenu as $alerta)
                        @php
                        $iconosMenu = [
                        'SOAT' => ['icon' => 'fas fa-shield-alt', 'color' => 'success'],
                        'Licencia Conducción' => ['icon' => 'fas fa-id-card', 'color' => 'info'],
                        'Tecnomecanica' => ['icon' => 'fas fa-tools', 'color' => 'danger'],
                        'Tarjeta Propiedad' => ['icon' => 'fas fa-credit-card', 'color' => 'warning']
                        ];
                        $configMenu = $iconosMenu[$alerta->tipo_vencimiento] ?? ['icon' => 'fas fa-exclamation-triangle', 'color' => 'warning'];

                        // Obtener placa y conductor
                        $placaMenu = null;
                        $conductorMenu = null;
                        if ($alerta->documentoVehiculo && $alerta->documentoVehiculo->vehiculo) {
                        $placaMenu = $alerta->documentoVehiculo->vehiculo->placa;
                        if ($alerta->documentoVehiculo->vehiculo->conductor) {
                        $conductorMenu = $alerta->documentoVehiculo->vehiculo->conductor->nombre . ' ' . $alerta->documentoVehiculo->vehiculo->conductor->apellido;
                        }
                        }
                        if ($alerta->documentoConductor && $alerta->documentoConductor->conductor) {
                        $conductorMenu = $alerta->documentoConductor->conductor->nombre . ' ' . $alerta->documentoConductor->conductor->apellido;
                        }
                        @endphp
                        <li>
                            <div class="dropdown-item py-2 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="me-2">
                                        <i class="{{ $configMenu['icon'] }} text-{{ $configMenu['color'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <strong class="small">{{ $alerta->tipo_vencimiento }}</strong>
                                            <small class="text-muted">{{ optional($alerta->fecha_alerta)->format('d/m') }}</small>
                                        </div>
                                        @if($placaMenu || $conductorMenu)
                                        <div class="small mb-1">
                                            @if($placaMenu)
                                            <span class="badge bg-dark me-1"><i class="fas fa-car me-1"></i>{{ $placaMenu }}</span>
                                            @endif
                                            @if($conductorMenu)
                                            <span class="text-primary"><i class="fas fa-user me-1"></i>{{ Str::limit($conductorMenu, 20) }}</span>
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
                        @if($totalAlertasNoLeidas > 0)
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
                        <span class="d-none d-md-inline">{{ auth()->user()->nombre ?? 'Usuario' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Perfil</a></li>
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



    {{-- Overlay para cerrar sidebar en móvil --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    {{-- Sidebar --}}

    <div id="sidebar" class="sidebar">
        @if(in_array(auth()->user()->rol, ['ADMIN', 'SST']))
        {{-- Menú para ADMIN y SST --}}
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-home me-2"></i>Inicio
        </a>

        @if(auth()->user()->rol === 'ADMIN')
        <a class="nav-link" href="{{ route('porteria.index') }}">
            <i class="fas fa-door-open me-2"></i>Portería
        </a>
        @endif

        <!-- Gestión de Vehículos con submenú -->
        <a class="nav-link" data-bs-toggle="collapse" href="#vehiculosSubmenu" role="button" aria-expanded="false" aria-controls="vehiculosSubmenu">
            <i class="fas fa-car me-2"></i>Gestión de Vehículos
            <i class="fas fa-chevron-down float-end"></i>
        </a>
        <div class="collapse" id="vehiculosSubmenu">
            <ul class="list-unstyled ps-3">
                <li>
                    <a class="nav-link" href="{{ route('vehiculos.index', ['clasificacion' => 'EMPLEADO']) }}">
                        <i class="fas fa-building me-2"></i>Empleados
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('vehiculos.index', ['clasificacion' => 'CONTRATISTA']) }}">
                        <i class="fas fa-hard-hat me-2"></i>Contratistas
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('vehiculos.index', ['clasificacion' => 'FAMILIAR']) }}">
                        <i class="fas fa-people-roof me-2"></i>Familiares
                    </a>
                </li>
            </ul>
        </div>

        <a class="nav-link" data-bs-toggle="collapse" href="#conductoresSubmenu" role="button">
            <i class="fas fa-id-card-clip me-2"></i>Gestión de Conductores
            <i class="fas fa-chevron-down float-end"></i>
        </a>
        <div class="collapse" id="conductoresSubmenu">
            <ul class="list-unstyled ps-3">
                <li>
                    <a class="nav-link" href="{{ route('conductores.index', ['clasificacion' => 'EMPLEADO']) }}">
                        <i class="fas fa-building me-2"></i>Empleados
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('conductores.index', ['clasificacion' => 'CONTRATISTA']) }}">
                        <i class="fas fa-hard-hat me-2"></i>Contratistas
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('conductores.index', ['clasificacion' => 'FAMILIAR']) }}">
                        <i class="fas fa-people-roof me-2"></i>Familiares
                    </a>
                </li>
            </ul>
        </div>

        <a class="nav-link" href="{{ route('reportes.centro') }}">
            <i class="fas fa-chart-bar me-2"></i>Centro de Reportes
        </a>

        <a class="nav-link" href="{{ route('alertas.index') }}">
            <i class="fas fa-bell me-2"></i>Centro de Alertas
            @if(isset($totalAlertasNoLeidas) && $totalAlertasNoLeidas > 0)

            @endif
        </a>

        @if(auth()->user()->rol === 'ADMIN')
        <a class="nav-link" href="{{ route('usuarios.index') }}">
            <i class="fas fa-users me-2"></i>Gestión de Usuarios
        </a>
        @endif

        <span class="text-uppercase fw-bold d-block ">Acciones Rápidas</span>

        <a class="nav-link" href="{{ route('vehiculos.create', ['clasificacion' => 'EMPLEADO']) }}">
            <i class="fas fa-plus-circle me-2"></i>Nuevo Registro
        </a>

        @endif
    </div>


    {{-- Topbar - Solo visible en desktop --}}
    <nav class="topbar d-none d-lg-flex">
        {{-- Espacio para breadcrumbs u otros elementos si se necesitan --}}
    </nav>

    {{-- Contenido Principal --}}
    <div class="content con-sidebar">
        @yield('content')
    </div>

    {{-- Sin Navbar ni Sidebar --}}
    @else
    <div class="{{ empty($sinPadding) ? 'content p-4' : '' }}">
        @yield('content')
    </div>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            const isOpen = sidebar.classList.toggle('open');
            overlay.classList.toggle('active', isOpen);
            body.classList.toggle('sidebar-open', isOpen);
        }

        // Cerrar Sidebar
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            body.classList.remove('sidebar-open');
        }

        // Inicialización cuando el DOM está listo
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
            const toggleBtn = document.querySelector('.btn-toggle-menu');

            // Cerrar sidebar al hacer click en un enlace (solo en móvil/tablet)
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // No cerrar si es un collapse toggle
                    if (this.getAttribute('data-bs-toggle') === 'collapse') {
                        return;
                    }
                    if (window.innerWidth <= 991) {
                        closeSidebar();
                    }
                });
            });

            // Cerrar sidebar con tecla Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });

            // Cerrar sidebar al hacer resize si pasa a desktop
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (window.innerWidth > 991 && sidebar) {
                        closeSidebar();
                    }
                }, 100);
            });

            // Marcar enlace activo
            const currentPath = window.location.pathname;
            sidebarLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href === currentPath || (href && currentPath.startsWith(href) && href !== '/')) {
                    link.classList.add('active');
                    // Abrir el collapse parent si existe
                    const parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                    }
                }
            });

            // Alertas
            window.markAlertRead = function(id) {
                fetch('/alertas/' + id + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(json => {
                    if (json.ok) {
                        fetch('/alertas/unread-count')
                            .then(r => r.json())
                            .then(d => {
                                const badge = document.querySelector('.badge.bg-danger');
                                if (badge) badge.innerText = d.unread;
                            });
                        const alertRow = document.getElementById('alert-row-' + id);
                        if (alertRow) alertRow.classList.add('text-muted');
                    }
                });
            };
        });
    </script>

    @auth
    <script>
        // Auto-logout por inactividad (sincronizado con session.lifetime)
        (function() {
            const SESSION_LIFETIME_MS = {{ config('session.lifetime') }} * 60 * 1000;
            const WARNING_BEFORE_MS = 60 * 1000; // Avisar 1 minuto antes
            let inactivityTimer, warningTimer;

            function resetTimers() {
                clearTimeout(inactivityTimer);
                clearTimeout(warningTimer);

                warningTimer = setTimeout(function() {
                    alert('Tu sesion se cerrara en 1 minuto por inactividad.');
                }, SESSION_LIFETIME_MS - WARNING_BEFORE_MS);

                inactivityTimer = setTimeout(function() {
                    window.location.href = '{{ route("login") }}';
                }, SESSION_LIFETIME_MS);
            }

            ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function(evt) {
                document.addEventListener(evt, resetTimers, { passive: true });
            });

            resetTimers();
        })();
    </script>
    @endauth

</body>

</html>
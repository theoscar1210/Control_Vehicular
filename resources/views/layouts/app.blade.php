<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Control Vehicular')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <nav class=" navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="navbar-logo me-2">
                <div class="d-flex flex-column lh-sm">
                    <span class="text-titulo">Control Vehicular</span>
                    <small class="text-muted">Club Campestre Altos del Chicalá</small>
                </div>
            </a>

            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Notificaciones --}}
                <li class="nav-item me-3 position-relative">
                    <a class="nav-link text-dark" href="{{ route('alertas.index') }}">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ \App\Models\Alerta::where('leida',0)->where(function($q){ 
                                    $q->where('visible_para','TODOS')->orWhere('visible_para', auth()->user()->rol);
                                })->count() }}
                        </span>
                    </a>
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
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-home me-2"></i>Inicio
        </a>

        <a class="nav-link" href="{{ route('vehiculos.index') }}">
            <i class="fas fa-car me-2"></i>Registro de Vehículos
        </a>

        <a class="nav-link" href="{{ route('conductores.create') }}">
            <i class="fas fa-id-card me-2"></i>Registro de Conductores
        </a>

        <a class="nav-link" href="#">
            <i class="fas fa-file-alt me-2"></i>Verificación Documentos
        </a>

        <a class="nav-link" href="{{ route('usuarios.index') }}">
            <i class="fas fa-users me-2"></i>Gestión de Usuarios
        </a>

        <span class="text-muted">Acciones Rápidas</span>

        <a class="nav-link" href="#">
            <i class="fas fa-plus-circle me-2"></i>Nuevo Registro
        </a>

        <a class="nav-link" href="{{ route('documentos.consultar') }}">
            <i class="fas fa-chart-line me-2"></i>Generar Reporte
        </a>
    </div>

    {{-- Topbar --}}
    <nav class="topbar">
        <button class="btn-toggle-menu" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>

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

            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        // Cerrar Sidebar
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }

        // Cerrar sidebar al hacer click en un enlace (solo en móvil)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991) {
                        closeSidebar();
                    }
                });
            });

            // Marcar enlace activo
            const currentPath = window.location.pathname;
            sidebarLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
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

</body>

</html>
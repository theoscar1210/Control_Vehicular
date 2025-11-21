<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Título dinámico: si la vista define @section('title'), se usa; si no, se muestra "Control Vehicular" --}}
    <title>@yield('title', 'Control Vehicular')</title>

    <!-- Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Fuente moderna Roboto desde Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>

    {{-- ========================================================
        1) Navbar especial (solo si la vista lo activa con $navbarEspecial)
    ========================================================= --}}
    @if(isset($navbarEspecial) && $navbarEspecial === true)
    {{-- Incluye un navbar alternativo --}}
    @include('profile.partials.navbar-especial')

    {{-- Contenido principal con padding y margen superior --}}
    <div class="content p-4 mt-5">
        @yield('content')
    </div>

    {{-- ========================================================
        2) Layout completo normal (navbar principal + sidebar)
           Solo si NO está oculto y NO es navbar especial
    ========================================================= --}}
    @elseif(empty($ocultarNavbar))

    {{-- Navbar principal fijo arriba --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container-fluid px-4">
            {{-- Logo + título --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="navbar-logo me-2">
                <div class="d-flex flex-column lh-sm">
                    <span class="text-titulo">Control Vehicular</span>
                    <small class="text-muted">Club Campestre Altos del Chicalá</small>
                </div>
            </a>

            {{-- Buscador central (visible solo en pantallas medianas en adelante) --}}
            <form class="d-none d-md-flex mx-auto w-50">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none" placeholder="Buscar vehículo o conductor">
                </div>
            </form>

            {{-- Menú de notificaciones y usuario --}}
            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Notificaciones --}}
                <li class="nav-item me-3 position-relative">
                    <a class="nav-link text-dark" href="{{ route('alertas.index') }}">
                        <i class="fas fa-bell fa-lg"></i>
                        {{-- Badge rojo con número de notificaciones (ejemplo fijo en 3) --}}
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"> {{ \App\Models\Alerta::where('leida',0)->where(function($q){ $q->where('visible_para','TODOS')->orWhere('visible_para', auth()->user()->rol);})->count() }}</span>
                    </a>
                </li>

                {{-- Menú desplegable de usuario --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        {{-- Iniciales del usuario en círculo --}}
                        <div class="circulo-redondo">
                            {{ strtoupper(substr(auth()->user()->nombre ?? 'U', 0, 2)) }}
                        </div>
                        {{ auth()->user()->nombre ?? 'Usuario' }}
                    </a>

                    {{-- Opciones del menú --}}
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Perfil</a></li>
                        <li>
                            {{-- Botón de logout con formulario POST --}}
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

    {{-- Contenedor principal con sidebar y contenido --}}
    <div class="d-flex">
        {{-- Sidebar lateral izquierdo --}}
        <div class="sidebar bg-white shadow-sm border-end pt-3">
            <ul class="nav flex-column mt-4">
                {{-- Enlaces principales --}}
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('dashboard') }}">
                        <i class="fas fa-home me-2"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('vehiculos.index') }}">
                        <i class="fas fa-car me-2"></i>Registro de Vehículos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('conductores.create') }}">
                        <i class="fas fa-id-card me-2"></i>Registro de Conductores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-file-alt me-2"></i>Verificación Documentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('usuarios.index') }}">
                        <i class="fas fa-users me-2"></i>Gestión de Usuarios
                    </a>
                </li>

                <hr>

                {{-- Sección de acciones rápidas --}}
                <span class="text-muted px-3 small">Acciones Rápidas</span>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-plus-circle me-2"></i>Nuevo Registro</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-chart-line me-2"></i>Generar Reporte</a>
                </li>
            </ul>
        </div>

        {{-- Contenido principal (a la derecha del sidebar) --}}
        <div class="content flex-grow-1 p-4 mt-5 con-sidebar">
            @yield('content')
        </div>
    </div>

    {{-- ========================================================
        3) Vista sin navbar ni sidebar (ejemplo: login)
    ========================================================= --}}
    @else
    <div class="content p-4 mt-5">
        @yield('content')
    </div>
    @endif

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function markAlertRead(id) {
            fetch('/alertas/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(r => r.json()).then(json => {
                if (json.ok) {
                    // actualizar contador
                    fetch('/alertas/unread-count').then(r => r.json()).then(d => {
                        document.getElementById('alerts-badge').innerText = d.unread;
                    });
                    // ocultar elemento UI o cambiar estilo
                    document.getElementById('alert-row-' + id).classList.add('text-muted');
                }
            });
        }
    </script>

</body>

</html>
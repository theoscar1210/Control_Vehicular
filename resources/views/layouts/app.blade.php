<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Control Vehicular')</title>


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Fuente moderna (opcional) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>


{{-- Navbar superior --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">

        {{-- Logo + nombre --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="navbar-logo me-2">
            <div class="d-flex flex-column lh-sm">
                <span class="text-titulo">Control Vehicular</span>
                <small class="text-muted">Club Campestre Altos del Chicalá</small>
            </div>
        </a>

        {{-- Barra de búsqueda --}}
        <form class="d-none d-md-flex mx-auto w-50">
            <div class="input-group">
                <span class="input-group-text bg-light border-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Buscar vehículo o conductor">
            </div>
        </form>

        {{-- Iconos de notificaciones y usuario --}}
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item me-3 position-relative">
                <a href="#" class="nav-link text-dark">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    {{--inicales dentro de circulo verde--}}
                    <div class="circulo-redondo">
                        {{ strtoupper(substr(auth()->user()->nombre ?? 'U', 0, 2)) }}
                    </div>
                    {{ auth()->user()->nombre ?? 'Usuario' }}
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

{{-- Contenedor general --}}
<div class="d-flex">
    {{-- Sidebar --}}
    <div class="sidebar bg-white shadow-sm border-end pt-3">
        <ul class="nav flex-column mt-4">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('dashboard') }}"><i class="fas fa-home me-2"></i>Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-car me-2"></i>Registro de Vehículos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-id-card me-2"></i>Registro de Conductores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-file-alt me-2"></i>Verificación Documentos</a>
            </li>
            <!--funcionalidad exclusiva para admin-->
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-users me-2"></i>Gestión de Usuarios</a>
            </li>

            <hr>

            <span class="text-muted px-3 small">Acciones Rápidas</span>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-plus-circle me-2"></i>Nuevo Registro</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-chart-line me-2"></i>Generar Reporte</a>
            </li>
        </ul>
    </div>

    {{-- Contenido principal --}}
    <div class="content flex-grow-1 p-4 mt-5">
        @yield('content')
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
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


<body class="bg-light">
    {{-- Navbar superior --}}

    @if (!isset($ocultarNavbar) || !$ocultarNavbar)
    <nav class="navbar navbar-expand-lg navbar-dark custom-navbar shadow-sm fixed-top">
        <div class="container-fluid px-4">

            {{-- Logo + Marca --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="navbar-logo me-2">
                <span class="titulo">Control Vehicular<h6>Club Campestre Altos del Chicala</h6></span>

            </a>



            {{-- Barra de búsqueda (centro) --}}

            <form class="d-none d-md-flex mx-lg-4 my-lg-0" role="search">
                <input class="form-control form-control-sm me-2" type="search" placeholder="Buscar..."
                    aria-label="Buscar">
                <button class="btn btn-light btn-sm" type="submit"><i class="fas fa-search"></i></button>
            </form>



            {{-- Notificaciones + Usuario (derecha) --}}
            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Campana de notificaciones --}}
                <li class="nav-item me-3 position-relative">
                    <a href="#" class="nav-link text-black">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </a>
                </li>

                {{-- Usuario autenticado --}}
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ auth()->user()->nombre ?? auth()->user()->usuario ?? 'Usuario' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Perfil</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth
            </ul>
        </div>
        </div>
    </nav>
    @endif

    {{-- Sidebar + Contenido --}}
    <div class="sidebar bg-white shadow-sm" id="sidebar">
        <div class="sidebar-header text-center py-3">
            <i class="fas fa-bars d-lg-none toggle-btn" id="toggleSidebar"></i>
            <h6 class="text-uppercase text-muted mb-0">Menú</h6>
        </div>

        <ul class="list-unstyled ps-3">
            <li><a href="{{ route('dashboard') }}" class="sidebar-link"><i class="fas fa-home me-2"></i>Inicio</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-car me-2"></i>Registro de Vehiculos</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-users me-2"></i>Registro Conductores</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cogs me-2"></i>Verificacion Documentos</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cogs me-2"></i>Verificacion Documentos</a></li>

        </ul>
    </div>

    {{-- Contenido principal --}}
    <div id="page-content" class="flex-grow-1 p-4" style="margin-top: 70px;">
        @yield('content')
    </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle en móviles
        document.getElementById('toggleSidebar')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    @stack('scripts')
</body>

</html>
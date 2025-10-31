<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Control Vehicular')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Fuente moderna (opcional) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="bg-light">

    {{-- Navbar condicional --}}
    @if (!isset($ocultarNavbar) || !$ocultarNavbar)
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Control Vehicular</a>
            <div class="collapse navbar-collapse">
                @auth
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <!-- agrega más links según rol -->
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            Hola, {{ auth()->user()->nombre ?? auth()->user()->usuario ?? auth()->user()->email }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-light btn-sm" type="submit">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </nav>
    @endif

    {{-- Contenido principal --}}
    <div class="container my-4">
        @yield('content')
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
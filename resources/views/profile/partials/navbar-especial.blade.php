{{-- Navbar principal --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="navbar-logo me-2">
            <div class="d-flex flex-column lh-sm">
                <span class="text-titulo">Control Vehicular</span>

            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEspecial">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarEspecial">
            <ul class="navbar-nav ms-auto">


                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">Inicio</a>
                </li>

                <!-- Gestión de Vehículos con submenú -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="vehiculosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Gestión de Vehículos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="vehiculosDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('vehiculos.index') }}">
                                Listado de Vehículos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('vehiculos.create') }}">
                                Nuevo Vehículo
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('conductores.create') }}">
                                Registrar Conductor
                            </a>
                        </li>
                    </ul>
                </li>




                <li class="nav-item">
                    <a class="nav-link" href="{{ route('documentos.consultar') }}">Consultas y Reportes</a>
                </li>
            </ul>
        </div>

        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item me-3 position-relative">
                <a href="#" class="nav-link text-dark">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                </a>
            </li>

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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Denegado - Control Vehicular</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('imagenes/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('imagenes/favicon-32x32.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .error-code {
            font-size: 4rem;
            font-weight: 700;
            color: #5B8238;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        .btn-home {
            background-color: #5B8238;
            border-color: #5B8238;
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            background-color: #4a6b2d;
            border-color: #4a6b2d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(91, 130, 56, 0.4);
        }
        .btn-back {
            background-color: transparent;
            border: 2px solid #5B8238;
            color: #5B8238;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #5B8238;
            color: white;
        }
        .card-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            background: white;
        }
        .logo-img {
            max-height: 60px;
            margin-bottom: 1rem;
        }
        @media (max-width: 576px) {
            .error-icon { font-size: 3.5rem; }
            .error-code { font-size: 3rem; }
            .error-title { font-size: 1.2rem; }
            .error-container { padding: 1.5rem; }
            .logo-img { max-height: 45px; }
        }
    </style>
</head>
<body>
    <div class="container-fluid" style="max-width: 500px;">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-body error-container">
                        <!-- Logo -->
                        <img src="{{ asset('imagenes/Logo_solo.png') }}" alt="Logo" class="logo-img">

                        <!-- Icono de error -->
                        <div class="error-icon">
                            <i class="fas fa-lock"></i>
                        </div>

                        <!-- Código de error -->
                        <div class="error-code">403</div>

                        <!-- Título -->
                        <h1 class="error-title">Acceso Denegado</h1>

                        <!-- Mensaje -->
                        <p class="error-message">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Lo sentimos, no tienes permisos para acceder a esta sección del sistema.
                        </p>

                        <!-- Información adicional -->
                        <div class="alert alert-light border mb-4" role="alert">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Si crees que esto es un error, por favor contacta al administrador del sistema.
                            </small>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-home">
                                    <i class="fas fa-home me-2"></i>Ir al Inicio
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-home">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </a>
                            @endauth
                            <button onclick="history.back()" class="btn btn-back">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <p class="text-center text-muted mt-4 small">
                    <i class="fas fa-shield-alt me-1"></i>
                    Control Vehicular - Club Campestre Altos del Chicalá
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

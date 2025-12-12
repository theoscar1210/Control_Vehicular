@php($ocultarNavbar = true)
@php($sinPadding = true)
@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #ffffff 0%, #A3c585 100%);
        height: auto;
    }
</style>

<div class="login-container">
    <div class="login-card">
        {{-- Logo --}}
        <img src="{{ asset('imagenes/Logo_Chicala.png') }}" alt="Logo" class="brand-logo img-fluid mx-auto d-block">

        <div class="text-center mt-4">
            <h3 class="text-verde">Control Vehicular</h3>
            <h4 class="mb-2">Bienvenido</h4>
            <h5 class="mb-3">Ingrese sus credenciales para acceder al sistema</h5>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3 position-relative">
                <input id="usuario" type="text" name="usuario" value="{{ old('usuario') }}"
                    class="form-control form-control-lg ps-5" required autofocus
                    placeholder="Ingrese su Usuario" autocomplete="username">
                <span class="position-absolute top-50 start-0 translate-middle-y ps-3">
                    <i class="fas fa-user text-muted"></i>
                </span>
            </div>

            <div class="mb-3 position-relative">
                <input id="password" type="password" name="password"
                    class="form-control form-control-lg ps-5 pe-5" required
                    placeholder="********" autocomplete="current-password">
                <span class="position-absolute top-50 start-0 translate-middle-y ps-3">
                    <i class="fas fa-lock text-muted"></i>
                </span>
            </div>

            <div class="d-grid">
                <button class="btn btn-principal btn-lg">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</div>

<p class="footer-text text-center">
    © {{ date('Y') }} Sistema de Control Vehicular — Club Campestre Altos del Chicalá. Todos los derechos reservados.
</p>
@endsection
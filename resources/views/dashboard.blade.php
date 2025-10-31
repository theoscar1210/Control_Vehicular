@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
$user = auth()->user();
$rol = $user->rol ?? 'PORTERIA';
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Dashboard</h3>
    <div>
        <span class="badge bg-secondary">Rol: {{ $rol }}</span>
    </div>
</div>

<!-- Contenido común -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Resumen general</h5>
                <p class="card-text">Aquí puedes poner KPIs, alertas de documentos por vencer, o enlaces rápidos.</p>
            </div>
        </div>

        <!-- Sección: administración (solo ADMIN) -->
        @if(in_array($rol, ['ADMIN']))
        <div class="card mb-3 border-primary">
            <div class="card-body">
                <h5 class="card-title">Panel de Administración</h5>
                <p class="card-text">Enlaces para gestionar usuarios, configuración global, logs, etc.</p>
                <a href="#" class="btn btn-outline-primary btn-sm">Usuarios</a>
                <a href="#" class="btn btn-outline-primary btn-sm">Configuración</a>
            </div>
        </div>
        @endif

        <!-- Sección: SST (SST y ADMIN ven la misma info relevante para SST) -->
        @if(in_array($rol, ['SST', 'ADMIN']))
        <div class="card mb-3 border-success">
            <div class="card-body">
                <h5 class="card-title">Panel SST</h5>
                <p class="card-text">Controles y reportes SST, incidentes, protocolos, etc.</p>
                <a href="#" class="btn btn-outline-success btn-sm">Reportar incidente</a>
                <a href="#" class="btn btn-outline-success btn-sm">Ver estadísticas</a>
            </div>
        </div>
        @endif

        <!-- Sección visible para otros roles -->
        @if(!in_array($rol, ['SST','ADMIN']))
        <div class="card mb-3 border-secondary">
            <div class="card-body">
                <h5 class="card-title">Acciones</h5>
                <p class="card-text">Acciones disponibles para tu rol.</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Widgets laterales -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Alertas</h6>
                <p class="mb-0">0 alertas por vencer (ejemplo). Aquí listar alertas desde la tabla `alertas`.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Accesos rápidos</h6>
                <ul class="list-unstyled mb-0">
                    <li><a href="#">Propietarios</a></li>
                    <li><a href="#">Conductores</a></li>
                    <li><a href="#">Vehículos</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
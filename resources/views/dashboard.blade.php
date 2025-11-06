@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
$user = $user ?? auth()->user();
// Asumimos que el controlador/middleware ya validó que $user existe y tiene rol válido.
$rol = $user->rol;
@endphp

{{--Encabezdo--}}
<br><br>
<div class="contentor mb-4">
    <h4 class="fw-bold">Bienvenido a la página principal</h4>
    <p class="text-muted mb-0">
        Resumen del estado del cumplimiento documental y actividad del sistema <br>
        <small>Última actualización: 25 e agosto, 19:31</small>
    </p>
    <div>
        <span class="badge bg-secondary">Rol: {{ $rol }}</span>
    </div>
</div>



<!-- Tarjetas de resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Total Vehículos</h6>
                <h3 class="fw-bold text-success">1.247</h3>
                <small class="text-success">+12% este mes <i class="fa-solid fa-arrow-up"></i></small>
            </div>
        </div>
    </div>


    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Conductores Activos</h6>
                <h3 class="fw-bold text-success">892</h3>
                <small class="text-succes">+12% este mes <i class="fa-solid fa-arrow-up"></i></small>
            </div>
        </div>
    </div>


    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Documentos por Vencer</h6>
                <h3 class="fw-bold text-warning">2</h3>
                <small class="text-warning">Próximos 30 días</small>
            </div>
        </div>
    </div>


    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Vencidos</h6>
                <h3 class="fw-bold text-danger">1,2</h3>
                <small class="text-danger">+12% este mes</small>
            </div>
        </div>
    </div>

    <!-- Secciones Actividad reciente y alertas -->

    <div class="row g4 mb-4 mt-5">
        <div class="col-lg-8">
            <div class="stat-card shadow-lg border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 ">
                        <h6 class="fw-bold mb-0">Actividad Reciente</h6>
                        <a href="#" class="text-success small">Ver todo</a>
                    </div>


                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 bg-success-subtle rounded mb-2">
                            <i class="fa-solid fa-circle-check text-success me-2"></i>
                            Vehículo <strong>ABC123</strong> Registrado exitosamente
                            <small class="text-muted float-end">Hace 15 minutos</small>
                        </div>


                        <div class="list-group-item border-0 bg-warning-subtle rounded mb-2">
                            <i class="fa-solid fa-id-card text-warning me-2"></i>
                            Conductor <strong>Carlos Rodríguez</strong>Licencia Actualizada
                            <small class="text-muted float-end">Hace 5 minutos</small>
                        </div>

                        <div class="list-group-item border-0 bg-success-subtle rounded ">
                            <i class="fa-solid fa-file-circle-check text-success me-2"></i>
                            Reporte generado correctamente
                            <small class="text-muted float-end">Hace 20 minutos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!--alertas urgentes -->

        <div class="col-lg-4">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-danger">Alertas Urgentes</h6>

                    <div class="alert alert-danger py-2 mb-2">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        Licencia de conducción vencida - <strong> Maria Gonzalez</strong>
                        <small>Vencida hace 3 dias - <a href="#" class="text-danger fw-semibold">Revisar</a></small>
                    </div>


                    <div class="alert alert-warning py-2 mb-2">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        Tecnomecánica próxima a vencer — <strong>Vehículo AJV45F</strong><br>
                        <small>Vence en 3 días</small>
                    </div>


                    <div class="alert alert-warning py-2">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        SOAT próximo a vencer — <strong>5 vehículos</strong><br>
                        <small><a href="#" class="text-warning fw-semibold">Ver lista</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>




    {{-- Footer --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>

</div>
@endsection
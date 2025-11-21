@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
$user = $user ?? auth()->user();
$rol = $user->rol ?? 'N/A';
// Valores dinámicos: el controlador debe pasar estos datos opcionalmente.
$totalVehiculos = $totalVehiculos ?? 1247;
$conductoresActivos = $conductoresActivos ?? 892;
$porVencerCount = $porVencerCount ?? 2;
$vencidosCount = $vencidosCount ?? 12; // número entero, no "1,2"
// Alertas: el controlador debe pasar $alertas (LengthAwarePaginator o Collection paginada)
$alertas = $alertas ?? collect();
@endphp

<br><br>
<div class="contentor mb-4">
    <h4 class="fw-bold">Bienvenido a la página principal</h4>
    <p class="text-muted mb-0">
        Resumen del estado del cumplimiento documental y actividad del sistema <br>
        <small>Última actualización: {{ $ultima_actualizacion ?? now()->format('d M, H:i') }}</small>
    </p>
    <div class="mt-2">
        <span class="badge bg-secondary">Rol: {{ $rol }}</span>
    </div>
</div>

<!-- Tarjetas de resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Total Vehículos</h6>
                <h3 class="fw-bold text-success">{{ number_format($totalVehiculos) }}</h3>
                <small class="text-success">+12% este mes <i class="fa-solid fa-arrow-up"></i></small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Conductores Activos</h6>
                <h3 class="fw-bold text-success">{{ number_format($conductoresActivos) }}</h3>
                <small class="text-success">+12% este mes <i class="fa-solid fa-arrow-up"></i></small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Documentos por vencer</h6>
                <h3 class="fw-bold text-warning">{{ $porVencerCount }}</h3>
                <small class="text-warning">Próximos 30 días</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-lg h-100">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Vencidos</h6>
                <h3 class="fw-bold text-danger">{{ $vencidosCount }}</h3>
                <small class="text-danger">Comparativa mensual</small>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<div class="container mb-4">
    <h3>Alertas</h3>

    <form method="POST" action="{{ route('alertas.mark_all_read') }}">
        @csrf
        <button class="btn btn-sm btn-outline-primary">Marcar todas como leídas</button>
    </form>

    <div class="list-group mt-3">
        @forelse($alertas as $a)
        <div id="alert-row-{{ $a->id_alerta }}" class="list-group-item d-flex justify-content-between {{ $a->leida ? 'text-muted' : '' }}">
            <div>
                <strong>{{ $a->tipo_vencimiento }}</strong> — {{ $a->mensaje }}
                <div><small class="text-muted">{{ optional($a->fecha_alerta)->format('Y-m-d') ?? $a->fecha_alerta }}</small></div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('alertas.show', $a->id_alerta) }}" class="btn btn-sm btn-primary">Ver</a>
                @if(!$a->leida)
                <button onclick="event.preventDefault(); markAlertRead({{ $a->id_alerta }} )" class="btn btn-sm btn-outline-success">Marcar leída</button>
                @endif
            </div>
        </div>
        @empty
        <div class="list-group-item text-center text-muted">No hay alertas.</div>
        @endforelse
    </div>

    @if(method_exists($alertas, 'links'))
    <div class="mt-3">{{ $alertas->links() }}</div>
    @endif
</div>

{{-- Footer --}}
<footer class="text-center mt-5 mb-3 text-muted small">
    © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
</footer>

{{-- JS para marcar alerta como leída (usa fetch + rutas definidas) --}}
@push('scripts')
<script>
    function markAlertRead(id) {
        fetch('/alertas/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(json => {
                if (json.ok) {
                    // actualizar contador del nav (si existe endpoint)
                    fetch('{{ route("alertas.unread_count") }}').then(r => r.json()).then(d => {
                        const badge = document.getElementById('alerts-badge');
                        if (badge) badge.innerText = d.unread;
                    }).catch(() => {});
                    // aplicar estilo visual
                    const row = document.getElementById('alert-row-' + id);
                    if (row) row.classList.add('text-muted');
                } else if (json.error) {
                    console.error(json.error);
                }
            }).catch(err => console.error(err));
    }
</script>
@endpush

@endsection
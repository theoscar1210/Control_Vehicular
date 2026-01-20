@extends('layouts.app')

@section('title','Gestión de Usuarios')

@section('content')
<div class="container mt-4">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="titulo-seccion mb-0">
            <i class="fas fa-users me-2 text-success"></i>Gestión de Usuarios
        </h3>
        <a href="{{ route('usuarios.create') }}" class="btn btn-principal btn-sm">
            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
        </a>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Mensaje de error --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Botones de acción --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <div class="d-flex gap-2 align-items-center">
                <span class="text-muted me-2"><i class="fas fa-hand-pointer me-1"></i>Seleccione un usuario:</span>
                <button type="button" id="btnEditar" class="btn btn-warning btn-sm" disabled>
                    <i class="fas fa-edit me-1"></i>Editar
                </button>
                <button type="button" id="btnEliminar" class="btn btn-danger btn-sm" disabled>
                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                </button>
                <span id="seleccionInfo" class="ms-auto text-muted small"></span>
            </div>
        </div>
    </div>

    {{-- Formulario oculto para eliminar --}}
    <form id="formEliminar" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Tarjeta contenedora --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            {{-- Tabla responsive --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                    <thead class="table-usuarios">
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <i class="fas fa-check-square"></i>
                            </th>
                            <th style="width: 50px;">#</th>
                            <th>Nombre</th>
                            <th style="width: 100px;">Usuario</th>
                            <th>Email</th>
                            <th style="width: 90px;">Rol</th>
                            <th style="width: 90px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="table-usuarios-body">
                        @forelse($usuarios as $u)
                        <tr class="fila-usuario" data-id="{{ $u->id_usuario }}" data-nombre="{{ $u->nombre }} {{ $u->apellido }}">
                            <td class="text-center">
                                <input type="radio" name="usuario_seleccionado" value="{{ $u->id_usuario }}" class="form-check-input radio-usuario" style="cursor: pointer;">
                            </td>
                            <td>{{ $u->id_usuario }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="circulo-redondo me-2" style="width: 32px; height: 32px; font-size: 11px;">
                                        {{ strtoupper(substr($u->nombre, 0, 1)) }}{{ strtoupper(substr($u->apellido, 0, 1)) }}
                                    </div>
                                    <strong class="text-nowrap">{{ $u->nombre }} {{ $u->apellido }}</strong>
                                </div>
                            </td>
                            <td><code class="small">{{ $u->usuario }}</code></td>
                            <td class="small">{{ $u->email }}</td>
                            <td>
                                @php
                                    $rolColors = [
                                        'ADMIN' => 'danger',
                                        'SST' => 'primary',
                                        'PORTERIA' => 'secondary'
                                    ];
                                    $color = $rolColors[$u->rol] ?? 'info';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $u->rol }}</span>
                            </td>
                            <td>
                                @if($u->activo)
                                <span class="badge bg-success"><i class="fas fa-check"></i> Sí</span>
                                @else
                                <span class="badge bg-secondary"><i class="fas fa-times"></i> No</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-2 d-block"></i>
                                No hay usuarios registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación --}}
        <div class="card-footer bg-white border-0">
            {{ $usuarios->links() }}
        </div>
    </div>

    {{-- Footer --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>
</div>

{{-- Modal de confirmación para eliminar --}}
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar al usuario <strong id="nombreUsuarioEliminar"></strong>?</p>
                <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.radio-usuario');
    const btnEditar = document.getElementById('btnEditar');
    const btnEliminar = document.getElementById('btnEliminar');
    const seleccionInfo = document.getElementById('seleccionInfo');
    const formEliminar = document.getElementById('formEliminar');
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));
    const nombreUsuarioEliminar = document.getElementById('nombreUsuarioEliminar');
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');

    let usuarioSeleccionado = null;
    let nombreSeleccionado = '';

    // Seleccionar fila completa al hacer clic
    document.querySelectorAll('.fila-usuario').forEach(function(fila) {
        fila.style.cursor = 'pointer';
        fila.addEventListener('click', function(e) {
            if (e.target.type !== 'radio') {
                const radio = this.querySelector('.radio-usuario');
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
    });

    // Manejar cambio en radio buttons
    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                usuarioSeleccionado = this.value;
                const fila = this.closest('.fila-usuario');
                nombreSeleccionado = fila.dataset.nombre;

                // Habilitar botones
                btnEditar.disabled = false;
                btnEliminar.disabled = false;

                // Mostrar info de selección
                seleccionInfo.innerHTML = '<i class="fas fa-user-check text-success me-1"></i>Seleccionado: <strong>' + nombreSeleccionado + '</strong>';

                // Resaltar fila seleccionada
                document.querySelectorAll('.fila-usuario').forEach(f => f.classList.remove('table-active'));
                fila.classList.add('table-active');
            }
        });
    });

    // Botón Editar
    btnEditar.addEventListener('click', function() {
        if (usuarioSeleccionado) {
            window.location.href = '{{ url("usuarios") }}/' + usuarioSeleccionado + '/edit';
        }
    });

    // Botón Eliminar - mostrar modal
    btnEliminar.addEventListener('click', function() {
        if (usuarioSeleccionado) {
            nombreUsuarioEliminar.textContent = nombreSeleccionado;
            modalEliminar.show();
        }
    });

    // Confirmar eliminación
    btnConfirmarEliminar.addEventListener('click', function() {
        if (usuarioSeleccionado) {
            formEliminar.action = '{{ url("usuarios") }}/' + usuarioSeleccionado;
            formEliminar.submit();
        }
    });
});
</script>

<style>
.fila-usuario:hover {
    background-color: #f8f9fa;
}
.fila-usuario.table-active {
    background-color: #e8f5e9 !important;
}
.radio-usuario {
    width: 18px;
    height: 18px;
}
.circulo-redondo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #5B8238;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.table th, .table td {
    white-space: nowrap;
    padding: 0.5rem 0.75rem;
}
</style>
@endsection

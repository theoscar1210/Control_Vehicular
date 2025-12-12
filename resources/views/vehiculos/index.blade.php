@php ($navbarEspecial = true)

@php($ocultarNavbar = true)
@php($sinPadding = true)



@extends('layouts.app')


@section('title', 'Gestión de Vehículos')

@section('content')
<br><br><br>
<div class="container-fluid py-4">

    {{-- ENCABEZADO --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h3 class="fw-bold text-dark">Gestión de Vehículos</h3>

        @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
        <a href="{{ route('vehiculos.create') }}"
            class="btn px-3 py-2"
            style="background-color:#5B8238; color:white; border-radius:12px;">
            <i class="fa-solid fa-circle-plus me-1"></i> Nuevo Vehículo
        </a>
        @endif
    </div>

    {{-- TARJETA PRINCIPAL --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-3 p-md-4">

            {{-- BUSCADOR --}}
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="Buscar vehículo...">





            {{-- TABLA RESPONSIVA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center" id="vehiculosTable">

                    {{-- CABECERA --}}
                    <thead style=" background-color:#5B8238; color:white;">
                        <tr>
                            <th>#</th>
                            <th>Placa</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Color</th>
                            <th>Tipo</th>
                            <th>Propietario</th>
                            <th>Conductor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    {{-- CUERPO --}}
                    <tbody>
                        @forelse ($vehiculos as $vehiculo)
                        <tr class="table-row-custom">
                            <td>{{ $vehiculo->id_vehiculo }}</td>

                            <td class="fw-semibold">{{ $vehiculo->placa }}</td>

                            <td class="text-truncate" style="max-width:120px;">
                                {{ $vehiculo->marca }}
                            </td>

                            <td>{{ $vehiculo->modelo }}</td>

                            <td>{{ $vehiculo->color }}</td>

                            <td>{{ $vehiculo->tipo }}</td>

                            <td class="text-truncate" style="max-width:150px;">
                                {{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}
                            </td>

                            <td class="text-truncate" style="max-width:150px;">
                                @if ($vehiculo->conductor)
                                {{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}
                                @else
                                <span class="text-muted fst-italic">Sin asignar</span>
                                @endif
                            </td>

                            <td>
                                @if ($vehiculo->estado === 'Activo')
                                <span class="badge px-3 py-2"
                                    style="background-color:#5B8238;">
                                    Activo
                                </span>
                                @else
                                <span class="badge bg-secondary px-3 py-2">Inactivo</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
                                <div class="d-flex justify-content-center gap-2">

                                    {{-- EDITAR DOCUMENTO --}}
                                    <a href="{{ route('documentos_vehiculo.edit', $vehiculo->id_vehiculo) }}"
                                        class="btn btn-sm border-0 action-edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- ELIMINAR --}}
                                    <form action="{{ route('vehiculos.destroy', $vehiculo->id_vehiculo) }}"
                                        method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm border-0 action-delete"
                                            onclick="return confirm('¿Deseas eliminar este vehículo?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                No hay vehículos registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-3 d-flex justify-content-center">
                {{ $vehiculos->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
    {{-- Footer --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>
</div>

{{-- ESTILOS PERSONALIZADOS --}}
<style>
    .table-row-custom:hover {
        background-color: #f3f8ef !important;
        transition: 0.3s ease-in-out;
    }

    .action-edit {
        background-color: #E3F2FD;
        color: #1976D2;
        border-radius: 8px;
        padding: 8px 10px;
    }

    .action-edit:hover {
        background-color: #bbdefb;
        color: #0d47a1;
    }

    .action-delete {
        background-color: #FFEBEE;
        color: #D32F2F;
        border-radius: 8px;
        padding: 8px 10px;
    }

    .action-delete:hover {
        background-color: #ffcdd2;
        color: #b71c1c;
    }

    table td:last-child,
    table th:last-child {
        white-space: nowrap;
        min-width: 120px;


    }


    /* Mejoras responsive */
    @media (max-width: 768px) {
        h3 {
            font-size: 1.3rem;
        }

        table th,
        table td {
            font-size: 0.85rem;
        }
    }
</style>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#vehiculosTable tbody tr");

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>


@endsection
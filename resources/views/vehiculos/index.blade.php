@extends('layouts.app')

@section('title', 'Gestión de Vehículos')

@section('content')
<div class="container-fluid py-4">

    {{-- Encabezado --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h3>Gestión de Vehículos</h3>


        @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
        <a href="{{ route('vehiculos.create') }}"
            class="btn"
            style="background-color:#5B8238; color:white; border:none; border-radius:10px; padding:8px 16px;">
            <i class="fa-solid fa-circle-plus me-1"></i> Nuevo Vehículo
        </a>
        @endif
    </div>

    {{-- Tarjeta principal --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-3 p-md-4">

            {{-- Tabla responsive --}}
            <div class="table-responsive-sm">
                <table class="table table-hover align-middle text-center">
                    <thead style="background-color:#5B8238; color:white;">
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
                    <tbody>
                        @forelse ($vehiculos as $vehiculo)
                        <tr>
                            <td>{{ $vehiculo->id_vehiculo }}</td>
                            <td>
                                <span>{{ $vehiculo->placa }}</span>
                            </td>
                            <td class="text-truncate" style="max-width:120px;">{{ $vehiculo->marca }}</td>
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
                                <span class="badge" style="background-color:#5B8238;">Activo</span>
                                @else
                                <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('documentos_vehiculo.edit', $vehiculo->id_vehiculo) }}"
                                        class="btn btn-sm btn-warning border-0"
                                        style="color:#1976D2; background-color:#E3F2FD; border-radius:8px;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('vehiculos.destroy', $vehiculo->id_vehiculo) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm border-0"
                                            style="color:#D32F2F; background-color:#FFEBEE; border-radius:8px;"
                                            onclick="return confirm('¿Deseas eliminar este vehículo?')">
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
                                <i class="fa-solid fa-circle-info me-2"></i>No hay vehículos registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
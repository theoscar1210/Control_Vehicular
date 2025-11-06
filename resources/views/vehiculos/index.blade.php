@extends('layouts.app')

@section('title', 'Gestión de Vehículos')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <i class="bi bi-truck-front-fill me-2"></i> Gestión de Vehículos
        </h2>
        @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
        <a href="{{ route('vehiculos.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Vehículo
        </a>
        @endif
    </div>

    {{-- Tarjeta principal --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">

            {{-- Tabla de vehículos --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Placa</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Color</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Propietario</th>
                            <th scope="col">Conductor</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehiculos as $vehiculo)
                        <tr>
                            <td>{{ $vehiculo->id_vehiculo }}</td>
                            <td><span class="badge bg-dark">{{ $vehiculo->placa }}</span></td>
                            <td>{{ $vehiculo->marca }}</td>
                            <td>{{ $vehiculo->modelo }}</td>
                            <td>{{ $vehiculo->color }}</td>
                            <td>{{ $vehiculo->tipo }}</td>
                            <td>{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}</td>
                            <td>
                                @if ($vehiculo->conductor)
                                {{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}
                                @else
                                <span class="text-muted fst-italic">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if ($vehiculo->estado === 'Activo')
                                <span class="badge bg-success">Activo</span>
                                @else
                                <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
                                <a href="{{ route('vehiculos.edit', $vehiculo->id_vehiculo) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('vehiculos.destroy', $vehiculo->id_vehiculo) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Deseas eliminar este vehículo?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle me-2"></i>No hay vehículos registrados.
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
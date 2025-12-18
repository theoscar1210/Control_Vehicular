@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Gestión de Vehículos')

@section('content')
<br><br><br>

<div class="container-fluid py-4">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Gestión de Vehículos</h3>

        @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
        <a href="{{ route('vehiculos.create') }}"
            class="btn"
            style="background-color:#5B8238;color:white;border-radius:12px;">
            <i class="fa-solid fa-circle-plus me-1"></i> Nuevo Vehículo
        </a>
        @endif
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body">

            <input type="text" id="searchInput" class="form-control mb-3" placeholder="Buscar vehículo...">

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center" id="vehiculosTable">
                    <thead style="background:#5B8238;color:white;">
                        <tr>
                            <th>Placa</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Tipo</th>
                            <th>Propietario</th>
                            <th>SOAT</th>
                            <th>Tecnomecánica</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($vehiculos as $vehiculo)

                        @php
                        $soat = $vehiculo->estado_soat;
                        $tecno = $vehiculo->estado_tecno;
                        @endphp

                        <tr>
                            <td class="fw-semibold">{{ $vehiculo->placa }}</td>
                            <td>{{ $vehiculo->marca }}</td>
                            <td>{{ $vehiculo->modelo }}</td>
                            <td>{{ $vehiculo->tipo }}</td>
                            <td>{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}</td>

                            {{-- SOAT --}}
                            {{-- SOAT --}}
                            <td>
                                @if($soat['estado'] === 'SIN_REGISTRO')
                                <span class="badge bg-secondary">Sin registro</span>
                                @else
                                <span class="badge bg-{{ $soat['clase'] }}">
                                    {{ $soat['estado'] === 'POR_VENCER' ? $soat['dias'].'d' : $soat['estado'] }}
                                </span>

                                <div class="small text-muted">
                                    {{ $soat['fecha']->format('d/m/Y') }}
                                </div>

                                {{-- ICONO RENOVAR SOLO SI ESTÁ VENCIDO --}}
                                @if($soat['estado'] === 'VENCIDO')
                                <div class="mt-1">
                                    <a href="{{ route('vehiculos.documentos.edit', [$vehiculo->id_vehiculo, $soat['id']]) }}"
                                        class="text-danger"
                                        title="Renovar SOAT">
                                        <i class="fa-solid fa-file-arrow-up"></i>
                                    </a>
                                </div>
                                @endif
                                @endif
                            </td>

                            </td>

                            {{-- TECNO --}}
                            <td>
                                @if($tecno['estado'] === 'SIN_REGISTRO')
                                <span class="badge bg-secondary">Sin registro</span>
                                @else
                                <span class="badge bg-{{ $tecno['clase'] }}">
                                    {{ $tecno['estado'] === 'POR_VENCER' ? $tecno['dias'].'d' : $tecno['estado'] }}
                                </span>

                                <div class="small text-muted">
                                    {{ $tecno['fecha']->format('d/m/Y') }}
                                </div>

                                {{-- ICONO RENOVAR SOLO SI ESTÁ VENCIDO --}}
                                @if($tecno['estado'] === 'VENCIDO')
                                <div class="mt-1">
                                    <a href="{{ route('vehiculos.documentos.edit', [$vehiculo->id_vehiculo, $tecno['id']]) }}"
                                        class="text-danger"
                                        title="Renovar Tecnomecánica">
                                        <i class="fa-solid fa-file-arrow-up"></i>
                                    </a>
                                </div>
                                @endif
                                @endif
                            </td>


                            {{-- ESTADO --}}
                            <td>
                                <span class="badge {{ $vehiculo->estado === 'Activo' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $vehiculo->estado }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">

                                    {{-- HISTORIAL --}}
                                    <a href="{{ route('vehiculos.documentos.historial', [$vehiculo->id_vehiculo, 'SOAT']) }}"
                                        class="btn btn-sm btn-outline-success"
                                        title="Historial SOAT">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </a>



                                    {{-- ELIMINAR --}}
                                    <form action="{{ route('vehiculos.destroy', $vehiculo->id_vehiculo) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Eliminar vehículo?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="10" class="text-muted py-4">
                                No hay vehículos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $vehiculos->links('pagination::bootstrap-5') }}

        </div>
    </div>
</div>
@endsection
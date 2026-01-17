@php ($navbarEspecial = true)
@php($ocultarNavbar = true)
@php($sinPadding = true)

@extends('layouts.app')

@section('title', 'Renovar Documento')

@section('content')
<div class="container py-4">

    <a href="{{ route('vehiculos.documentos.index', $vehiculo->id_vehiculo) }}"
        class="btn btn-sm btn-secondary mb-3">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>

    <div class="card shadow border-0">
        <div class="card-header bg-warning">
            <h5 class="mb-0 fw-bold">
                <i class="fa-solid fa-rotate-right me-2"></i>
                Renovar {{ str_replace('_',' ', $documento->tipo_documento) }}
            </h5>
        </div>

        <div class="card-body">
            <p class="mb-3">
                <strong>Vehículo:</strong> {{ $vehiculo->placa }}
                <br>
                <strong>Documento actual:</strong>
                v{{ $documento->version }} – {{ $documento->numero_documento }}
            </p>

            <form method="POST"
                action="{{ route('vehiculos.documentos.storeRenovacion', [$vehiculo->id_vehiculo, $documento->id_doc_vehiculo]) }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Número del documento</label>
                        <input type="text" name="numero_documento"
                            class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Entidad emisora</label>
                        <input type="text" name="entidad_emisora"
                            class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de emisión</label>
                        <input type="date" name="fecha_emision"
                            class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nueva versión</label>
                        <input type="text"
                            class="form-control"
                            value="v{{ $nuevaVersion }}"
                            disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nota</label>
                    <textarea name="nota" class="form-control"></textarea>
                </div>

                <button class="btn btn-warning w-100 fw-bold">
                    <i class="fa-solid fa-check"></i> Renovar Documento
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
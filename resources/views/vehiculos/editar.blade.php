@extends('layouts.app')

@section('title','Crear Vehículo')

@section('content')
<div class="container">
    <h3>Nuevo Vehículo</h3>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Placa</label>
                        <input name="placa" value="{{ old('placa') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Marca</label>
                        <input name="marca" value="{{ old('marca') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Modelo</label>
                        <input name="modelo" value="{{ old('modelo') }}" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Color</label>
                        <input name="color" value="{{ old('color') }}" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="Carro">Carro</option>
                            <option value="Moto">Moto</option>
                            <option value="Camion">Camión</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Propietario (ID)</label>
                        <input name="id_propietario" type="number" value="{{ old('id_propietario') }}" class="form-control" required>
                        <small class="text-muted">Usa el id de la tabla propietarios</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Conductor (ID)</label>
                        <input name="id_conductor" type="number" value="{{ old('id_conductor') }}" class="form-control">
                        <small class="text-muted">Opcional: id de la tabla conductores</small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button class="btn btn-primary">Crear vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
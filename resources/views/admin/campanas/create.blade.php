@extends('layouts.dashboard')

@section('title', 'Nueva Campaña')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Crear Nueva Campaña</h1>
    <a href="{{ route('campanas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('campanas.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Campaña</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Promoción Cambio de Aceite">
            </div>

            <div class="mb-3">
                <label for="mensaje" class="form-label">Mensaje de WhatsApp</label>
                <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required placeholder="Hola {{nombre_cliente}}, tenemos una promoción especial para ti..."></textarea>
                <small class="form-text text-muted">Variables disponibles: <code>{{nombre_cliente}}</code>, <code>{{servicio_interes}}</code></small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fecha_programada" class="form-label">Fecha y Hora Programada</label>
                    <input type="datetime-local" class="form-control" id="fecha_programada" name="fecha_programada" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="servicio_id" class="form-label">Filtro de Clientes (Por Interés)</label>
                    <select class="form-select" id="servicio_id" name="servicio_id">
                        <option value="">Enviar a Todos los Clientes</option>
                        @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id_servicio }}">Clientes de: {{ $servicio->nombreServicio }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Programar Campaña
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

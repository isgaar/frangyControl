@extends('layouts.dashboard')

@section('title', 'Nuevo tipo de vehículo')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text">
                    {{ Session::get('status') }}
                    @php
                        Session::forget('status');
                    @endphp
                </span>
            </div>
        </div>
    @endif
@stop

@section('content')
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Clasificación operativa</span>
                    <h1 class="resource-hero__title">Agregar tipos de vehículo</h1>
                    <p>Carga nuevas clasificaciones para que las órdenes identifiquen mejor cada unidad desde el inicio.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('tipo_vehiculo.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al módulo
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-form-layout">
            <section class="resource-form-card">
                <div class="resource-form-card__header">
                    <div>
                        <span class="resource-form-card__eyebrow">Captura múltiple</span>
                        <h2 class="resource-form-card__title">Tipos nuevos</h2>
                        <p class="resource-form-card__copy">Agrega varias categorías si necesitas dejar listo el catálogo en una sola pasada.</p>
                    </div>
                </div>

                {!! Form::open(['route' => 'tipo_vehiculo.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                    <div id="camposTipoContainer" class="resource-kv mt-4">
                        <div class="form-group mb-0 resource-kv__item">
                            <label for="tipo-vehiculo-0">Tipo de vehículo</label>
                            <input id="tipo-vehiculo-0" type="text" class="form-control" name="tipos[]" required oninput="formatInput(this)">
                        </div>
                    </div>

                    <div class="resource-footer-actions mt-4">
                        <button type="button" class="btn btn-outline-dark" onclick="agregarCampoTipo()">
                            <i class="fas fa-plus mr-1"></i> Agregar otro
                        </button>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('tipo_vehiculo.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar tipos</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Consejos</span>
                <h2 class="resource-form-card__title">Qué conviene usar</h2>
                <p class="resource-side-card__copy">Nombres simples y consistentes ayudan a que recepción y taller hablen el mismo idioma.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Usa clasificaciones conocidas por el equipo: Sedan, SUV, Pickup, Van.</li>
                    <li>Evita variantes repetidas del mismo concepto con escritura distinta.</li>
                    <li>Si agregas varios tipos, revísalos antes de guardar para evitar duplicados.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

<script>
    function formatInput(input) {
        input.value = input.value
            .replace(/[^A-Za-z0-9ÁÉÍÓÚÜÑáéíóúüñ\s]/g, '')
            .replace(/\s+/g, ' ')
            .trimStart()
            .replace(/\b\w/g, function(letter) {
                return letter.toUpperCase();
            });
    }

    function agregarCampoTipo() {
        var camposTipoContainer = document.getElementById('camposTipoContainer');
        var index = camposTipoContainer.children.length;
        var tipoField = document.createElement('div');
        tipoField.className = 'form-group mb-0 resource-kv__item';
        tipoField.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3" style="gap:.75rem;">
                <label for="tipo-vehiculo-${index}" class="mb-0">Tipo de vehículo</label>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarCampoTipo(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input id="tipo-vehiculo-${index}" type="text" class="form-control" name="tipos[]" required oninput="formatInput(this)">
        `;
        camposTipoContainer.appendChild(tipoField);
    }

    function eliminarCampoTipo(button) {
        button.closest('.resource-kv__item').remove();
    }
</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Nuevo servicio')

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
                    <span class="resource-hero__eyebrow">Catálogo de servicios</span>
                    <h1 class="resource-hero__title">Agregar tipos de servicio</h1>
                    <p>Crea uno o varios servicios en la misma captura para dejar listo el catálogo operativo del taller.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('tipo_servicio.index') }}" class="btn btn-outline-light">
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
                        <h2 class="resource-form-card__title">Servicios nuevos</h2>
                        <p class="resource-form-card__copy">Agrega más campos si necesitas cargar varias opciones de una sola vez.</p>
                    </div>
                </div>

                {!! Form::open(['route' => 'tipo_servicio.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                    <div id="camposTipoContainer" class="resource-kv mt-4">
                        <div class="form-group mb-0 resource-kv__item">
                            <label for="tipo-servicio-0">Nombre del servicio</label>
                            <input id="tipo-servicio-0" type="text" class="form-control" name="tipos[]" required oninput="formatInput(this)">
                        </div>
                    </div>

                    <div class="resource-footer-actions mt-4">
                        <button type="button" class="btn btn-outline-dark" onclick="agregarCampoTipo()">
                            <i class="fas fa-plus mr-1"></i> Agregar otro
                        </button>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('tipo_servicio.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar servicios</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Consejos</span>
                <h2 class="resource-form-card__title">Buenas prácticas</h2>
                <p class="resource-side-card__copy">Mantén los nombres simples para que el personal los ubique rápido al registrar órdenes.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Usa nombres cortos y claros, por ejemplo: Afinación, Diagnóstico o Lavado.</li>
                    <li>Evita caracteres especiales para conservar consistencia en el catálogo.</li>
                    <li>Si agregas varios, revisa que no repitas servicios con distinta escritura.</li>
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
                <label for="tipo-servicio-${index}" class="mb-0">Nombre del servicio</label>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarCampoTipo(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input id="tipo-servicio-${index}" type="text" class="form-control" name="tipos[]" required oninput="formatInput(this)">
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

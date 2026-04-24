@extends('layouts.dashboard')

@section('title', 'Nueva marca')

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
                    <span class="resource-hero__eyebrow">Marcas de vehículos</span>
                    <h1 class="resource-hero__title">Agregar marcas</h1>
                    <p>Crea una o varias marcas para que estén disponibles al capturar nuevas órdenes dentro del sistema.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('datosv.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al panel
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-form-layout">
            <section class="resource-form-card">
                <div class="resource-form-card__header">
                    <div>
                        <span class="resource-form-card__eyebrow">Captura múltiple</span>
                        <h2 class="resource-form-card__title">Marcas nuevas</h2>
                        <p class="resource-form-card__copy">Agrega más campos si necesitas cargar varias marcas en una sola visita.</p>
                    </div>
                </div>

                {!! Form::open(['route' => 'datosv.storeunique', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                    <div id="camposMarcaContainer" class="resource-kv mt-4">
                        <div class="form-group mb-0 resource-kv__item">
                            <label for="marca-0">Marca</label>
                            <input id="marca-0" type="text" class="form-control" name="marcas[]" required oninput="formatInput(this)">
                        </div>
                    </div>

                    <div class="resource-footer-actions mt-4">
                        <button type="button" class="btn btn-outline-dark" onclick="agregarCampoMarca()">
                            <i class="fas fa-plus mr-1"></i> Agregar otra
                        </button>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('datosv.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar marcas</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Consejos</span>
                <h2 class="resource-form-card__title">Catálogo limpio</h2>
                <p class="resource-side-card__copy">Un catálogo de marcas ordenado hace más ágil la captura de órdenes.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Usa el nombre comercial más reconocido por el equipo.</li>
                    <li>Evita duplicados con diferencias mínimas como mayúsculas o espacios extra.</li>
                    <li>Si agregas varias marcas, revisa la ortografía antes de guardar.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

<script>
    function formatInput(input) {
        input.value = input.value
            .replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, '')
            .replace(/\s+/g, ' ')
            .trimStart()
            .replace(/\b\w/g, function(letter) {
                return letter.toUpperCase();
            });
    }

    function agregarCampoMarca() {
        var camposMarcaContainer = document.getElementById('camposMarcaContainer');
        var index = camposMarcaContainer.children.length;
        var marcaField = document.createElement('div');
        marcaField.className = 'form-group mb-0 resource-kv__item';
        marcaField.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3" style="gap:.75rem;">
                <label for="marca-${index}" class="mb-0">Marca</label>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarCampoMarca(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input id="marca-${index}" type="text" class="form-control" name="marcas[]" required oninput="formatInput(this)">
        `;
        camposMarcaContainer.appendChild(marcaField);
    }

    function eliminarCampoMarca(button) {
        button.closest('.resource-kv__item').remove();
    }
</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

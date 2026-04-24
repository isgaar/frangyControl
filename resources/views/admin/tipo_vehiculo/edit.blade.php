@extends('layouts.dashboard')

@section('title', 'Editar tipo de vehículo')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text dashboard-legacy-alert__text--compact">
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
                    <h1 class="resource-hero__title">Editar tipo de vehículo</h1>
                    <p>Actualiza la etiqueta para que el catálogo siga siendo claro y consistente en todo el panel.</p>
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
                        <span class="resource-form-card__eyebrow">Edición</span>
                        <h2 class="resource-form-card__title">{{ $tipoVehiculo->tipo }}</h2>
                        <p class="resource-form-card__copy">Guarda el cambio cuando el nombre final represente correctamente la categoría.</p>
                    </div>
                </div>

                {!! Form::model($tipoVehiculo, ['route' => ['tipo_vehiculo.update', $tipoVehiculo->id_tvehiculo], 'method' => 'put']) !!}
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="tipo">Tipo de vehículo</label>
                            <input id="tipo" type="text" class="form-control" name="tipo" value="{{ $tipoVehiculo->tipo }}" required oninput="formatInput(this)">
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('tipo_vehiculo.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar tipo</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Tip</span>
                <h2 class="resource-form-card__title">Ajuste recomendado</h2>
                <p class="resource-side-card__copy">Piensa en cómo lo buscará el equipo en recepción y operación.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Usa nombres breves, sin variantes innecesarias del mismo tipo.</li>
                    <li>Si la categoría cambia mucho, revisa también las órdenes donde se usará en adelante.</li>
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
</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

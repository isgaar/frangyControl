@extends('layouts.dashboard')

@section('title', 'Editar servicio')

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
                    <span class="resource-hero__eyebrow">Catálogo de servicios</span>
                    <h1 class="resource-hero__title">Editar servicio</h1>
                    <p>Ajusta nombre, precio base y rebajas para mantener cotizaciones consistentes.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('catalogos.servicios.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-1"></i> Volver al módulo
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-form-layout">
            <section class="resource-form-card">
                <div class="resource-form-card__header">
                    <div>
                        <span class="resource-form-card__eyebrow">Edición</span>
                        <h2 class="resource-form-card__title">{{ $tipoServicio->nombreServicio }}</h2>
                        <p class="resource-form-card__copy">Guarda el cambio cuando el servicio y su precio queden listos para cotizar.</p>
                    </div>
                </div>

                <form action="{{ route('catalogos.servicios.update', $tipoServicio->id_servicio) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="nombreServicio">Nombre del servicio</label>
                            <input type="text" name="nombreServicio" id="nombreServicio" value="{{ old('nombreServicio', $tipoServicio->nombreServicio) }}" class="form-control @error('nombreServicio') is-invalid @enderror" maxlength="40" required>
                            @error('nombreServicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label for="precio_base">Precio base</label>
                            <input class="form-control" id="precio_base" name="precio_base" type="number" min="0" step="0.01" value="{{ old('precio_base', $tipoServicio->precio_base) }}">
                        </div>
                        <div class="form-group mb-0">
                            <label for="descuento_porcentaje">Rebaja %</label>
                            <input class="form-control" id="descuento_porcentaje" name="descuento_porcentaje" type="number" min="0" max="100" step="0.01" value="{{ old('descuento_porcentaje', $tipoServicio->descuento_porcentaje) }}">
                        </div>
                        <div class="form-group mb-0">
                            <label for="descuento_inicio">Inicio de rebaja</label>
                            <input class="form-control" id="descuento_inicio" name="descuento_inicio" type="date" value="{{ old('descuento_inicio', optional($tipoServicio->descuento_inicio)->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group mb-0">
                            <label for="descuento_fin">Fin de rebaja</label>
                            <input class="form-control" id="descuento_fin" name="descuento_fin" type="date" value="{{ old('descuento_fin', optional($tipoServicio->descuento_fin)->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('catalogos.servicios.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar servicio</button>
                        </div>
                    </div>
                </form>
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Tip</span>
                <h2 class="resource-form-card__title">Cómo nombrarlo</h2>
                <p class="resource-side-card__copy">Usa precios base como referencia y activa rebajas solo durante fechas concretas.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>La cotización guarda el precio calculado al momento de crearla.</li>
                    <li>Si no hay fechas, la rebaja se considera vigente mientras tenga porcentaje.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

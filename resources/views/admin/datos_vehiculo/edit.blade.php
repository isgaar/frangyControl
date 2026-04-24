@extends('layouts.dashboard')

@section('title', 'Editar marca')

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
                    <span class="resource-hero__eyebrow">Marcas de vehículos</span>
                    <h1 class="resource-hero__title">Editar marca</h1>
                    <p>Actualiza la marca para mantener consistente el catálogo usado por recepción y taller.</p>
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
                        <span class="resource-form-card__eyebrow">Edición</span>
                        <h2 class="resource-form-card__title">{{ $datoVehiculo->marca }}</h2>
                        <p class="resource-form-card__copy">Guarda el ajuste cuando el nombre refleje correctamente la marca final.</p>
                    </div>
                </div>

                {!! Form::model($datoVehiculo, ['route' => ['datosv.update', $datoVehiculo->id_vehiculo], 'method' => 'put']) !!}
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="marca">Marca</label>
                            {!! Form::UTTextOnly('marca', '', 'marca', $datoVehiculo->marca, $errors, 40, true) !!}
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('datosv.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar marca</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Tip</span>
                <h2 class="resource-form-card__title">Evita duplicados</h2>
                <p class="resource-side-card__copy">Una marca bien escrita reduce errores al seleccionar vehículos dentro de las órdenes.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Usa el nombre oficial o el más común dentro del taller.</li>
                    <li>Revisa que no exista la misma marca con otra capitalización.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

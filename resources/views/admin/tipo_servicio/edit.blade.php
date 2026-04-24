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
                    <p>Ajusta el nombre para mantener el catálogo más claro y fácil de usar al crear órdenes.</p>
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
                        <span class="resource-form-card__eyebrow">Edición</span>
                        <h2 class="resource-form-card__title">{{ $tipoServicio->nombreServicio }}</h2>
                        <p class="resource-form-card__copy">Guarda el cambio cuando el nombre final quede exactamente como quieres verlo en el panel.</p>
                    </div>
                </div>

                {!! Form::model($tipoServicio, ['route' => ['tipo_servicio.update', $tipoServicio->id_servicio], 'method' => 'put']) !!}
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="nombreServicio">Nombre del servicio</label>
                            {!! Form::UTTextOnly('nombreServicio', '', 'nombreServicio', $tipoServicio->nombreServicio, $errors, 40, true) !!}
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('tipo_servicio.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar servicio</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Tip</span>
                <h2 class="resource-form-card__title">Cómo nombrarlo</h2>
                <p class="resource-side-card__copy">Usa una etiqueta fácil de reconocer para recepción y operación.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Evita abreviaturas demasiado internas si más personas usarán el catálogo.</li>
                    <li>Un nombre claro mejora las búsquedas dentro del módulo de órdenes.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Eliminar tipo de vehículo')

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
        <section class="resource-confirm-card">
            <div class="resource-confirm-card__header">
                <div>
                    <span class="resource-form-card__eyebrow">Acción sensible</span>
                    <h1 class="resource-confirm-card__title">Eliminar tipo de vehículo</h1>
                    <p class="resource-confirm-card__copy">Revisa la clasificación antes de retirarla del catálogo operativo.</p>
                </div>
            </div>

            {!! Form::open(['route' => ['tipo_vehiculo.destroy', $tipoVehiculo->id_tvehiculo], 'method' => 'get']) !!}
                <div class="resource-kv mt-4">
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Tipo de vehículo</span>
                        <p class="resource-kv__value">{{ $tipoVehiculo->tipo }}</p>
                    </div>
                </div>

                <div class="resource-warning mt-4">
                    <strong>Importante</strong>
                    Si el tipo ya se usa en el registro de órdenes, confirma primero que no afectará tu operación diaria.
                </div>

                <div class="resource-confirm-card__footer">
                    <div class="resource-footer-actions">
                        <a href="{{ route('tipo_vehiculo.index') }}" class="btn btn-outline-dark">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Eliminar tipo</button>
                    </div>
                </div>
            {!! Form::close() !!}
        </section>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

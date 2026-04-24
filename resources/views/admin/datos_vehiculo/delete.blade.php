@extends('layouts.dashboard')

@section('title', 'Eliminar marca')

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
                    <h1 class="resource-confirm-card__title">Eliminar marca</h1>
                    <p class="resource-confirm-card__copy">Confirma la acción antes de retirar esta marca del catálogo general.</p>
                </div>
            </div>

            {!! Form::open(['route' => ['datosv.destroy', $datoVehiculo->id_vehiculo], 'method' => 'get']) !!}
                <div class="resource-kv mt-4">
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Marca</span>
                        <p class="resource-kv__value">{{ $datoVehiculo->marca }}</p>
                    </div>
                </div>

                <div class="resource-warning mt-4">
                    <strong>Importante</strong>
                    Si la marca ya se usa dentro de órdenes o historiales, revisa primero el impacto antes de eliminarla.
                </div>

                <div class="resource-confirm-card__footer">
                    <div class="resource-footer-actions">
                        <a href="{{ route('datosv.index') }}" class="btn btn-outline-dark">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Eliminar marca</button>
                    </div>
                </div>
            {!! Form::close() !!}
        </section>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

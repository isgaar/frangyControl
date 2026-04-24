@extends('layouts.dashboard')

@section('title', 'Eliminar servicio')

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
                    <h1 class="resource-confirm-card__title">Eliminar servicio</h1>
                    <p class="resource-confirm-card__copy">Revisa el nombre antes de confirmar la eliminación del catálogo.</p>
                </div>
            </div>

            {!! Form::open(['route' => ['tipo_servicio.destroy', $tipoServicio->id_servicio], 'method' => 'get']) !!}
                <div class="resource-kv mt-4">
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Nombre del servicio</span>
                        <p class="resource-kv__value">{{ $tipoServicio->nombreServicio }}</p>
                    </div>
                </div>

                <div class="resource-warning mt-4">
                    <strong>Importante</strong>
                    Si este servicio ya se usa en órdenes, verifica primero que la eliminación no afecte tu operación.
                </div>

                <div class="resource-confirm-card__footer">
                    <div class="resource-footer-actions">
                        <a href="{{ route('tipo_servicio.index') }}" class="btn btn-outline-dark">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Eliminar servicio</button>
                    </div>
                </div>
            {!! Form::close() !!}
        </section>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

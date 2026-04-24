@extends('layouts.dashboard')

@section('title', 'Eliminar usuario')

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
                    <h1 class="resource-confirm-card__title">Confirmar eliminación</h1>
                    <p class="resource-confirm-card__copy">Vas a eliminar un usuario del panel. Revisa los datos antes de continuar.</p>
                </div>

                <div class="resource-pill" style="background: rgba(248, 113, 113, 0.14); color: #991b1b;">
                    <i class="fas fa-triangle-exclamation"></i> Permanente
                </div>
            </div>

            {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'get']) !!}
                <div class="resource-kv mt-4">
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Nombre del usuario</span>
                        <p class="resource-kv__value">{{ $user->name }}</p>
                    </div>
                    <div class="resource-kv__item">
                        <span class="resource-kv__label">Correo electrónico</span>
                        <p class="resource-kv__value">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="resource-warning mt-4">
                    <strong>Importante</strong>
                    El usuario perderá acceso al sistema una vez confirmes la acción.
                </div>

                <div class="resource-confirm-card__footer">
                    <div class="resource-footer-actions">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-dark">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Eliminar usuario</button>
                    </div>
                </div>
            {!! Form::close() !!}
        </section>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

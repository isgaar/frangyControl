@extends('layouts.dashboard')

@section('title', 'Editar usuario')

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
                    <span class="resource-hero__eyebrow">Administración de accesos</span>
                    <h1 class="resource-hero__title">Editar usuario</h1>
                    <p>Actualiza el perfil, cambia correo o ajusta el rol sin salir del flujo del panel administrativo.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a usuarios
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-form-layout">
            <section class="resource-form-card">
                <div class="resource-form-card__header">
                    <div>
                        <span class="resource-form-card__eyebrow">Perfil</span>
                        <h2 class="resource-form-card__title">{{ $user->name }}</h2>
                        <p class="resource-form-card__copy">La contraseña es opcional. Solo captúrala si necesitas reemplazar la actual.</p>
                    </div>
                </div>

                {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put']) !!}
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="name">Nombre del usuario</label>
                            {!! Form::text('name', $user->name, ['class' => 'form-control', 'id' => 'name', 'oninput' => 'capitalizeInput(this)']) !!}
                            @if ($errors->has('name'))
                                <span class="text-danger d-block mt-2">{{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="form-group mb-0">
                            <label for="email">Correo electrónico</label>
                            {!! Form::email('email', $user->email, ['class' => 'form-control', 'id' => 'email']) !!}
                            @if ($errors->has('email'))
                                <span class="text-danger d-block mt-2">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        <div class="form-group mb-0">
                            <label for="password">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Deja en blanco para conservar la actual">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label for="roles">Rol</label>
                            {!! Form::select('roles', $roles->pluck('name', 'id'), null, ['class' => 'form-control', 'id' => 'roles']) !!}
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar usuario</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Sugerencias</span>
                <h2 class="resource-form-card__title">Revisión rápida</h2>
                <p class="resource-side-card__copy">Estos ajustes ayudan a que cada cuenta siga alineada con los permisos correctos.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Confirma que el correo siga siendo único antes de guardar.</li>
                    <li>Usa cambio de contraseña solo cuando sea necesario para no invalidar accesos sin aviso.</li>
                    <li>El rol define qué módulos aparecen dentro del panel del usuario.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

<script>
    function capitalizeInput(input) {
        input.value = input.value.replace(/\b\w/g, function(letter) {
            return letter.toUpperCase();
        });
    }

    function togglePasswordVisibility(fieldId, button) {
        var field = document.getElementById(fieldId);
        var icon = button.querySelector('i');

        field.type = field.type === 'password' ? 'text' : 'password';

        if (icon) {
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    }
</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

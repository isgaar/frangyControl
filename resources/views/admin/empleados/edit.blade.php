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
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-1"></i> Volver a usuarios
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
                        <p class="resource-form-card__copy">
                            @if(Auth::id() == $user->id)
                                La contraseña es opcional. Solo captúrala si necesitas reemplazar la actual.
                            @else
                                Por motivos de seguridad, solo el titular de la cuenta puede cambiar su propia contraseña.
                            @endif
                        </p>
                    </div>
                </div>

                <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="name">Nombre del usuario</label>
                            <input type="text" name="name" id="name" class="form-control" oninput="capitalizeInput(this)" value="{{ old('name', $user->name) }}">
                            @if ($errors->has('name'))
                                <span class="text-danger d-block mt-2">{{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="form-group mb-0">
                            <label for="email">Correo electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}">
                            @if ($errors->has('email'))
                                <span class="text-danger d-block mt-2">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        @if(Auth::id() == $user->id)
                        <div class="form-group mb-0">
                            <label for="password">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Deja en blanco para conservar la actual">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="form-group mb-0">
                            <label for="roles">Rol</label>
                            <select name="roles" id="roles" class="form-select">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (in_array($role->id, $user->roles->pluck('id')->toArray()) || old('roles') == $role->id) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar usuario</button>
                        </div>
                    </div>
                </form>
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

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
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
@endsection

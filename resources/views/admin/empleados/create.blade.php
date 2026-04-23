@extends('adminlte::page')

@section('content_header')
@if (Session::has('status'))
<div class="col-md-12 alert-section">
    <div class="alert alert-{{ Session::get('status_type') }}"
        style="text-align: center; padding: 5px; margin-bottom: 5px;">
        <span style="font-size: 20px; font-weight: bold;">
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
<style>
    .register-user-panel {
        border-radius: 14px;
        border: 1px solid rgba(13, 110, 253, 0.12);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
    }

    .register-user-panel .card-header {
        border-bottom: 0;
    }

    .helper-copy {
        font-size: 0.88rem;
        color: #6c757d;
    }

    .password-check {
        font-size: 0.88rem;
    }
</style>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Revisa el formulario.</strong>
    <ul class="mb-0 mt-2 pl-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card-body px-0">
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-primary register-user-panel">
                <div class="card-header bg-info">
                    <h3 class="card-title mb-0">Registrar usuario</h3>
                </div>

                <form method="POST" action="{{ route('users.store') }}" id="adminUserCreateForm" novalidate>
                    @csrf

                    <div class="card-body">
                        <div class="alert alert-light border">
                            Completa los datos básicos del usuario, asigna un rol y confirma la contraseña antes de
                            guardar.
                        </div>

                        <div class="form-group">
                            <label for="name">Nombre del usuario</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                maxlength="40" autocomplete="name" required>
                            <small class="helper-copy">Usa el nombre y apellido del empleado, tal como quedará en el
                                panel.</small>
                            <div class="invalid-feedback">Escribe el nombre completo del usuario.</div>
                            @error('name')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" autocomplete="email" required>
                            <small class="helper-copy">Este correo se usará para iniciar sesión y recuperar acceso si
                                hace falta.</small>
                            <div class="invalid-feedback">Ingresa un correo válido, por ejemplo:
                                usuario@dominio.com.</div>
                            @error('email')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="roles">Rol del usuario</label>
                            <select name="roles" id="roles" class="form-control @error('roles') is-invalid @enderror"
                                required>
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ (string) old('roles') === (string) $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                            <small class="helper-copy">El rol define qué módulos puede ver y administrar el
                                usuario.</small>
                            <div class="invalid-feedback">Selecciona un rol para continuar.</div>
                            @error('roles')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror" required
                                            minlength="8" autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-toggle-password="password">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="helper-copy">Debe tener al menos 8 caracteres.</small>
                                    <div class="invalid-feedback">Escribe una contraseña de mínimo 8
                                        caracteres.</div>
                                    <div id="passwordStrength" class="password-check mt-2 text-muted">
                                        La contraseña aún no cumple el mínimo recomendado.
                                    </div>
                                    @error('password')
                                    <span class="text-danger d-block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control" required minlength="8"
                                            autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-toggle-password="password_confirmation">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="helper-copy">Repite exactamente la misma contraseña.</small>
                                    <div class="invalid-feedback">Confirma la contraseña del usuario.</div>
                                    <div id="passwordMatch" class="password-check mt-2 text-muted">
                                        La confirmación debe coincidir para habilitar un alta segura.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-dark">Retroceder</a>
                            <button type="submit" class="btn btn-success" id="userSubmitButton">
                                Guardar usuario
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card register-user-panel border-0">
                <div class="card-body">
                    <h4 class="text-info">Antes de guardar</h4>
                    <p class="mb-2">Estas mejoras ayudan a que el alta sea más clara y rápida para el equipo:</p>
                    <ul class="pl-3 mb-0">
                        <li>El formulario valida correo, rol y confirmación de contraseña antes de enviar.</li>
                        <li>El botón de guardar muestra estado de carga y evita doble clic.</li>
                        <li>La contraseña indica si ya cumple el mínimo recomendado.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('js/validatorFields.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('adminUserCreateForm');
        var password = document.getElementById('password');
        var confirmation = document.getElementById('password_confirmation');
        var strength = document.getElementById('passwordStrength');
        var match = document.getElementById('passwordMatch');

        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (window.FormHelpers) {
                    window.FormHelpers.togglePasswordVisibility(button.dataset.togglePassword, button);
                }
            });
        });

        function updatePasswordStrength() {
            if (password.value.length >= 8) {
                strength.textContent = 'La contraseña cumple con el mínimo recomendado.';
                strength.className = 'password-check mt-2 text-success';
            } else {
                strength.textContent = 'La contraseña aún no cumple el mínimo recomendado.';
                strength.className = 'password-check mt-2 text-muted';
            }
        }

        function updatePasswordMatch() {
            if (!confirmation.value) {
                confirmation.setCustomValidity('');
                match.textContent = 'La confirmación debe coincidir para habilitar un alta segura.';
                match.className = 'password-check mt-2 text-muted';
                return;
            }

            if (password.value === confirmation.value) {
                confirmation.setCustomValidity('');
                match.textContent = 'Las contraseñas coinciden.';
                match.className = 'password-check mt-2 text-success';
            } else {
                confirmation.setCustomValidity('La confirmación no coincide.');
                match.textContent = 'Las contraseñas no coinciden todavía.';
                match.className = 'password-check mt-2 text-danger';
            }
        }

        password.addEventListener('input', function () {
            updatePasswordStrength();
            updatePasswordMatch();
        });

        confirmation.addEventListener('input', updatePasswordMatch);

        form.addEventListener('submit', function (event) {
            updatePasswordStrength();
            updatePasswordMatch();

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });

        if (window.FormHelpers) {
            window.FormHelpers.attachSubmitLoading(form, '#userSubmitButton', 'Guardando usuario...');
        }

        updatePasswordStrength();
        updatePasswordMatch();
    });
</script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Registrar usuario')

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
<style>
    .user-form-grid {
        display: grid;
        gap: 1rem;
    }

    .user-form-grid--split {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }

    .access-note {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: 1rem;
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        background: var(--dashboard-surface-soft);
        color: var(--dashboard-text);
    }

    .access-note__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--dashboard-primary-soft);
        color: var(--dashboard-primary);
    }

    .access-note__title {
        margin: 0;
        font-weight: 800;
    }

    .access-note__copy,
    .helper-copy {
        color: var(--dashboard-muted);
        font-size: .88rem;
    }

    .access-note__copy {
        margin: .2rem 0 0;
    }

    .password-check {
        color: var(--dashboard-muted);
        font-size: .88rem;
        min-height: 1.25rem;
    }

    .password-check.text-success {
        color: var(--bs-success) !important;
    }

    .password-check.text-danger {
        color: var(--bs-danger) !important;
    }

    .password-toggle {
        min-width: 44px;
    }
</style>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Revisa el formulario.</strong>
    <ul class="mb-0 mt-2 ps-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="resource-page">
    <section class="resource-hero">
        <div class="resource-hero__top">
            <div class="resource-hero__copy">
                <span class="resource-hero__eyebrow">Accesos y permisos</span>
                <h1 class="resource-hero__title">Registrar usuario</h1>
                <p>Crea una cuenta para el equipo y asigna el rol correcto dentro del panel administrativo.</p>
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
                    <span class="resource-form-card__eyebrow">Formulario</span>
                    <h2 class="resource-form-card__title">Datos de acceso</h2>
                    <p class="resource-form-card__copy">Completa el perfil, rol y contraseña inicial del nuevo usuario.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('usuarios.store') }}" id="adminUserCreateForm" novalidate>
                @csrf

                <div class="user-form-grid mt-4">
                    <div class="access-note">
                        <span class="access-note__icon" aria-hidden="true">
                            <i class="fas fa-user-shield"></i>
                        </span>
                        <div>
                            <p class="access-note__title">Alta administrativa</p>
                            <p class="access-note__copy">Usa un correo vigente y un rol acorde con las actividades reales del usuario.</p>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="name" class="form-label">Nombre del usuario</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            maxlength="40" autocomplete="name" required>
                        <small class="helper-copy">Nombre y apellido como aparecerán en el panel.</small>
                        <div class="invalid-feedback">Escribe el nombre completo del usuario.</div>
                        @error('name')
                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" autocomplete="email" required>
                        <small class="helper-copy">Correo que usará para iniciar sesión.</small>
                        <div class="invalid-feedback">Ingresa un correo válido, por ejemplo: usuario@dominio.com.</div>
                        @error('email')
                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label for="roles" class="form-label">Rol del usuario</label>
                        <select name="roles" id="roles" class="form-select @error('roles') is-invalid @enderror" required>
                            <option value="">Seleccione un rol</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ (string) old('roles') === (string) $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                        <small class="helper-copy">El rol define los módulos administrativos disponibles.</small>
                        <div class="invalid-feedback">Selecciona un rol para continuar.</div>
                        @error('roles')
                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="user-form-grid user-form-grid--split">
                        <div class="form-group mb-0">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" required
                                    minlength="8" autocomplete="new-password">
                                <button class="btn btn-outline-dark password-toggle" type="button"
                                    data-toggle-password="password" title="Mostrar u ocultar contraseña">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="helper-copy">Debe tener al menos 8 caracteres.</small>
                            <div class="invalid-feedback">Escribe una contraseña de mínimo 8 caracteres.</div>
                            <div id="passwordStrength" class="password-check mt-2">
                                La contraseña aún no cumple el mínimo recomendado.
                            </div>
                            @error('password')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" required minlength="8" autocomplete="new-password">
                                <button class="btn btn-outline-dark password-toggle" type="button"
                                    data-toggle-password="password_confirmation" title="Mostrar u ocultar contraseña">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="helper-copy">Repite exactamente la misma contraseña.</small>
                            <div class="invalid-feedback">Confirma la contraseña del usuario.</div>
                            <div id="passwordMatch" class="password-check mt-2">
                                La confirmación debe coincidir para habilitar un alta segura.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="resource-form-card__footer">
                    <div class="resource-footer-actions w-100">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-dark">Cancelar</a>
                        <button type="submit" class="btn btn-primary ms-sm-auto" id="userSubmitButton">
                            <i class="fas fa-save me-1"></i> Guardar usuario
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <aside class="resource-side-card">
            <span class="resource-form-card__eyebrow">Criterios</span>
            <h2 class="resource-form-card__title">Antes de guardar</h2>
            <p class="resource-side-card__copy">Una cuenta clara facilita auditoría, soporte y seguimiento operativo.</p>

            <ul class="resource-side-card__list mt-4">
                <li>Asigna solo el rol necesario para sus responsabilidades.</li>
                <li>Usa un correo activo al que el usuario tenga acceso.</li>
                <li>Entrega la contraseña inicial por un canal privado.</li>
            </ul>
        </aside>
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
                strength.className = 'password-check mt-2';
            }
        }

        function updatePasswordMatch() {
            if (!confirmation.value) {
                confirmation.setCustomValidity('');
                match.textContent = 'La confirmación debe coincidir para habilitar un alta segura.';
                match.className = 'password-check mt-2';
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

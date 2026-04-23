@extends('adminlte::auth.register')

@section('content')
<style>
    .public-register-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
    }

    .public-register-card .card-header {
        border-bottom: 0;
    }

    .register-note {
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="card public-register-card">
                <div class="card-header bg-info text-white">
                    <strong>{{ __('Registrar Usuario') }}</strong>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>No pudimos completar el registro.</strong>
                        <ul class="mb-0 mt-2 pl-3">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="alert alert-light border">
                        Completa los datos básicos y confirma la contraseña antes de crear la cuenta.
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="publicRegisterForm" novalidate>
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">{{ __('Nombre') }}</label>
                            <input id="name" type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required autocomplete="name" autofocus maxlength="60">
                            <small class="register-note">Escribe el nombre completo del usuario que se va a registrar.</small>
                            <div class="invalid-feedback">Ingresa el nombre completo.</div>
                            @error('name')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">{{ __('Correo Electrónico') }}</label>
                            <input id="email" type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                required autocomplete="email">
                            <small class="register-note">Usa un correo válido, por ejemplo: nombre@dominio.com.</small>
                            <div class="invalid-feedback">Ingresa un correo electrónico válido.</div>
                            @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password">{{ __('Contraseña') }}</label>
                                    <div class="input-group">
                                        <input id="password" type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" required
                                            autocomplete="new-password" minlength="8">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-toggle-password="password">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="register-note">Debe tener al menos 8 caracteres.</small>
                                    <div class="invalid-feedback">Crea una contraseña de mínimo 8 caracteres.</div>
                                    @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password-confirm">{{ __('Confirmar Contraseña') }}</label>
                                    <div class="input-group">
                                        <input id="password-confirm" type="password" name="password_confirmation"
                                            class="form-control" required autocomplete="new-password" minlength="8">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-toggle-password="password-confirm">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="register-note">Repite exactamente la contraseña para evitar errores de acceso.</small>
                                    <div class="invalid-feedback">Confirma la contraseña.</div>
                                    <div id="publicPasswordMatch" class="register-note mt-2">
                                        La confirmación debe coincidir antes de enviar.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4" id="publicRegisterSubmit">
                                {{ __('Crear Cuenta') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/validatorFields.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('publicRegisterForm');
        var password = document.getElementById('password');
        var confirmation = document.getElementById('password-confirm');
        var matchLabel = document.getElementById('publicPasswordMatch');

        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (window.FormHelpers) {
                    window.FormHelpers.togglePasswordVisibility(button.dataset.togglePassword, button);
                }
            });
        });

        function updatePasswordMatch() {
            if (!confirmation.value) {
                confirmation.setCustomValidity('');
                matchLabel.textContent = 'La confirmación debe coincidir antes de enviar.';
                matchLabel.className = 'register-note mt-2';
                return;
            }

            if (password.value === confirmation.value) {
                confirmation.setCustomValidity('');
                matchLabel.textContent = 'Las contraseñas coinciden.';
                matchLabel.className = 'register-note mt-2 text-success';
            } else {
                confirmation.setCustomValidity('Las contraseñas no coinciden.');
                matchLabel.textContent = 'Las contraseñas aún no coinciden.';
                matchLabel.className = 'register-note mt-2 text-danger';
            }
        }

        password.addEventListener('input', updatePasswordMatch);
        confirmation.addEventListener('input', updatePasswordMatch);

        form.addEventListener('submit', function (event) {
            updatePasswordMatch();

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });

        if (window.FormHelpers) {
            window.FormHelpers.attachSubmitLoading(form, '#publicRegisterSubmit', 'Creando cuenta...');
        }

        updatePasswordMatch();
    });
</script>
@endsection

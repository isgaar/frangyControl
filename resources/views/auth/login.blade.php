@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<style>
    :root {
        --login-red: #c1121f;
        --login-red-dark: #9f0712;
        --login-soft: #fff1f2;
        --login-border: rgba(185, 28, 28, 0.16);
        --login-text: #2b1114;
        --login-muted: #6b4b4f;
        --login-input: #ffffff;
    }

    html[data-theme='dark'] {
        --login-soft: rgba(248, 113, 113, 0.08);
        --login-border: rgba(248, 113, 113, 0.2);
        --login-text: #fff1f2;
        --login-muted: rgba(255, 228, 230, 0.74);
        --login-input: rgba(255, 255, 255, 0.06);
    }

    body.public-shell-body {
        font-family: 'Inter', sans-serif;
    }

    .login-page {
        min-height: calc(100vh - 150px);
        display: grid;
        place-items: center;
        color: var(--login-text);
        padding: 2rem 0;
    }

    .login-card {
        width: min(100%, 460px);
        border: 1px solid var(--login-border);
        border-radius: 12px;
        background: var(--login-soft);
        padding: 1.75rem;
    }

    .login-brand {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .login-brand img {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .login-brand small {
        display: block;
        color: var(--login-red);
        font-weight: 800;
    }

    .login-brand h1 {
        margin: 0.15rem 0 0;
        font-size: 1.6rem;
        font-weight: 800;
    }

    .login-alert {
        margin-bottom: 1rem;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        line-height: 1.5;
    }

    .login-alert-danger {
        color: #7f1d1d;
        background: #fee2e2;
        border: 1px solid #fecaca;
    }

    .login-alert-warning {
        color: #78350f;
        background: #fef3c7;
        border: 1px solid #fde68a;
    }

    .login-form {
        display: grid;
        gap: 1rem;
    }

    .login-field label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 700;
    }

    .login-input {
        min-height: 48px;
        border-radius: 10px;
        border: 1px solid var(--login-border);
        background: var(--login-input);
        color: var(--login-text);
    }

    .login-input:focus {
        border-color: var(--login-red);
        box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.16);
        color: var(--login-text);
        background: var(--login-input);
    }

    .login-input.is-invalid {
        border-color: #dc2626;
        background-image: none;
    }

    .login-field .invalid-feedback {
        display: block;
        color: #dc2626;
        font-weight: 600;
    }

    .login-password-group {
        position: relative;
    }

    .login-password-group .login-input {
        padding-right: 3.1rem;
    }

    .login-password-toggle {
        position: absolute;
        top: 50%;
        right: 0.35rem;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 8px;
        color: var(--login-muted);
        background: transparent;
    }

    .login-password-toggle:hover,
    .login-password-toggle:focus {
        color: var(--login-red);
        background: rgba(220, 38, 38, 0.08);
    }

    .login-password-toggle svg {
        width: 18px;
        height: 18px;
        pointer-events: none;
    }

    .login-meta {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        color: var(--login-muted);
        font-size: 0.92rem;
    }

    .login-remember {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
    }

    .login-check-input:checked {
        background-color: var(--login-red);
        border-color: var(--login-red);
    }

    .login-link {
        color: var(--login-red);
        font-weight: 700;
        text-decoration: none;
    }

    .login-link:hover,
    .login-link:focus {
        color: var(--login-red-dark);
        text-decoration: underline;
    }

    .login-submit {
        min-height: 48px;
        border: 1px solid var(--login-red);
        border-radius: 10px;
        color: #fff;
        background: var(--login-red);
        font-weight: 800;
    }

    .login-submit:hover,
    .login-submit:focus {
        color: #fff;
        background: var(--login-red-dark);
        border-color: var(--login-red-dark);
    }

    .login-note {
        margin: 0;
        color: var(--login-muted);
        text-align: center;
        font-size: 0.9rem;
    }

    @media (max-width: 575.98px) {
        .login-page {
            padding: 1rem 0;
        }

        .login-card {
            padding: 1.25rem;
        }
    }
</style>

<div class="container">
    <div class="login-page">
        <section class="login-card">
            <div class="login-brand">
                <img src="{{ asset('franlogo.png') }}" alt="Frangy Control">
                <div>
                    <small>Frangy Control</small>
                    <h1>Iniciar sesión</h1>
                </div>
            </div>

            @if ($errors->has('login_error'))
            <div class="login-alert login-alert-danger">
                {{ $errors->first('login_error') }}
            </div>
            @elseif ($errors->any())
            <div class="login-alert login-alert-warning">
                Revisa los campos marcados antes de continuar.
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="login-form" id="loginForm">
                @csrf

                <div class="login-field">
                    <label for="email">{{ __('Correo electrónico') }}</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control login-input @error('email') is-invalid @enderror"
                        placeholder="nombre@empresa.com"
                        required
                        autocomplete="email"
                        autofocus>
                    @error('email')
                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-field">
                    <label for="password">{{ __('Contraseña') }}</label>
                    <div class="login-password-group">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control login-input @error('password') is-invalid @enderror"
                            placeholder="Escribe tu contraseña"
                            required
                            autocomplete="current-password">
                        <button
                            class="login-password-toggle"
                            type="button"
                            id="togglePassword"
                            aria-label="Mostrar u ocultar contraseña"
                            aria-pressed="false">
                            <svg id="passwordIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-meta">
                    <label class="login-remember" for="remember">
                        <input
                            class="form-check-input login-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <span>{{ __('Recuérdame') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a class="login-link" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                    @endif
                </div>

                <button type="submit" class="btn login-submit">
                    {{ __('Entrar al panel') }}
                </button>

                <p class="login-note">
                    Solo personal autorizado puede acceder a este sistema.
                </p>
            </form>
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var passwordInput = document.getElementById('password');
        var togglePasswordButton = document.getElementById('togglePassword');
        var passwordIcon = document.getElementById('passwordIcon');

        if (!passwordInput || !togglePasswordButton || !passwordIcon) {
            return;
        }

        var openEyeIcon = [
            '<path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"></path>',
            '<circle cx="12" cy="12" r="3"></circle>'
        ].join('');

        var closedEyeIcon = [
            '<path d="m3 3 18 18"></path>',
            '<path d="M10.6 6.2A11 11 0 0 1 12 6c6.4 0 10 6 10 6a18.7 18.7 0 0 1-4.2 4.6"></path>',
            '<path d="M6.7 6.7C4 8.3 2 12 2 12s3.6 6 10 6c1.7 0 3.2-.4 4.5-1"></path>',
            '<path d="M9.9 9.9A3 3 0 1 0 14.1 14.1"></path>'
        ].join('');

        togglePasswordButton.addEventListener('click', function () {
            var isHidden = passwordInput.getAttribute('type') === 'password';

            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            togglePasswordButton.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
            passwordIcon.innerHTML = isHidden ? closedEyeIcon : openEyeIcon;
        });
    });
</script>
@endsection

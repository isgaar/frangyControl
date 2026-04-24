@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<style>
    :root {
        --login-bg: #08111f;
        --login-surface: rgba(8, 17, 31, 0.78);
        --login-surface-soft: rgba(255, 255, 255, 0.08);
        --login-border: rgba(255, 255, 255, 0.16);
        --login-text: #f8fafc;
        --login-text-soft: rgba(226, 232, 240, 0.82);
        --login-primary: #38bdf8;
        --login-primary-dark: #0ea5e9;
        --login-secondary: #f59e0b;
        --login-danger: #f87171;
        --login-shadow: 0 28px 80px rgba(2, 6, 23, 0.42);
    }

    body {
        min-height: 100vh;
        color: var(--login-text);
        font-family: 'Nunito', sans-serif;
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.22), transparent 30%),
            radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.18), transparent 26%),
            linear-gradient(135deg, rgba(8, 17, 31, 0.96), rgba(15, 23, 42, 0.9)),
            url('{{ asset("fondos/image1.jpg") }}') center/cover fixed;
    }

    #app {
        min-height: 100vh;
    }

    .navbar.navbar-light.bg-white {
        background: rgba(7, 14, 26, 0.55) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(18px);
        box-shadow: none;
    }

    .navbar-light .navbar-brand,
    .navbar-light .nav-link,
    .navbar-light .navbar-toggler {
        color: var(--login-text) !important;
        border-color: rgba(255, 255, 255, 0.16);
    }

    .navbar .header-blur {
        display: none;
    }

    main.py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 2rem !important;
    }

    .auth-login-page {
        position: relative;
    }

    .login-shell {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
    }

    .login-grid {
        width: 100%;
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(360px, 460px);
        gap: 1.75rem;
        align-items: stretch;
    }

    .login-showcase,
    .login-card {
        border: 1px solid var(--login-border);
        box-shadow: var(--login-shadow);
        backdrop-filter: blur(18px);
        animation: loginReveal 0.75s ease both;
    }

    .login-showcase {
        position: relative;
        overflow: hidden;
        min-height: 620px;
        border-radius: 32px;
        background:
            linear-gradient(180deg, rgba(6, 11, 22, 0.18), rgba(6, 11, 22, 0.72)),
            url('{{ asset("fondos/image3.jpg") }}') center/cover no-repeat;
    }

    .login-showcase::before,
    .login-showcase::after {
        content: "";
        position: absolute;
        inset: auto;
        border-radius: 999px;
        filter: blur(12px);
        opacity: 0.8;
    }

    .login-showcase::before {
        top: 1.5rem;
        right: 1.5rem;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.55), transparent 70%);
        animation: floatGlow 8s ease-in-out infinite;
    }

    .login-showcase::after {
        bottom: 2rem;
        left: 2rem;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.38), transparent 72%);
        animation: floatGlow 10s ease-in-out infinite reverse;
    }

    .login-showcase-content {
        position: relative;
        z-index: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2.25rem;
    }

    .login-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        width: fit-content;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: rgba(8, 17, 31, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: var(--login-text);
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .login-kicker::before {
        content: "";
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--login-primary), var(--login-secondary));
        box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.16);
    }

    .login-showcase h1 {
        max-width: 10ch;
        margin: 1.5rem 0 0.9rem;
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        line-height: 0.94;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .login-showcase p {
        max-width: 34rem;
        margin: 0;
        color: var(--login-text-soft);
        font-size: 1.02rem;
        line-height: 1.75;
    }

    .login-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.35rem;
    }

    .login-badges span,
    .login-showcase-stat {
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(8, 17, 31, 0.36);
        color: var(--login-text);
    }

    .login-badges span {
        padding: 0.55rem 0.95rem;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .login-showcase-points {
        display: grid;
        gap: 0.85rem;
        margin-top: 2rem;
    }

    .login-showcase-point {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.9rem;
        padding: 1rem 1.1rem;
        background: rgba(8, 17, 31, 0.38);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
    }

    .login-showcase-point-index {
        width: 2.4rem;
        height: 2.4rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.28), rgba(245, 158, 11, 0.26));
        color: var(--login-text);
        font-weight: 800;
    }

    .login-showcase-point strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 0.15rem;
    }

    .login-showcase-point span {
        color: var(--login-text-soft);
        line-height: 1.55;
    }

    .login-showcase-footer {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 2rem;
    }

    .login-showcase-stat {
        padding: 1rem 1.1rem;
        min-width: 150px;
        text-align: left;
    }

    .login-showcase-stat small {
        display: block;
        margin-bottom: 0.3rem;
        color: var(--login-text-soft);
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .login-showcase-stat strong {
        display: block;
        font-size: 1.2rem;
        line-height: 1.2;
    }

    .login-card {
        position: relative;
        z-index: 1;
        border-radius: 28px;
        background: var(--login-surface);
        padding: 2rem;
    }

    .login-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 28px 28px 0 0;
        background: linear-gradient(90deg, var(--login-primary), var(--login-secondary));
    }

    .login-card-brand {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.8rem;
    }

    .login-card-brand img {
        width: 58px;
        height: 58px;
        object-fit: contain;
        padding: 0.4rem;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .login-card-brand small {
        display: block;
        color: var(--login-primary);
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .login-card-brand h2 {
        margin: 0.2rem 0 0.25rem;
        font-size: 1.8rem;
        font-weight: 800;
    }

    .login-card-brand p {
        margin: 0;
        color: var(--login-text-soft);
        line-height: 1.6;
    }

    .login-alert {
        margin-bottom: 1.25rem;
        padding: 0.95rem 1rem;
        border-radius: 18px;
        border: 1px solid transparent;
        font-size: 0.95rem;
        line-height: 1.55;
    }

    .login-alert-danger {
        color: #fee2e2;
        background: rgba(127, 29, 29, 0.35);
        border-color: rgba(248, 113, 113, 0.35);
    }

    .login-alert-warning {
        color: #fef3c7;
        background: rgba(120, 53, 15, 0.35);
        border-color: rgba(245, 158, 11, 0.35);
    }

    .login-form {
        display: grid;
        gap: 1rem;
    }

    .login-field label {
        display: block;
        margin-bottom: 0.45rem;
        color: var(--login-text);
        font-weight: 700;
    }

    .login-input,
    .login-password-toggle,
    .login-check-input {
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .login-input {
        min-height: 54px;
        padding: 0.95rem 1rem;
        background: rgba(255, 255, 255, 0.06);
        color: var(--login-text);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .login-input::placeholder {
        color: rgba(226, 232, 240, 0.46);
    }

    .login-input:focus {
        color: var(--login-text);
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(56, 189, 248, 0.55);
        box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.15);
    }

    .login-input.is-invalid {
        border-color: rgba(248, 113, 113, 0.58);
        background-image: none;
    }

    .login-password-group {
        position: relative;
    }

    .login-password-toggle {
        position: absolute;
        top: 50%;
        right: 0.55rem;
        transform: translateY(-50%);
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.04);
        color: var(--login-text-soft);
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .login-password-toggle:hover,
    .login-password-toggle:focus {
        background: rgba(255, 255, 255, 0.1);
        color: var(--login-text);
        border-color: rgba(56, 189, 248, 0.4);
    }

    .login-password-toggle svg {
        width: 18px;
        height: 18px;
        pointer-events: none;
    }

    .login-field .invalid-feedback {
        display: block;
        margin-top: 0.45rem;
        color: #fecaca;
        font-weight: 600;
    }

    .login-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 0.2rem;
    }

    .login-remember {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        color: var(--login-text-soft);
    }

    .login-check-input {
        width: 1.1rem;
        height: 1.1rem;
        margin-top: 0;
        background-color: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.25);
    }

    .login-check-input:checked {
        background-color: var(--login-primary-dark);
        border-color: var(--login-primary-dark);
    }

    .login-link {
        color: #bae6fd;
        font-weight: 700;
        text-decoration: none;
    }

    .login-link:hover,
    .login-link:focus {
        color: #e0f2fe;
        text-decoration: underline;
    }

    .login-submit {
        min-height: 54px;
        margin-top: 0.5rem;
        border: 0;
        border-radius: 18px;
        font-weight: 800;
        letter-spacing: 0.02em;
        color: #04111f;
        background: linear-gradient(135deg, #7dd3fc, #fbbf24);
        box-shadow: 0 18px 32px rgba(14, 165, 233, 0.18);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .login-submit:hover,
    .login-submit:focus {
        transform: translateY(-1px);
        filter: brightness(1.02);
        box-shadow: 0 24px 36px rgba(14, 165, 233, 0.24);
    }

    .login-note {
        margin: 0.3rem 0 0;
        color: var(--login-text-soft);
        text-align: center;
        font-size: 0.92rem;
        line-height: 1.6;
    }

    .login-divider {
        height: 1px;
        margin: 0.35rem 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.16), transparent);
    }

    @keyframes loginReveal {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes floatGlow {
        0%, 100% {
            transform: translate3d(0, 0, 0);
        }

        50% {
            transform: translate3d(8px, -12px, 0);
        }
    }

    @media (max-width: 1199.98px) {
        .login-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .login-showcase {
            min-height: 360px;
        }

        .login-showcase h1 {
            max-width: none;
        }
    }

    @media (max-width: 767.98px) {
        main.py-4 {
            padding-top: 1rem !important;
            padding-bottom: 1.5rem !important;
        }

        .login-shell {
            min-height: auto;
        }

        .login-showcase-content,
        .login-card {
            padding: 1.35rem;
        }

        .login-showcase {
            min-height: 320px;
            border-radius: 24px;
        }

        .login-card {
            border-radius: 24px;
        }

        .login-card-brand {
            align-items: flex-start;
        }

        .login-showcase-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .login-showcase-stat {
            width: 100%;
        }

        .login-meta {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="auth-login-page">
    <div class="container login-shell">
        <div class="login-grid">
            <section class="login-showcase">
                <div class="login-showcase-content">
                    <div>
                        <span class="login-kicker">Frangy Control</span>
                        <h1>Accede y sigue la operación sin fricción.</h1>
                        <p>
                            Centraliza clientes, vehículos y órdenes de servicio desde un acceso claro,
                            rápido y pensado para el trabajo diario.
                        </p>

                        <div class="login-badges">
                            <span>Laravel 10</span>
                            <span>MySQL 8</span>
                            <span>Panel operativo</span>
                        </div>
                    </div>

                    <div>
                        <div class="login-showcase-points">
                            <div class="login-showcase-point">
                                <span class="login-showcase-point-index">01</span>
                                <div>
                                    <strong>Seguimiento centralizado</strong>
                                    <span>Consulta el estado de las órdenes y la información del cliente sin perder contexto.</span>
                                </div>
                            </div>

                            <div class="login-showcase-point">
                                <span class="login-showcase-point-index">02</span>
                                <div>
                                    <strong>Acceso estable para el equipo</strong>
                                    <span>Un flujo de entrada directo para retomar trabajo administrativo y operativo en segundos.</span>
                                </div>
                            </div>
                        </div>

                        <div class="login-showcase-footer">
                            <p>
                                Usa tu cuenta autorizada para continuar con el panel de control
                                y la administración diaria del sistema.
                            </p>

                            <div class="login-showcase-stat">
                                <small>Disponibilidad</small>
                                <strong>Acceso web en un solo punto</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="login-card">
                <div class="login-card-brand">
                    <img src="{{ asset('franlogo.png') }}" alt="Frangy Control">
                    <div>
                        <small>Acceso seguro</small>
                        <h2>Bienvenido de vuelta</h2>
                        <p>Ingresa tus credenciales para entrar al panel.</p>
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
                                class="btn login-password-toggle"
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

                    <div class="login-divider"></div>

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

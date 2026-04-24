@extends('layouts.app')

@section('title', 'Restablecer contraseña')

@section('content')
<style>
    body {
        min-height: 100vh;
        color: #f8fafc;
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 30%),
            radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.18), transparent 28%),
            linear-gradient(135deg, rgba(8, 17, 31, 0.96), rgba(15, 23, 42, 0.92)),
            url('{{ asset("fondos/image3.jpg") }}') center/cover fixed;
    }

    .auth-reset-shell {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
    }

    .auth-reset-grid {
        width: 100%;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(340px, 460px);
        gap: 1.5rem;
        align-items: stretch;
    }

    .auth-reset-showcase,
    .auth-reset-card {
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        box-shadow: 0 30px 80px rgba(2, 6, 23, 0.42);
        backdrop-filter: blur(18px);
    }

    .auth-reset-showcase {
        padding: 2rem;
        background:
            linear-gradient(180deg, rgba(6, 11, 22, 0.28), rgba(6, 11, 22, 0.78)),
            url('{{ asset("fondos/image2.jpg") }}') center/cover no-repeat;
    }

    .auth-reset-card {
        padding: 2rem;
        background: rgba(8, 17, 31, 0.82);
    }

    .auth-reset-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: rgba(56, 189, 248, 0.14);
        color: #bae6fd;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .auth-reset-kicker::before {
        content: "";
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #38bdf8, #f59e0b);
    }

    .auth-reset-showcase h1,
    .auth-reset-card h2 {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 800;
    }

    .auth-reset-showcase h1 {
        margin: 1.3rem 0 0.9rem;
        font-size: clamp(2.4rem, 5vw, 4rem);
        line-height: 0.95;
    }

    .auth-reset-showcase p,
    .auth-reset-card p {
        color: rgba(226, 232, 240, 0.82);
    }

    .auth-reset-steps {
        display: grid;
        gap: 0.9rem;
        margin-top: 1.6rem;
    }

    .auth-reset-step {
        padding: 1rem 1.1rem;
        border-radius: 20px;
        background: rgba(8, 17, 31, 0.38);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .auth-reset-step strong {
        display: block;
        margin-bottom: 0.2rem;
    }

    .auth-reset-card h2 {
        margin-bottom: 0.55rem;
        font-size: 1.9rem;
    }

    .auth-reset-field {
        margin-bottom: 1rem;
    }

    .auth-reset-field label {
        display: block;
        margin-bottom: 0.45rem;
        font-weight: 700;
    }

    .auth-reset-input {
        min-height: 54px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        color: #f8fafc;
    }

    .auth-reset-input:focus {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(56, 189, 248, 0.55);
        box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.14);
    }

    .auth-reset-submit {
        width: 100%;
        min-height: 52px;
        border: 0;
        border-radius: 16px;
        font-weight: 800;
        color: #04111f;
        background: linear-gradient(135deg, #7dd3fc, #fbbf24);
    }

    @media (max-width: 991.98px) {
        .auth-reset-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="container auth-reset-shell">
    <div class="auth-reset-grid">
        <section class="auth-reset-showcase">
            <span class="auth-reset-kicker">Nueva contraseña</span>
            <h1>Define una contraseña clara y vuelve al panel.</h1>
            <p>
                Estás en el último paso del proceso. Actualiza tu contraseña para recuperar el acceso seguro a la cuenta.
            </p>

            <div class="auth-reset-steps">
                <article class="auth-reset-step">
                    <strong>Usa una clave robusta</strong>
                    <span>Combina longitud y una frase fácil de recordar para ti, pero difícil de adivinar.</span>
                </article>

                <article class="auth-reset-step">
                    <strong>Confirma sin errores</strong>
                    <span>Repite exactamente la nueva contraseña para evitar bloqueos al iniciar sesión.</span>
                </article>
            </div>
        </section>

        <section class="auth-reset-card">
            <h2>Restablecer contraseña</h2>
            <p class="mb-4">Completa los datos de la cuenta y registra la nueva contraseña.</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="auth-reset-field">
                    <label for="email">Correo electrónico</label>
                    <input
                        id="email"
                        type="email"
                        class="form-control auth-reset-input @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ $email ?? old('email') }}"
                        required
                        autocomplete="email"
                        autofocus>

                    @error('email')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="auth-reset-field">
                    <label for="password">Nueva contraseña</label>
                    <input
                        id="password"
                        type="password"
                        class="form-control auth-reset-input @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="auth-reset-field">
                    <label for="password-confirm">Confirmar contraseña</label>
                    <input
                        id="password-confirm"
                        type="password"
                        class="form-control auth-reset-input"
                        name="password_confirmation"
                        required
                        autocomplete="new-password">
                </div>

                <button type="submit" class="btn auth-reset-submit">
                    Guardar nueva contraseña
                </button>
            </form>
        </section>
    </div>
</div>
@endsection

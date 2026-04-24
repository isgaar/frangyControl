@extends('layouts.app')

@section('title', 'Confirmar contraseña')

@section('content')
<style>
    body {
        min-height: 100vh;
        color: #f8fafc;
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 30%),
            radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.18), transparent 28%),
            linear-gradient(135deg, rgba(8, 17, 31, 0.96), rgba(15, 23, 42, 0.92)),
            url('{{ asset("fondos/image4.jpg") }}') center/cover fixed;
    }

    .auth-confirm-shell {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
    }

    .auth-confirm-grid {
        width: 100%;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 420px);
        gap: 1.5rem;
        align-items: stretch;
    }

    .auth-confirm-showcase,
    .auth-confirm-card {
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        box-shadow: 0 30px 80px rgba(2, 6, 23, 0.42);
        backdrop-filter: blur(18px);
    }

    .auth-confirm-showcase {
        padding: 2rem;
        background:
            linear-gradient(180deg, rgba(6, 11, 22, 0.28), rgba(6, 11, 22, 0.78)),
            url('{{ asset("fondos/image1.jpg") }}') center/cover no-repeat;
    }

    .auth-confirm-card {
        padding: 2rem;
        background: rgba(8, 17, 31, 0.82);
    }

    .auth-confirm-kicker {
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

    .auth-confirm-kicker::before {
        content: "";
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #38bdf8, #f59e0b);
    }

    .auth-confirm-showcase h1,
    .auth-confirm-card h2 {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 800;
    }

    .auth-confirm-showcase h1 {
        margin: 1.3rem 0 0.9rem;
        font-size: clamp(2.4rem, 5vw, 4rem);
        line-height: 0.95;
    }

    .auth-confirm-showcase p,
    .auth-confirm-card p {
        color: rgba(226, 232, 240, 0.82);
    }

    .auth-confirm-card h2 {
        margin-bottom: 0.55rem;
        font-size: 1.9rem;
    }

    .auth-confirm-field {
        margin-bottom: 1rem;
    }

    .auth-confirm-field label {
        display: block;
        margin-bottom: 0.45rem;
        font-weight: 700;
    }

    .auth-confirm-input {
        min-height: 54px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        color: #f8fafc;
    }

    .auth-confirm-input:focus {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(56, 189, 248, 0.55);
        box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.14);
    }

    .auth-confirm-submit,
    .auth-confirm-link {
        width: 100%;
        min-height: 52px;
        border-radius: 16px;
        font-weight: 800;
    }

    .auth-confirm-submit {
        border: 0;
        color: #04111f;
        background: linear-gradient(135deg, #7dd3fc, #fbbf24);
    }

    .auth-confirm-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 0.8rem;
        color: #f8fafc;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .auth-confirm-link:hover {
        color: #f8fafc;
        text-decoration: none;
    }

    @media (max-width: 991.98px) {
        .auth-confirm-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="container auth-confirm-shell">
    <div class="auth-confirm-grid">
        <section class="auth-confirm-showcase">
            <span class="auth-confirm-kicker">Seguridad</span>
            <h1>Confirma tu identidad antes de continuar.</h1>
            <p>
                Esta validación protege operaciones sensibles dentro del sistema y evita cambios no autorizados.
            </p>
        </section>

        <section class="auth-confirm-card">
            <h2>Confirmar contraseña</h2>
            <p class="mb-4">Escribe tu contraseña actual para autorizar esta acción.</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="auth-confirm-field">
                    <label for="password">Contraseña actual</label>
                    <input
                        id="password"
                        type="password"
                        class="form-control auth-confirm-input @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn auth-confirm-submit">
                    Confirmar identidad
                </button>

                @if (Route::has('password.request'))
                    <a class="auth-confirm-link" href="{{ route('password.request') }}">
                        Necesito recuperar mi contraseña
                    </a>
                @endif
            </form>
        </section>
    </div>
</div>
@endsection

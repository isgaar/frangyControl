@extends('layouts.app')

@section('title', 'Recuperar acceso')

@section('content')
<style>
    body {
        min-height: 100vh;
        color: #f8fafc;
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 30%),
            radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.18), transparent 28%),
            linear-gradient(135deg, rgba(8, 17, 31, 0.96), rgba(15, 23, 42, 0.92)),
            url('{{ asset("fondos/image1.jpg") }}') center/cover fixed;
    }

    .auth-support-shell {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
    }

    .auth-support-grid {
        width: 100%;
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(320px, 430px);
        gap: 1.5rem;
        align-items: stretch;
    }

    .auth-support-showcase,
    .auth-support-card {
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        box-shadow: 0 30px 80px rgba(2, 6, 23, 0.42);
        backdrop-filter: blur(18px);
    }

    .auth-support-showcase {
        padding: 2rem;
        background:
            linear-gradient(180deg, rgba(6, 11, 22, 0.26), rgba(6, 11, 22, 0.76)),
            url('{{ asset("fondos/image4.jpg") }}') center/cover no-repeat;
    }

    .auth-support-card {
        padding: 2rem;
        background: rgba(8, 17, 31, 0.8);
    }

    .auth-support-kicker {
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

    .auth-support-kicker::before {
        content: "";
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #38bdf8, #f59e0b);
    }

    .auth-support-showcase h1,
    .auth-support-card h2 {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 800;
    }

    .auth-support-showcase h1 {
        margin: 1.3rem 0 0.9rem;
        font-size: clamp(2.4rem, 5vw, 4rem);
        line-height: 0.95;
    }

    .auth-support-showcase p,
    .auth-support-card p {
        color: rgba(226, 232, 240, 0.82);
    }

    .auth-support-points {
        display: grid;
        gap: 0.9rem;
        margin-top: 1.6rem;
    }

    .auth-support-point {
        padding: 1rem 1.1rem;
        border-radius: 20px;
        background: rgba(8, 17, 31, 0.38);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .auth-support-point strong {
        display: block;
        margin-bottom: 0.2rem;
    }

    .auth-support-card h2 {
        margin-bottom: 0.55rem;
        font-size: 1.9rem;
    }

    .auth-support-alert {
        margin-bottom: 1rem;
        padding: 0.95rem 1rem;
        border-radius: 18px;
        border: 1px solid transparent;
    }

    .auth-support-alert.is-success {
        color: #dcfce7;
        background: rgba(22, 163, 74, 0.26);
        border-color: rgba(34, 197, 94, 0.24);
    }

    .auth-support-field {
        margin-bottom: 1rem;
    }

    .auth-support-field label {
        display: block;
        margin-bottom: 0.45rem;
        font-weight: 700;
    }

    .auth-support-input {
        min-height: 54px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        color: #f8fafc;
    }

    .auth-support-input:focus {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(56, 189, 248, 0.55);
        box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.14);
    }

    .auth-support-input::placeholder {
        color: rgba(226, 232, 240, 0.44);
    }

    .auth-support-submit,
    .auth-support-link {
        min-height: 52px;
        border-radius: 16px;
        font-weight: 800;
    }

    .auth-support-submit {
        width: 100%;
        border: 0;
        color: #04111f;
        background: linear-gradient(135deg, #7dd3fc, #fbbf24);
    }

    .auth-support-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-top: 0.8rem;
        color: #f8fafc;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .auth-support-link:hover {
        color: #f8fafc;
        text-decoration: none;
    }

    @media (max-width: 991.98px) {
        .auth-support-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="container auth-support-shell">
    <div class="auth-support-grid">
        <section class="auth-support-showcase">
            <span class="auth-support-kicker">Recuperación</span>
            <h1>Recupera tu acceso sin perder el ritmo.</h1>
            <p>
                Si olvidaste tu contraseña, te enviaremos un enlace seguro para restablecerla y volver al panel.
            </p>

            <div class="auth-support-points">
                <article class="auth-support-point">
                    <strong>Proceso guiado</strong>
                    <span>Usa el correo de tu cuenta y te mandaremos un acceso temporal para cambiar la contraseña.</span>
                </article>

                <article class="auth-support-point">
                    <strong>Seguridad primero</strong>
                    <span>El enlace se genera solo para la cuenta solicitada y mantiene protegido el panel administrativo.</span>
                </article>
            </div>
        </section>

        <section class="auth-support-card">
            <h2>Enviar enlace</h2>
            <p class="mb-4">Escribe tu correo para recibir las instrucciones de restablecimiento.</p>

            @if (session('status'))
                <div class="auth-support-alert is-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="auth-support-field">
                    <label for="email">Correo electrónico</label>
                    <input
                        id="email"
                        type="email"
                        class="form-control auth-support-input @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nombre@empresa.com"
                        required
                        autocomplete="email"
                        autofocus>

                    @error('email')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn auth-support-submit">
                    Enviar enlace de recuperación
                </button>
            </form>

            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="auth-support-link">Volver al acceso</a>
            @endif
        </section>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Verificar correo')

@section('content')
<style>
    .verify-shell {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
    }

    .verify-card {
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        background: rgba(8, 17, 31, 0.78);
        box-shadow: 0 30px 80px rgba(2, 6, 23, 0.38);
        backdrop-filter: blur(18px);
        color: #f8fafc;
    }

    .verify-card__header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1.5rem 1.5rem 1rem;
    }

    .verify-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: rgba(56, 189, 248, 0.12);
        color: #bae6fd;
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .verify-card__header h1 {
        margin: 1rem 0 0.5rem;
        font-size: clamp(2rem, 4vw, 2.7rem);
        font-weight: 800;
    }

    .verify-card__header p,
    .verify-card__body,
    .verify-note {
        color: rgba(226, 232, 240, 0.82);
    }

    .verify-card__body {
        padding: 1.5rem;
    }

    .verify-actions {
        display: flex;
        gap: 0.85rem;
        flex-wrap: wrap;
        margin-top: 1.4rem;
    }

    .verify-btn {
        min-height: 50px;
        padding: 0.8rem 1.15rem;
        border: 0;
        border-radius: 16px;
        font-weight: 800;
    }

    .verify-btn-primary {
        color: #04111f;
        background: linear-gradient(135deg, #7dd3fc, #fbbf24);
    }

    .verify-btn-secondary {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.08);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
</style>

<div class="container verify-shell">
    <div class="row justify-content-center w-100">
        <div class="col-lg-8">
            <div class="verify-card">
                <div class="verify-card__header">
                    <span class="verify-kicker">Verificación</span>
                    <h1>Revisa tu correo antes de continuar</h1>
                    <p class="mb-0">
                        Enviamos un enlace de validación a tu dirección de correo para confirmar el acceso a la cuenta.
                    </p>
                </div>

                <div class="verify-card__body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Enviamos un nuevo enlace de verificación a tu correo electrónico.
                        </div>
                    @endif

                    <p class="mb-0">
                        Abre el mensaje y sigue el enlace de verificación. Si no lo encuentras,
                        puedes solicitar uno nuevo desde aquí.
                    </p>

                    <div class="verify-actions">
                        @if (Route::has('verification.resend'))
                            <form method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="verify-btn verify-btn-primary">
                                    Reenviar enlace
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('home') }}" class="verify-btn verify-btn-secondary">
                            Ir al panel
                        </a>
                    </div>

                    <p class="verify-note mt-3 mb-0">
                        Si después de unos minutos no llega el correo, revisa tu carpeta de spam o intenta de nuevo.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

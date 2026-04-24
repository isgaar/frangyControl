@extends('layouts.app')

@section('content')
<style>
    :root {
        --landing-bg: #07131f;
        --landing-surface: rgba(7, 19, 31, 0.76);
        --landing-surface-soft: rgba(255, 255, 255, 0.06);
        --landing-border: rgba(255, 255, 255, 0.12);
        --landing-text: #f8fafc;
        --landing-text-soft: rgba(226, 232, 240, 0.78);
        --landing-primary: #38bdf8;
        --landing-primary-strong: #0ea5e9;
        --landing-accent: #f59e0b;
        --landing-shadow: 0 30px 80px rgba(2, 6, 23, 0.38);
    }

    body {
        min-height: 100vh;
        color: var(--landing-text);
        font-family: 'Nunito', sans-serif;
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 28%),
            radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.16), transparent 26%),
            linear-gradient(135deg, rgba(7, 19, 31, 0.96), rgba(15, 23, 42, 0.92)),
            url('{{ asset("fondos/image2.jpg") }}') center/cover fixed;
    }

    #app {
        min-height: 100vh;
    }

    .navbar.navbar-light.bg-white {
        background: rgba(7, 19, 31, 0.52) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(18px);
        box-shadow: none;
    }

    .navbar-light .navbar-brand,
    .navbar-light .nav-link,
    .navbar-light .navbar-toggler {
        color: var(--landing-text) !important;
        border-color: rgba(255, 255, 255, 0.16);
    }

    .navbar .header-blur {
        display: none;
    }

    main.py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 2rem !important;
    }

    .landing-page {
        position: relative;
        overflow: hidden;
    }

    .landing-page::before,
    .landing-page::after {
        content: "";
        position: fixed;
        z-index: 0;
        border-radius: 999px;
        filter: blur(22px);
        opacity: 0.85;
        pointer-events: none;
    }

    .landing-page::before {
        top: 12%;
        left: -4rem;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.28), transparent 70%);
        animation: landingFloat 12s ease-in-out infinite;
    }

    .landing-page::after {
        right: -5rem;
        bottom: 8%;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.22), transparent 72%);
        animation: landingFloat 16s ease-in-out infinite reverse;
    }

    .landing-content {
        position: relative;
        z-index: 1;
    }

    .landing-hero {
        padding: 2rem 0 1rem;
    }

    .landing-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(320px, 430px);
        gap: 1.5rem;
        align-items: stretch;
    }

    .landing-panel,
    .landing-card,
    .landing-feature,
    .landing-highlight {
        border: 1px solid var(--landing-border);
        box-shadow: var(--landing-shadow);
        backdrop-filter: blur(18px);
        animation: landingReveal 0.7s ease both;
    }

    .landing-panel {
        position: relative;
        overflow: hidden;
        min-height: 620px;
        padding: 2.2rem;
        border-radius: 34px;
        background:
            linear-gradient(180deg, rgba(7, 19, 31, 0.14), rgba(7, 19, 31, 0.74)),
            url('{{ asset("fondos/image4.jpg") }}') center/cover no-repeat;
    }

    .landing-panel::before {
        content: "";
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        width: 190px;
        height: 190px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.42), transparent 72%);
        filter: blur(10px);
    }

    .landing-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.55rem 0.95rem;
        border-radius: 999px;
        background: rgba(7, 19, 31, 0.42);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: var(--landing-text);
        font-size: 0.88rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .landing-kicker::before {
        content: "";
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--landing-primary), var(--landing-accent));
        box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.14);
    }

    .landing-headline {
        max-width: 11ch;
        margin: 1.5rem 0 1rem;
        font-size: clamp(2.8rem, 5vw, 5rem);
        line-height: 0.94;
        letter-spacing: -0.05em;
        font-weight: 900;
    }

    .landing-lead {
        max-width: 38rem;
        margin: 0;
        color: var(--landing-text-soft);
        font-size: 1.05rem;
        line-height: 1.75;
    }

    .landing-actions {
        display: flex;
        gap: 0.9rem;
        flex-wrap: wrap;
        margin-top: 1.6rem;
    }

    .landing-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 52px;
        padding: 0.85rem 1.25rem;
        border-radius: 16px;
        font-weight: 800;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .landing-btn-primary {
        color: #04111f;
        background: linear-gradient(135deg, #7dd3fc, #fbbf24);
        box-shadow: 0 18px 34px rgba(14, 165, 233, 0.18);
    }

    .landing-btn-primary:hover,
    .landing-btn-primary:focus {
        color: #04111f;
        transform: translateY(-1px);
        box-shadow: 0 24px 40px rgba(14, 165, 233, 0.22);
    }

    .landing-btn-secondary {
        color: var(--landing-text);
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .landing-btn-secondary:hover,
    .landing-btn-secondary:focus {
        color: var(--landing-text);
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.1);
    }

    .landing-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.6rem;
    }

    .landing-badges span,
    .landing-mini-stat {
        border-radius: 999px;
        background: rgba(7, 19, 31, 0.36);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .landing-badges span {
        padding: 0.55rem 0.95rem;
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--landing-text);
    }

    .landing-overview {
        display: grid;
        gap: 0.95rem;
        margin-top: 2rem;
    }

    .landing-feature {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.9rem;
        padding: 1rem 1.1rem;
        border-radius: 22px;
        background: rgba(7, 19, 31, 0.42);
    }

    .landing-feature-index {
        width: 2.5rem;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.3), rgba(245, 158, 11, 0.25));
        font-weight: 900;
    }

    .landing-feature strong {
        display: block;
        margin-bottom: 0.15rem;
        font-size: 1rem;
    }

    .landing-feature span {
        color: var(--landing-text-soft);
        line-height: 1.6;
    }

    .landing-side {
        display: grid;
        gap: 1rem;
    }

    .landing-card {
        position: relative;
        overflow: hidden;
        padding: 1.5rem;
        border-radius: 28px;
        background: var(--landing-surface);
    }

    .landing-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--landing-primary), var(--landing-accent));
    }

    .landing-brand {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .landing-brand img {
        width: 62px;
        height: 62px;
        object-fit: contain;
        padding: 0.4rem;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .landing-brand small {
        display: block;
        margin-bottom: 0.2rem;
        color: var(--landing-primary);
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .landing-brand h2 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 900;
    }

    .landing-card p {
        margin: 0;
        color: var(--landing-text-soft);
        line-height: 1.65;
    }

    .landing-checklist {
        display: grid;
        gap: 0.8rem;
        margin: 1.3rem 0 0;
        padding: 0;
        list-style: none;
    }

    .landing-checklist li {
        display: flex;
        gap: 0.8rem;
        align-items: flex-start;
        color: var(--landing-text);
    }

    .landing-checklist li::before {
        content: "";
        flex: 0 0 0.8rem;
        width: 0.8rem;
        height: 0.8rem;
        margin-top: 0.35rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--landing-primary), var(--landing-accent));
        box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.12);
    }

    .landing-sections {
        display: grid;
        gap: 1rem;
        margin-top: 1.25rem;
    }

    .landing-section-card {
        padding: 1.35rem;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .landing-section-card h3 {
        margin-bottom: 0.55rem;
        font-size: 1.12rem;
        font-weight: 800;
    }

    .landing-section-card p {
        margin-bottom: 0;
    }

    @keyframes landingReveal {
        from {
            opacity: 0;
            transform: translateY(18px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes landingFloat {
        0%, 100% {
            transform: translate3d(0, 0, 0);
        }

        50% {
            transform: translate3d(10px, -14px, 0);
        }
    }

    @media (max-width: 1199.98px) {
        .landing-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .landing-panel {
            min-height: 420px;
        }

        .landing-headline {
            max-width: none;
        }
    }

    @media (max-width: 767.98px) {
        main.py-4 {
            padding-top: 1rem !important;
        }

        .landing-hero {
            padding-top: 1rem;
        }

        .landing-panel,
        .landing-card {
            padding: 1.3rem;
            border-radius: 24px;
        }

        .landing-brand {
            align-items: flex-start;
        }

        .landing-headline {
            font-size: clamp(2.3rem, 10vw, 3.2rem);
        }
    }
</style>

@php
    $appName = config('app.name', 'Frangy Control');
@endphp

<div class="landing-page">
    <div class="container landing-content">
        <section class="landing-hero">
            <div class="landing-grid">
                <div class="landing-panel">
                    <span class="landing-kicker">Operación centralizada</span>

                    <h1 class="landing-headline">Frangy Control para trabajo diario real.</h1>

                    <p class="landing-lead">
                        Un punto de entrada claro para organizar clientes, vehículos y órdenes de servicio
                        con una interfaz pensada para el ritmo operativo del taller o del equipo administrativo.
                    </p>

                    <div class="landing-actions">
                        <a href="{{ route('login') }}" class="landing-btn landing-btn-primary">Iniciar sesión</a>
                    </div>

                    <div class="landing-overview">
                        <article class="landing-feature">
                            <span class="landing-feature-index">01</span>
                            <div>
                                <strong>Clientes y órdenes en el mismo flujo</strong>
                                <span>Evita saltar entre pantallas improvisadas y mantén el seguimiento en un solo sistema.</span>
                            </div>
                        </article>

                        <article class="landing-feature">
                            <span class="landing-feature-index">02</span>
                            <div>
                                <strong>Trabajo más claro para administración</strong>
                                <span>Consulta estados, usuarios y registros con una base consistente para la operación diaria.</span>
                            </div>
                        </article>

                        <article class="landing-feature">
                            <span class="landing-feature-index">03</span>
                            <div>
                                <strong>Acceso rápido desde navegador</strong>
                                <span>Entra al sistema desde `localhost:9000` y continúa sin instalar herramientas extra en cada equipo.</span>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="landing-side">
                    <section class="landing-card">
                        <div class="landing-brand">
                            <img src="{{ asset('franlogo.png') }}" alt="{{ $appName }}">
                            <div>
                                <small>{{ $appName }}</small>
                                <h2>Un panel que sí se siente preparado</h2>
                            </div>
                        </div>

                        <p>
                            Esta vista pública presenta el proyecto con una identidad más clara y un acceso directo
                            al panel para que el usuario entre sin distracciones.
                        </p>

                        <ul class="landing-checklist">
                            <li>Entrada directa a autenticación sin ruido innecesario.</li>
                            <li>Resumen visual del propósito del sistema.</li>
                            <li>Diseño responsivo para escritorio y móvil.</li>
                        </ul>
                    </section>

                    <section class="landing-card">
                        <div class="landing-sections">
                            <article class="landing-section-card">
                                <h3>¿Qué resuelve?</h3>
                                <p>Ordena el control operativo con una superficie de trabajo más limpia que la vista pública anterior.</p>
                            </article>

                            <article class="landing-section-card">
                                <h3>¿Para quién está pensada?</h3>
                                <p>Para personal administrativo, operadores y usuarios que necesitan entrar al panel sin perder tiempo.</p>
                            </article>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

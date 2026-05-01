@extends('layouts.app')

@section('content')
<style>
    :root {
        --welcome-red: #c1121f;
        --welcome-red-dark: #9f0712;
        --welcome-soft: #fff1f2;
        --welcome-border: rgba(185, 28, 28, 0.14);
        --welcome-text: #2b1114;
        --welcome-muted: #6b4b4f;
    }

    html[data-theme='dark'] {
        --welcome-soft: rgba(248, 113, 113, 0.08);
        --welcome-border: rgba(248, 113, 113, 0.18);
        --welcome-text: #fff1f2;
        --welcome-muted: rgba(255, 228, 230, 0.74);
    }

    body.public-shell-body {
        font-family: 'Inter', sans-serif;
    }

    .welcome-page {
        color: var(--welcome-text);
    }

    .welcome-hero {
        min-height: calc(100vh - 150px);
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 390px);
        gap: 2rem;
        align-items: center;
        padding: 2rem 0;
    }

    .welcome-copy {
        max-width: 680px;
    }

    .welcome-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        font-weight: 800;
        color: var(--welcome-red);
    }

    .welcome-brand img {
        width: 44px;
        height: 44px;
        object-fit: contain;
    }

    .welcome-title {
        margin: 0;
        font-size: clamp(2.2rem, 5vw, 4.6rem);
        line-height: 1.02;
        font-weight: 800;
    }

    .welcome-lead {
        max-width: 58ch;
        margin: 1.25rem 0 0;
        color: var(--welcome-muted);
        font-size: 1.05rem;
        line-height: 1.75;
    }

    .welcome-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.85rem;
        margin-top: 1.8rem;
    }

    .welcome-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 0.8rem 1.2rem;
        border-radius: 10px;
        font-weight: 800;
        text-decoration: none;
    }

    .welcome-btn-primary {
        color: #fff;
        background: var(--welcome-red);
        border: 1px solid var(--welcome-red);
    }

    .welcome-btn-primary:hover,
    .welcome-btn-primary:focus {
        color: #fff;
        background: var(--welcome-red-dark);
        border-color: var(--welcome-red-dark);
    }

    .welcome-summary {
        border: 1px solid var(--welcome-border);
        border-radius: 12px;
        background: var(--welcome-soft);
        padding: 1.5rem;
    }

    .welcome-summary h2 {
        margin: 0 0 1rem;
        font-size: 1.25rem;
        font-weight: 800;
    }

    .welcome-list {
        display: grid;
        gap: 0.85rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .welcome-list li {
        display: flex;
        gap: 0.7rem;
        color: var(--welcome-muted);
        line-height: 1.55;
    }

    .welcome-list li::before {
        content: "";
        flex: 0 0 0.55rem;
        width: 0.55rem;
        height: 0.55rem;
        margin-top: 0.5rem;
        border-radius: 999px;
        background: var(--welcome-red);
    }

    @media (max-width: 991.98px) {
        .welcome-hero {
            min-height: auto;
            grid-template-columns: 1fr;
            align-items: start;
        }
    }
</style>

@php
    $appName = config('app.name', 'Frangy Control');
@endphp

<div class="welcome-page">
    <div class="container">
        <section class="welcome-hero">
            <div class="welcome-copy">
                <div class="welcome-brand">
                    <img src="{{ asset('franlogo.png') }}" alt="{{ $appName }}">
                    <span>{{ $appName }}</span>
                </div>

                <h1 class="welcome-title">Control operativo claro para el trabajo diario.</h1>

                <p class="welcome-lead">
                    Administra clientes, vehículos y órdenes de servicio desde una entrada limpia,
                    rápida y alineada con la identidad de la empresa.
                </p>

                <div class="welcome-actions">
                    <a href="{{ route('login') }}" class="welcome-btn welcome-btn-primary">
                        Iniciar sesión
                    </a>
                </div>
            </div>

            <aside class="welcome-summary" aria-label="Resumen del sistema">
                <h2>Enfoque del sistema</h2>
                <ul class="welcome-list">
                    <li>Acceso directo al panel sin distracciones innecesarias.</li>
                    <li>Información operativa organizada para el equipo administrativo.</li>
                    <li>Diseño ligero, responsivo y con acentos rojos de marca.</li>
                </ul>
            </aside>
        </section>
    </div>
</div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Acerca')
@php
    $pageTitle = 'Acerca del proyecto';
    $pageSubtitle = 'Contexto, tecnologías y referencias de la aplicación Frangy Control.';
    $breadcrumbs = [
        ['label' => 'Panel', 'url' => route('panel.index')],
        ['label' => 'Acerca'],
    ];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident/')) {
        $browser = 'Internet Explorer';
    } elseif (str_contains($userAgent, 'Edg')) {
        $browser = 'Microsoft Edge';
    } elseif (str_contains($userAgent, 'Firefox')) {
        $browser = 'Mozilla Firefox';
    } elseif (str_contains($userAgent, 'Chrome')) {
        $browser = 'Google Chrome o derivados';
    } elseif (str_contains($userAgent, 'Safari')) {
        $browser = 'Apple Safari';
    } elseif (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR/')) {
        $browser = 'Opera';
    } else {
        $browser = 'Otro navegador';
    }
@endphp

@section('content')
<div class="dashboard-grid dashboard-grid--2">

    {{-- Tarjeta: Características del proyecto --}}
    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Proyecto</span>
        <h2 class="mt-2">Frangy Control</h2>
        <ul style="list-style:none; padding:0; margin: 0.75rem 0 0;">
            @foreach([
                ['color'=>'#378ADD','label'=>'Gestión de órdenes de servicio y seguimiento'],
                ['color'=>'#639922','label'=>'Control de inventario y refacciones'],
                ['color'=>'#BA7517','label'=>'Registro de clientes y vehículos'],
                ['color'=>'#7F77DD','label'=>'Panel administrativo con métricas'],
                ['color'=>'#D85A30','label'=>'Autenticación y roles de usuario'],
            ] as $feature)
            <li style="display:flex; align-items:center; gap:10px; padding:7px 0; border-bottom:0.5px solid rgba(128,128,128,.2); font-size:13.5px;">
                <span style="width:8px; height:8px; border-radius:50%; background:{{ $feature['color'] }}; flex-shrink:0;"></span>
                {{ $feature['label'] }}
            </li>
            @endforeach
        </ul>
        <hr style="border:none; border-top:0.5px solid rgba(128,128,128,.2); margin:1rem 0 0.5rem;">
        <div class="home-list">
            <a class="home-list__item" href="https://github.com/isgaar/frangyControl/tree/main" target="_blank" rel="noopener noreferrer">
                <div class="home-list__row">
                    <p class="home-list__title">Repositorio del proyecto</p>
                    <span class="dashboard-badge is-info">GitHub</span>
                </div>
                <p class="home-list__subtitle">Código fuente y seguimiento general del sistema.</p>
            </a>
            <a class="home-list__item" href="mailto:may17jun2002@outlook.com">
                <div class="home-list__row">
                    <p class="home-list__title">Contacto del desarrollador</p>
                    <span class="dashboard-badge is-success">Correo</span>
                </div>
                <p class="home-list__subtitle">Canal directo para dudas o seguimiento académico.</p>
            </a>
        </div>
    </article>

    {{-- Tarjeta: Tecnologías usadas --}}
    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Stack</span>
        <h2 class="mt-2">Tecnologías usadas</h2>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:1rem;">
            @foreach([
                ['bg'=>'#8892BF','text'=>'php','name'=>'PHP','ver'=> PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,'textColor'=>'#fff','fontSize'=>'26px'],
                ['bg'=>'#FF2D20','text'=>'L','name'=>'Laravel','ver'=> app()->version(),'textColor'=>'#fff','fontSize'=>'38px'],
                ['bg'=>'#1a1a1a','text'=>'Blade','name'=>'Blade','ver'=>'Templates','textColor'=>'#fff','fontSize'=>'18px'],
                ['bg'=>'#4479A1','text'=>'MySQL','name'=>'MySQL','ver'=>'Base de datos','textColor'=>'#fff','fontSize'=>'16px'],
                ['bg'=>'#F7DF1E','text'=>'JS','name'=>'JavaScript','ver'=>'Nativo','textColor'=>'#222','fontSize'=>'28px'],
                ['bg'=>'#264de4','text'=>'CSS3','name'=>'CSS3','ver'=>'Estilos','textColor'=>'#fff','fontSize'=>'20px'],
            ] as $tech)
            <div style="display:flex; flex-direction:column; align-items:center; gap:6px; padding:12px 6px; border:0.5px solid rgba(128,128,128,.2); border-radius:10px;">
                <svg width="48" height="48" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
                    <rect width="128" height="128" rx="20" fill="{{ $tech['bg'] }}"/>
                    <text x="50%" y="58%" dominant-baseline="middle" text-anchor="middle"
                          font-size="{{ $tech['fontSize'] }}" font-weight="700" fill="{{ $tech['textColor'] }}"
                          font-family="sans-serif">{{ $tech['text'] }}</text>
                </svg>
                <span style="font-size:12px; font-weight:500;">{{ $tech['name'] }}</span>
                <span style="font-size:11px; opacity:.5;">{{ $tech['ver'] }}</span>
            </div>
            @endforeach
        </div>
    </article>

    {{-- Tarjeta: Entorno de ejecución --}}
    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Stack actual</span>
        <h3 class="mt-2">Entorno de ejecución</h3>
        <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:1rem;">
            <span class="dashboard-badge is-info">Versión {{ config('app.version') }}</span>
            <span class="dashboard-badge is-success">PHP {{ PHP_VERSION }}</span>
            <span class="dashboard-badge is-warning">Laravel {{ app()->version() }}</span>
            <span class="dashboard-badge is-danger">{{ php_uname('s') }}</span>
        </div>
        <p style="margin-top:1rem; margin-bottom:0; font-size:13.5px;">
            Cliente web detectado: <strong>{{ $browser }}</strong>
        </p>
    </article>

</div>
@endsection
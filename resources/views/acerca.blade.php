@extends('layouts.dashboard')

@section('title', 'Acerca')

@php
    $pageTitle = 'Acerca del proyecto';
    $pageSubtitle = 'Contexto, créditos y referencias de la aplicación Frangy Control.';
    $breadcrumbs = [
        ['label' => 'Panel', 'url' => route('home')],
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
    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Proyecto</span>
        <h2 class="mt-2">Frangy Control</h2>
        <p class="mb-0">
            Aplicación administrativa para el centro de servicio automotriz y llantera Frangy, desarrollada
            como proyecto de estadía para obtener el grado de T.S.U. en Desarrollo de Software Multiplataforma.
        </p>
    </article>

    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Agradecimientos</span>
        <h2 class="mt-2">Créditos del desarrollo</h2>
        <p class="mb-0">
            El proyecto reconoce el acompañamiento académico del Lic. Cesar Aldaraca Juárez y la
            Ing. Rosalina Autrán Velasco, así como el apoyo constante de la familia del autor durante
            el proceso de entrega.
        </p>
    </article>

    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Stack actual</span>
        <h3 class="mt-2">Tecnología detectada</h3>
        <div class="d-flex flex-wrap mt-3 dashboard-inline-gap">
            <span class="dashboard-badge is-info">Versión {{ config('app.version') }}</span>
            <span class="dashboard-badge is-success">PHP {{ PHP_VERSION }}</span>
            <span class="dashboard-badge is-warning">Laravel {{ app()->version() }}</span>
            <span class="dashboard-badge is-danger">{{ php_uname('s') }}</span>
        </div>
        <p class="mt-3 mb-0">Cliente web detectado: {{ $browser }}</p>
    </article>

    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Referencias</span>
        <h3 class="mt-2">Enlaces útiles</h3>
        <div class="home-list mt-3">
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
</div>
@endsection

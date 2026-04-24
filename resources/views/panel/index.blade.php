@extends('layouts.dashboard')

@section('title', 'Panel')

@php
    $pageTitle = 'Panel de control';
    $pageSubtitle = 'Punto de entrada rápido para los módulos principales del sistema.';
    $breadcrumbs = [
        ['label' => 'Panel', 'url' => route('home')],
        ['label' => 'Vista rápida'],
    ];
@endphp

@section('content')
<div class="dashboard-grid dashboard-grid--3">
    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Inicio</span>
        <h2 class="mt-2">Resumen operativo</h2>
        <p>Consulta métricas, actividad reciente y alertas desde la pantalla principal.</p>
        <a href="{{ route('home') }}" class="home-card__link">Ir al inicio</a>
    </article>

    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Operación</span>
        <h2 class="mt-2">Órdenes y clientes</h2>
        <p>Accede a los módulos administrativos que concentran el trabajo diario del taller.</p>
        <a href="{{ route('ordenes.index') }}" class="home-card__link">Ver órdenes</a>
    </article>

    <article class="dashboard-panel-card">
        <span class="home-card__eyebrow">Proyecto</span>
        <h2 class="mt-2">Información general</h2>
        <p>Revisa el contexto del sistema, créditos y la referencia técnica del proyecto.</p>
        <a href="{{ route('acerca') }}" class="home-card__link">Ver acerca</a>
    </article>
</div>
@endsection
